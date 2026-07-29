<?php

/**
 * Rhyme Isotope Feeds Bundle
 * Copyright (c) 2026 Rhyme.Digital
 * @license LGPL-3.0+
 */

declare(strict_types=1);

namespace {

    use Isotope\Model\Attribute;

    $table = Attribute::getTable();
    $dca = &$GLOBALS['TL_DCA'][$table];

    /**
     * Add legend
     */
    $dca['fields']['legend']['options'][] = 'feed_legend:hide';
}