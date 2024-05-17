<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Controller;

\defined('_JEXEC') or die();

use Exception;
use RuntimeException;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;
use Kunena\Forum\Libraries\Route\KunenaRoute;

/**
 * Kunena Ranks Controller
 *
 * @since   Kunena 2.0
 */
class RanksController extends AdminController
{
    /**
     * @var     null|string
     * @since   Kunena 6.0
     */
    protected $baseurl = null;

    /**
     * Constructor.
     *
     * @param   array                $config   An optional associative array of configuration settings.
     *                                         Recognized key values include 'name', 'default_task', 'model_path', and
     *                                         'view_path' (this list is not meant to be comprehensive).
     *
     * @since   Kunena 2.0
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->baseurl = 'administrator/index.php?option=com_kunena&view=ranks';
    }

    /**
     * Edit a rank
     *
     * @param   null  $key     key
     * @param   null  $urlVar  url var
     *
     * @return  void
     *
     * @since   Kunena 2.0
     */
    public function edit($key = null, $urlVar = null)
    {
        if (!Session::checkToken()) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        $cid = $this->input->get('cid', [], 'array');
        $cid = ArrayHelper::toInteger($cid, []);

        $id = array_shift($cid);

        if (!$id) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_A_NO_RANKS_SELECTED'), 'notice');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        $this->setRedirect(Route::_("index.php?option=com_kunena&view=rank&layout=edit&id={$id}", false));
    }

    /**
     * Remove
     *
     * @return  void
     *
     * @throws  Exception
     * @throws  null
     * @since   Kunena 6.0
     */
    public function remove(): void
    {
        $db = Factory::getContainer()->get('DatabaseDriver');

        if (!Session::checkToken()) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        $cid = $this->input->get('cid', [], 'array');
        $cid = ArrayHelper::toInteger($cid, []);

        $cids = implode(',', $cid);

        if ($cids) {
            $query = $db->createQuery()
                ->delete()
                ->from("{$db->quoteName('#__kunena_ranks')}")
                ->where("rankId IN ($cids)");

            $db->setQuery($query);

            try {
                $db->execute();
            } catch (RuntimeException $e) {
                $this->app->enqueueMessage($e->getMessage(), 'error');

                return;
            }
        }

        $this->app->enqueueMessage(Text::_('COM_KUNENA_RANK_DELETED'), 'success');
        $this->setRedirect(KunenaRoute::_($this->baseurl, false));
    }
}
