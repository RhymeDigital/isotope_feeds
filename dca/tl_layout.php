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
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_layout']['palettes']['default'] = str_replace('calendarfeeds','calendarfeeds,productfeeds',$GLOBALS['TL_DCA']['tl_layout']['palettes']['default']);

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_layout']['fields']['productfeeds'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['productfeeds'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'options_callback'        => array('Rhyme\Backend\Layout\FeedCallbacks', 'getProductfeeds'),
	'eval'                    => array('multiple'=>true),
	'sql'                     => "blob NULL"
);

