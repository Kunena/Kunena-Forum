<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Forum.Message
 *
 * @copyright     Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\Utilities\ArrayHelper;
use Joomla\Database\QueryInterface;

/**
 * Class KunenaForumMessageFinder
 *
 * @since   Kunena 6.0
 */
class KunenaForumMessageFinder extends KunenaDatabaseObjectFinder
{
	/**
	 * @var string
	 * @since   Kunena 6.0
	 */
	protected $table = '#__kunena_messages';

	/**
	 * @var array
	 * @since   Kunena 6.0
	 */
	protected $hold = array(0);

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected $moved = null;

	/**
	 * Constructor.
	 *
	 * @since   Kunena
	 * @throws  Exception
	 */
	public function __construct()
	{
		parent::__construct();

		$this->limit = KunenaConfig::getInstance()->messages_per_page;
	}

	/**
	 * Filter by user access to the categories.
	 *
	 * It is very important to use this or category filter. Otherwise messages from unauthorized categories will be
	 * included to the search results.
	 *
	 * @param   KunenaUser  $user  user
	 *
	 * @return $this
	 * @since   Kunena
	 * @throws  Exception
	 */
	public function filterByUserAccess(KunenaUser $user)
	{
		$categories = $user->getAllowedCategories();
		$list       = implode(',', $categories);
		$this->query->where('a.catid IN (' . $list . ')');

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
	 * @param   array  $categories  categories
	 *
	 * @return $this|void
	 * @since   Kunena 6.0
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
			$this->query->where('a.catid IN (' . $list . ')');

			return $this;
		}
	}

	/**
	 * Filter by time.
	 *
	 * @param   Joomla\CMS\Date\Date  $starting  Starting date or null if older than ending date.
	 * @param   Joomla\CMS\Date\Date  $ending    Ending date or null if newer than starting date.
	 *
	 * @return $this
	 * @since   Kunena 6.0
	 */
	public function filterByTime(Joomla\CMS\Date\Date $starting = null, Joomla\CMS\Date\Date $ending = null)
	{
		if ($starting && $ending)
		{
			$this->query->where('a.time BETWEEN ' . $this->db->quote($starting->toUnix()) . ' AND ' . $this->db->quote($ending->toUnix()));
		}
		elseif ($starting)
		{
			$this->query->where('a.time > ' . $this->db->quote($starting->toUnix()));
		}
		elseif ($ending)
		{
			$this->query->where('a.time <= ' . $this->db->quote($ending->toUnix()));
		}

		return $this;
	}

	/**
	 * Filter by users role in the message. For now use only once.
	 *
	 * posted = User has posted the message.
	 *
	 * @param   KunenaUser  $user    user
	 * @param   string      $action  Action or negation of the action (!action).
	 *
	 * @return $this
	 * @since   Kunena 6.0
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
				$this->query->where('a.userid = ' . (int) $user->userid);
				break;
			case '!author':
				$this->query->where('a.userid != ' . (int) $user->userid);
				break;
			case 'editor':
				$this->query->where('a.modified_by = ' . (int) $user->userid);
				break;
			case '!editor':
				$this->query->where('a.modified_by != ' . (int) $user->userid);
				break;
			case 'thanker':
				$this->query->innerJoin($this->db->quoteName('#__kunena_thankyou', 'th') . ' ON th.postid = a.id AND th.userid = ' . (int) $user->userid);
				break;
			case '!thanker':
				$this->query->innerJoin($this->db->quoteName('#__kunena_thankyou', 'th') . ' ON th.postid = a.id AND th.userid != ' . (int) $user->userid);
				break;
			case 'thankee':
				$this->query->innerJoin($this->db->quoteName('#__kunena_thankyou', 'th') . ' ON th.postid = a.id AND th.targetuserid = ' . (int) $user->userid);
				break;
			case '!thankee':
				$this->query->innerJoin($this->db->quoteName('#__kunena_thankyou', 'th') . ' ON th.postid = a.id AND th.targetuserid != ' . (int) $user->userid);
				break;
		}

		return $this;
	}

	/**
	 * Filter by hold (0=published, 1=unapproved, 2=deleted, 3=topic deleted).
	 *
	 * @param   array  $hold  List of hold states to display.
	 *
	 * @return $this
	 * @since   Kunena 6.0
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
	 *
	 * @return  array|KunenaForumMessage[]
	 * @since   Kunena
	 * @throws  null
	 * @throws  Exception
	 */
	public function find($access = 'read')
	{
		$results = parent::find();

		return KunenaForumMessageHelper::getMessages($results, $access);
	}

	/**
	 * @param   QueryInterface  $query  query
	 *
	 * @return  void
	 * @since   Kunena 6.0
	 */
	protected function build(QueryInterface $query = null)
	{
		if (!empty($this->hold))
		{
			$this->hold = ArrayHelper::toInteger($this->hold, 0);
			$hold       = implode(',', $this->hold);
			$query->where('a.hold IN (' . $hold . ')');
		}
	}
}
