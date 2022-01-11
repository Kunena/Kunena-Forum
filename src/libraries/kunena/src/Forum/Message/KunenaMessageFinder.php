<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Forum.Message
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum\Message;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Date\Date;
use Joomla\Database\QueryInterface;
use Joomla\Utilities\ArrayHelper;
use Kunena\Forum\Libraries\Database\Object\KunenaFinder;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\User\KunenaUser;

/**
 * Class \Kunena\Forum\Libraries\Forum\Message\MessageFinder
 *
 * @since   Kunena 6.0
 */
class KunenaMessageFinder extends KunenaFinder
{
	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $table = '#__kunena_messages';

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $hold = [0];

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected $moved = null;

	/**
	 * Constructor.
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function __construct()
	{
		parent::__construct();

		$this->limit = KunenaFactory::getConfig()->messagesPerPage;
	}

	/**
	 * Filter by user access to the categories.
	 *
	 * It is very important to use this or category filter. Otherwise messages from unauthorized categories will be
	 * included to the search results.
	 *
	 * @param   KunenaUser  $user  user
	 *
	 * @return  $this
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function filterByUserAccess(KunenaUser $user): KunenaMessageFinder
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
	 * @return  $this
	 *
	 * @since   Kunena 6.0
	 */
	public function filterByCategories(array $categories)
	{
		$list = [];

		if (!empty($categories))
		{
			foreach ($categories as $category)
			{
				if ($category instanceof KunenaCategory)
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
	 * @param   Date|null  $starting  Starting date or null if older than ending date.
	 * @param   Date|null  $ending    Ending date or null if newer than starting date.
	 *
	 * @return  $this
	 *
	 * @since   Kunena 6.0
	 */
	public function filterByTime(Date $starting = null, Date $ending = null): KunenaMessageFinder
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
	 * @param   KunenaUser|null  $user    user
	 * @param   string           $action  Action or negation of the action (!action).
	 *
	 * @return  $this
	 *
	 * @since   Kunena 6.0
	 */
	public function filterByUser(KunenaUser $user = null, $action = 'posted'): KunenaMessageFinder
	{
		if (\is_null($user) || \is_null($user->userid))
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
	 * @return  $this
	 *
	 * @since   Kunena 6.0
	 */
	public function filterByHold(array $hold = [0]): KunenaMessageFinder
	{
		$this->hold = $hold;

		return $this;
	}

	/**
	 * Get messages.
	 *
	 * @param   string  $access  Kunena action access control check.
	 *
	 * @return array
	 *
	 * @since   Kunena 6.0
	 * @throws \Exception
	 */
	public function find($access = 'read'): array
	{
		$results = parent::find();

		return KunenaMessageHelper::getMessages($results, $access);
	}

	/**
	 * @param   QueryInterface|null  $query  query
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function build(QueryInterface $query = null): void
	{
		if (!empty($this->hold))
		{
			$this->hold = ArrayHelper::toInteger($this->hold, 0);
			$hold       = implode(',', $this->hold);
			$query->where('a.hold IN (' . $hold . ')');
		}
	}
}
