<?php

declare(strict_types=1);

/**
 * Rhyme Isotope Feeds Bundle
 * Copyright (c) 2026 Rhyme.Digital
 * @license LGPL-3.0+
 */

namespace Rhyme\IsotopeFeedsBundle\FeedItem;

use Contao\StringUtil;
use Contao\System;
use Rhyme\IsotopeFeedsBundle\Event\TruncateGoogleFeedItemDataEvent;

/**
 * Creates a Google Merchant compatible feed item
 */
class GoogleMerchant extends Rss20
{
	/**
	 * Cache the item's XML node to a file
	 * @param string $strLocation
	 */
	public function cache($strLocation)
	{
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
            'shipping_label',
            'shipping_weight',
			'color',
			'material',
			'pattern',
			'size',
			'gender',
			'age_group',
            'product_highlight',
		);

        // If the GTIN is empty and the combination of the MPN and brand is empty
        if (\trim((string)$this->gtin) === '' &&
            (\trim((string)$this->mpn) === '' || \trim((string)$this->brand) === '')
        ) {
		    $this->identifier_exists = 'no';
            $arrGoogleFields[] = 'identifier_exists';
        }

        // Truncate the data to adhere to Google's product data requirements
        $event = new TruncateGoogleFeedItemDataEvent(
            ['title', 'description', 'link', ...$arrGoogleFields], $this
        );
        System::getContainer()->get('event_dispatcher')->dispatch($event, TruncateGoogleFeedItemDataEvent::NAME);

        // Start the XML
		$xml = '	<item>' . "\n";
		$xml .= '      <title>' . StringUtil::specialchars($this->title) . '</title>' . "\n";
		$xml .= '      <description><![CDATA[' . \preg_replace('/[\n\r]+/', ' ', $this->description) . ']]></description>' . "\n";
		$xml .= '      <link><![CDATA[' . StringUtil::specialchars($this->link) . ']]></link>' . "\n";
		
		foreach($arrGoogleFields as $strKey)
		{
			if($this->__isset($strKey) )
			{
				if(\is_array($this->{$strKey}) && \count($this->{$strKey}))
				{
					foreach($this->{$strKey} as $value)
					{
						$xml .= '      <g:'.$strKey.'><![CDATA[' . StringUtil::specialchars($value) . ']]></g:'.$strKey.'>' . "\n";
					}
				}
				elseif(!\is_array($this->{$strKey}) && \strlen($this->{$strKey}))
				{
					$xml .= '      <g:'.$strKey.'><![CDATA[' . StringUtil::specialchars($this->{$strKey}) . ']]></g:'.$strKey.'>' . "\n";
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
