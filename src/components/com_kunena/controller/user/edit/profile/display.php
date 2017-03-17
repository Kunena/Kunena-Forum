<?php
/**
 * Kunena Component
 * @package         Kunena.Site
 * @subpackage      Controller.User
 *
 * @copyright       Copyright (C) 2008 - 2017 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerUserEditProfileDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerUserEditProfileDisplay extends ComponentKunenaControllerUserEditDisplay
{
	/**
	 * @var string
	 * @since Kunena
	 */
	protected $name = 'User/Edit/Profile';

	/**
	 * Prepare profile form items.
	 *
	 * @return void
	 * @since Kunena
	 */
	protected function before()
	{
		parent::before();

		$bd = $this->profile->birthdate ? explode("-", $this->profile->birthdate) : array();

		if (count($bd) == 3)
		{
			$this->birthdate["year"]  = $bd[0];
			$this->birthdate["month"] = $bd[1];
			$this->birthdate["day"]   = $bd[2];
		}

		$this->genders[] = JHtml::_('select.option', '0', JText::_('COM_KUNENA_MYPROFILE_GENDER_UNKNOWN'));
		$this->genders[] = JHtml::_('select.option', '1', JText::_('COM_KUNENA_MYPROFILE_GENDER_MALE'));
		$this->genders[] = JHtml::_('select.option', '2', JText::_('COM_KUNENA_MYPROFILE_GENDER_FEMALE'));

		$config = KunenaConfig::getInstance();

		if ($config->social)
		{
			$this->social = array('twitter', 'facebook', 'myspace', 'skype', 'linkedin', 'delicious',
				'friendfeed', 'digg', 'yim', 'aim', 'google', 'icq', 'microsoft', 'blogspot', 'flickr',
				'bebo', 'instagram', 'qq', 'qzone', 'weibo', 'wechat', 'apple', 'vk', 'telegram');
		}
		else
		{
			$this->social = null;
		}

		$this->headerText = JText::_('COM_KUNENA_PROFILE_EDIT_PROFILE_TITLE');
	}

	/**
	 * Prepare document.
	 *
	 * @return void
	 * @since Kunena
	 */
	protected function prepareDocument()
	{
		$app       = JFactory::getApplication();
		$menu_item = $app->getMenu()->getActive();

		if ($menu_item)
		{
			$params             = $menu_item->params;
			$params_title       = $params->get('page_title');
			$params_keywords    = $params->get('menu-meta_keywords');
			$params_description = $params->get('menu-meta_description');

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
}
