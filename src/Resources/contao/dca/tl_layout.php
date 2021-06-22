<?php

declare(strict_types=1);

/**
 * Rhyme Isotope Feeds Bundle
 * Copyright (c) 2021 Rhyme.Digital
 * @license LGPL-3.0+
 */

use Contao\CoreBundle\DataContainer\PaletteManipulator;

/**
 * Palettes
 */

// Extend default palette
PaletteManipulator::create()
    ->addLegend('feed_legend', 'modules_legend', PaletteManipulator::POSITION_BEFORE)
    ->addField('productfeeds', 'feed_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_layout')
;

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

