<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Models
 *
 * @copyright       Copyright (C) 2008 - 2024 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Model;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Model\KunenaModel;
use Kunena\Forum\Libraries\Tables\TableKunenaCategories;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use RuntimeException;

/**
 * Categories Model for Kunena
 *
 * @since 2.0
 */
class CategoriesModel extends KunenaModel
{
    /**
     * @var     string
     * @since   Kunena 6.0
     */
    public $context;

    /**
     * @var     KunenaCategory[]
     * @since   Kunena 6.0
     */
    protected $internalAdminCategories = false;

    /**
     * @var     KunenaCategory
     * @since   Kunena 6.0
     */
    protected $internalAdminCategory = false;

    /**
     * @return  Pagination
     * @since   Kunena 6.0
     */
    public function getAdminNavigation(): Pagination
    {
        return new Pagination($this->getState('list.total'), $this->getState('list.start'), $this->getState('list.limit'));
    }

    /**
     * @return  array|boolean
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function getAdminOptions()
    {
        $category = $this->getAdminCategory();

        if (!$category) {
            return false;
        }

        $category->params = new Registry($category->params);

        // Make a standard yes/no list
        $published    = [];
        $published [] = HTMLHelper::_('select.option', 1, Text::_('COM_KUNENA_PUBLISHED'));
        $published [] = HTMLHelper::_('select.option', 0, Text::_('COM_KUNENA_UNPUBLISHED'));

        // Make a standard yes/no list
        $yesno    = [];
        $yesno [] = HTMLHelper::_('select.option', 0, Text::_('COM_KUNENA_NO'));
        $yesno [] = HTMLHelper::_('select.option', 1, Text::_('COM_KUNENA_YES'));

        // Anonymous posts default
        $postAnonymous    = [];
        $postAnonymous [] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_CATEGORY_ANONYMOUS_X_REG'));
        $postAnonymous [] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_CATEGORY_ANONYMOUS_X_ANO'));

        $catParams                = [];
        $catParams['ordering']    = 'ordering';
        $catParams['toplevel']    = Text::_('COM_KUNENA_TOPLEVEL');
        $catParams['sections']    = 1;
        $catParams['unpublished'] = 1;
        $catParams['catid']       = $category->id;
        $catParams['action']      = 'admin';

        $channelsParams           = [];
        $channelsParams['catid']  = $category->id;
        $channelsParams['action'] = 'admin';

        $channelsOptions    = [];
        $channelsOptions [] = HTMLHelper::_('select.option', 'THIS', Text::_('COM_KUNENA_CATEGORY_CHANNELS_OPTION_THIS'));
        $channelsOptions [] = HTMLHelper::_('select.option', 'CHILDREN', Text::_('COM_KUNENA_CATEGORY_CHANNELS_OPTION_CHILDREN'));

        if (empty($category->channels)) {
            $category->channels = 'THIS';
        }

        $topicOrderingOptions   = [];
        $topicOrderingOptions[] = HTMLHelper::_('select.option', 'lastpost', Text::_('COM_KUNENA_CATEGORY_TOPIC_ORDERING_OPTION_LASTPOST'));
        $topicOrderingOptions[] = HTMLHelper::_('select.option', 'creation', Text::_('COM_KUNENA_CATEGORY_TOPIC_ORDERING_OPTION_CREATION'));
        $topicOrderingOptions[] = HTMLHelper::_('select.option', 'alpha', Text::_('COM_KUNENA_CATEGORY_TOPIC_ORDERING_OPTION_ALPHA'));
        $topicOrderingOptions[] = HTMLHelper::_('select.option', 'views', Text::_('COM_KUNENA_CATEGORY_TOPIC_ORDERING_OPTION_VIEWS'));
        $topicOrderingOptions[] = HTMLHelper::_('select.option', 'posts', Text::_('COM_KUNENA_CATEGORY_TOPIC_ORDERING_OPTION_POSTS'));

        $aliases = array_keys($category->getAliases());

        $lists                    = [];
        $lists ['accesstypes']    = KunenaAccess::getInstance()->getAccessTypesList($category);
        $lists ['accesslists']    = KunenaAccess::getInstance()->getAccessOptions($category);
        $lists ['categories']     = HTMLHelper::_('kunenaforum.categorylist', 'parentid', 0, null, $catParams, 'class="inputbox form-select"', 'value', 'text', $category->parentid);
        $lists ['channels']       = HTMLHelper::_('kunenaforum.categorylist', 'channels[]', 0, $channelsOptions, $channelsParams, 'class="inputbox form-select" multiple="multiple"', 'value', 'text', explode(',', $category->channels));
        $lists ['aliases']        = $aliases ? HTMLHelper::_('kunenaforum.checklist', 'aliases', $aliases, true, 'category_aliases') : null;
        $lists ['published']      = HTMLHelper::_('select.genericlist', $published, 'published', 'class="inputbox form-select"', 'value', 'text', $category->published);
        $lists ['forumLocked']    = HTMLHelper::_('select.genericlist', $yesno, 'locked', 'class="inputbox form-select" size="1"', 'value', 'text', $category->locked);
        $lists ['forumReview']    = HTMLHelper::_('select.genericlist', $yesno, 'review', 'class="inputbox form-select" size="1"', 'value', 'text', $category->review);
        $lists ['allowPolls']     = HTMLHelper::_('select.genericlist', $yesno, 'allowPolls', 'class="inputbox form-select" size="1"', 'value', 'text', $category->allowPolls);
        $lists ['allowAnonymous'] = HTMLHelper::_('select.genericlist', $yesno, 'allowAnonymous', 'class="inputbox form-select" size="1"', 'value', 'text', $category->allowAnonymous);
        $lists ['postAnonymous']  = HTMLHelper::_('select.genericlist', $postAnonymous, 'postAnonymous', 'class="inputbox form-select" size="1"', 'value', 'text', $category->postAnonymous);
        $lists ['topicOrdering']  = HTMLHelper::_('select.genericlist', $topicOrderingOptions, 'topicOrdering', 'class="inputbox form-select" size="1"', 'value', 'text', $category->topicOrdering);
        $lists ['allowRatings']   = HTMLHelper::_('select.genericlist', $yesno, 'allowRatings', 'class="inputbox form-select" size="1"', 'value', 'text', $category->allowRatings);

        $options                 = [];
        $options[0]              = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_A_CATEGORY_CFG_OPTION_NEVER'));
        $options[1]              = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_A_CATEGORY_CFG_OPTION_SECTION'));
        $options[2]              = HTMLHelper::_('select.option', '2', Text::_('COM_KUNENA_A_CATEGORY_CFG_OPTION_CATEGORY'));
        $options[3]              = HTMLHelper::_('select.option', '3', Text::_('COM_KUNENA_A_CATEGORY_CFG_OPTION_SUBCATEGORY'));
        $lists['display_parent'] = HTMLHelper::_('select.genericlist', $options, 'params[display][index][parent]', 'class="inputbox form-select" size="1"', 'value', 'text', $category->params->get('display.index.parent', '3'));

        unset($options[1]);

        $lists['display_children'] = HTMLHelper::_('select.genericlist', $options, 'params[display][index][children]', 'class="inputbox form-select" size="1"', 'value', 'text', $category->params->get('display.index.children', '3'));

        $topicIcons     = [];
        $topicIconslist = Folder::folders(JPATH_ROOT . '/media/kunena/topic_icons');

        foreach ($topicIconslist as $icon) {
            $topicIcons[] = HTMLHelper::_('select.option', $icon, $icon);
        }

        if (empty($category->iconset)) {
            $value = KunenaTemplate::getInstance()->params->get('DefaultIconset');
        } else {
            $value = $category->iconset;
        }

        $lists ['categoryIconset'] = HTMLHelper::_('select.genericlist', $topicIcons, 'iconset', 'class="inputbox form-select" size="1"', 'value', 'text', $value);

        return $lists;
    }

    /**
     * @return  boolean|KunenaCategory|void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function getAdminCategory()
    {
        $category = KunenaCategoryHelper::get($this->getState('item.id'));

        if (!$this->me->isAdmin($category)) {
            return false;
        }

        if ($this->internalAdminCategory === false) {
            if ($category->exists()) {
                if (!$category->isCheckedOut($this->me->userid)) {
                    $category->checkout($this->me->userid);
                }
            } else {
                // New category is by default child of the first section -- this will help new users to do it right
                $db = $this->getDatabase();

                $query = $db->getQuery(true)
                    ->select('a.id, a.name')
                    ->from("{$db->quoteName('#__kunena_categories')} AS a")
                    ->where("parentid={$db->quote('0')}")
                    ->where("id!={$db->quote($category->id)}")
                    ->order('ordering');

                $db->setQuery($query);

                try {
                    $sections = $db->loadObjectList();
                } catch (RuntimeException $e) {
                    Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');

                    return;
                }

                $category->parentid     = $this->getState('item.parent_id');
                $category->published    = 0;
                $category->ordering     = 9999;
                $category->pubRecurse   = 1;
                $category->adminRecurse = 1;
                $category->accesstype   = 'joomla.level';
                $category->access       = 1;
                $category->pubAccess    = 1;
                $category->adminAccess  = 8;
            }

            $this->internalAdminCategory = $category;
        }

        return $this->internalAdminCategory;
    }

    /**
     * @return  array|boolean
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function getAdminModerators()
    {
        $category = $this->getAdminCategory();

        if (!$category) {
            return false;
        }

        return $category->getModerators(false);
    }

    /**
     * Save the new order of categories choosed by user in the table TableKunenaCategories
     * 
     * @param   TableKunenaCategories $tableObject
     * @param   null                  $pks    pks
     * @param   null                  $order  order
     *
     * @return  boolean
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function saveOrder(TableKunenaCategories $tableObject, $pks = null, $order = null): bool
    {
        $conditions = [];

        if (empty($pks)) {
            return false;
        }

        // Update ordering values
        foreach ($pks as $i => $pk) {
            $tableObject->load((int) $pk);

            if ($tableObject->ordering != $order[$i]) {
                $tableObject->ordering = $order[$i];

                try {
                    $tableObject->store();
                } catch (Exception $e) {
                    Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
                }

                // Remember to reOrder within position and client_id
                $condition = $this->getReorderConditions($tableObject);
                $found     = false;

                foreach ($conditions as $cond) {
                    if ($cond[1] == $condition) {
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $key          = $tableObject->getKeyName();
                    $conditions[] = [$tableObject->$key, $condition];
                }
            }
        }

        // Execute reOrder for each category.
        foreach ($conditions as $cond) {
            $tableObject->load($cond[0]);
            $tableObject->reOrder($cond[1]);
        }

        // Clear the component's cache
        $this->cleanCache();

        return true;
    }

    /**
     * @param   \Joomla\CMS\Table\Table  $table  table
     *
     * @return  array
     *
     * @since   Kunena 6.0
     */
    protected function getReorderConditions(Table $table): array
    {
        $condition   = [];
        $condition[] = 'parentid = ' . (int) $table->parentid;

        return $condition;
    }

