<?php

declare(strict_types=1);

/**
 * Rhyme Isotope Feeds Bundle
 * Copyright (c) 2021 Rhyme.Digital
 * @license LGPL-3.0+
 */

namespace Rhyme\IsotopeFeedsBundle\FeedItem;

use Contao\FeedItem as ContaoFeedItem;
use Contao\StringUtil;

/**
 * Creates a RSS 2.0 compatible feed item
 *
 * @copyright  Rhyme Digital 2015
 * @author     Blair Winans <blair@rhyme.digital>
 * @author     Adam Fisher <adam@rhyme.digital>
 * @package    Isotope_Feeds
 */
class Rss20 extends ContaoFeedItem
{
	/**
	 * Cache the item's XML node to a file
	 * @param string
	 */
	public function cache($strLocation)
	{
		$xml = '<item>';
		$xml .= '<title>' . StringUtil::specialchars($this->title) . '</title>';
		$xml .= '<description><![CDATA[' . preg_replace('/[\n\r]+/', ' ', $this->description) . ']]></description>';
		$xml .= '<link>' . StringUtil::specialchars($this->link) . '</link>';
		$xml .= '<pubDate>' . date('r', $this->published) . '</pubDate>';
		$xml .= '<guid>' . ($this->guid ?: StringUtil::specialchars($this->link)) . '</guid>';

		// Enclosures
		if (is_array($this->enclosure))
		{
			foreach ($this->enclosure as $arrEnclosure)
			{
				$xml .= '<enclosure url="' . $arrEnclosure['url'] . '" length="' . $arrEnclosure['length'] . '" type="' . $arrEnclosure['type'] . '" />';
			}
		}

		$xml .= '</item>';
		
		$this->write($xml, $strLocation);
	}
	
	/**
	 * Write the XML to a file
	 * @param string
	 */
	protected function write($strXml, $strLocation)
	{
		$objFile = new \File($strLocation);
		$objFile->truncate();
		$objFile->write($strXml);
		$objFile->close();
	}
	
}
