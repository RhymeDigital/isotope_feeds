<?php

declare(strict_types=1);

/**
 * Rhyme Isotope Feeds Bundle
 * Copyright (c) 2021 Rhyme.Digital
 * @license LGPL-3.0+
 */

namespace Rhyme\IsotopeFeedsBundle\Helper;

use Contao\Environment;
use Contao\StringUtil;
use Contao\Controller;
use Contao\Database;
use Contao\System;
use Contao\Input;
use Contao\File;
use Contao\Files;
use Contao\Folder;
use Contao\FilesModel;
use Contao\PageModel;
use Contao\CoreBundle\Monolog\ContaoContext;
use Isotope\Model\Config as IsoConfig;
use Isotope\Interfaces\IsotopeProduct;
use Isotope\Model\Product;
use Isotope\Model\ProductCollection\Cart;
use Isotope\Isotope;
use Rhyme\IsotopeFeedsBundle\Feed\Rss20;
use Rhyme\IsotopeFeedsBundle\Model\GoogleTaxonomy;
use Psr\Log\LogLevel;
use Exception;

/**
 * Preserves feeds
 *
 * @copyright  Rhyme Digital 2015
 * @author     Blair Winans <blair@rhyme.digital>
 * @author     Adam Fisher <adam@rhyme.digital>
 * @package    Isotope_Feeds
 */
class IsotopeFeeds extends Controller
{

    /**
     * Cache of files for each feed
     * @var array
     */
    protected static $arrFeedCache = array();

    /**
     * Cache of directories for the XML item files
     * @var array
     */
    protected static $arrXMLDirCache = array();

	public function __construct()
    {
        parent::__construct();
    }

    /**
	 * Return a feed name from a config
	 * @param mixed \Isotope\Model\Config or Contao\DatabaseResult
	 * @return string
	 */
	public static function getFeedBaseName($objConfig)
	{
    	return $objConfig->feedName != '' ? $objConfig->feedName : 'products' . $objConfig->id;
    }

    /**
	 * Return the base feed cache dir from a config
	 * @param mixed \Isotope\Model\Config or Contao\DatabaseResult
	 * @return string
	 */
	public static function getFeedCacheBaseDir($objConfig)
	{
    	return 'isotope/cache/' . $objConfig->id;
    }

    /**
	 * Return the feed cache directories
	 * @param mixed \Isotope\Model\Config or Contao\DatabaseResult
	 * @return array
	 */
	public static function getCacheDirectories($objConfig)
	{
    	if(!is_array(static::$arrXMLDirCache[$objConfig->id] ?? null))
    	{
        	$arrFeedTypes = StringUtil::deserialize($objConfig->feedTypes, true);
    		foreach( $arrFeedTypes as $feedType )
    		{
        	    static::$arrXMLDirCache[$objConfig->id][$feedType] =  static::getFeedCacheBaseDir($objConfig) . '/' .  $feedType;
            }
        }

    	return static::$arrXMLDirCache[$objConfig->id];
    }

    /**
	 * Return the feed type from the feed file
	 * @param string
	 * @return string
	 */
	public static function getTypeFromCacheDir($objConfig, $strDir)
	{
    	$arrTypes = \array_flip(static::$arrXMLDirCache[$objConfig->id]);
    	return $arrTypes[$strDir];
    }

    /**
	 * Return an array of its feed files from a config
	 * @param mixed \Isotope\Model\Config or Contao\DatabaseResult
	 * @return array
	 */
	public static function getFeedFiles($objConfig)
	{
    	if(!\is_array(static::$arrFeedCache[$objConfig->id] ?? null))
    	{
        	static::$arrFeedCache[$objConfig->id] = array();
        	$strBase = static::getFeedBaseName($objConfig);
        	$arrTypes = StringUtil::deserialize($objConfig->feedTypes, true);
        	foreach($arrTypes as $feedType)
            {
                $strWebDir = StringUtil::stripRootDir(System::getContainer()->getParameter('contao.web_dir'));
                $strFile = $strWebDir . '/share/' . $strBase . '-' . $feedType . '.xml';
                static::$arrFeedCache[$objConfig->id][$feedType] = $strFile;
            }
        }

    	return static::$arrFeedCache[$objConfig->id];
    }


    /**
	 * Return the feed type from the feed file
	 * @param string
	 * @return string
	 */
	public static function getTypeFromFeedFile($objConfig, $strFile)
	{
    	if(!isset(static::$arrFeedCache[$objConfig->id]))
    	{
        	static::getFeedFiles($objConfig);
    	}

    	$arrTypes = array_flip(static::$arrFeedCache[$objConfig->id]);
    	return $arrTypes[$strFile];
    }

