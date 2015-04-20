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

use Contao\FeedItem as ContaoFeedItem;

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
		$xml .= '<item>';
		$xml .= '<title>' . specialchars($this->title) . '</title>';
		$xml .= '<description><![CDATA[' . preg_replace('/[\n\r]+/', ' ', $this->description) . ']]></description>';
		$xml .= '<link>' . specialchars($this->link) . '</link>';
		$xml .= '<pubDate>' . date('r', $this->published) . '</pubDate>';
		$xml .= '<guid>' . ($this->guid ? $this->guid : specialchars($this->link)) . '</guid>';

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
