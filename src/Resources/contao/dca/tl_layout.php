<?php

declare(strict_types=1);

/**
 * Rhyme Isotope Feeds Bundle
 * Copyright (c) 2021 Rhyme.Digital
 * @license LGPL-3.0+
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
	'options_callback'        => array('Rhyme\IsotopeFeedsBundle\Backend\Layout\FeedCallbacks', 'getProductfeeds'),
	'eval'                    => array('multiple'=>true),
	'sql'                     => "blob NULL"
);