	/**
	 * Update a particular store config's RSS feeds
	 * @param integer
	 */
	public function generateFeed($intId)
	{
    	$objConfig = IsoConfig::findByPk($intId);

		if (null === $objConfig || !$objConfig->addFeed)
		{
			return;
		}

		$arrFeedFiles = static::getFeedFiles($objConfig);
        foreach($arrFeedFiles as $feedFile)
        {
			// Delete XML file
    		if (Input::get('act') == 'delete')
    		{
			    Files::getInstance()->delete($feedFile);
		    }
		    //Update
		    else
		    {
			    $this->generateFile($objConfig, $feedFile);
				$logger = static::getContainer()->get('monolog.logger.contao');
				$logger->log(LogLevel::INFO, 'Generated product feed ' . $feedFile, array('contao' => new ContaoContext(__METHOD__, ContaoContext::CRON)));
		    }
		}
	}


	/**
	 * Delete old files and generate all product feeds
	 */
	public function generateFeeds()
	{
        $this->import('Automator');
        $this->Automator->purgeXmlFiles();

		$objConfig = IsoConfig::findBy('addFeed', '1');

		if (!$objConfig) {
			return;
		}

		while ($objConfig->next())
		{
			$arrFeedFiles = static::getFeedFiles($objConfig);
			foreach( $arrFeedFiles as $feedFile)
			{
				$this->generateFile($objConfig, $feedFile);
				$logger = static::getContainer()->get('monolog.logger.contao');
				$logger->log(LogLevel::INFO, 'Generated product feed ' . $feedFile, array('contao' => new ContaoContext(__METHOD__, ContaoContext::CRON)));
			}
		}
	}
	
	
	/**
	 * Cache the product XML for each store config
	 * @param \Isotope\Interfaces\IsotopeProduct
	 */
	public function cacheProduct($intId)
	{
    	$objProduct = Product::findPublishedByPk((int) $intId);
    	if(null === $objProduct)
    	{
        	return;
    	}

		$objConfigs = IsoConfig::findBy('addFeed', '1');
		foreach ($objConfigs as $objConfig)
		{
		    // Override shop configuration to generate correct price
            $objCart = new Cart();
            $objCart->config_id = $objConfig->id;
            Isotope::setConfig($objConfig);
            Isotope::setCart($objCart);

			$arrFeedFiles = static::getFeedFiles($objConfig);
			foreach( $arrFeedFiles as $feedFile )
			{
				$this->generateProductXML($feedFile, $objProduct, $objConfig);
			}
		}
	}

	/**
	 * Generate an XML files and save them to the root directory
	 * @param array
	 * @param string
	 */
	protected function generateFile($objConfig, $strFile)
	{
    	$strType = static::getTypeFromFeedFile($objConfig, $strFile);
		$arrType = $GLOBALS['ISO_FEEDS'][$strType]['feed'];
		$time = time();
		$strLink = $objConfig->feedBase != '' ? $objConfig->feedBase : Environment::get('base');
		$projectDir = System::getContainer()->getParameter('kernel.project_dir');

		try
		{
			$objFeed = new $arrType[0]($strFile);
		}
		catch (Exception $e)
		{
			$objFeed = new Rss20($strFile);
		}

		$objFeed->link = $strLink;
		$objFeed->title = $objConfig->feedTitle;
		$objFeed->description = $objConfig->feedDesc;
        $request = System::getContainer()->get('request_stack')->getCurrentRequest();
		$objFeed->language = $objConfig->feedLanguage ?: ($request ? $request->getLocale() : 'en');
		$objFeed->published = time();
		
		$strDir = static::getFeedCacheBaseDir($objConfig) . '/' . $strType;
		
		if(is_dir($projectDir . '/' .  $strDir))
		{
			$arrFiles = Folder::scan($projectDir . '/' .  $strDir);
			
			//HOOK for other data that needs to be added and sorting the array
			if (isset($GLOBALS['ISO_HOOKS']['feedFiles']) && is_array($GLOBALS['ISO_HOOKS']['feedFiles']))
			{
				foreach ($GLOBALS['ISO_HOOKS']['feedFiles'] as $callback)
				{
					$this->import($callback[0]);
					$arrFiles = $this->{$callback[0]}->{$callback[1]}($arrFiles, $strType, $objFeed, $objConfig);
				}
			}
			
			foreach($arrFiles as $file)
			{
				if(is_file($projectDir  . '/' . $strDir . '/' . $file))
				{
					$objFile = new File($strDir . '/' . $file);
					$objFeed->addFile($objFile);
				}
			}

			// Create file
			$objFeedFile = new File($strFile);
			$objFeedFile->write(System::getContainer()->get('contao.insert_tag.parser')->replaceInline($objFeed->{$arrType[1]}()));
			$objFeedFile->close();
		}
	}

