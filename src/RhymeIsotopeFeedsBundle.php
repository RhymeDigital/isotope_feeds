<?php

declare(strict_types=1);

/**
 * Rhyme Isotope Feeds Bundle
 * Copyright (c) 2021 Rhyme.Digital
 * @license LGPL-3.0+
 */

namespace Rhyme\IsotopeFeedsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Rhyme\IsotopeFeedsBundle\DependencyInjection\Extension;

/**
 * Configures the bundle
 */
final class RhymeIsotopeFeedsBundle extends Bundle
{
    /**
     * @return Extension
     */
    public function getContainerExtension()
    {
        return new Extension();
    }
}