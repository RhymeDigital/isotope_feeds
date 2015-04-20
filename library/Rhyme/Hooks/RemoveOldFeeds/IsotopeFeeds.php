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

namespace Rhyme\Hooks\RemoveOldFeeds;

use Rhyme\IsotopeFeeds as FeedBase;
use Isotope\Model\Config as IsoConfig;


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
	 * remove feeds hook to preserve files
	 * @return array
	 */
	public static function preserveFeeds()
	{
    	$arrFeeds = array();
		$objConfig = IsoConfig::findBy('addFeed', '1');
		if(null !== $objConfig)
		{
    	    while($objConfig->next())
    	    {
        	    $strBase = FeedBase::getFeedBaseName($objConfig);
        		$arrFeedTypes = deserialize($objConfig->feedTypes, true);
        		if(is_array($arrFeedTypes) && count($arrFeedTypes)>0)
        		{
        			foreach( $arrFeedTypes as $feedType )
        			{
        				$arrFeeds[] = $strBase . '-'. $feedType;
        			}
        		}
    	    }	
		}
		
        return $arrFeeds;
	}
}
