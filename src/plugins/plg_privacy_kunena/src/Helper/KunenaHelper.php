<?php

/**
 * Kunena Privacy Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Privacy
 *
 * @copyright       Copyright (C) 2023 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Plugin\Privacy\Kunena\Helper;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\ParameterType;

// No direct access
\defined('_JEXEC') or die;

/**
 * Class KunenaHelper
 *
 * @since  6.1.0
 */
abstract class KunenaHelper
{
    /**
     * Function to get user item(s) from table
     *
     * @param   string  $table   The Database Table to get the data from
     * @param   string  $field   The Table Field to match the User ID with
     * @param   int     $userid  The User ID
     * @param   bool    $single  Get single item or multiple / all items
     *
     * @return array
     *
     * @since   6.1.0
     */
    public static function getUserItems(string $table, string $field, int $userid, bool $single = false): array
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName($table))
            ->where($db->quoteName($field) . ' = :userid')
            ->bind(':userid', $userid, ParameterType::INTEGER);

        if ($single) {
            $data = $db->setQuery($query)->loadAssoc();
        } else {
            $data = $db->setQuery($query)->loadAssocList();
        }

        return $data;
    }

    /**
     * Function to remove / redact user data
     *
     * @param   array  $item      The item to process
     * @param   array  $excluded  The fields to exclude / remove
     * @param   array  $redacted  The fields to redact / replace values
     *
     * @return array
     *
     * @since   6.1.0
     */
    public static function processUserData(array $item, array $excluded, array $redacted): array
    {
        $data = [];

        foreach ($item as $fieldName => $fieldValue) {
            if (\in_array($fieldName, $redacted)) {
                $data[$fieldName] = Text::_('PLG_PRIVACY_KUNENA_DATA_REDACTED');
            } else {
                if (!\in_array($fieldName, $excluded)) {
                    $data[$fieldName] = $fieldValue;
                }
            }
        }

        return $data;
    }

    /**
     * Function to anomynize user data in database table
     *
     * @param   string  $table     The Database Table to get the data from
     * @param   string  $field     The Table Field to match the User ID with
     * @param   int     $userid    The User ID
     * @param   string  $setField  The Table Field to set the new value for
     * @param   string  $setValue  The new value to set
     *
     * @return array
     *
     * @since   6.1.0
     */
    public static function anomynizeUserData(string $table, string $field, int $userid, string $setField, string $setValue): bool
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->update($db->quoteName($table))
            ->where($db->quoteName($field) . ' = :userid')
            ->set($db->quoteName($setField) . ' = ' . $db->quote($setValue))
            ->bind(':userid', $userid, ParameterType::INTEGER);

        $db->setQuery($query);

        return $db->execute();
    }

    /**
     * Function to delete user data in database table
     *
     * @param   string  $table     The Database Table to get the data from
     * @param   string  $field     The Table Field to match the User ID with
     * @param   int     $userid    The User ID
     *
     * @return array
     *
     * @since   6.1.0
     */
    public static function deleteUserData(string $table, string $field, int $userid): bool
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->delete($db->quoteName($table))
            ->where($db->quoteName($field) . ' = :userid')
            ->bind(':userid', $userid, ParameterType::INTEGER);

        $db->setQuery($query);

        return $db->execute();
    }
}