	/**
	 * Generate an XML file for a product and save it to the cache
	 * @param string - feed file
	 * @param \Isotope\Model\Product
	 * @param \Isotope\Model\Config - store config
	 */
	public function generateProductXML($feedFile, Product $objProduct, $objConfig)
	{
	    //Refresh all data from the database
        $objProduct->refresh();

		$time = \time();
		$strType = static::getTypeFromFeedFile($objConfig, $feedFile);
		$strLink = $objConfig->feedBase != '' ? $objConfig->feedBase : '';
		$strCacheFile = 'isotope/cache/' . $objConfig->id . '/' .  $strType . '/' . $objProduct->alias . ($objProduct->pid > 0 ? '-' . $objProduct->id : '') . '.xml';
		$projectDir = System::getContainer()->getParameter('kernel.project_dir');
		
		// Get root pages that belong to this store config.
		$arrPages = array();
		$productCategories = \array_map('\intval', $objProduct->getCategories(true));
		$objRoot = PageModel::findBy(array("type='root'", "iso_config"), $objConfig->id);
		if(null !== $objRoot)
		{
			$arrRoots = $objRoot->fetchEach('id');
			$arrPages = Database::getInstance()->getChildRecords($arrRoots, 'tl_page', false, $arrRoots);
		}

		// Get default URL - Check product first and if not fall back to config reader page
        $intJumpTo = $objProduct->feedJumpTo ?: $objConfig->feedJumpTo;

		if($intJumpTo == '')
		{
			//Get the first reader page we can find
			$objModules = Database::getInstance()->prepare("SELECT iso_reader_jumpTo FROM tl_module WHERE ".(count($arrPages)>0 ? "iso_reader_jumpTo IN (" . implode(',',$arrPages) . ") AND " : ''). "iso_reader_jumpTo !=''")->limit(1)->execute();

			if($objModules->numRows)
			{
				$intJumpTo = $objModules->iso_reader_jumpTo;
			}
		}

		// Ensure the product is published, it's set to be in the feed, and it's been set to a category in one of the site roots for this config
		if ($objProduct->isPublished() && $objProduct->useFeed && !empty(\array_intersect($productCategories, $arrPages)))
		{
			//Check for variants and run them instead if they exist
			if($objProduct->hasVariants() && !$objProduct->isVariant())
			{
				foreach($objProduct->getVariantIds() as $variantID)
				{
					$objVariant = Product::findPublishedByPk($variantID);
					$this->generateProductXML($feedFile, $objVariant, $objConfig);
				}
				
				//Do not run the parent and delete the cache file if it exists
                if(is_file($projectDir . '/' . $strCacheFile))
                {
                    Files::getInstance()->delete($strCacheFile);
                }
				return;
			}
			
			try
			{
				$objItem = new $GLOBALS['ISO_FEEDS'][$strType]['item']();
			}
			catch (Exception $e)
			{
				$objItem = new Rss20();
			}

			$objItem->title = $objProduct->name;
			$objItem->published = $time;

			if (($jumpToPage = PageModel::findByPk($intJumpTo)) !== null) {
				$jumpToPage->loadDetails();
			}
			$productUrl = $objProduct->generateUrl($jumpToPage);

			// The product and page model generated an absolute URL!
			if (\stripos($productUrl, 'http') === 0) {

				// If no "Feed Base Link" was provided, just use the absolute URL that was generated
				if (!$strLink) {
					$objItem->link = $productUrl;

					if ($jumpToPage !== null
						&& \trim($jumpToPage->domain)
						&& ($firstPageModel = PageModel::findFirstPublishedByPid($jumpToPage->rootId)) !== null
					) {
						$strLink = $firstPageModel->getFrontendUrl();
					}
				}
				else
				{
					// Try to adjust the absolute URL so it uses the "Feed Base Link" provided in the store config
					if ($jumpToPage !== null && ($domain = \trim($jumpToPage->domain))
					) {
						$objItem->link = $strLink . \substr($productUrl, \strpos($productUrl, $domain) + \strlen($domain) + 1);
					}
				}
			}

			// Fall back to the current domain if it's not set here
			if (empty($strLink)) {
				$strLink = Environment::get('base');
			}
			if (empty($objItem->link)) {
				$objItem->link = $strLink . $productUrl;
			}

			// Prepare the description
			if (null !== ($objType = $objProduct->getType()) &&
                (in_array('gid_description', $objType->getAttributes(), true)
                    || ($objProduct->isVariant() && in_array('gid_description', $objType->getVariantAttributes()))
                )
            ) {
                $strDescription = $objProduct->gid_description ?: $objProduct->description;
			} else {
			    $strDescription = $objProduct->description;
			}
			$strDescription = System::getContainer()->get('contao.insert_tag.parser')->replaceInline($strDescription ?? '');
			$objItem->description = $this->convertRelativeUrls($strDescription, $strLink);

			//Sku, price, etc
			$objItem->id = $objProduct->id;
			$objItem->sku = ($objProduct->sku && strlen($objProduct->sku)) ? $objProduct->sku : $objProduct->alias;
			$objItem->price = Isotope::formatPriceWithCurrency($objProduct->getPrice(Isotope::getCart())->getAmount(), false);

			//Google basic settings
			$objItem->condition = $objProduct->gid_condition;
			$objItem->availability = $objProduct->gid_availability;
			$objItem->brand = $objProduct->gid_brand;
			$objItem->gtin = $objProduct->gid_gtin;
			$objItem->mpn = $objProduct->gid_mpn;

            //Additional settings
            $objItem->color = $objProduct->gid_color;
            $objItem->material = $objProduct->gid_material;
            $objItem->pattern = $objProduct->gid_pattern;

            //Shipping settings
            $objItem->shipping_label = $objProduct->gid_shipping_label;
            $defaultShippingArr = StringUtil::deserialize($objProduct->shipping_weight, true);
            $googleShippingArr = StringUtil::deserialize($objProduct->gid_shipping_weight, true);
            $defaultShipping = $defaultShippingArr['value'] . ' ' . $defaultShippingArr['unit'];
            $googleShipping = $googleShippingArr['value'] . ' ' . $googleShippingArr['unit'];
            $objItem->shipping_weight = strlen(trim($googleShipping)) ? $googleShipping : (strlen(trim($defaultShipping)) ? $defaultShipping : false);

            $objGT = GoogleTaxonomy::findByPk($objProduct->gid_google_product_category);
            $productCategory = strlen(($objProduct->gid_google_product_category_manual ?? '')) ? $objProduct->gid_google_product_category_manual : ($objGT ? $objGT->fullname : '');
			$objItem->google_product_category = $productCategory;
			
			//Google variants only
			if($objProduct->pid>0)
			{
				$objItem->item_group_id = strlen($objProduct->sku) ? $objProduct->sku : $objProduct->alias;
			}
			
			//Custom product category taxonomy
            $productType = StringUtil::deserialize($objProduct->gid_product_type);
			$objItem->product_type = strlen(($objProduct->gid_product_type_manual ?? '')) ? $objProduct->gid_product_type_manual : $productType;
			
			//HOOK for other data that needs to be added
			if (isset($GLOBALS['ISO_HOOKS']['feedItem']) && is_array($GLOBALS['ISO_HOOKS']['feedItem']))
			{
				foreach ($GLOBALS['ISO_HOOKS']['feedItem'] as $callback)
				{
					$this->import($callback[0]);
					$objItem = $this->{$callback[0]}->{$callback[1]}($strType, $objItem, $objProduct);
				}
			}

			//Prepare the images
			$arrImages = static::getProductImages($objProduct, $objConfig, $strLink);
			if(is_array($arrImages) && count($arrImages)>0)
			{
				$objItem->image_link = $arrImages[0];
				$objItem->addEnclosure($arrImages[0]);
				unset($arrImages[0]);
				if(count($arrImages)>0)
				{
					//Additional images
					$objItem->additional_image_link = $arrImages;
				}
			}

			//Cache the file
			$objItem->cache($strCacheFile);
		}
		else
		{
			//Delete the cache file if it exists
			if(is_file($projectDir . '/' . $strCacheFile))
			{
				Files::getInstance()->delete($strCacheFile);
			}
		}
	}

