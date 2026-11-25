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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Kunena\Forum\Administrator\Helper\ConfigHelper;
use Kunena\Forum\Administrator\Model\ConfigModel;
use RuntimeException;
use Throwable;

/**
 * Saves the configuration of Kunena, as the options page of Joomla does.
 *
 * @since   Kunena 8.0
 */
class ConfigController extends BaseController
{
    /**
     * Save and stay on the configuration, on the same section.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public function apply(): void
    {
        $this->store(true);
    }

    /**
     * Save and go back to the dashboard.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public function save(): void
    {
        $this->store(false);
    }

    /**
     * Leave the configuration without saving.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public function cancel(): void
    {
        $this->app->setUserState(ConfigModel::DATA_CONTEXT, null);
        $this->setRedirect(Route::_('index.php?option=com_kunena&view=cpanel', false));
    }

    /**
     * @param   bool  $stay  Stay on the configuration after the save.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    private function store(bool $stay): void
    {
        $this->checkToken();

        $user    = $this->app->getIdentity();
        $section = $this->input->post->getCmd('section', '');
        $here    = Route::_(ConfigHelper::link($section), false);

        if (!$user->authorise('core.admin', 'com_kunena') && !$user->authorise('core.options', 'com_kunena')) {
            $this->setRedirect(Route::_('index.php?option=com_kunena', false), Text::_('JERROR_ALERTNOAUTHOR'), 'error');

            return;
        }

        $data = $this->input->post->get('jform', [], 'array');

        // Only who may administer Kunena changes its permissions.
        if (!$user->authorise('core.admin', 'com_kunena')) {
            unset($data['rules']);
        }

        /** @var ConfigModel $configModel */
        $configModel = $this->getModel('Config', 'Administrator', ['ignore_request' => true]);
        $model       = $configModel->getComponentModel();
        $valid       = $model->validate($model->getForm(), $data);

        if ($valid === false) {
            $this->app->setUserState(ConfigModel::DATA_CONTEXT, $data);

            foreach ($model->getErrors() as $error) {
                $this->app->enqueueMessage($error instanceof Throwable ? $error->getMessage() : (string) $error, 'warning');
            }

            $this->setRedirect($here);

            return;
        }

        try {
            $model->save(['params' => $valid, 'id' => ComponentHelper::getComponent('com_kunena')->id, 'option' => 'com_kunena']);
        } catch (RuntimeException $e) {
            $this->app->setUserState(ConfigModel::DATA_CONTEXT, $data);
            $this->setRedirect($here, Text::sprintf('JERROR_SAVE_FAILED', $e->getMessage()), 'error');

            return;
        }

        $this->app->setUserState(ConfigModel::DATA_CONTEXT, null);
        $this->setRedirect($stay ? $here : Route::_('index.php?option=com_kunena&view=cpanel', false), Text::_('COM_KUNENA_CONFIG_SAVED'));
    }
}
