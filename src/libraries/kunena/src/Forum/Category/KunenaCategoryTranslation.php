<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Forum.Category
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum\Category;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

/**
 * The translations of the name and the descriptions of the categories, one row per category and language.
 *
 * @since   Kunena 7.2
 */
abstract class KunenaCategoryTranslation
{
    /**
     * The fields of a category which can be translated.
     *
     * @since   Kunena 7.2
     */
    public const FIELDS = ['name', 'description', 'headerdesc'];

    /**
     * The translations in a language, of all the categories.
     *
     * @param   string  $language  The language code.
     *
     * @return  array  Rows (name, description, headerdesc) by category id.
     *
     * @since   Kunena 7.2
     */
    public static function loadLanguage(string $language): array
    {
        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->createQuery()
            ->select($db->quoteName(['category_id', 'name', 'description', 'headerdesc']))
            ->from($db->quoteName('#__kunena_categories_translations'))
            ->where($db->quoteName('language') . ' = :language')
            ->bind(':language', $language);

        try {
            return (array) $db->setQuery($query)->loadObjectList('category_id');
        } catch (Exception $e) {
            // The table is not there yet (the extension was not updated).
            return [];
        }
    }

    /**
     * All the translations of a category.
     *
     * @param   int  $categoryId  The category.
     *
     * @return  array  Rows (name, description, headerdesc) by language code.
     *
     * @since   Kunena 7.2
     */
    public static function getAll(int $categoryId): array
    {
        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->createQuery()
            ->select($db->quoteName(['language', 'name', 'description', 'headerdesc']))
            ->from($db->quoteName('#__kunena_categories_translations'))
            ->where($db->quoteName('category_id') . ' = :id')
            ->bind(':id', $categoryId, ParameterType::INTEGER);

        try {
            return (array) $db->setQuery($query)->loadObjectList('language');
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Save the translations of a category: a language with nothing filled in has no row.
     *
     * @param   int    $categoryId     The category.
     * @param   array  $translations   Posted values, by language code: name, description and headerdesc.
     * @param   array  $languages      The language codes which may be saved (the content languages).
     *
     * @return  void
     *
     * @since   Kunena 7.2
     */
    public static function save(int $categoryId, array $translations, array $languages): void
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);

        foreach ($languages as $language) {
            $row = (array) ($translations[$language] ?? []);

            $values = [];

            foreach (self::FIELDS as $field) {
                $values[$field] = trim((string) ($row[$field] ?? ''));
            }

            $delete = $db->createQuery()
                ->delete($db->quoteName('#__kunena_categories_translations'))
                ->where($db->quoteName('category_id') . ' = :id')
                ->where($db->quoteName('language') . ' = :language')
                ->bind(':id', $categoryId, ParameterType::INTEGER)
                ->bind(':language', $language);
            $db->setQuery($delete)->execute();

            if ($values['name'] === '' && $values['description'] === '' && $values['headerdesc'] === '') {
                continue;
            }

            $insert = $db->createQuery()
                ->insert($db->quoteName('#__kunena_categories_translations'))
                ->columns($db->quoteName(['category_id', 'language', 'name', 'description', 'headerdesc']))
                ->values(':id, :language, :name, :description, :headerdesc')
                ->bind(':id', $categoryId, ParameterType::INTEGER)
                ->bind(':language', $language)
                ->bind(':name', $values['name'])
                ->bind(':description', $values['description'])
                ->bind(':headerdesc', $values['headerdesc']);
            $db->setQuery($insert)->execute();
        }
    }

    /**
     * Remove the translations of a category.
     *
     * @param   int  $categoryId  The category.
     *
     * @return  void
     *
     * @since   Kunena 7.2
     */
    public static function delete(int $categoryId): void
    {
        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->createQuery()
            ->delete($db->quoteName('#__kunena_categories_translations'))
            ->where($db->quoteName('category_id') . ' = :id')
            ->bind(':id', $categoryId, ParameterType::INTEGER);

        try {
            $db->setQuery($query)->execute();
        } catch (Exception $e) {
            // The table is not there yet.
        }
    }
}
