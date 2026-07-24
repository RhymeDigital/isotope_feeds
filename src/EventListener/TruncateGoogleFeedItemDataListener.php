<?php

/**
 * Rhyme Isotope Feeds Bundle
 * Copyright (c) 2026 Rhyme.Digital
 * @license LGPL-3.0+
 */

declare(strict_types=1);

namespace Rhyme\IsotopeFeedsBundle\EventListener;

use Rhyme\IsotopeFeedsBundle\Event\TruncateGoogleFeedItemDataEvent;

class TruncateGoogleFeedItemDataListener
{
    /**
     * @param TruncateGoogleFeedItemDataEvent $event
     * @return void
     */
    public function __invoke(TruncateGoogleFeedItemDataEvent $event): void
    {
        $productFeedItem = $event->getFeedItem();

        // Truncate to the maximum number of characters allowed by Google's product data
        // specification. A null $maxLength means the field has no character limit.
        foreach ($event->getGoogleFields() as $fieldName) {

            $maxLength = null;

            switch ($fieldName)
            {
                case 'id':
                case 'gtin':
                case 'item_group_id':
                    $maxLength = 50;
                    break;

                case 'sale_price_effective_date':
                    $maxLength = 51;
                    break;

                case 'brand':
                case 'mpn':
                    $maxLength = 70;
                    break;

                case 'shipping_label':
                case 'color':
                case 'pattern':
                case 'size':
                    $maxLength = 100;
                    break;

                case 'title':
                    $maxLength = 150;
                    break;

                case 'material':
                    $maxLength = 200;
                    break;

                case 'product_type':
                    $maxLength = 750;
                    break;

                case 'additional_image_link':
                    $maxLength = 2000;
                    break;
            }

            // No limit
            if ($maxLength === null || !$productFeedItem->__isset($fieldName)) {
                continue;
            }

            $value = $productFeedItem->{$fieldName};

            if (\is_array($value)) {
                foreach ($value as $key => $singleValue) {
                    $value[$key] = $this->truncate($singleValue, $maxLength);
                }
            }
            else {
                $value = $this->truncate($value, $maxLength);
            }

            $productFeedItem->{$fieldName} = $value;
        }
    }

    /**
     * @param $value
     * @param $maxLength
     * @return string
     */
    protected function truncate($value, $maxLength): string
    {
        if (\strlen((string) $value) > $maxLength) {
            $value = \substr((string) $value, 0, $maxLength);
        }

        return (string)$value;
    }
}