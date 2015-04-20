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

namespace Rhyme\Backend\IsotopeConfig;


/**
 * Provides methods used by the tl_iso_config DCA
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
	 * Return all feed types
	 * @return array
	 */
	public function getFeedTypes()
	{
		$return = array();

		foreach ($GLOBALS['ISO_FEEDS'] as $k=>$v)
		{
			$return[$k] = $GLOBALS['TL_LANG']['ISO_FEEDS'][$k];
		}
		return $return;
	}

}
