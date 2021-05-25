<?php

declare(strict_types=1);

/**
 * Rhyme Isotope Feeds Bundle
 * Copyright (c) 2021 Rhyme.Digital
 * @license LGPL-3.0+
 */

namespace Rhyme\IsotopeFeedsBundle\Backend\IsotopeProduct;

use Contao\Backend;
use Contao\Controller;
use Contao\DataContainer;
use Contao\Environment;
use Contao\Input;

/**
 * Provides methods used by the tl_iso_product DCA
 *
 * @copyright  Rhyme Digital 2015
 * @author     Blair Winans <blair@rhyme.digital>
 * @author     Adam Fisher <adam@rhyme.digital>
 * @package    Isotope_Feeds
 */
class FeedCallbacks extends Backend
{

    /**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('Contao\BackendUser', 'User');
	}

    /**
     * Check whether the required Google info has been submitted,
     * @param mixed
     * @param DataContainer
     * @return string
     * @throws \Exception
     */
	public function checkGoogle($varValue, DataContainer $dc)
	{
		// Check whether the required Google info has been submitted, 
		// but we don't want to require if it is not set to be a feed product
		if ($dc->activeRecord->useFeed && !strlen($varValue))
		{
			throw new \Exception($GLOBALS['TL_LANG']['ERR']['googleReq']);
		}

		return $varValue;
	}

	/**
	 * Generate full feed files
	 * @param mixed
	 * @param object
	 * @return string
	 */
	public function generateFeeds()
	{
		if (\Input::get('act') == 'generateFeeds' && \Input::get('key') == '')
		{
			$objFeeds = \System::importStatic('Rhyme\IsotopeFeedsBundle\Helper\IsotopeFeeds');
			$objFeeds->generateFeeds();
			
			Controller::redirect(str_replace('&act=generateFeeds', '', Environment::get('request')));
		}
	}

	/**
	 * Cache the product XML for each store config
	 * @param $id
	 */
	public function cacheProduct($id)
	{
	    if($id instanceof \DC_ProductData) {
	        $id = $id->activeRecord->id;
        }

		$this->import('Rhyme\IsotopeFeedsBundle\Helper\IsotopeFeeds', 'IsotopeFeeds');
		$this->IsotopeFeeds->cacheProduct($id);
	}

	/**
	 * Cache the product XML for each store config
	 * @param mixed
	 * @param mixed
	 */
	public function toggleProduct($varValue, $dc)
	{
		if ($dc instanceof DataContainer)
		{
			$this->cacheProduct($dc->id);
		}
		else if (strlen(Input::get('tid')))
		{
			$this->cacheProduct(Input::get('tid'));
		}

		return $varValue;
	}
	
	/**
	 * Generate full feed files
	 * @param mixed
	 * @param object
	 * @return string
	 */
	public function cacheButton()
	{
		return '<a href="'.TL_PATH.'/isofeeds/cache" onclick="Backend.getScrollOffset();RefreshCache.openModalSelector({\'width\':250,\'height\':200,\'title\':\'Refresh Cache\',\'url\':this.href,\'id\':\'refresh-cache\'});return false" class="header_iso_feeds isotope-tools">'.$GLOBALS['TL_LANG']['tl_iso_product']['cache_feeds'].'</a>';
	}

}