    /**
     * Get list of categories to be displayed in drop-down select in batch
     *
     * @return  array
     *
     * @throws  null
     * @throws  Exception
     * @since   Kunena 5.1
     */
    public function getBatchCategories(): string
    {
        $categories         = $this->getAdminCategories();
        $batch_categories   = array();
        $batch_categories[] = HTMLHelper::_('select.option', 'select', Text::_('JSELECT'));

        foreach ($categories as $category) {
            $batch_categories [] = HTMLHelper::_('select.option', $category->id, str_repeat('...', count($category->indent) - 1) . ' ' . $category->name);
        }

        $list = HTMLHelper::_('select.genericlist', $batch_categories, 'batch_catid_target', 'class="form-select" size="1"', 'value', 'text', 'select');

        return $list;
    }

    /**
     * @return  array|KunenaCategory[]
     *
     * @throws  Exception
     * @throws  null
     * @since   Kunena 6.0
     */
    public function getAdminCategories()
    {
        if ($this->internalAdminCategories === false) {
            $params = [
                'ordering'         => $this->getState('list.ordering'),
                'direction'        => $this->getState('list.direction') == 'asc' ? 1 : -1,
                'search'           => $this->getState('filter.search'),
                'unpublished'      => 1,
                'published'        => $this->getState('filter.published'),
                'filterTitle'      => $this->getState('filter.title'),
                'filterType'       => $this->getState('filter.type'),
                'filterAccess'     => $this->getState('filter.access'),
                'filterLocked'     => $this->getState('filter.locked'),
                'filterAllowPolls' => $this->getState('filter.allowPolls'),
                'filterReview'     => $this->getState('filter.review'),
                'filterAnonymous'  => $this->getState('filter.anonymous'),
                'action'           => 'none',
            ];

            $catid      = $this->getState('item.id', 0);
            $categories = [];
            $orphans    = [];

            if ($catid) {
                $categories   = KunenaCategoryHelper::getParents($catid, $this->getState('filter.levels') - 1, ['unpublished' => 1, 'action' => 'none']);
                $categories[] = KunenaCategoryHelper::get($catid);
            } else {
                $orphans = KunenaCategoryHelper::getOrphaned($this->getState('filter.levels') - 1, $params);
            }

            $categories = array_merge($categories, KunenaCategoryHelper::getChildren($catid, $this->getState('filter.levels') - 1, $params));
            $categories = array_merge($orphans, $categories);

            $categories = KunenaCategoryHelper::getIndentation($categories);
            $this->setState('list.total', \count($categories));

            if ($this->getState('list.limit')) {
                $this->internalAdminCategories = \array_slice($categories, $this->getState('list.start'), $this->getState('list.limit'));
            } else {
                $this->internalAdminCategories = $categories;
            }

            $admin = 0;
            $acl   = KunenaAccess::getInstance();

            foreach ($this->internalAdminCategories as $category) {
                // TODO: Following is needed for J!2.5 only:
                $parent   = $category->getParent();
                $siblings = array_keys(KunenaCategoryHelper::getCategoryTree($category->parentid));

                if ($parent) {
                    $category->up      = $this->me->isAdmin($parent) && reset($siblings) != $category->id;
                    $category->down    = $this->me->isAdmin($parent) && end($siblings) != $category->id;
                    $category->reOrder = $this->me->isAdmin($parent);
                } else {
                    $category->up      = $this->me->isAdmin($category) && reset($siblings) != $category->id;
                    $category->down    = $this->me->isAdmin($category) && end($siblings) != $category->id;
                    $category->reOrder = $this->me->isAdmin($category);
                }

                // Get ACL groups for the category.
                $access               = $acl->getCategoryAccess($category);
                $category->accessname = [];

                foreach ($access as $item) {
                    if (!empty($item['admin.link'])) {
                        $category->accessname[] = '<a href="' . htmlentities($item['admin.link'], ENT_COMPAT, 'utf-8') . '">' . htmlentities($item['title'], ENT_COMPAT, 'utf-8') . '</a>';
                    } else {
                        $category->accessname[] = htmlentities($item['title'], ENT_COMPAT, 'utf-8');
                    }
                }

                $category->accessname = implode(' / ', $category->accessname);

                // Checkout?
                if ($this->me->isAdmin($category) && $category->isCheckedOut(0)) {
                    $category->editor = KunenaFactory::getUser($category->checked_out)->getName();
                } else {
                    $category->checked_out = 0;
                    $category->editor      = '';
                }

                $admin += $this->me->isAdmin($category);
            }

            $this->setState('list.count.admin', $admin);
        }

        if (!empty($orphans)) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_CATEGORY_ORPHAN_DESC'), 'notice');
        }

