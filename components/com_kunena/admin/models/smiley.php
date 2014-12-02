<?php
/**
 * Kunena Component
 * @package Kunena.Administrator
 * @subpackage Models
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.application.component.modellist' );

/**
 * Smiley Model for Kunena
 *
 * @since 3.0
 */
class KunenaAdminModelSmiley extends KunenaModel {

	/**
	 * Method to auto-populate the model state.
	 */
	protected function populateState() {
		$this->context = 'com_kunena.admin.smiley';

		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		$layout = $app->input->get('layout');
		if ($layout) {
			$this->context .= '.'.$layout;
		}

		$value = JFactory::getApplication()->input->getInt('id');
		$this->setState($this->getName() . '.id', $value);
		$this->setState('item.id', $value);
	}

	public function getSmiley() {
		$db = JFactory::getDBO ();

		$id = $this->getState($this->getName() . '.id');
		if ($id) {
			$db->setQuery ( "SELECT * FROM #__kunena_smileys WHERE id={$db->quote($id)}" );
			$selected = $db->loadObject ();
			if (KunenaError::checkDatabaseError ())
				return null;

			return $selected;
		}
		return null;
	}

	public function getSmileyspaths() {
		$template = KunenaFactory::getTemplate();

		$selected = $this->getSmiley();

		$smileypath = $template->getSmileyPath();
		$files1 = (array) JFolder::Files(JPATH_SITE.'/'.$smileypath,false,false,false,array('index.php','index.html'));
		$files1 = (array) array_flip($files1);
		foreach ($files1 as $key=>&$path) $path = $smileypath.$key;

		$smileypath = 'media/kunena/emoticons/';
		$files2 = (array) JFolder::Files(JPATH_SITE.'/'.$smileypath,false,false,false,array('index.php','index.html'));
		$files2 = (array) array_flip($files2);
		foreach ($files2 as $key=>&$path) $path = $smileypath.$key;

		$smiley_images = $files1 + $files2;
		ksort($smiley_images);

		$smiley_list = array();
		foreach ( $smiley_images as $file => $path ) {
			$smiley_list[] = JHtml::_ ( 'select.option', $path, $file );
		}
		$list = JHtml::_('select.genericlist', $smiley_list, 'smiley_url', 'class="inputbox" onchange="update_smiley(this.options[selectedIndex].value);" onmousemove="update_smiley(this.options[selectedIndex].value);"', 'value', 'text', !empty($selected->location) ? $smiley_images[$selected->location] : '' );

		return $list;
	}
}
