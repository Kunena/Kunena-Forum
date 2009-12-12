<?php
/**
 * @version		$Id: view.html.php 1123 2009-10-21 16:13:46Z mahagr $
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;

kimport('application.view');
kimport('html.bbcode');

/**
 * The HTML Kunena recent view.
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaViewRecent extends KView
{
	/**
	 * Display the view.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function display($tpl = null) {
		$this->assignRef ( 'state', $this->get ( 'State' ) );

		// Create shortcut to parameters.
		$params = $this->state->get('params');

		$this->assignRef ( 'threads', $this->get ( 'Items' ) );

		$app = JFactory::getApplication();

		jimport( 'joomla.application.menu' );
		$menu = JSite::getMenu();
		$menuitem = $menu->getActive();

		if ($params->get('show_page_title') && $menuitem->query['view'] == 'recent' && $menuitem->query['type'] == $this->state->type) $title = $params->get('page_title');
		else if ($this->state->type == 'all') $title = 'Recent Discussions';
		else if ($this->state->type == 'my') $title = 'My Discussions';
		else if ($this->state->type == 'category' && $this->state->{'category.id'})
		{
			$catmodel = $this->getModel('categories');
			$this->assignRef('path', $catmodel->getPath());
			$category = end($this->path);
			$title = $category->name;
			foreach ($this->path as &$category) $pathway->addItem($this->escape($category->name), JHtml::_('klink.categories', 'url', $category->id, '', ''));
		}
		else if ($this->state->type == 'category') $title = 'Category';
		$this->assign ( 'title', $title);

		$document = JFactory::getDocument();
		$document->setLink(JRoute::_('index.php?option=com_kunena'));

		$bbcode = KBBCode::getInstance();
		//echo '<pre>',print_r($this->threads), '</pre>'; die();
		foreach ($this->threads as $thread)
		{
			$item = new JFeedItem();
			$item->author = $thread->last_post_name;
			$item->category = $thread->catname;
			// $item->comments = '';
			$item->date = JHTML::_('date', $thread->last_post_time);
			$item->description =  $bbcode->Parse(stripslashes($thread->last_post_message));
			$item->guid = $thread->id;
			// $item->link = '';
			$item->pubDate = JHTML::_('date', $thread->first_post_time);
			$item->title = $thread->topic_subject;
			$document->addItem($item);
		}
	}
}