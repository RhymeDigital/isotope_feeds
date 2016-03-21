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

/**
 * Isotope Feeds
 */
$GLOBALS['ISO_FEEDS'] = array
(
	'googlebase' => array
	(
		'feed'	=> array('Rhyme\Feed\GoogleMerchant', 'generate'),
		'item'	=> 'Rhyme\FeedItem\GoogleMerchant'
	),
	'rss20'		 => array
	(
		'feed'	=> array('Rhyme\Feed\Rss20', 'generate'),
		'item'	=> 'Rhyme\FeedItem\Rss20'
	),
);

if (TL_MODE == 'BE')
{
	$GLOBALS['TL_CSS'][] = 'system/modules/isotope_feeds/assets/css/isotope-feeds.css';
	$GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/isotope_feeds/assets/js/isotope-feeds.js|static';
}


/**
 * Cron jobs
 */
$GLOBALS['TL_CRON']['daily'][] = array('Rhyme\IsotopeFeeds', 'generateFeeds');

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['removeOldFeeds'][] = array('Rhyme\Hooks\RemoveOldFeeds\IsotopeFeeds', 'preserveFeeds');
$GLOBALS['TL_HOOKS']['executePreActions'][] = array('Rhyme\Hooks\ExecutePreActions\IsotopeFeeds', 'ajaxHandler');

/**
* Models
*/
$GLOBALS['TL_MODELS'][\Rhyme\Model\GoogleTaxonomy::getTable()] = 'Rhyme\Model\GoogleTaxonomy';
