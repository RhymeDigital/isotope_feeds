<?php

/**
 * Isotope Feeds - Create RSS and other feeds from Isotope Products
 *
 * @copyright  Rhyme.Digital 2015, Winans Creative 2009, Intelligent Spark 2010, iserv.ch GmbH 2010
 *
 * @package    Isotope_Feeds
 * @link       http://rhyme.digital
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */

namespace Rhyme\Hooks\ExecutePreActions;

use Isotope\Isotope;
use Isotope\Model\ProductCollection\Cart;
use Rhyme\IsotopeFeeds as IsoFeeds;
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
class IsotopeFeeds extends \Controller
{
	/**
	 * Handle the AJAX request that will rebuild the cache, etc
	 * @param string
	 * @return string
	 */
	 public function ajaxHandler($strAction)
	 {
		 if($strAction=='startCache')
		 {
			$intLimit = 5;
			$intOffset = (int) \Input::post('offset');
			
			$objProducts = Product::findPublished(array('limit'=> $intLimit, 'offset'=> $intOffset));
			$objConfigs = IsoConfig::findBy('addFeed', '1');
			
			$varOffset = null !== $objProducts ? ($intOffset+$intLimit) :  'finished';
			$strMessage = null !== $objProducts ? ($intOffset+$intLimit) . ' products cached...' : 'Cache refresh complete';
			
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
							if((int) \Input::post('offset') == 0)
							{
								$strLocation = IsoFeeds::getFeedCacheBaseDir($objConfig) . '/' .  IsoFeeds::getTypeFromFeedFile($objConfig, $feedFile);
								if(is_dir(TL_ROOT . '/' . $strLocation))
								{
									$objFolder = new \Folder($strLocation);
									$objFolder->clear();
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