	/**
	 * Return an array of the product's original and/or watermarked images
	 * @param Isotope\Interfaces\IsotopeProduct
	 * @return array
	 */
	protected static function getProductImages(IsotopeProduct $objProduct, $objConfig, $strLink)
	{
		$arrImages = array();
		$intID = $objProduct->pid ? $objProduct->pid : $objProduct->id;
		$varValue = StringUtil::deserialize($objProduct->images);
		$projectDir = System::getContainer()->getParameter('kernel.project_dir');

		if(is_array($varValue) && count($varValue))
		{
			foreach( $varValue as $k => $file )
			{
				$strFile = $file['src'];

                // File without path must be located in the isotope root folder
                if (strpos($strFile, '/') === false) {
                    $strFile = 'isotope/' . strtolower(substr($strFile, 0, 1)) . '/' . $strFile;
                }

                if (is_file($projectDir . '/' . $strFile))
                {
                    $arrImages[] = $strLink . $strFile;
                }
			}
		}

		// No image available, add placeholder from store configuration
        if (empty($arrReturn)) {
            $objPlaceholder = FilesModel::findByPk($objConfig->placeholder);
            if (null !== $objPlaceholder && is_file($projectDir . '/' . $objPlaceholder->path)) {
                $arrImages[] = $strLink . $objPlaceholder->path;
            }
        }

		return $arrImages;
	}
}
