<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Announcement
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Announcement\Item;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Forum\Announcement\KunenaAnnouncementHelper;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Controller\KunenaController;
use RuntimeException;

/**
 * Class ComponentAnnouncementControllerItemDisplay
 *
 * @since   Kunena 4.0
 */
class AnnouncementItemDisplay extends KunenaControllerDisplay
{
    /**
     * @var     string
     * @since   Kunena 6.0
     */
    public $announcement;

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    protected $name = 'Announcement/Item';

    /**
     * Prepare announcement display.
     *
     * @return  void
     * @throws  null
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function before()
    {
        parent::before();

        $id = $this->input->getInt('id', null);

        $this->announcement = KunenaAnnouncementHelper::get($id);
        $this->announcement->tryAuthorise();

        $Itemid = $this->input->getInt('Itemid');

        if (!$Itemid && $this->config->sefRedirect) {
            try {
                $itemid = KunenaRoute::fixMissingItemID();

                $params = [
                    'option' => 'com_kunena',
                    'view' => 'announcement',
                    'layout' => 'default',
                    'id' => $id,
                    'Itemid' => $itemid
                ];

                return $this->app->redirect(KunenaRoute::_('index.php?' . http_build_query($params), false));
            }

            catch (Exception $e) {
                throw new RuntimeException('Failed to create controller: ' . $e->getMessage());
            }
        }
    }

    /**
     * Prepare document.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function prepareDocument()
    {
        $menu_item = $this->app->getMenu()->getActive();

        if ($menu_item) {
            $params             = $menu_item->getParams();
            $params_title       = $params->get('page_title');
            $params_description = $params->get('menu-meta_description');

            if (!empty($params_title)) {
                $title = $params->get('page_title');
                $this->setTitle($title);
            } else {
                $this->setTitle($this->announcement->title);
            }

            if (!empty($params_description)) {
                $description = $params->get('menu-meta_description');
                $this->setDescription($description);
            } else {
                $this->setDescription(Text::_('COM_KUNENA_ANN_ANNOUNCEMENTS'));
            }
        }
    }
}
