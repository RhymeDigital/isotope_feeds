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
 * Table tl_google_taxonomy
 */
$GLOBALS['TL_DCA']['tl_google_taxonomy'] = array
(
    // Config
    'config' => array
    (
        'enableVersioning'                  => false,
        'sql' => array
        (
            'keys' => array
            (
                'id'    => 'primary',
                'pid'   => 'index'
            )
        ),
    ),

    // Fields
    'fields' => array
    (
        'id' => array
        (
            'sql'                           => "int(10) unsigned NOT NULL auto_increment",
        ),
        'pid' => array
        (
            'sql'                           => "int(10) unsigned NOT NULL default '0'",
        ),
        'name' => array
        (
            'sql'                           => "varchar(255) NOT NULL default ''",
        ),
        'fullname' => array
        (
            'sql'                           => "varchar(255) NOT NULL default ''",
        ),
        'depth' => array
        (
            'sql'                           => "int(10) unsigned NOT NULL default '0'",
        ),
    ),
);
