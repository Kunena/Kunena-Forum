<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	(C) 2008 Kunena. All rights reserved, see COPYRIGHT.php
 * @license		GNU General Public License, see LICENSE.php
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die('Invalid Request.');

jimport('joomla.html.html');

/**
 * List form field type object
 *
 * @package 	Zine
 */
class JXFieldTypeList_Category extends JXFieldTypeList
{
   /**
	* Field type
	*
	* @access	protected
	* @var		string
	*/
	var	$_type = 'ListCategory';

	function _getOptions(&$node)
	{
		if ($this->_parent->getValue('parent_id') == 0 && $this->_parent->getValue('id') != 0) {
			$options = array(
				JHTML::_('select.option', 0, '- None -')
			);
		}
		else {

			$model = &JModel::getInstance('Categories', 'KunenaModel', array('ignore_request' => true));
			$model->setState('list.select',	'a.id AS value, a.title AS text');
			$model->setState('list.order',	'a.left_id');
			$model->setState('list.tree',	true);
			$options	= $model->getItems(false);

			foreach ($options as $i => $option) {
				$options[$i]->text = str_pad($option->text, strlen($option->text) + 2*$option->level, '- ', STR_PAD_LEFT);
			}
		}

		return $options;
	}
}