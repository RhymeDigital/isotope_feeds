<?php

declare(strict_types=1);

/**
 * Rhyme Isotope Feeds Bundle
 * Copyright (c) 2026 Rhyme.Digital
 * @license LGPL-3.0+
 */

namespace Rhyme\IsotopeFeedsBundle\Hooks\RemoveOldFeeds;

use Contao\Controller;
use Contao\StringUtil;
use Rhyme\IsotopeFeedsBundle\Helper\IsotopeFeeds as FeedBase;
use Isotope\Model\Config as IsoConfig;


/**
 * Removes old feeds
 */
class IsotopeFeeds extends Controller
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
        		$arrFeedTypes = StringUtil::deserialize($objConfig->feedTypes, true);
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