        return $this->internalAdminCategories;
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @param   string  $ordering   ordering
     * @param   string  $direction  direction
     *
     * @return  void
     *
     * @since   Kunena 6.0
     */
    protected function populateState($ordering = 'a.lft', $direction = 'asc')
    {
        // Load the parameters.
        $params = ComponentHelper::getParams('com_kunena');
        $this->setState('params', $params);

        // Set list state in order to be able to re-order the categories.
        $value = $this->getUserStateFromRequest($this->context . '.list.start', 'limitstart', 0, 'int');
        $this->setState('list.start', $value);

        $value = $this->getUserStateFromRequest($this->context . '.list.limit', 'limit', $this->app->get('list_limit'), 'int');
        $this->setState('list.limit', $value);

        $value = $this->getUserStateFromRequest($this->context . '.list.ordering', 'filter_order', 'ordering', 'cmd');
        $this->setState('list.ordering', $value);

        $value = $this->getUserStateFromRequest($this->context . '.list.direction', 'filter_order_Dir', 'asc', 'word');

        if ($value != 'asc') {
            $value = 'desc';
        }

        $this->setState('list.direction', $value);

        $value = $this->getUserStateFromRequest($this->context . '.list.search', 'filter_search', '', 'cmd');
        $this->setState('filter.search', $value);

        // List state information.
        parent::populateState($ordering, $direction);
    }
}
