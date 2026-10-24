<?php

/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Acl
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Plugin\Kunena\Acl\Helper;

\defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Access\KunenaAccessAbstract;
use Kunena\Forum\Libraries\Database\KunenaDatabaseObject;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * Permission by rank: a category can be limited to the users which have a rank.
 *
 * The category setting is:
 *
 *  - access: the minimum rank. It is a rank which is reached with a number of posts (0 = every registered user).
 *  - params special_ranks: special ranks (for example "VIP") which have access whatever the number of posts is.
 *  - params post_rank, reply_rank: a higher minimum rank to create topics or to reply (0 = same as the access).
 *
 * Guests have no rank, so they never have access to a category which is limited by rank. Moderators and
 * administrators are handled by Kunena, they always have access.
 *
 * @since   Kunena 7.2
 */
class KunenaAccessAcl extends KunenaAccessAbstract
{
    /**
     * The access type of this plugin. It must be less than 20 characters.
     *
     * @var     string
     * @since   Kunena 7.2
     */
    public const string TYPE = 'kunena.rank';

    /**
     * The ranks of the forum, loaded once: rankId => rank.
     *
     * @var     array|null
     * @since   Kunena 7.2
     */
    private static ?array $ranks = null;

    /**
     * Get list of supported access types.
     *
     * @return  array  Supported access types.
     * @since   Kunena 7.2
     */
    public function getAccessTypes(): array
    {
        return [self::TYPE];
    }

    /**
     * All ranks of the forum.
     *
     * @return  array  rankId => object with rankId, rankTitle, rankMin and rankSpecial.
     *
     * @since   Kunena 7.2
     */
    private function getRanks(): array
    {
        if (self::$ranks === null) {
            $db    = Factory::getContainer()->get('DatabaseDriver');
            $query = $db->createQuery()
                ->select($db->quoteName(['rankId', 'rankTitle', 'rankMin', 'rankSpecial']))
                ->from($db->quoteName('#__kunena_ranks'))
                ->order($db->quoteName('rankMin') . ' ASC, ' . $db->quoteName('rankId') . ' ASC');
            $db->setQuery($query);

            self::$ranks = [];

            foreach ((array) $db->loadObjectList() as $rank) {
                self::$ranks[(int) $rank->rankId] = $rank;
            }
        }

        return self::$ranks;
    }

    /**
     * @param   int  $rankId  The rank.
     *
     * @return  string  The title of the rank.
     *
     * @since   Kunena 7.2
     */
    private function getRankTitle(int $rankId): string
    {
        $ranks = $this->getRanks();

        return isset($ranks[$rankId]) ? Text::_($ranks[$rankId]->rankTitle) : '';
    }

    /**
     * The params of a category as a Registry.
     *
     * @param   KunenaCategory  $category  The category.
     *
     * @return  Registry
     *
     * @since   Kunena 7.2
     */
    private function getParams(KunenaCategory $category): Registry
    {
        return $category->params instanceof Registry ? $category->params : new Registry($category->params);
    }

    /**
     * Has the user a rank which is enough?
     *
     * @param   int    $userid   The user.
     * @param   int    $minRank  The minimum rank (0 = every registered user).
     * @param   array  $special  Special ranks which have access whatever the number of posts is.
     *
     * @return  boolean
     *
     * @since   Kunena 7.2
     */
    private function hasRank(int $userid, int $minRank, array $special = []): bool
    {
        // A guest has no rank.
        if ($userid <= 0) {
            return false;
        }

        $user = KunenaUserHelper::get($userid);

        // A special rank which was given to the user.
        if ($user->rank && \in_array((int) $user->rank, array_map('intval', $special), true)) {
            return true;
        }

        if ($minRank <= 0) {
            return true;
        }

        $ranks = $this->getRanks();

        // The rank was deleted: nobody gets in by accident.
        if (!isset($ranks[$minRank]) || $ranks[$minRank]->rankSpecial) {
            return false;
        }

        return (int) $user->posts >= (int) $ranks[$minRank]->rankMin;
    }

    /**
     * The select of the ranks which are reached with posts.
     *
     * @param   string  $name      Name of the field.
     * @param   int     $selected  Selected rank.
     * @param   string  $zero      Text of the option for zero.
     *
     * @return  string
     *
     * @since   Kunena 7.2
     */
    private function getMinRankSelect(string $name, int $selected, string $zero): string
    {
        $options   = [];
        $options[] = HTMLHelper::_('select.option', 0, $zero);

        foreach ($this->getRanks() as $rank) {
            if (!$rank->rankSpecial) {
                $options[] = HTMLHelper::_('select.option', (int) $rank->rankId, Text::sprintf('PLG_KUNENA_ACL_OPTION_FROM_POSTS', Text::_($rank->rankTitle), (int) $rank->rankMin));
            }
        }

        return HTMLHelper::_('select.genericlist', $options, $name, 'class="form-select" size="1"', 'value', 'text', $selected, $name);
    }

