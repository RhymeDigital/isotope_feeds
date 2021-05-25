<?php

declare(strict_types=1);

/**
 * Rhyme Isotope Feeds Bundle
 * Copyright (c) 2021 Rhyme.Digital
 * @license LGPL-3.0+
 */

/**
 * Isotope Feeds
 */
$GLOBALS['ISO_FEEDS'] = array
(
	'googlebase' => array
	(
		'feed'	=> array('Rhyme\IsotopeFeedsBundle\Feed\GoogleMerchant', 'generate'),
		'item'	=> 'Rhyme\IsotopeFeedsBundle\FeedItem\GoogleMerchant'
	),
	'rss20'		 => array
	(
		'feed'	=> array('Rhyme\IsotopeFeedsBundle\Feed\Rss20', 'generate'),
		'item'	=> 'Rhyme\IsotopeFeedsBundle\FeedItem\Rss20'
	),
);

if (TL_MODE == 'BE')
{
	$GLOBALS['TL_CSS'][] = 'web/bundles/rhymeisotopefeeds/assets/css/isotope-feeds.css|static';
	$GLOBALS['TL_JAVASCRIPT'][] = 'web/bundles/rhymeisotopefeeds/assets/js/isotope-feeds.js|static';
}


/**
 * Cron jobs
 */
$GLOBALS['TL_CRON']['daily'][] = array('Rhyme\IsotopeFeedsBundle\Helper\IsotopeFeeds', 'generateFeeds');

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['removeOldFeeds'][] = array('Rhyme\IsotopeFeedsBundle\Hooks\RemoveOldFeeds\IsotopeFeeds', 'preserveFeeds');
$GLOBALS['TL_HOOKS']['executePreActions'][] = array('Rhyme\IsotopeFeedsBundle\Hooks\ExecutePreActions\IsotopeFeeds', 'ajaxHandler');

/**
* Models
*/
$GLOBALS['TL_MODELS'][\Rhyme\IsotopeFeedsBundle\Model\GoogleTaxonomy::getTable()] = 'Rhyme\IsotopeFeedsBundle\Model\GoogleTaxonomy';
