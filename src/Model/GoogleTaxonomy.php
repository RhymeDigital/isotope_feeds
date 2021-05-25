<?php

declare(strict_types=1);

/**
 * Rhyme Isotope Feeds Bundle
 * Copyright (c) 2021 Rhyme.Digital
 * @license LGPL-3.0+
 */

namespace Rhyme\IsotopeFeedsBundle\Model;

use Contao\Model;

/**
 * Google Taxonomy Model
 *
 * @copyright  Rhyme Digital 2015
 * @author     Blair Winans <blair@rhyme.digital>
 * @author     Adam Fisher <adam@rhyme.digital>
 * @package    Isotope_Feeds
 */
class GoogleTaxonomy extends Model
{

    /**
     * Name of the current table
     * @var string
     */
    protected static $strTable = 'tl_google_taxonomy';

}
