<?php

declare(strict_types=1);

/**
 * Rhyme Isotope Feeds Bundle
 * Copyright (c) 2021 Rhyme.Digital
 * @license LGPL-3.0+
 */

namespace Rhyme\IsotopeFeedsBundle\Controller;

use Contao\CoreBundle\Framework\ContaoFramework;
use Rhyme\IsotopeFeedsBundle\Backend\IsotopeProduct\FeedCache;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Handles custom scripts
 * *
 * @Route("/isofeeds", defaults={"_scope" = "backend", "_token_check" = true})
 */
class FeedController extends AbstractController
{
    private ContaoFramework $framework;

    public function __construct(ContaoFramework $framework)
    {
        $this->framework = $framework;
    }

    /**
     * Handles caching the product XML
     *
     * @return Response
     *
     * @Route("/cache", name="rhyme_isofeeds_cache")
     */
    public function feedCacheAction()
    {
        $this->framework->initialize();

        $controller = new FeedCache();

        return new Response($controller->run());
    }

}
