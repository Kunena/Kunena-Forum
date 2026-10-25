<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Language
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Language;

\defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Multilanguage;

/**
 * Multilanguage support: the language of categories and topics follows the content languages of Joomla.
 *
 * On a site with one language nothing changes: the language of every category and topic is "All" (*).
 *
 * @since   Kunena 8.0
 */
abstract class KunenaMultilanguage
{
    /**
     * The code of "all languages".
     *
     * @since   Kunena 8.0
     */
    public const string ALL = '*';

    /**
     * The language code of the page.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function getCurrent(): string
    {
        return Factory::getApplication()->getLanguage()->getTag();
    }

    /**
     * The published content languages of Joomla.
     *
     * @return  array  Title by language code, like en-GB => English (en-GB).
     *
     * @since   Kunena 8.0
     */
    public static function getLanguages(): array
    {
        static $languages = null;

        if ($languages === null) {
            $languages = [];

            // Like Joomla does for its multilingual check: a content language counts when it is published, also when its
            // language pack is not installed (yet).
            foreach (LanguageHelper::getContentLanguages([1], false) as $code => $language) {
                $languages[$code] = $language->title;
            }
        }

        return $languages;
    }

    /**
     * Are there several content languages to choose from (so the language fields are useful in the forms).
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function hasLanguages(): bool
    {
        return \count(self::getLanguages()) > 1;
    }

    /**
     * Does the site filter on the language: the site is multilingual (the plugin System - Language Filter is on and
     * there are several content languages) and the page is on the site. Nothing is hidden in the backend.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function isEnabled(): bool
    {
        static $enabled = null;

        return $enabled ??= Factory::getApplication()->isClient('site') && Multilanguage::isEnabled();
    }

    /**
     * Does a category or topic of the language show on the page: always on a single language site, else only when
     * it is for all languages or for the language of the page.
     *
     * @param   string|null  $language  The language of the category or topic.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function isVisible(?string $language): bool
    {
        if (!self::isEnabled() || $language === null || $language === '' || $language === self::ALL) {
            return true;
        }

        return $language === self::getCurrent();
    }
}
