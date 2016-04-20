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

namespace Rhyme\Backend\IsotopeProduct;


/**
 * Provides methods used by the tl_iso_product DCA
 *
 * @copyright  Rhyme Digital 2015
 * @author     Blair Winans <blair@rhyme.digital>
 * @author     Adam Fisher <adam@rhyme.digital>
 * @package    Isotope_Feeds
 */
class FeedCallbacks extends \Backend
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
	 * @param Contao\DataContainer
	 * @return string
	 */
	public function checkGoogle($varValue, \DataContainer $dc)
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
			$objFeeds = \System::importStatic('Rhyme\IsotopeFeeds');
			$objFeeds->generateFeeds();
			
			\Controller::redirect(str_replace('&act=generateFeeds', '', \Environment::get('request')));
		}
	}

	/**
	 * Cache the product XML for each store config
	 * @param $id
	 */
	public function cacheProduct($id)
	{
		$this->import('Rhyme\IsotopeFeeds', 'IsotopeFeeds');
		$this->IsotopeFeeds->cacheProduct($id);
	}

	/**
	 * Cache the product XML for each store config
	 * @param mixed
	 * @param mixed
	 */
	public function toggleProduct($varValue, $dc)
	{
		if ($dc instanceof \DataContainer)
		{
			$this->cacheProduct($dc->id);
		}
		else if (strlen(\Input::get('tid')))
		{
			$this->cacheProduct(\Input::get('tid'));
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
		return '<a href="'.TL_PATH.'/system/modules/isotope_feeds/assets/refresh-cache.php" onclick="Backend.getScrollOffset();RefreshCache.openModalSelector({\'width\':250,\'height\':200,\'title\':\'Refresh Cache\',\'url\':this.href,\'id\':\'refresh-cache\'});return false" class="header_iso_feeds isotope-tools">'.$GLOBALS['TL_LANG']['tl_iso_product']['cache_feeds'].'</a>';
	}

}
