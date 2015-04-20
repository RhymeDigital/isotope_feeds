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
 
namespace Rhyme\FeedItem;

/**
 * Creates a Google Merchant compatible feed item
 *
 * @copyright  Rhyme Digital 2015
 * @author     Blair Winans <blair@rhyme.digital>
 * @author     Adam Fisher <adam@rhyme.digital>
 * @package    Isotope_Feeds
 */
class GoogleMerchant extends Rss20
{
	/**
	 * Cache the item's XML node to a file
	 * @param string
	 */
	public function cache($strLocation)
	{
		//double-check: if we do not have two out of 3 unique identifiers, delete cache b/c it is invalid
		if((!$this->condition || !$this->availability || !$this->brand) || (!strlen($this->gtin) && !strlen($this->mpn))) 
        {   
			if(is_file(TL_ROOT . '/' . $strLocation)) 
			{
				\Files::getInstance()->delete($strLocation);
			}
			return;
		}

		$arrGoogleFields = array
		(
			'id',
			'price',
			'availability',
			'condition',
			'image_link',
			'product_type',
			'google_product_category',
			'brand',
			'gtin',
			'mpn',
			'additional_image_link',
			'sale_price',
			'sale_price_effective_date',
			'item_group_id',
			'color',
			'material',
			'pattern',
			'size',
			'gender',
			'age_group',
		);
		
		
		$xml .= '	<item>' . "\n";
		$xml .= '      <title>' . specialchars($this->title) . '</title>' . "\n";
		$xml .= '      <description><![CDATA[' . preg_replace('/[\n\r]+/', ' ', $this->description) . ']]></description>' . "\n";
		$xml .= '      <link><![CDATA[' . specialchars($this->link) . ']]></link>' . "\n";
		
		foreach($arrGoogleFields as $strKey)
		{
			if($this->__isset($strKey) && count($this->$strKey) )
			{
				if(is_array($this->$strKey) && count($this->$strKey))
				{
					foreach($this->$strKey as $value)
					{
						$xml .= '      <g:'.$strKey.'><![CDATA[' . specialchars($value) . ']]></g:'.$strKey.'>' . "\n";
					}
				}
				elseif(!is_array($this->$strKey) && strlen($this->$strKey))
				{
					$xml .= '      <g:'.$strKey.'><![CDATA[' . specialchars($this->$strKey) . ']]></g:'.$strKey.'>' . "\n";
				}
			}
		}
		if($this->shipping)
		{
			$xml .= $this->shipping;
		}
		$xml .= '	</item>' . "\n";
		
		$this->write($xml, $strLocation);
	}
}
