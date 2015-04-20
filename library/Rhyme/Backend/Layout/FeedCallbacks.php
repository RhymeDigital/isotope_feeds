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

namespace Rhyme\Backend\Layout;


/**
 * Provides methods used by the tl_layout DCA
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
	 * Return all XML product feeds
	 * @return array
	 */
	public function getProductfeeds()
	{
		$objFeed = \Database::getInstance()->execute("SELECT * FROM tl_iso_config WHERE addFeed=1");

		if ($objFeed->numRows < 1)
		{
			return array();
		}

		$return = array();

		while ($objFeed->next())
		{
			$arrFeeds = deserialize($objFeed->feedTypes);
			if(is_array($arrFeeds) && count($arrFeeds) > 0)
			{
				foreach($arrFeeds as $feed)
				{
					$return[$objFeed->id . '|'. $feed] = $objFeed->id . ': '. $GLOBALS['TL_LANG']['ISO_FEEDS'][$feed];
				}
			}
		}

		return $return;
	}
}
