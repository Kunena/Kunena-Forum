<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Forum.Message
 *
 * @copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Class KunenaForumMessageFinder
 */
class KunenaForumMessageFinder extends KunenaDatabaseObjectFinder
{
	protected $table = '#__kunena_messages';
	protected $hold = array(0);
	protected $moved = null;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->limit = KunenaConfig::getInstance()->messages_per_page;
	}

	/**
	 * @param $field
	 * @param $operation
	 * @param $value
	 *
	 * @return $this
	 * @deprecated Use where() instead.
	 */
	public function filterBy($field, $operation, $value)
	{
		return $this->where($field, $operation, $value);
	}

	/**
	 * Filter by user access to the categories.
	 *
	 * It is very important to use this or category filter. Otherwise messages from unauthorized categories will be
	 * included to the search results.
	 *
	 * @param   KunenaUser $user
	 *
	 * @return $this
	 */
	public function filterByUserAccess(KunenaUser $user)
	{
		$categories = $user->getAllowedCategories();
		$list = implode(',', $categories);
		$this->query->where("a.catid IN ({$list})");

		return $this;
	}

	/**
	 * Filter by list of categories.
	 *
	 * It is very important to use this or user access filter. Otherwise messages from unauthorized categories will be
	 * included to the search results.
	 *
	 * $messages->filterByCategories($me->getAllowedCategories())->limit(20)->find();
	 *
	 * @param   array $categories
	 *
	 * @return $this
	 */
	public function filterByCategories(array $categories)
	{
		$list = array();

		if (!empty($categories))
		{
			foreach ($categories as $category)
			{
				if ($category instanceof KunenaForumCategory)
				{
					$list[] = (int) $category->id;
				}
				else
				{
					$list[] = (int) $category;
				}
			}

			$list = implode(',', $list);
			$this->query->where("a.catid IN ({$list})");

			return $this;
		}
	}

	/**
	 * Filter by time.
	 *
	 * @param   JDate $starting  Starting date or null if older than ending date.
	 * @param   JDate $ending    Ending date or null if newer than starting date.
	 *
	 * @return $this
	 */
	public function filterByTime(JDate $starting = null, JDate $ending = null)
	{
		if ($starting && $ending)
		{
			$this->query->where("a.time BETWEEN {$this->db->quote($starting->toUnix())} AND {$this->db->quote($ending->toUnix())}");
		}
		elseif ($starting)
		{
			$this->query->where("a.time > {$this->db->quote($starting->toUnix())}");
		}
		elseif ($ending)
		{
			$this->query->where("a.time <= {$this->db->quote($ending->toUnix())}");
		}

		return $this;
	}

	/**
	 * Filter by users role in the message. For now use only once.
	 *
	 * posted = User has posted the message.
	 *
	 * @param   KunenaUser $user
	 * @param   string     $action Action or negation of the action (!action).
	 *
	 * @return $this
	 */
	public function filterByUser(KunenaUser $user = null, $action = 'posted')
	{
		if (is_null($user) || is_null($user->userid))
		{
			return $this;
		}

		switch ($action)
		{
			case 'author':
				$this->query->where('a.userid=' . (int) $user->userid);
				break;
			case '!author':
				$this->query->where('a.userid!=' . (int) $user->userid);
				break;
			case 'editor':
				$this->query->where('a.modified_by=' . (int) $user->userid);
				break;
			case '!editor':
				$this->query->where('a.modified_by!=' . (int) $user->userid);
				break;
			case 'thanker':
				$this->query->innerJoin('#__kunena_thankyou AS th ON th.postid=a.id AND th.userid=' . (int) $user->userid);
				break;
			case '!thanker':
				$this->query->innerJoin('#__kunena_thankyou AS th ON th.postid=a.id AND th.userid!=' . (int) $user->userid);
				break;
			case 'thankee':
				$this->query->innerJoin('#__kunena_thankyou AS th ON th.postid=a.id AND th.targetuserid=' . (int) $user->userid);
				break;
			case '!thankee':
				$this->query->innerJoin('#__kunena_thankyou AS th ON th.postid=a.id AND th.targetuserid!=' . (int) $user->userid);
				break;
		}

		return $this;
	}

	/**
	 * Filter by hold (0=published, 1=unapproved, 2=deleted, 3=topic deleted).
	 *
	 * @param   array $hold  List of hold states to display.
	 *
	 * @return $this
	 */
	public function filterByHold(array $hold = array(0))
	{
		$this->hold = $hold;

		return $this;
	}

	/**
	 * Get messages.
	 *
	 * @param   string  $access  Kunena action access control check.
	 * @return array|KunenaForumMessage[]
	 */
	public function find($access = 'read')
	{
		$results = parent::find();

		return KunenaForumMessageHelper::getMessages($results, $access);
	}

	protected function build(JDatabaseQuery $query)
	{
		// TODO: remove the field..
		$query->where("a.moved=0");
		if (!empty($this->hold))
		{
			Joomla\Utilities\ArrayHelper::toInteger($this->hold, 0);
			$hold = implode(',', $this->hold);
			$query->where("a.hold IN ({$hold})");
		}
	}
}
