<?php

declare(strict_types=1);

/**
 * Rhyme Isotope Feeds Bundle
 * Copyright (c) 2021 Rhyme.Digital
 * @license LGPL-3.0+
 */

/**
 * Config
 */
$GLOBALS['TL_DCA']['tl_iso_product']['config']['onload_callback'][] = array('Rhyme\IsotopeFeedsBundle\Backend\IsotopeProduct\FeedCallbacks', 'generateFeeds');
$GLOBALS['TL_DCA']['tl_iso_product']['config']['onsubmit_callback'][] = array('Rhyme\IsotopeFeedsBundle\Backend\IsotopeProduct\FeedCallbacks', 'cacheProduct');
$GLOBALS['TL_DCA']['tl_iso_product']['config']['ondelete_callback'][] = array('Rhyme\IsotopeFeedsBundle\Backend\IsotopeProduct\FeedCallbacks', 'cacheProduct');

/**
 * Global operations
 */
$GLOBALS['TL_DCA']['tl_iso_product']['list' ]['global_operations']['cache_feeds'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_iso_product']['cache_feeds'],
	'button_callback'	=> array('Rhyme\IsotopeFeedsBundle\Backend\IsotopeProduct\FeedCallbacks', 'cacheButton'),
	'attributes'		=> 'onclick="Backend.getScrollOffset();"',
);

$GLOBALS['TL_DCA']['tl_iso_product']['list' ]['global_operations']['generate_feeds'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_iso_product']['generate_feeds'],
	'href'				=> 'act=generateFeeds',
	'class'				=> 'header_iso_feeds isotope-tools',
	'attributes'		=> 'onclick="Backend.getScrollOffset();"',
);

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_iso_product']['fields']['published']['save_callback'][] = array('Rhyme\IsotopeFeedsBundle\Backend\IsotopeProduct\FeedCallbacks', 'toggleProduct');

$GLOBALS['TL_DCA']['tl_iso_product']['fields']['useFeed'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_product']['useFeed'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'clr m12'),
	'attributes'			  => array('legend'=>'feed_legend:hide', 'fixed'=>true, 'variant_fixed'=>true),
	'sql'                     => "char(1) NOT NULL default ''"
);
 
$GLOBALS['TL_DCA']['tl_iso_product']['fields']['gid_condition'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_product']['gid_condition'],
	'exclude'                 => true,
	'default'				  => 'new',
	'inputType'               => 'select',
	'options'				  => array('new','used','refurbished'),
	'save_callback'			  => array(array('Rhyme\IsotopeFeedsBundle\Backend\IsotopeProduct\FeedCallbacks', 'checkGoogle')),
	'eval'                    => array('tl_class'=>'w50'),
	'reference'				  => &$GLOBALS['TL_LANG']['tl_iso_product']['gid'],
	'attributes'			  => array('legend'=>'feed_legend:hide', 'fixed'=>true),
	'sql'                     => "varchar(64) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_iso_product']['fields']['gid_availability'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_product']['gid_availability'],
	'exclude'                 => true,
	'default'				  => 'in stock',
	'inputType'               => 'select',
	'options'				  => array('in stock','available for order','out of stock','preorder'),
	'save_callback'			  => array(array('Rhyme\IsotopeFeedsBundle\Backend\IsotopeProduct\FeedCallbacks', 'checkGoogle')),
	'eval'                    => array('tl_class'=>'w50'),
	'reference'				  => &$GLOBALS['TL_LANG']['tl_iso_product'],
	'attributes'			  => array('legend'=>'feed_legend:hide', 'fixed'=>true),
	'sql'                     => "varchar(64) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_iso_product']['fields']['gid_brand'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_product']['gid_brand'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'save_callback'			  => array(array('Rhyme\IsotopeFeedsBundle\Backend\IsotopeProduct\FeedCallbacks', 'checkGoogle')),
	'eval'                    => array('tl_class'=>'w50'),
	'attributes'			  => array('legend'=>'feed_legend:hide', 'fixed'=>true),
	'sql'                     => "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_iso_product']['fields']['gid_gtin'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_product']['gid_gtin'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('rgxp'=>'digit', 'tl_class'=>'w50'),
	'attributes'			  => array('legend'=>'feed_legend:hide', 'fixed'=>true),
	'sql'                     => "varchar(64) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_iso_product']['fields']['gid_mpn'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_product']['gid_mpn'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('tl_class'=>'w50'),
	'attributes'			  => array('legend'=>'feed_legend:hide', 'fixed'=>true),
	'sql'                     => "varchar(64) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_iso_product']['fields']['gid_google_product_category'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_product']['gid_google_product_category'],
	'exclude'                 => true,
 	'inputType' 		      => 'tableTree',
 	'eval'      		      => array(
 		'fieldType' 		      => 'radio',
 		'tableColumn'		      => 'tl_google_taxonomy.name',
 		'title'				      => &$GLOBALS['TL_LANG']['tl_google_taxonomy']['customSubTitle'],
 		'children' 			      => true,
 		'childrenOnly'		      => false,
 		'tl_class'			      => 'clr',
 	),
 	'attributes'			  => array('legend'=>'feed_legend:hide', 'fixed'=>true),
 	'sql'                     => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_iso_product']['fields']['gid_product_type'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_product']['gid_product_type'],
	'exclude'                 => true,
	'inputType'               => 'listWizard',
	'save_callback'			  => array(array('Rhyme\IsotopeFeedsBundle\Backend\IsotopeProduct\FeedCallbacks', 'checkGoogle')),
	'eval'                    => array('allowHtml'=>true, 'tl_class' => 'clr m12'),
	'attributes'			  => array('legend'=>'feed_legend:hide', 'fixed'=>true),
	'sql'                     => "blob NULL"
);

$GLOBALS['TL_DCA']['tl_iso_product']['fields']['gid_description'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_iso_product']['gid_description'],
    'exclude'                 => true,
    'inputType'               => 'textarea',
    'eval'                    => array('mandatory'=>true, 'tl_class' => 'clr'),
    'attributes'              => array('legend'=>'feed_legend:hide'),
    'sql'                     => "text NULL"
);
