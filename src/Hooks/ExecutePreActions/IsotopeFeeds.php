<?php

declare(strict_types=1);

/**
 * Rhyme Isotope Feeds Bundle
 * Copyright (c) 2021 Rhyme.Digital
 * @license LGPL-3.0+
 */

namespace Rhyme\IsotopeFeedsBundle\Hooks\ExecutePreActions;

use Contao\Controller;
use Isotope\Isotope;
use Isotope\Model\ProductCollection\Cart;
use Rhyme\IsotopeFeedsBundle\Helper\IsotopeFeeds as IsoFeeds;
use Haste\Http\Response\JsonResponse;
use Isotope\Model\Product;
use Isotope\Model\Config as IsoConfig;

/**
 * Preserves feeds
 *
 * @copyright  Rhyme Digital 2015
 * @author     Blair Winans <blair@rhyme.digital>
 * @author     Adam Fisher <adam@rhyme.digital>
 * @package    Isotope_Feeds
 */
class IsotopeFeeds extends Controller
{
    /**
     * Handle the AJAX request that will rebuild the cache, etc
     * @param string
     * @return void
     */
    public function ajaxHandler($strAction)
    {
        if($strAction=='startCache')
        {
            $isCleared = false;
            $intLimit = 5;
            $intOffset = (int) \Input::post('offset');

            $objProducts = Product::findPublished(array('limit'=> $intLimit, 'offset'=> $intOffset));
            $objConfigs = IsoConfig::findBy('addFeed', '1');

            $varOffset = null !== $objProducts ? ($intOffset+$intLimit) :  'finished';
            $strMessage = null !== $objProducts ? ($intOffset+$intLimit) . ' products scanned...' : 'Cache refresh complete';

            if(null !== $objProducts)
            {
                while($objProducts->next())
                {
                    foreach($objConfigs as $objConfig)
                    {
                        // Override shop configuration to generate correct price
                        $objCart = new Cart();
                        $objCart->config_id = $objConfig->id;
                        Isotope::setConfig($objConfig);
                        Isotope::setCart($objCart);

                        $arrFeedFiles = IsoFeeds::getFeedFiles($objConfig);
                        foreach( $arrFeedFiles as $feedFile )
                        {
                            //Empty the cache folder if this is first run
                            if((int) \Input::post('offset') === 0 && !$isCleared)
                            {
                                $strLocation = IsoFeeds::getFeedCacheBaseDir($objConfig) . '/' .  IsoFeeds::getTypeFromFeedFile($objConfig, $feedFile);
                                if(is_dir(TL_ROOT . '/' . $strLocation))
                                {
                                    $objFolder = new \Folder($strLocation);
                                    $objFolder->purge();
                                    $isCleared = true;
                                }
                            }

                            $objIsoFeeds = new IsoFeeds();
                            $objIsoFeeds->generateProductXML($feedFile, $objProducts->current(), $objConfig);
                        }
                    }
                }
            }

            $arrContent = array
            (
                'data' => array(

                    'offset'	=> $varOffset,
                    'message'	=> $strMessage,
                ),
                'token'   => REQUEST_TOKEN,
                'content' => ""
            );

            $objResponse = new JsonResponse($arrContent);
            $objResponse->send();
        }
    }
}
