<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.User
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerUserEditUserDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerUserEditUserDisplay extends ComponentKunenaControllerUserEditDisplay
{
	protected $name = 'User/Edit/User';

	public $changeUsername;

	public $frontendForm;

	/**
	 * Load user form.
	 *
	 * @return void
	 */
	protected function before()
	{
		parent::before();

		$userParams = JComponentHelper::getParams('com_users');

		// Check if user is allowed to change his name.
		$this->changeUsername = $userParams->get('change_login_name', 1);

		// Check to see if Frontend User Params have been enabled.
		if ($userParams->get('frontend_userparams', 0))
		{
			JFactory::getLanguage()->load('com_users', JPATH_ADMINISTRATOR);

			JForm::addFormPath(JPATH_ROOT . '/components/com_users/models/forms');
			JForm::addFieldPath(JPATH_ROOT . '/components/com_users/models/fields');

			JPluginHelper::importPlugin('user');

			$registry = new JRegistry($this->user->params);
			$form = JForm::getInstance('com_users.profile', 'frontend');
			$data = new StdClass;
			$data->params = $registry->toArray();
			$dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger('onContentPrepareForm', array($form, $data));

			$form->bind($data);
			$this->frontendForm = $form->getFieldset('params');
		}

		$this->headerText = JText::_('COM_KUNENA_PROFILE_EDIT_USER_TITLE');
	}

	/**
	 * Prepare document.
	 *
	 * @return void
	 */
	protected function prepareDocument()
	{
		$app = JFactory::getApplication();
		$menu_item   = $app->getMenu()->getActive(); // get the active item
		$params = $menu_item->params; // get the params
		$params_title = $params->get('page_title');
		$params_keywords = $params->get('menu-meta_keywords');
		$params_description = $params->get('menu-description');

		if (!empty($params_title))
		{
			$title = $params->get('page_title');
			$this->setTitle($title);
		}
		else
		{
			$this->setTitle($this->headerText);
		}

		if (!empty($params_keywords))
		{
			$keywords = $params->get('menu-meta_keywords');
			$this->setKeywords($keywords);
		}
		else
		{
			$this->setKeywords($this->headerText);
		}

		if (!empty($params_description))
		{
			$description = $params->get('menu-meta_description');
			$this->setDescription($description);
		}
		else
		{
			$this->setDescription($this->headerText);
		}
	}
}
