# Isotope Feeds
Updated implementation for creating Product Feeds in Isotope eCommerce for Contao Open Source CMS

By default, this extension ships with two Feed Types:

1. RSS 2.0
2. Google Merchant (formerly known as Google Base or also Google Shopping)

Note: In order to create a properly formatted Google Merchant feed, you will need to fill out the additional information on your products in the product management section.

## Dependencies

Built for
* https://github.com/isotope/core

## Installation

Install the bundle via Composer:

    composer require rhymedigital/isotope-feeds

or via the Contao Manager by searching for `Isotope Feeds`

Be sure to run the Database Installer.

## Configuration

In order to get your product feed working, you will need to do some setup to tell the system which feed types you 
are using, and then which products you would like to use in the feed.

This configuration assumes that you have installed and set up your Isotope storefront using their configuration 
guide first.

### Store Configuration

1. Login to your Contao backend, and go to Isotope eCommerce > Store Configuration, and edit your store config.
2. You will see a new section called "Feeds" with a checkbox to "Add Product Feeds." Check the box.
3. Select your Feed Type here. For Google Merchant feeds, select "Google Base"
4. You should enter a prefix for the feed file, which will determine its name (i.e. `products` will output a file 
   called `products.xml`). You can also optionally add a base link (it will use the environment base by 
   default) and set a title and description. You should also set the default Reader page for your products, which 
   will be used to generate the product URL in the feed.

### Product Type Configuration

The next step is to enable the fields that are part of the Google Merchant feed in your Product Types. Without 
setting the "Use in Feed" setting on a product, it will not beused in the feed, so we need to add the Feed fields to 
your product palettes.

1. Go to Store Configuration > Product Types, and edit each of your Product Types.
2. By default, the `Use in Feed`, `Condition`, `Availability`, `Brand`, `GTIN`, and `MPN` fields will be checked by 
   default and when you save the Product Type they will be enabled on your products. There are also several other 
   available fields for you to add into the Feed Legend, but which are not required.
3. Saving your Product Types will add these Feed fields to each product of that type.

### Product Configuration

In order to be eligible for use in the Feeds, you need to have the `Use in Feed?` option checked on each product you 
would like to include. This also applies for variant products as well!

1. In Product Management, edit your products and/or variant products and make sure that the `Use in Feed` checkbox 
   is checked.
2. If you are setting up Google Merchant feeds, you need to make sure that you have filled out the minimum required 
   information which is 2 out of the 3 fields for `Brand`, `GTIN`, or `MPN`.

When you enable each product, a cache file is created in the root `isotope/cache` folder for each product/variant. 
These cached XMl files are then assembled into the single Feed XML file which will be placed in the `public/share` 
directory (or `web/share` on older Contao 4 installations).

The feed file will be generated daily on the `generateFeeds` Hook, or you can manually create it by clicking the 
`Regenerate Feed` operations button in Product Management. 

You can also do a mass-regeneration of all the cached individual product/variant files by clicking the `Refresh feed 
cache` button as well. Keep in mind that you will then also need to click the `Regenerate Feed` button after it is 
completed.

## Customization

You may find yourself with a need to create an entirely different feed type than the two provided. All of the 
feeds provided are extended from the base `Contao\Feed` class as a starting point, and each individual 
product/variant XML is extended from the base `Contao\FeedItem` class. 

You are free to create both your own Feed and customized FeedItem classes, and to use them with Isotope Feeds, you 
just need to register them in your `config.php` file for Contao by inserting them into the  `$GLOBALS['ISO_FEEDS']` 
array. See below for the initial configuration of the two included feeds for an example.


    $GLOBALS['ISO_FEEDS'] = array (
      'googlebase' => array (
         'feed'	=> array('Rhyme\IsotopeFeedsBundle\Feed\GoogleMerchant', 'generate'),
         'item'	=> 'Rhyme\IsotopeFeedsBundle\FeedItem\GoogleMerchant',
         'format'=> 'rss',
      ),
      'rss20'		 => array (
         'feed'	=> array('Rhyme\IsotopeFeedsBundle\Feed\Rss20', 'generate'),
         'item'	=> 'Rhyme\IsotopeFeedsBundle\FeedItem\Rss20',
         'format'=> 'rss',
      ),
    );

