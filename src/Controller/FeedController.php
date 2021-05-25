<?php

declare(strict_types=1);

/**
 * Rhyme Isotope Feeds Bundle
 * Copyright (c) 2021 Rhyme.Digital
 * @license LGPL-3.0+
 */

namespace Rhyme\IsotopeFeedsBundle\Controller;

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

    /**
     * Handles caching the product XML
     *
     * @return Response
     *
     * @Route("/cache", name="rhyme_isofeeds_cache")
     */
    public function feedCacheAction()
    {
        if ($this->container->has('contao.framework')) {
            $this->container->get('contao.framework')->initialize();
        }

        $controller = new FeedCache();

        return new Response($controller->run());

    }

}