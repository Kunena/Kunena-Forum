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
 * Rank Model for Kunena
 *
 * @since 3.0
 */
class KunenaAdminModelRank extends KunenaModel {

	/**
	 * Method to auto-populate the model state.
	 */
	protected function populateState() {
		$this->context = 'com_kunena.admin.rank';

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

	public function getRank() {
		$db = JFactory::getDBO ();

		$id = $this->getState($this->getName() . '.id');
		if ($id) {
			$db->setQuery ( "SELECT * FROM #__kunena_ranks WHERE rank_id={$db->quote($id)}" );
			$selected = $db->loadObject ();
			if (KunenaError::checkDatabaseError()) return null;

			return $selected;
		}
		return null;
	}

	public function getRankspaths() {
		$template = KunenaFactory::getTemplate();

		$selected = $this->getRank();

		$rankpath = $template->getRankPath();
		$files1 = (array) JFolder::Files(JPATH_SITE.'/'.$rankpath,false,false,false,array('index.php','index.html'));
		$files1 = (array) array_flip($files1);
		foreach ($files1 as $key=>&$path) $path = $rankpath.$key;

		$rankpath = 'media/kunena/ranks/';
		$files2 = (array) JFolder::Files(JPATH_SITE.'/'.$rankpath,false,false,false,array('index.php','index.html'));
		$files2 = (array) array_flip($files2);
		foreach ($files2 as $key=>&$path) $path = $rankpath.$key;

		$rank_images = $files1 + $files2;
		ksort($rank_images);

		$rank_list = array();
		foreach ( $rank_images as $file => $path ) {
			$rank_list[] = JHtml::_ ( 'select.option', $path, $file );
		}
		$list = JHtml::_('select.genericlist', $rank_list, 'rank_image', 'class="inputbox" onchange="update_rank(this.options[selectedIndex].value);" onmousemove="update_rank(this.options[selectedIndex].value);"', 'value', 'text', isset($selected->rank_image) ? $rank_images[$selected->rank_image] : '' );

		return $list;
	}
}
