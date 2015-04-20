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
 
namespace Rhyme\Feed;

/**
 * Creates a Google Merchant compatible feed
 *
 * @copyright  Rhyme Digital 2015
 * @author     Blair Winans <blair@rhyme.digital>
 * @author     Adam Fisher <adam@rhyme.digital>
 * @package    Isotope_Feeds
 */
class GoogleMerchant extends Rss20
{
	/**
	 * Generate an Google RSS 2.0 feed and return it as XML string
	 * @return string
	 */
	public function generate()
	{

		$xml  = '<?xml version="1.0" encoding="' . $GLOBALS['TL_CONFIG']['characterSet'] . '"?>' . "\n";
		$xml .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">' . "\n";
		$xml .= '  <channel>' . "\n";
		$xml .= '    <title>' . specialchars($this->title) . '</title>' . "\n";
		$xml .= '    <description>' . specialchars($this->description) . '</description>' . "\n";
		$xml .= '    <link>' . specialchars($this->link) . '</link>' . "\n";
		$xml .= '    <language>' . $this->language . '</language>' . "\n";
		$xml .= '    <pubDate>' . date('r', $this->published) . '</pubDate>' . "\n";
		$xml .= '    <generator>Contao Open Source CMS</generator>' . "\n";

		foreach ($this->arrFiles as $objFile)
		{
			$xml .= $objFile->getContent();
		}

		$xml .= '  </channel>' . "\n";
		$xml .= '</rss>';

		return $xml;
	}

}
