<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Models
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Model;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\Database\DatabaseInterface;
use Kunena\Forum\Libraries\User\KunenaUserBadges;

/**
 * Badges Model for Kunena: the badges are kept in the parameters of the component.
 *
 * @since   Kunena 7.2
 */
class BadgesModel extends AdminModel
{
    /**
     * @param   array    $data      data
     * @param   boolean  $loadData  load data
     *
     * @return  \Joomla\CMS\Form\Form|false
     *
     * @since   Kunena 7.2
     */
    public function getForm($data = [], $loadData = true)
    {
        return $this->loadForm('com_kunena.badges', 'badges', ['control' => 'jform', 'load_data' => $loadData]);
    }

    /**
     * The data of the form: the defined badges.
     *
     * @return  array
     *
     * @since   Kunena 7.2
     */
    protected function loadFormData()
    {
        $data = Factory::getApplication()->getUserState('com_kunena.edit.badges.data', []);

        if (empty($data)) {
            $data = ['badges' => KunenaUserBadges::getDefinitions()];
        }

        return $data;
    }

    /**
     * Save the badges in the parameters of the component.
     *
     * @param   array  $data  The data of the form.
     *
     * @return  boolean
     *
     * @since   Kunena 7.2
     */
    public function save($data)
    {
        $rows = [];

        // An id is kept as text, no id as an empty text.
        $id = static fn (int $value): string => $value > 0 ? (string) $value : '';

        foreach ((array) ($data['badges'] ?? []) as $row) {
            $row   = (array) $row;
            $title = trim((string) ($row['title'] ?? ''));

            // A badge needs a label and a user group or a rank.
            if ($title === '') {
                continue;
            }

            $rows['badges' . \count($rows)] = [
                'title'     => $title,
                'color'     => preg_match('/^#[0-9a-f]{6}$/i', (string) ($row['color'] ?? '')) ? $row['color'] : '#0d6efd',
                'usergroup' => $id((int) ($row['usergroup'] ?? 0)),
                'rank'      => $id((int) ($row['rank'] ?? 0)),
            ];
        }

        $params = ComponentHelper::getParams('com_kunena');
        $params->set('badges', $rows);
        $json = $params->toString();

        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->createQuery()
            ->update($db->quoteName('#__extensions'))
            ->set($db->quoteName('params') . ' = :params')
            ->where($db->quoteName('element') . ' = ' . $db->quote('com_kunena'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('component'))
            ->bind(':params', $json);

        try {
            $db->setQuery($query)->execute();
        } catch (Exception $e) {
            $this->setError($e->getMessage());

            return false;
        }

        return true;
    }
}
