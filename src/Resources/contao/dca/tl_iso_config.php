<?php

declare(strict_types=1);

/**
 * Rhyme Isotope Feeds Bundle
 * Copyright (c) 2021 Rhyme.Digital
 * @license LGPL-3.0+
 */

/**
 * Table tl_iso_config
 */

use Contao\System;

$GLOBALS['TL_DCA']['tl_iso_config']['config']['onsubmit_callback'][] = array('Rhyme\IsotopeFeedsBundle\Helper\IsotopeFeeds', 'generateFeeds');
$GLOBALS['TL_DCA']['tl_iso_config']['palettes']['__selector__'][] = 'addFeed';
$GLOBALS['TL_DCA']['tl_iso_config']['palettes']['default'] = str_replace('{analytics_legend}', '{feed_legend},addFeed;{images_legend}', $GLOBALS['TL_DCA']['tl_iso_config']['palettes']['default']);
$GLOBALS['TL_DCA']['tl_iso_config']['subpalettes']['addFeed'] = 'feedTypes,feedName,feedBase,feedLanguage,feedTitle,feedDesc,feedJumpTo';


// Fields
$GLOBALS['TL_DCA']['tl_iso_config']['fields']['addFeed'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_config']['addFeed'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'					  => array('submitOnChange'=>true),
	'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['feedTypes'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_config']['feedTypes'],
	'exclude'                 => true,
	'inputType'               => 'checkboxWizard',
	'options_callback'        => array('Rhyme\IsotopeFeedsBundle\Backend\IsotopeConfig\FeedCallbacks', 'getFeedTypes'),
	'eval'                    => array('multiple'=>true),
	'sql'                     => "blob NULL"
);

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['feedName'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_config']['feedName'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['feedBase'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_config']['feedBase'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['feedTitle'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_config']['feedTitle'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['feedDesc'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_config']['feedDesc'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['feedJumpTo'] = array
(
	'label'					  => &$GLOBALS['TL_LANG']['tl_iso_config']['feedJumpTo'],
	'inputType'				  => 'pageTree',
	'eval'					  => array('fieldType'=>'radio', 'tl_class'=>'clr'),
	'sql'                     => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['feedLanguage'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_iso_config']['feedLanguage'],
    'default'                 => str_replace('-', '_', System::getContainer()->get('request_stack')->getCurrentRequest()->getLocale()),
    'exclude'                 => true,
    'filter'                  => true,
    'inputType'               => 'select',
    'eval'                    => array('rgxp'=>'locale', 'tl_class'=>'w50'),
    'load_callback'           => array(
        array('Rhyme\IsotopeFeedsBundle\Backend\IsotopeConfig\FeedCallbacks', 'setDefaultLanguage'),
    ),
    'options_callback' => static function ()
    {
        return System::getLanguages(true);
    },
    'sql'                     => "varchar(5) NOT NULL default ''"
);
