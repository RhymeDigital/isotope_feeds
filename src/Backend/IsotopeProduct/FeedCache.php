<?php

declare(strict_types=1);

/**
 * Rhyme Isotope Feeds Bundle
 * Copyright (c) 2021 Rhyme.Digital
 * @license LGPL-3.0+
 */

namespace Rhyme\IsotopeFeedsBundle\Backend\IsotopeProduct;

use Contao\Ajax;
use Contao\Backend;
use Contao\BackendTemplate;
use Contao\Environment;
use Contao\Input;
use Contao\System;

/**
 * Popup to generate feed cache
 *
 * @copyright  Rhyme Digital 2015
 * @author     Blair Winans <blair@rhyme.digital>
 * @author     Adam Fisher <adam@rhyme.digital>
 * @package    Isotope_Feeds
 */
class FeedCache extends Backend
{

    /**
     * Current Ajax object
     * @var object
     */
    protected $objAjax;


    /**
     * Initialize the controller
     *
     * 1. Import user
     * 2. Call parent constructor
     * 3. Authenticate user
     * 4. Load language files
     * DO NOT CHANGE THIS ORDER!
     */
    public function __construct()
    {
        $this->import('BackendUser', 'User');
        parent::__construct();

        $this->User->authenticate();

        \System::loadLanguageFile('default');
        \System::loadLanguageFile('modules');
    }

    /**
     * Run controller and parse the login template
     */
    public function run()
    {
        $container = System::getContainer();

        $this->Template = new BackendTemplate('be_cache-refresh');
        $this->Template->main = '';

        if (Environment::get('isAjaxRequest'))
        {
            $this->objAjax = new Ajax(Input::post('action'));
            $this->objAjax->executePreActions();
        }

        $this->Template->rt = $container->get('contao.csrf.token_manager')->getToken($container->getParameter('contao.csrf_token_name'))->getValue();

        $this->Template->startMsg = 'Starting';
        $this->Template->endMsg = 'Finished';
        $this->Template->importSubmit = 'Refresh Cache';

        $this->Template->theme = $this->getTheme();
        $this->Template->base = Environment::get('base');
        $this->Template->language = $GLOBALS['TL_LANGUAGE'];
        $this->Template->pageOffset = $this->Input->cookie('BE_PAGE_OFFSET');
        $this->Template->error = ($this->Input->get('act') == 'error') ? $GLOBALS['TL_LANG']['ERR']['general'] : '';
        $this->Template->skipNavigation = $GLOBALS['TL_LANG']['MSC']['skipNavigation'];
        $this->Template->request = ampersand(Environment::get('request'));
        $this->Template->top = $GLOBALS['TL_LANG']['MSC']['backToTop'];
        $this->Template->expandNode = $GLOBALS['TL_LANG']['MSC']['expandNode'];
        $this->Template->collapseNode = $GLOBALS['TL_LANG']['MSC']['collapseNode'];

        return $this->Template->parse();
    }
}