    /**
     * Get the options of the category form.
     *
     * @param   string|null     $accesstype  Access type.
     * @param   KunenaCategory  $category    The category.
     *
     * @return  array
     *
     * @since   Kunena 7.2
     */
    public function getAccessOptions($accesstype, KunenaCategory $category): array
    {
        $html = [];

        if ($accesstype && $accesstype !== self::TYPE) {
            return $html;
        }

        $params  = $this->getParams($category);
        $current = $category->accesstype === self::TYPE;

        $html['kunena-rank']['access'] = [
            'title' => Text::_('PLG_KUNENA_ACL_ACCESS_TITLE'),
            'desc'  => Text::_('PLG_KUNENA_ACL_ACCESS_DESC_FORM'),
            'input' => $this->getMinRankSelect('access-kunena-rank', $current ? (int) $category->access : 0, Text::_('PLG_KUNENA_ACL_OPTION_REGISTERED')),
        ];

        $special = [];

        foreach ($this->getRanks() as $rank) {
            if ($rank->rankSpecial) {
                $special[] = HTMLHelper::_('select.option', (int) $rank->rankId, Text::_($rank->rankTitle));
            }
        }

        if ($special) {
            $html['kunena-rank']['special'] = [
                'title' => Text::_('PLG_KUNENA_ACL_SPECIAL_TITLE'),
                'desc'  => Text::_('PLG_KUNENA_ACL_SPECIAL_DESC'),
                'input' => HTMLHelper::_(
                    'select.genericlist',
                    $special,
                    'params-kunena-rank[special_ranks][]',
                    'class="form-select" multiple="multiple" size="5"',
                    'value',
                    'text',
                    array_map('intval', (array) $params->get('special_ranks', [])),
                    'params-kunena-rank-special'
                ),
            ];
        }

        if (!$category->isSection()) {
            $html['kunena-rank']['post'] = [
                'title' => Text::_('PLG_KUNENA_ACL_POST_TITLE'),
                'desc'  => Text::_('PLG_KUNENA_ACL_POST_DESC'),
                'input' => $this->getMinRankSelect('params-kunena-rank[post_rank]', (int) $params->get('post_rank', 0), Text::_('PLG_KUNENA_ACL_OPTION_SAME')),
            ];
            $html['kunena-rank']['reply'] = [
                'title' => Text::_('PLG_KUNENA_ACL_REPLY_TITLE'),
                'desc'  => Text::_('PLG_KUNENA_ACL_REPLY_DESC'),
                'input' => $this->getMinRankSelect('params-kunena-rank[reply_rank]', (int) $params->get('reply_rank', 0), Text::_('PLG_KUNENA_ACL_OPTION_SAME')),
            ];
        }

        return $html;
    }

    /**
     * Get the access of the category, shown in the lists of the categories.
     *
     * @param   KunenaCategory  $category  The category.
     *
     * @return  array
     *
     * @since   Kunena 7.2
     */
    public function getCategoryAccess(KunenaCategory $category): array
    {
        $list = [];

        if ($category->accesstype !== self::TYPE) {
            return $list;
        }

        $id    = (int) $category->access;
        $title = $id > 0 ? Text::sprintf('PLG_KUNENA_ACL_X_OR_HIGHER', $this->getRankTitle($id)) : Text::_('PLG_KUNENA_ACL_OPTION_REGISTERED');

        $list['kunena.rank.' . $id] = [
            'type'  => self::TYPE,
            'id'    => $id,
            'alias' => $title,
            'title' => $title,
        ];

        return $list;
    }

    /**
     * Permissions to create topics and to reply, on top of the permissions of Kunena. A higher rank can be needed.
     *
     * @param   KunenaCategory  $category  The category.
     * @param   int             $userid    The user.
     *
     * @return  array
     *
     * @since   Kunena 7.2
     */
    public function getAuthoriseActions(KunenaCategory $category, int $userid): array
    {
        if ($category->accesstype !== self::TYPE) {
            return [];
        }

        $params  = $this->getParams($category);
        $special = (array) $params->get('special_ranks', []);
        $post    = (int) $params->get('post_rank', 0);
        $reply   = (int) $params->get('reply_rank', 0);

        $canPost  = $post <= 0 || $this->hasRank($userid, $post, $special);
        $canReply = $reply <= 0 || $this->hasRank($userid, $reply, $special);

        return ['topic.create' => $canPost, 'topic.reply' => $canReply, 'topic.post.reply' => $canReply];
    }

    /**
     * Authorise list of categories.
     *
     * @param   int    $userid      The user (0 = visitor).
     * @param   array  $categories  List of categories.
     *
     * @return  array  Category ids in the keys.
     *
     * @since   Kunena 7.2
     */
    public function authoriseCategories(int $userid, array $categories): array
    {
        $catlist = [];

        foreach ($categories as $category) {
            if ($category->accesstype !== self::TYPE) {
                continue;
            }

            $special = (array) $this->getParams($category)->get('special_ranks', []);

            if ($this->hasRank($userid, (int) $category->access, $special)) {
                $catlist[$category->id] = $category->id;
            }
        }

        return $catlist;
    }

    /**
     * Authorise a list of users to a topic or category, used for subscriptions and notifications.
     *
     * @param   KunenaDatabaseObject  $topic    Category or topic.
     * @param   array                 $userids  The users.
     *
     * @return  array  list(allow, deny).
     *
     * @since   Kunena 7.2
     */
    public function authoriseUsers(KunenaDatabaseObject $topic, array $userids): array
    {
        $allow = $deny = [];

        if (empty($userids)) {
            return [$allow, $deny];
        }

        $category = $topic->getCategory();

        if ($category->accesstype !== self::TYPE) {
            return [$allow, $deny];
        }

        $special = (array) $this->getParams($category)->get('special_ranks', []);

        // One query for all the users, the check uses the loaded users.
        KunenaUserHelper::loadUsers($userids);

        foreach ($userids as $userid) {
            if ($this->hasRank((int) $userid, (int) $category->access, $special)) {
                $allow[] = $userid;
            }
        }

        return [$allow, $deny];
    }
}
