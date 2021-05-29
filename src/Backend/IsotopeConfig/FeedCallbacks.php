<?php

declare(strict_types=1);

/**
 * Rhyme Isotope Feeds Bundle
 * Copyright (c) 2021 Rhyme.Digital
 * @license LGPL-3.0+
 */

namespace Rhyme\IsotopeFeedsBundle\Backend\IsotopeConfig;

use Contao\Backend;
use Contao\DataContainer;
use Contao\System;


/**
 * Provides methods used by the tl_iso_config DCA
 *
 * @copyright  Rhyme Digital 2015
 * @author     Blair Winans <blair@rhyme.digital>
 * @author     Adam Fisher <adam@rhyme.digital>
 * @package    Isotope_Feeds
 */
class FeedCallbacks extends Backend
{
	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('Contao\BackendUser', 'User');
	}

	/**
	 * Return all feed types
	 * @return array
	 */
	public function getFeedTypes()
	{
		$return = array();

		foreach ($GLOBALS['ISO_FEEDS'] as $k=>$v)
		{
			$return[$k] = $GLOBALS['TL_LANG']['ISO_FEEDS'][$k];
		}
		return $return;
	}

    /**
     * Update the default value
     * @param $varValue
     * @param DataContainer $dc
     * @return mixed
     */
	public function setDefaultLanguage($varValue, DataContainer $dc) {
        if($varValue === null || $varValue === '')
        {
            $locale = System::getContainer()->get('request_stack')->getCurrentRequest()->getLocale();
            $varValue = $locale;
        }
        return $varValue;
    }

}
