<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Controllers.User
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class ComponentKunenaControllerUserEditProfileDisplay
 */
class ComponentKunenaControllerUserEditProfileDisplay extends ComponentKunenaControllerUserEditDisplay
{
	protected $name = 'User/Edit/Profile';

	protected function before()
	{
		parent::before();

		$bd = @explode("-" , $this->profile->birthdate);

		$this->birthdate["year"] = $bd[0];
		$this->birthdate["month"] = $bd[1];
		$this->birthdate["day"] = $bd[2];

		$this->genders[] = JHtml::_('select.option', '0', JText::_('COM_KUNENA_MYPROFILE_GENDER_UNKNOWN'));
		$this->genders[] = JHtml::_('select.option', '1', JText::_('COM_KUNENA_MYPROFILE_GENDER_MALE'));
		$this->genders[] = JHtml::_('select.option', '2', JText::_('COM_KUNENA_MYPROFILE_GENDER_FEMALE'));

		$this->social = array('twitter', 'facebook', 'myspace', 'skype', 'linkedin', 'delicious',
			'friendfeed', 'digg', 'yim', 'aim', 'gtalk', 'icq', 'msn', 'blogspot', 'flickr', 'bebo');

		$this->headerText = JText::_('COM_KUNENA_PROFILE_EDIT_PROFILE_TITLE');
	}

	protected function prepareDocument()
	{
		$this->setTitle($this->headerText);
	}
}
