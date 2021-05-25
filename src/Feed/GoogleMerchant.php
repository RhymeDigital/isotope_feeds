<?php

declare(strict_types=1);

/**
 * Rhyme Isotope Feeds Bundle
 * Copyright (c) 2021 Rhyme.Digital
 * @license LGPL-3.0+
 */

namespace Rhyme\IsotopeFeedsBundle\Feed;

use Contao\StringUtil;

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
		$xml .= '    <title>' . StringUtil::specialchars($this->title) . '</title>' . "\n";
		$xml .= '    <description>' . StringUtil::specialchars($this->description) . '</description>' . "\n";
		$xml .= '    <link>' . StringUtil::specialchars($this->link) . '</link>' . "\n";
		$xml .= '    <language>' . $this->language . '</language>' . "\n";
		$xml .= '    <pubDate>' . date('r', (int) $this->published) . '</pubDate>' . "\n";
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
