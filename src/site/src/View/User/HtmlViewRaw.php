<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Views
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

\defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Kunena\Forum\Libraries\View\KunenaView;

/**
 * Users View
 *
 * @since   Kunena 6.0
 */
class KunenaViewUser extends KunenaView
{
    /**
     * @param   null  $tpl  tpl
     *
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function displayList($tpl = null)
    {
        $response = [];

        if ($this->me->exists()) {
            $users = $this->get('Items');

            foreach ($users as $user) {
                if ($this->config->username) {
                    $response[] = $user->username;
                } else {
                    $response[] = $user->name;
                }
            }
        }

        // Set the MIME type and header for JSON output.
        $this->document->setMimeEncoding('application/json');
        Factory::getApplication()->setHeader(
            'Content-Disposition',
            'attachment; filename="' . $this->getName() . '.' . $this->getLayout() . '.json"'
        );
        Factory::getApplication()->sendHeaders();

        echo json_encode($response);
    }

    /**
     * Method to return list of users by ajax request for userlist and search user
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     *
     * @since   Kunena 4.0
     *
     * @throws  Exception
     */
    public function displayListMention($tpl = null)
    {
        $response = [];

        if ($this->me->exists()) {
            $users = $this->get('Items');

            foreach ($users as $user) {
                $user_obj = new stdClass();

                $user_obj->id    = $user->id;
                $user_obj->photo = $user->getAvatarURL();
                $user_obj->name  = $user->username;

                $response[] = $user_obj;
            }
        }

        // Set the MIME type and header for JSON output.
        $this->document->setMimeEncoding('application/json');
        Factory::getApplication()->setHeader(
            'Content-Disposition',
            'attachment; filename="' . $this->getName() . '.' . $this->getLayout() . '.json"'
        );
        Factory::getApplication()->sendHeaders();

        echo json_encode($response);
    }
}
