<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register namespace
 */
if (class_exists('NamespaceClassLoader'))
{
    NamespaceClassLoader::add('Rhyme', 'system/modules/isotope_feeds/library');
}


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
    // Controllers
	'Contao\FeedController'    => 'system/modules/isotope_feeds/controllers/FeedController.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'be_cache-refresh'         => 'system/modules/isotope_feeds/templates/backend',
));
