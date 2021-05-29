<?php

declare(strict_types=1);

/**
 * Rhyme Isotope Feeds Bundle
 * Copyright (c) 2021 Rhyme.Digital
 * @license LGPL-3.0+
 */

namespace Rhyme\IsotopeFeedsBundle\EventListener;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Environment;
use Contao\LayoutModel;
use Contao\Model\Collection;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;
use Contao\Template;
use Isotope\Model\Config as IsoConfig;
use Rhyme\IsotopeFeedsBundle\Helper\IsotopeFeeds;

/**
 * @internal
 */
class GeneratePageListener
{
    /**
     * @var ContaoFramework
     */
    private $framework;

    public function __construct(ContaoFramework $framework)
    {
        $this->framework = $framework;
    }

    /**
     * Adds the feeds to the page header.
     */
    public function __invoke(PageModel $pageModel, LayoutModel $layoutModel): void
    {
        $productfeeds = StringUtil::deserialize($layoutModel->productfeeds);

        if (empty($productfeeds) || !\is_array($productfeeds)) {
            return;
        }

        $configIds = [];
        foreach ($productfeeds as $productfeed) {
            $arrFeed = explode('|', $productfeed);
            $configIds[] = $arrFeed[0];
        }

        $this->framework->initialize();

        /** @var IsoConfig $adapter */
        $adapter = $this->framework->getAdapter(IsoConfig::class);

        if (!($configs = $adapter->findMultipleByIds($configIds)) instanceof Collection) {
            return;
        }

        /** @var Template $template */
        $template = $this->framework->getAdapter(Template::class);

        /** @var Environment $environment */
        $environment = $this->framework->getAdapter(Environment::class);

        foreach ($configs as $config) {

            $strBase = IsotopeFeeds::getFeedBaseName($config);
            $arrTypes = StringUtil::deserialize($config->feedTypes, true);
            foreach($arrTypes as $feedType)
            {
                $strFile = sprintf('%sshare/' . $strBase . '-' . $feedType . '.xml', $config->feedBase ?: $environment->get('base'));
                $GLOBALS['TL_HEAD'][] = $template->generateFeedTag(
                    $strFile,
                    $GLOBALS['ISO_FEEDS'][$feedType]['format'] ?: 'rss',
                    $config->feedTitle
                );
            }
        }
    }
}
