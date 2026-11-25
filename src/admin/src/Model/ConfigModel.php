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

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\BaseModel;
use Joomla\Component\Config\Administrator\Model\ComponentModel;

/**
 * The configuration of Kunena: the options of the component, as in the options page of Joomla.
 *
 * The form and the save are the ones of com_config, on com_kunena: the same config.xml, the same validation, the same
 * permissions and the same events (context com_config.component), which the system plugin of Kunena listens to.
 *
 * @since   Kunena 8.0
 */
class ConfigModel extends BaseModel
{
    /**
     * The key of the posted data which is kept when the save fails, the one the model of com_config reads back.
     *
     * @since   Kunena 8.0
     */
    public const DATA_CONTEXT = 'com_config.edit.component.com_kunena.data';

    /**
     * @var  ComponentModel|null
     * @since   Kunena 8.0
     */
    private ?ComponentModel $componentModel = null;

    /**
     * The model of com_config which edits the options of a component, set on com_kunena.
     *
     * @return  ComponentModel
     *
     * @since   Kunena 8.0
     */
    public function getComponentModel(): ComponentModel
    {
        if (!$this->componentModel) {
            $model = Factory::getApplication()->bootComponent('com_config')->getMVCFactory()
                ->createModel('Component', 'Administrator', ['ignore_request' => true]);
            $model->setState('component.option', 'com_kunena');

            $this->componentModel = $model;
        }

        return $this->componentModel;
    }

    /**
     * The form of the configuration, with the saved options (or what was posted, when the save failed).
     *
     * @return  Form
     *
     * @since   Kunena 8.0
     */
    public function getForm(): Form
    {
        return $this->getComponentModel()->getForm();
    }
}
