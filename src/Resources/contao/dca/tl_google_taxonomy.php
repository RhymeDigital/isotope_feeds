<?php

declare(strict_types=1);

/**
 * Rhyme Isotope Feeds Bundle
 * Copyright (c) 2021 Rhyme.Digital
 * @license LGPL-3.0+
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
