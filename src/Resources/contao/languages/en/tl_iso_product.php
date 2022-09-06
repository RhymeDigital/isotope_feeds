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
 * Fields
 */
$GLOBALS['TL_LANG']['tl_iso_product']['useFeed'] 				= array('Use in Feed', 'Check this box if you wish to set and use this product data in feeds.');
$GLOBALS['TL_LANG']['tl_iso_product']['gid_condition'] 		= array('Condition', 'Please enter the condition of the product.');
$GLOBALS['TL_LANG']['tl_iso_product']['gid_availability'] 		= array('Availability', 'Please enter the availability of the product.');
$GLOBALS['TL_LANG']['tl_iso_product']['gid_brand'] 			= array('Brand', 'Please enter the brand/manufacturer of the product. Not required for custom products, books, or media, or if you are providing a GTIN and MPN.');
$GLOBALS['TL_LANG']['tl_iso_product']['gid_gtin'] 				= array('Global Trade Item Number', 'Please enter the GTIN for the product, an 8-, 12-, or 13-digit number (UPC, EAN, JAN, or ISBN). Not required for custom products, apparel, or media, or if you are providing Brand and MPN.');
$GLOBALS['TL_LANG']['tl_iso_product']['gid_mpn'] 				= array('Manufacturer Part Number', 'Please enter the MPN for the product. Not required for custom products, apparel, or media, or if you are providing Brand and GTIN');
$GLOBALS['TL_LANG']['tl_iso_product']['gid_google_product_category'] = array('Google Product Taxonomy', 'Please select from the predefined values from Google\'s product taxonomy.  Required for all items that belong to the \'Apparel and Accessories\', \'Media\', and \'Software\' categories. This attribute should be included in addition to, not as a replacement for, the \'Your Product Type\' attribute.');
$GLOBALS['TL_LANG']['tl_iso_product']['gid_google_product_category_manual'] = array('Your Manually-Added Google Product Taxonomy', 'Please select from the predefined values from Google\'s product taxonomy.  Required for all items that belong to the \'Apparel and Accessories\', \'Media\', and \'Software\' categories. This attribute should be included in addition to, not as a replacement for, the \'Your Product Type\' attribute.');
$GLOBALS['TL_LANG']['tl_iso_product']['gid_product_type'] = array('Your Product Type(s)', 'This attribute contains the category of the product according to your taxonomy. As with the \'Google Product Category\' attribute, include the category with full “breadcrumb” information. For example, \'Books > Non-Fiction > Sports > Baseball\' is better than just \'Baseball\'. Any separator such as > or / may be used.');
$GLOBALS['TL_LANG']['tl_iso_product']['gid_product_type_manual'] = array('Your Manually-Added Product Type(s)', 'This attribute contains the category of the product according to your taxonomy. As with the \'Google Product Category\' attribute, include the category with full “breadcrumb” information. For example, \'Books > Non-Fiction > Sports > Baseball\' is better than just \'Baseball\'. Any separator such as > or / may be used.');
$GLOBALS['TL_LANG']['tl_iso_product']['gid_description'] = array('Google Product Description', 'Please enter a description for this product in the Google Feed.');
$GLOBALS['TL_LANG']['tl_iso_product']['gid_color'] = array('Google Product Color', 'Please enter a color for this product in the Google Feed.');
$GLOBALS['TL_LANG']['tl_iso_product']['gid_material'] = array('Google Product Material', 'Please enter a material for this product in the Google Feed.');
$GLOBALS['TL_LANG']['tl_iso_product']['gid_pattern'] = array('Google Product Pattern', 'Please enter a pattern for this product in the Google Feed.');
$GLOBALS['TL_LANG']['tl_iso_product']['gid_shipping_weight'] = array('Google Product Shipping Weight', 'Please enter a shipping weight for this product in the Google Feed.');

/**
 * Reference
 */
$GLOBALS['TL_LANG']['tl_iso_product']['gid']['new'] 			= 'New';
$GLOBALS['TL_LANG']['tl_iso_product']['gid']['used'] 			= 'Used';
$GLOBALS['TL_LANG']['tl_iso_product']['gid']['refurbished'] 	= 'Refurbished';
$GLOBALS['TL_LANG']['tl_iso_product']['gid']['in stock'] 		= 'In Stock';
$GLOBALS['TL_LANG']['tl_iso_product']['gid']['available for order'] = 'Available For Order';
$GLOBALS['TL_LANG']['tl_iso_product']['gid']['out of stock'] 	= 'Out of Stock';
$GLOBALS['TL_LANG']['tl_iso_product']['gid']['preorder'] 		= 'Preorder';

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_iso_product']['cache_feeds'] = 'Refresh feed cache';
$GLOBALS['TL_LANG']['tl_iso_product']['generate_feeds'] = 'Regenerate feed';

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_iso_product']['feed_legend:hide']	= "Feed Settings";
$GLOBALS['TL_LANG']['tl_iso_product']['feed_legend']		= "Feed Settings";

