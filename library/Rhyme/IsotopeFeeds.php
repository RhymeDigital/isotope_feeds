<?php

/**
 * Isotope Feeds - Create RSS and other feeds from Isotope Products
 *
 * @copyright  Rhyme.Digital 2015, Winans Creative 2009, Intelligent Spark 2010, iserv.ch GmbH 2010
 *
 * @package    Isotope_Feeds
 * @link       http://rhyme.digital
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */

namespace Rhyme;

use Isotope\Model\Config as IsoConfig;
use Isotope\Interfaces\IsotopeProduct;
use Isotope\Model\Product;
use Isotope\Model\ProductPrice;
use Isotope\Isotope;
use Rhyme\Feed\Rss20;
use Rhyme\Model\GoogleTaxonomy;

/**
 * Preserves feeds
 *
 * @copyright  Rhyme Digital 2015
 * @author     Blair Winans <blair@rhyme.digital>
 * @author     Adam Fisher <adam@rhyme.digital>
 * @package    Isotope_Feeds
 */
class IsotopeFeeds extends \Controller
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
    	if(!is_array(static::$arrXMLDirCache[$objConfig->id]))
    	{
        	$arrFeedTypes = deserialize($objConfig->feedTypes, true);
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
    	$arrTypes = array_flip(static::$arrXMLDirCache[$objConfig->id]);
    	return $arrTypes[$strDir];
    }
    
    /**
	 * Return an array of its feed files from a config
	 * @param mixed \Isotope\Model\Config or Contao\DatabaseResult
	 * @return array
	 */
	public static function getFeedFiles($objConfig)
	{
    	if(!is_array(static::$arrFeedCache[$objConfig->id]))
    	{
        	static::$arrFeedCache[$objConfig->id] = array();
        	$strBase = static::getFeedBaseName($objConfig);
        	$arrTypes = deserialize($objConfig->feedTypes, true);
        	foreach($arrTypes as $feedType)
            {
                $strFile = 'share/' . $strBase . '-' . $feedType . '.xml';
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
    		if (\Input::get('act') == 'delete')
    		{
			    \Files::getInstance()->delete($feedFile);
		    }
		    //Update
		    else
		    {
			    $this->generateFile($objConfig, $feedFile);
                \System::log('Generated product feed ' . $feedFile, __METHOD__, TL_CRON);
		    }
		}
	}


	/**
	 * Delete old files and generate all product feeds
	 */
	public function generateFeeds()
	{
		$this->removeOldFeeds();
		$objConfig = IsoConfig::findBy('addFeed', '1');

		while ($objConfig->next())
		{
			$arrFeedFiles = static::getFeedFiles($objConfig);
			foreach( $arrFeedFiles as $feedFile)
			{
				$this->generateFile($objConfig, $feedFile);
				\System::log('Generated product feed ' . $feedFile, __METHOD__, TL_CRON);
			}
		}
	}
	
	
	/**
	 * Cache the product XML for each store config
	 * @param Isotope\Interfaces\IsotopeProduct
	 */
	public function cacheProduct($intId)
	{
    	$objProduct = Product::findByPk($intId);
    	if(null === $objProduct)
    	{
        	return;
    	}
    	
		$objConfig = IsoConfig::findBy('addFeed', '1');
		while ($objConfig->next())
		{
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
		$strLink = $objConfig->feedBase != '' ? $objConfig->feedBase : \Environment::get('base');

		try
		{
			$objFeed = new $arrType[0]($strFile);
		}
		catch (\Exception $e)
		{
			$objFeed = new Rss20($strFile);
		}

		$objFeed->link = $strLink;
		$objFeed->title = $objConfig->feedTitle;
		$objFeed->description = $objConfig->feedDesc;
		$objFeed->language = $objConfig->language;
		$objFeed->published = time();
		
		$strDir = static::getFeedCacheBaseDir($objConfig) . '/' . $strType;
		
		if(is_dir(TL_ROOT . '/' .  $strDir))
		{
			$arrFiles = scan(TL_ROOT . '/' .  $strDir);
			
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
				if(is_file(TL_ROOT  . '/' . $strDir . '/' . $file))
				{
					$objFile = new \File($strDir . '/' . $file);
					$objFeed->addFile($objFile);
				}
			}
			
			// Create file
			$objFeedFile = new \File($strFile);
			$objFeedFile->write($this->replaceInsertTags($objFeed->{$arrType[1]}()));
			$objFeedFile->close();
		}
	}

	/**
	 * Generate an XML file for a product and save it to the cache
	 * @param string - feed file
	 * @param Isotope\Interfaces\IsotopeProduct
	 * @param Isotope\Model\Config - store config
	 */
	public function generateProductXML($feedFile, IsotopeProduct $objProduct, $objConfig)
	{
		$time = time();
		$strType = static::getTypeFromFeedFile($objConfig, $feedFile);
		$arrFeedClass = $GLOBALS['ISO_FEEDS'][$strType];
		$strLink = $objConfig->feedBase != '' ? $objConfig->feedBase : \Environment::get('base');
		$strCacheFile = 'isotope/cache/' . $objConfig->id . '/' .  $strType . '/' . $objProduct->alias . ($objProduct->pid > 0 ? '-' . $objProduct->id : '') . '.xml';
		
		// Get root pages that belong to this store config.
		$arrPages = array();
		$objRoot = \PageModel::findBy(array("type='root'", "iso_config"), $objConfig->id);
		if(null !== $objRoot)
		{
				$arrRoots = $objRoot->fetchEach('id');
				$arrPages = \Database::getInstance()->getChildRecords($arrRoots, 'tl_page', false, $arrRoots);
		}

		// Get default URL
		$intJumpTo = $objConfig->feedJumpTo;
		if($intJumpTo=='')
		{
			//Get the first reader page we can find
			$objModules = \Database::getInstance()->prepare("SELECT iso_reader_jumpTo FROM tl_module WHERE ".(count($arrPages)>0 ? "iso_reader_jumpTo IN (" . implode(',',$arrPages) . ") AND " : ''). "iso_reader_jumpTo !=''")->limit(1)->execute();

			if($objModules->numRows)
			{
				$intJumpTo = $objModules->iso_reader_jumpTo;
			}
		}
		
		if($objProduct->isPublished() && $objProduct->useFeed)
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
				\Files::getInstance()->delete($strCacheFile);
				return;
			}
			
			try
			{
				$objItem = new $GLOBALS['ISO_FEEDS'][$strType]['item']();
			}
			catch (\Exception $e)
			{
				$objItem = new Rss20();
			}

			$strUrlKey = $objProduct->alias ?: ($objProduct->pid ?: $objProduct->id);

			$objItem->title = $objProduct->name;
			$objItem->link = $strLink . $objProduct->generateUrl(\PageModel::findByPk($intJumpTo));
			$objItem->published = time();

			// Prepare the description
			$strDescription = $objProduct->description;
			$strDescription = $this->replaceInsertTags($strDescription);
			$objItem->description = $this->convertRelativeUrls($strDescription, $strLink);

			//Sku, price, etc
			$objItem->id = $objProduct->id;
			$objItem->sku = strlen($objProduct->sku) ? $objProduct->sku : $objProduct->alias;
			$objPrice = ProductPrice::findOneByPid($objProduct->id);
			$objTiers = \Database::getInstance()->prepare("SELECT * FROM tl_iso_product_pricetier WHERE pid=? ORDER BY min")
			                                    ->limit(1)
			                                    ->execute($objPrice->id);
			$objItem->price = Isotope::formatPrice($objTiers->price) .' '. $objConfig->currency;

			//Google basic settings
			$objItem->condition = $objProduct->gid_condition;
			$objItem->availability = $objProduct->gid_availability;
			$objItem->brand = $objProduct->gid_brand;
			$objItem->gtin = $objProduct->gid_gtin;
			$objItem->mpn = $objProduct->gid_mpn;
			$objItem->google_product_category = (($objGT = GoogleTaxonomy::findByPk($objProduct->gid_google_product_category))==false ) ? '' : $objGT->fullname;
			
			//Google variants only
			if($objProduct->pid>0)
			{
				$objItem->item_group_id = strlen($objProduct->sku) ? $objProduct->sku : $objProduct->alias;
			}
			
			//Custom product category taxomony
			$objItem->product_type = deserialize($objProduct->gid_product_type);
			
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
			if(is_file(TL_ROOT . '/' . $strCacheFile))
			{
				\Files::getInstance()->delete($strCacheFile);
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
		$varValue = deserialize($objProduct->images);

		if(is_array($varValue) && count($varValue))
		{
			foreach( $varValue as $k => $file )
			{
				$strFile = $file['src'];

                // File without path must be located in the isotope root folder
                if (strpos($strFile, '/') === false) {
                    $strFile = 'isotope/' . strtolower(substr($strFile, 0, 1)) . '/' . $strFile;
                }
        
                if (is_file(TL_ROOT . '/' . $strFile)) 
                {
                    $arrImages[] = $strLink . $strFile;
                }
			}
		}

		// No image available, add placeholder from store configuration
        if (empty($arrReturn)) {
            $objPlaceholder = \FilesModel::findByPk($objConfig->placeholder);
            if (null !== $objPlaceholder && is_file(TL_ROOT . '/' . $objPlaceholder->path)) {
                $arrImages[] = $strLink . $objPlaceholder->path;
            }
        }

		return $arrImages;
	}
}
