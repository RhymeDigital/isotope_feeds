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

// Set the script name
define('TL_SCRIPT', 'system/modules/isotope_feeds/assets/refresh_cache.php');

// Initialize the system
define('TL_MODE', 'BE');
require dirname(__DIR__) . '/../../initialize.php';

// Run the controller
$controller = new FeedController;
$controller->run();