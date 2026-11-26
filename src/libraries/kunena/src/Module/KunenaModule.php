<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Module
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Module;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Cache\Cache;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Cache\Controller\OutputController;
use Joomla\CMS\Document\Document;
use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use stdClass;

/**
 * Class KunenaModule
 *
 * @since   Kunena 6.0
 */
abstract class KunenaModule
{
    /**
     * CSS file to be loaded.
     *
     * @var     string
     * @since   Kunena 6.0
     */
    protected static mixed $css = null;

    /**
     * @var     stdClass
     * @since   Kunena 6.0
     */
    protected mixed $module = null;

    /**
     * @var     Registry
     * @since   Kunena 6.0
     */
    protected mixed $params = null;

    /**
     * @var     Document
     * @since   Kunena 6.0
     */
    protected mixed $document = null;

    /**
     * @param   stdClass  $module  module
     * @param   Registry  $params  params
     *
     * @since   Kunena 6.0
     *
     * @throws Exception
     */
    public function __construct(stdClass $module, Registry $params)
    {
        $this->module   = $module;
        $this->params   = $params;
        $this->document = Factory::getApplication()->getDocument();
    }

    /**
     * Internal module function to display module contents.
     *
     * @return  void
     *
     * @since   Kunena 6.0
     */
    abstract protected function _display(): void;

    /**
     * Display module contents.
     *
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    final public function display(): void
    {
        // Load CSS only once
        if (static::$css) {
            $this->document->getWebAssetManager()->registerAndUseStyle('mod_kunena_css', Uri::root(true) . static::$css);
            static::$css = null;
        }

        // The module caches itself ("Caching": use the global setting of the site), also for the registered users: what it shows
        // depends on the user (the categories he may read), so the copy is kept for each user, language and settings of the module.
        // The cache of Joomla is not used for it (the setting "cache" of the module is not given), it would keep one copy for all
        // the users of the same access levels. The styles and scripts which the module adds are kept with the copy.
        if ($this->params->get('owncache', 0) && method_exists($this->document, 'getHeadData')) {
            $cache = Factory::getContainer()->get(CacheControllerFactoryInterface::class)
                ->createCacheController('output', ['defaultgroup' => 'com_kunena']);

            if ($cache->getCaching()) {
                $this->displayCached($cache);

                return;
            }
        }

        $this->renderModule();
    }

    /**
     * Shows the copy of the module from the cache, or renders the module and keeps a copy of it. A module with a form token (the
     * login form) is not kept: the token is the one of the session of the visitor, the form of the copy failed for everybody else.
     *
     * @param   OutputController  $cache  The cache of Kunena.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     *
     * @throws  Exception
     */
    protected function displayCached(OutputController $cache): void
    {
        $cache->setLifeTime((int) $this->params->get('cache_time', 180) / 60);

        $app = Factory::getApplication();
        $id  = 'module.' . (int) ($this->module->id ?? 0) . '.' . (int) $app->getIdentity()?->id . '.'
            . $app->getLanguage()->getTag() . '.' . md5(serialize($this->params->toArray()));

        $data = $cache->get($id);

        if (\is_array($data) && isset($data['output'])) {
            echo Cache::getWorkarounds($data['output'], ['mergehead' => 1]);

            return;
        }

        // The head of the page is emptied while the module renders, so that the copy keeps only what the module adds (as the
        // cache of the modules of Joomla does), then the head of the page and the one of the module are merged.
        $document = $this->document;
        $before   = $document->getHeadData();
        $document->resetHeadData();
        $document->getWebAssetManager()->reset();

        ob_start();

        try {
            $this->renderModule();
        } finally {
            $output = (string) ob_get_clean();
            $stored = Cache::setWorkarounds($output, ['modulemode' => 1, 'headerbefore' => $before, 'nopathway' => 1, 'nohead' => 0, 'nomodules' => 1]);
            $module = $document->getHeadData();
            $document->resetHeadData();
            $document->mergeHeadData($before);
            $document->mergeHeadData($module);
        }

        if (!str_contains($output, Session::getFormToken())) {
            $cache->store(['output' => $stored], $id);
        }

        echo $output;
    }

    /**
     * Echoes the module, without its cache.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     *
     * @throws  Exception
     */
    public function renderModule(): void
    {
        // Initialize Kunena.
        KunenaForum::setup();

        // Display module.
        $this->_display();
    }
}
