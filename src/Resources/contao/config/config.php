<?php

declare(strict_types=1);

/**
 * Rhyme Isotope Feeds Bundle
 * Copyright (c) 2026 Rhyme.Digital
 * @license LGPL-3.0+
 */
namespace {

    use Rhyme\IsotopeFeedsBundle\Feed;
    use Rhyme\IsotopeFeedsBundle\Model;
    use Rhyme\IsotopeFeedsBundle\Hooks;
    use Rhyme\IsotopeFeedsBundle\FeedItem;
    use Rhyme\IsotopeFeedsBundle\Helper\IsotopeFeeds as IsotopeFeedsHelper;

    /**
     * Isotope Feeds
     */
    $GLOBALS['ISO_FEEDS'] = [
        'googlebase' => [
            'feed' => [Feed\GoogleMerchant::class, 'generate'],
            'item' => FeedItem\GoogleMerchant::class,
            'format' => 'rss',
        ],
        'rss20' => [
            'feed' => [Feed\Rss20::class, 'generate'],
            'item' => FeedItem\Rss20::class,
            'format' => 'rss',
        ],
    ];

    /**
     * Scripts
     */
    if (TL_MODE === 'BE')
    {
        $GLOBALS['TL_CSS'][] = 'bundles/rhymeisotopefeeds/assets/css/isotope-feeds.css|static';
        $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/rhymeisotopefeeds/assets/js/isotope-feeds.js|static';
    }


    /**
     * Cron jobs
     */
    $GLOBALS['TL_CRON']['daily'][] = [IsotopeFeedsHelper::class, 'generateFeeds'];

    /**
     * Hooks
     */
    $GLOBALS['TL_HOOKS']['removeOldFeeds'][] = [Hooks\RemoveOldFeeds\IsotopeFeeds::class, 'preserveFeeds'];
    $GLOBALS['TL_HOOKS']['executePreActions'][] = [Hooks\ExecutePreActions\IsotopeFeeds::class, 'ajaxHandler'];

    /**
     * Models
     */
    $GLOBALS['TL_MODELS'][Model\GoogleTaxonomy::getTable()] = Model\GoogleTaxonomy::class;
}