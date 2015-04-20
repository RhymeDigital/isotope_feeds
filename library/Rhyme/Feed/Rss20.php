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

use Contao\Feed as ContaoFeed;

/**
 * Creates a RSS 2.0 compatible feed
 *
 * @copyright  Rhyme Digital 2015
 * @author     Blair Winans <blair@rhyme.digital>
 * @author     Adam Fisher <adam@rhyme.digital>
 * @package    Isotope_Feeds
 */
class Rss20 extends ContaoFeed
{

	/**
	 * Individual XML files
	 * @var array
	 */
	protected $arrFiles = array();
	
	
	/**
	 * Initialize the object and import default classes
	 * @param string
	 */
	public function __construct($strName='')
	{
		parent::__construct($strName);
	}
	
	
	/**
	 * Add a XML file
	 * @param object
	 */
	public function addFile(\Contao\File $objFile)
	{
		$this->arrFiles[] = $objFile;
	}


	/**
	 * Generate an RSS 2.0 feed and return it as XML string
	 * @return string
	 */
	public function generate()
	{
		$this->adjustPublicationDate();

		$xml  = '<?xml version="1.0" encoding="' . $GLOBALS['TL_CONFIG']['characterSet'] . '"?>';
		$xml .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">';
		$xml .= '<channel>';
		$xml .= '<title>' . specialchars($this->title) . '</title>';
		$xml .= '<description>' . specialchars($this->description) . '</description>';
		$xml .= '<link>' . specialchars($this->link) . '</link>';
		$xml .= '<language>' . $this->language . '</language>';
		$xml .= '<pubDate>' . date('r', $this->published) . '</pubDate>';
		$xml .= '<generator>Contao Open Source CMS</generator>';
		$xml .= '<atom:link href="' . specialchars($this->Environment->base . $this->strName) . '.xml" rel="self" type="application/rss+xml" />';

		foreach ($this->arrFiles as $objFile)
		{
			$xml .= $objFile->getContent();
		}

		$xml .= '</channel>';
		$xml .= '</rss>';

		return $xml;
	}

}
