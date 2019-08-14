<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Category
 *
 * @copyright   (C) 2008 - 2019 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use Joomla\CMS\Filesystem\Folder;
/**
 * Class ComponentKunenaControllerApplicationMiscDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerCategoryManageDisplay extends KunenaControllerDisplay
{
	/**
	 * @var string
	 * @since Kunena
	 */
	protected $name = 'Category/Manage';

	/**
	 * @var string
	 * @since Kunena
	 */
	public $headerText;

	/**
	 * @var KunenaForumCategory
	 * @since Kunena 5.1
	 */
	public $category;

	/**
	 * @var string
	 * @since Kunena
	 */
	public $total;

	/**
	 * @var string
	 * @since Kunena
	 */
	public $topics;

	/**
	 * @var KunenaPagination
	 * @since Kunena 5.1
	 */
	public $pagination;

	/**
	 * @var KunenaUser
	 * @since Kunena 5.1
	 */
	public $me;

	protected $state = null;

	/**
	 * Prepare category display.
	 *
	 * @return KunenaExceptionAuthorise|void
	 *
	 * @throws Exception
	 * @since Kunena 5.1
	 */
	protected function before()
	{
		$this->me = KunenaUserHelper::getMyself();

		if (!$this->me->isAdmin())
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), 403);
		}

		parent::before();

		require_once KPATH_SITE . '/models/category.php';

		$catid = $this->input->getInt('catid');

		KunenaFactory::loadLanguage('com_kunena.models', 'admin');
		KunenaFactory::loadLanguage('com_kunena.views', 'admin');
		KunenaFactory::loadLanguage('com_kunena', 'admin');

		$this->category = KunenaForumCategoryHelper::get($catid);
		$this->category->tryAuthorise();

		$category = $this->category;

		if (!$category)
		{
			return false;
		}

		$category->params = new Registry($category->params);

		$catList    = array();
		$catList [] = HTMLHelper::_('select.option', 0, Text::_('COM_KUNENA_TOPLEVEL'));

		// Make a standard yes/no list
		$published    = array();
		$published [] = HTMLHelper::_('select.option', 1, Text::_('COM_KUNENA_PUBLISHED'));
		$published [] = HTMLHelper::_('select.option', 0, Text::_('COM_KUNENA_UNPUBLISHED'));

		// Make a standard yes/no list
		$yesno    = array();
		$yesno [] = HTMLHelper::_('select.option', 0, Text::_('COM_KUNENA_NO'));
		$yesno [] = HTMLHelper::_('select.option', 1, Text::_('COM_KUNENA_YES'));

		// Anonymous posts default
		$post_anonymous    = array();
		$post_anonymous [] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_CATEGORY_ANONYMOUS_X_REG'));
		$post_anonymous [] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_CATEGORY_ANONYMOUS_X_ANO'));

		$cat_params                = array();
		$cat_params['ordering']    = 'ordering';
		$cat_params['toplevel']    = Text::_('COM_KUNENA_TOPLEVEL');
		$cat_params['sections']    = 1;
		$cat_params['unpublished'] = 1;
		$cat_params['catid']       = $category->id;
		$cat_params['action']      = 'admin';

		$channels_params           = array();
		$channels_params['catid']  = $category->id;
		$channels_params['action'] = 'admin';
		$channels_options          = array();
		$channels_options []       = HTMLHelper::_('select.option', 'THIS', Text::_('COM_KUNENA_CATEGORY_CHANNELS_OPTION_THIS'));
		$channels_options []       = HTMLHelper::_('select.option', 'CHILDREN', Text::_('COM_KUNENA_CATEGORY_CHANNELS_OPTION_CHILDREN'));

		if (empty($category->channels))
		{
			$category->channels = 'THIS';
		}

		$topic_ordering_options   = array();
		$topic_ordering_options[] = HTMLHelper::_('select.option', 'lastpost', Text::_('COM_KUNENA_CATEGORY_TOPIC_ORDERING_OPTION_LASTPOST'));
		$topic_ordering_options[] = HTMLHelper::_('select.option', 'creation', Text::_('COM_KUNENA_CATEGORY_TOPIC_ORDERING_OPTION_CREATION'));
		$topic_ordering_options[] = HTMLHelper::_('select.option', 'alpha', Text::_('COM_KUNENA_CATEGORY_TOPIC_ORDERING_OPTION_ALPHA'));
		$topic_ordering_options[] = HTMLHelper::_('select.option', 'views', Text::_('COM_KUNENA_CATEGORY_TOPIC_ORDERING_OPTION_VIEWS'));
		$topic_ordering_options[] = HTMLHelper::_('select.option', 'posts', Text::_('COM_KUNENA_CATEGORY_TOPIC_ORDERING_OPTION_POSTS'));

		$aliases = array_keys($category->getAliases());

		// Build the html select list
		// make a standard yes/no list
		$yesno    = array();
		$yesno [] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_A_NO'));
		$yesno [] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_A_YES'));

		$cat_params         = array('sections' => 1, 'catid' => 0);

		$lists                     = array();
		$lists ['accesstypes']     = KunenaAccess::getInstance()->getAccessTypesList($category);
		$lists ['accesslists']     = KunenaAccess::getInstance()->getAccessOptions($category);
		$lists ['categories']      = HTMLHelper::_('kunenaforum.categorylist', 'parent_id', 0, null, $cat_params, 'class="inputbox form-control"', 'value', 'text', $category->parent_id);
		$lists ['channels']        = HTMLHelper::_('kunenaforum.categorylist', 'channels[]', 0, $channels_options, $channels_params, 'class="inputbox form-control" multiple="multiple"', 'value', 'text', explode(',', $category->channels));
		$lists ['aliases']         = $aliases ? HTMLHelper::_('kunenaforum.checklist', 'aliases', $aliases, true, 'category_aliases') : null;
		$lists ['published']       = HTMLHelper::_('select.genericlist', $published, 'published', 'class="inputbox form-control"', 'value', 'text', $category->published);
		$lists ['forumLocked']     = HTMLHelper::_('select.genericlist', $yesno, 'locked', 'class="inputbox form-control" size="1"', 'value', 'text', $category->locked);
		$lists ['forumReview']     = HTMLHelper::_('select.genericlist', $yesno, 'review', 'class="inputbox form-control" size="1"', 'value', 'text', $category->review);
		$lists ['allow_polls']     = HTMLHelper::_('select.genericlist', $yesno, 'allow_polls', 'class="inputbox form-control" size="1"', 'value', 'text', $category->allow_polls);
		$lists ['allow_anonymous'] = HTMLHelper::_('select.genericlist', $yesno, 'allow_anonymous', 'class="inputbox form-control" size="1"', 'value', 'text', $category->allow_anonymous);
		$lists ['post_anonymous']  = HTMLHelper::_('select.genericlist', $post_anonymous, 'post_anonymous', 'class="inputbox form-control" size="1"', 'value', 'text', $category->post_anonymous);
		$lists ['topic_ordering']  = HTMLHelper::_('select.genericlist', $topic_ordering_options, 'topic_ordering', 'class="inputbox form-control" size="1"', 'value', 'text', $category->topic_ordering);
		$lists ['allow_ratings']   = HTMLHelper::_('select.genericlist', $yesno, 'allow_ratings', 'class="inputbox form-control" size="1"', 'value', 'text', $category->allow_ratings);

		$options                 = array();
		$options[0]              = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_A_CATEGORY_CFG_OPTION_NEVER'));
		$options[1]              = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_A_CATEGORY_CFG_OPTION_SECTION'));
		$options[2]              = HTMLHelper::_('select.option', '2', Text::_('COM_KUNENA_A_CATEGORY_CFG_OPTION_CATEGORY'));
		$options[3]              = HTMLHelper::_('select.option', '3', Text::_('COM_KUNENA_A_CATEGORY_CFG_OPTION_SUBCATEGORY'));
		$lists['display_parent'] = HTMLHelper::_('select.genericlist', $options, 'params[display][index][parent]', 'class="inputbox form-control" size="1"', 'value', 'text', $category->params->get('display.index.parent', '3'));

		unset($options[1]);

		$lists['display_children'] = HTMLHelper::_('select.genericlist', $options, 'params[display][index][children]', 'class="inputbox form-control" size="1"', 'value', 'text', $category->params->get('display.index.children', '3'));

		$topicicons     = array();
		$topiciconslist = Folder::folders(JPATH_ROOT . '/media/kunena/topic_icons');

		foreach ($topiciconslist as $icon)
		{
			$topicicons[] = HTMLHelper::_('select.option', $icon, $icon);
		}

		if (empty($category->iconset))
		{
			$value = KunenaTemplate::getInstance()->params->get('DefaultIconset');
		}
		else
		{
			$value = $category->iconset;
		}

		$lists ['category_iconset'] = HTMLHelper::_('select.genericlist', $topicicons, 'iconset', 'class="inputbox form-control" size="1"', 'value', 'text', $value);


		$this->lists = $lists;
	}

	/**
	 * Prepare document.
	 *
	 * @return void
	 * @throws Exception
	 * @since Kunena 5.1
	 */
	protected function prepareDocument()
	{
		$menu_item = $this->app->getMenu()->getActive();

		Factory::getDocument()->setMetaData('robots', 'nofollow, noindex');

		if ($menu_item)
		{
			$params             = $menu_item->params;
			$params_title       = $params->get('page_title');

			if (!empty($params_title))
			{
				$title = $params->get('page_title');
				Factory::getDocument()->setTitle($title);
			}
			else
			{
				$title = Text::_('COM_KUNENA_ADMIN');
				Factory::getDocument()->setTitle($title);
			}
		}
	}
}
