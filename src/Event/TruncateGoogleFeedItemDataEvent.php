<?php

declare(strict_types=1);

namespace Rhyme\IsotopeFeedsBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Rhyme\IsotopeFeedsBundle\FeedItem\GoogleMerchant;

class TruncateGoogleFeedItemDataEvent extends Event
{
    /**
     * Event name
     */
    public const NAME = 'rhyme_isofeeds.truncate_google_feed_item_data_event';

    /**
     * The Google Merchant product feed fields
     * @var array
     */
    protected array $arrGoogleFields;

    /**
     * The Google Merchant product feed item
     * @var GoogleMerchant
     */
    protected GoogleMerchant $feedItem;

    /**
     * @param array          $arrGoogleFields
     * @param GoogleMerchant $feedItem
     */
    public function __construct(
         array $arrGoogleFields,
         GoogleMerchant $feedItem
    ) {
        $this->arrGoogleFields = $arrGoogleFields;
        $this->feedItem = $feedItem;
    }

    /**
     * @return array
     */
    public function getGoogleFields(): array
    {
        return $this->arrGoogleFields;
    }

    /**
     * @param array $arrGoogleFields
     * @return void
     */
    public function setGoogleFields(array $arrGoogleFields): void
    {
        $this->arrGoogleFields = $arrGoogleFields;
    }

    /**
     * @return GoogleMerchant
     */
    public function getFeedItem(): GoogleMerchant
    {
        return $this->feedItem;
    }

    /**
     * @param GoogleMerchant $feedItem
     * @return void
     */
    public function setFeedItem(GoogleMerchant $feedItem): void
    {
        $this->feedItem = $feedItem;
    }
}