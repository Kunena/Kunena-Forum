<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Topic
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Topic\Item;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Access\Access;
use Kunena\Forum\Libraries\Attachment\Helper;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Forum\Category\Category;
use Kunena\Forum\Libraries\Forum\Topic\Topic;
use Kunena\Forum\Libraries\Html\Parser;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\KunenaPrivate\Message\Finder;
use Kunena\Forum\Libraries\Pagination\Pagination;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Template\Template;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\User\KunenaUser;
use stdClass;
use function defined;

/**
 * Class ComponentTopicControllerItemDisplay
 *
 * @since   Kunena 4.0
 */
class ComponentTopicControllerItemDisplay extends KunenaControllerDisplay
{
	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $name = 'Topic/Item';

	/**
	 * @var     KunenaUser
	 * @since   Kunena 6.0
	 */
	public $me;

	/**
	 * @var     Category
	 * @since   Kunena 6.0
	 */
	public $category;

	/**
	 * @var     Topic
	 * @since   Kunena 6.0
	 */
	public $topic;

	/**
	 * @var     Pagination
	 * @since   Kunena 6.0
	 */
	public $pagination;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $headerText;

	/**
	 * Prepare topic display.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	protected function before()
	{
		parent::before();

		$catid  = $this->input->getInt('catid', 0);
		$id     = $this->input->getInt('id', 0);
		$mesid  = $this->input->getInt('mesid', 0);
		$start  = $this->input->getInt('limitstart', 0);
		$limit  = $this->input->getInt('limit', 0);
		$Itemid = $this->input->getInt('Itemid');
		$format = $this->input->getInt('format');

		if (!$Itemid && $format != 'feed' && $this->config->sef_redirect)
		{
			$itemid     = KunenaRoute::fixMissingItemID();
			$controller = BaseController::getInstance("kunena");
			$controller->setRedirect(\Kunena\Forum\Libraries\Route\KunenaRoute::_("index.php?option=com_kunena&view=topic&catid={$catid}&id={$id}&Itemid={$itemid}", false));
			$controller->redirect();
		}

		if ($limit < 1 || $limit > 100)
		{
			$limit = $this->config->messages_per_page;
		}

		$this->me = \Kunena\Forum\Libraries\User\Helper::getMyself();

		$allowed = md5(serialize(Access::getInstance()->getAllowedCategories()));
		$cache   = Factory::getCache('com_kunena', 'output');

		/*
		if ($cache->start("{$this->ktemplate->name}.common.jump.{$allowed}", 'com_kunena.template'))
		 {
		 return;
		 }*/

		$options            = [];
		$options []         = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FORUM_TOP'));
		// Todo: fix params
		$cat_params         = ['sections' => 1, 'catid' => 0];
		$this->categorylist = HTMLHelper::_('select.genericlist', $options, 'catid', 'class="class="form-control fbs" size="1" onchange = "this.form.submit()"', 'value', 'text');

		// Load topic and message.
		if ($mesid)
		{
			// If message was set, use it to find the current topic.
			$this->message = \Kunena\Forum\Libraries\Forum\Message\Helper::get($mesid);
			$this->topic   = $this->message->getTopic();
		}
		else
		{
			// Note that redirect loops throw RuntimeException because of we added \Kunena\Forum\Libraries\Forum\Topic\Topic::getTopic() call!
			$this->topic   = \Kunena\Forum\Libraries\Forum\Topic\Helper::get($id)->getTopic();
			$this->message = \Kunena\Forum\Libraries\Forum\Message\Helper::get($this->topic->first_post_id);
		}

		// Load also category (prefer the URI variable if available).
		if ($catid && $catid != $this->topic->category_id)
		{
			$this->category = \Kunena\Forum\Libraries\Forum\Category\Helper::get($catid);
			$this->category->tryAuthorise();
		}
		else
		{
			$this->category = $this->topic->getCategory();
		}

		// Access check.
		$this->message->tryAuthorise();

		// Check if we need to redirect (category or topic mismatch, or resolve permanent URL).
		if ($this->primary)
		{
			$channels = $this->category->getChannels();

			if ($this->message->thread != $this->topic->id
				|| ($this->topic->category_id != $this->category->id && !isset($channels[$this->topic->category_id]))
			)
			{
				$this->app->redirect($this->message->getUrl(null, false));
			}
		}

		// Load messages from the current page and set the pagination.
		$hold   = Access::getInstance()->getAllowedHold($this->me, $this->category->id, false);
		$finder = new \Kunena\Forum\Libraries\Forum\Message\Finder;
		$finder
			->where('thread', '=', $this->topic->id)
			->filterByHold($hold);

		$start            = $mesid ? $this->topic->getPostLocation($mesid) : $start;
		$this->pagination = new Pagination($finder->count(), $start, $limit);

		$this->messages = $finder
			->order('time', $this->me->getMessageOrdering() == 'asc' ? 1 : -1)
			->start($this->pagination->limitstart)
			->limit($this->pagination->limit)
			->find();

		$this->prepareMessages($mesid);
		$doc = Factory::getApplication()->getDocument();

		if ($this->me->exists())
		{
			$pmFinder = new Finder;
			$pmFinder->filterByMessageIds(array_keys($this->messages))->order('id');

			if (!$this->me->isModerator($this->category))
			{
				$pmFinder->filterByUser($this->me);
			}

			$pms = $pmFinder->find();

			foreach ($pms as $pm)
			{
				$registry = new Registry($pm->params);
				$posts    = $registry->get('receivers.posts');

				foreach ($posts as $post)
				{
					if (!isset($this->messages[$post]->pm))
					{
						$this->messages[$post]->pm = [];
					}
				}

				$this->messages[$post]->pm[$pm->id] = $pm;
			}
		}

		if ($this->topic->unread)
		{
			$doc->setMetaData('robots', 'noindex, follow');
		}

		if (!$start)
		{
			foreach ($doc->_links as $key => $value)
			{
				if (is_array($value))
				{
					if (array_key_exists('relation', $value))
					{
						if ($value['relation'] == 'canonical')
						{
							$canonicalUrl               = $this->topic->getUrl();
							$doc->_links[$canonicalUrl] = $value;
							unset($doc->_links[$key]);
							break;
						}
					}
				}
			}
		}

		// Run events.
		$params = new Registry;
		$params->set('ksource', 'kunena');
		$params->set('kunena_view', 'topic');
		$params->set('kunena_layout', 'default');

		PluginHelper::importPlugin('kunena');
		Parser::prepareContent($content, 'topic_top');
		Factory::getApplication()->triggerEvent('onKunenaPrepare', ['kunena.topic', &$this->topic, &$params, 0]);
		Factory::getApplication()->triggerEvent('onKunenaPrepare', ['kunena.messages', &$this->messages, &$params, 0]);

		// Get user data, captcha & quick reply.
		$this->userTopic  = $this->topic->getUserTopic();
		$this->quickReply = $this->topic->isAuthorised('reply') && $this->me->exists() && $this->config->quickreply;

		$this->headerText = Parser::parseBBCode($this->topic->displayField('subject'));

		$data                           = new CMSObject;
		$data->{'@context'}             = "http://schema.org";
		$data->{'@type'}                = "DiscussionForumPosting";
		$data->{'id'}                   = Uri::getInstance()->toString(['scheme', 'host', 'port']) . $this->topic->getPermaUrl();
		$data->{'discussionUrl'}        = $this->topic->getPermaUrl();
		$data->{'headline'}             = $this->headerText;
		$data->{'image'}                = $this->docImage();
		$data->{'datePublished'}        = $this->topic->getFirstPostTime()->toISO8601();
		$data->{'dateModified'}         = Factory::getDate($this->message->modified_time)->toISO8601();
		$data->author                   = [];
		$tmp                            = new CMSObject;
		$tmp->{'@type'}                 = "Person";
		$tmp->{'name'}                  = $this->topic->getLastPostAuthor()->username;
		$data->author                   = $tmp;
		$data->interactionStatistic     = [];
		$tmp2                           = new CMSObject;
		$tmp2->{'@type'}                = "InteractionCounter";
		$tmp2->{'interactionType'}      = "InteractionCounter";
		$tmp2->{'userInteractionCount'} = $this->topic->getReplies();
		$data->interactionStatistic     = $tmp2;
		$tmp3                           = new CMSObject;
		$tmp3->{'@type'}                = "ImageObject";
		$tmp3->{'url'}                  = $this->docImage();
		$data->publisher                = [];
		$tmp4                           = new CMSObject;
		$tmp4->{'@type'}                = "Organization";
		$tmp4->{'name'}                 = $this->config->board_title;
		$tmp4->{'logo'}                 = $tmp3;
		$data->publisher                = $tmp4;
		$data->mainEntityOfPage         = [];
		$tmp5                           = new CMSObject;
		$tmp5->{'@type'}                = "WebPage";
		$tmp5->{'name'}                 = Uri::getInstance()->toString(['scheme', 'host', 'port']) . $this->topic->getPermaUrl();
		$data->mainEntityOfPage         = $tmp5;

		if ($this->category->allow_ratings && $this->config->ratingenabled && \Kunena\Forum\Libraries\Forum\Topic\Rate\Helper::getCount($this->topic->id) > 0)
		{
			$data->aggregateRating  = [];
			$tmp3                   = new CMSObject;
			$tmp3->{'@type'}        = "AggregateRating";
			$tmp3->{'itemReviewed'} = $this->headerText;
			$tmp3->{'ratingValue'}  = \Kunena\Forum\Libraries\Forum\Topic\Rate\Helper::getSelected($this->topic->id) > 0 ? \Kunena\Forum\Libraries\Forum\Topic\Rate\Helper::getSelected($this->topic->id) : 5;
			$tmp3->{'reviewCount'}  = \Kunena\Forum\Libraries\Forum\Topic\Rate\Helper::getCount($this->topic->id);
			$data->aggregateRating  = $tmp3;
		}

		Template::getInstance()->addScriptDeclaration(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), 'application/ld+json');
	}

	/**
	 * Prepare messages for display.
	 *
	 * @param   int  $mesid  Selected message Id.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function prepareMessages($mesid)
	{
		// Get thank yous for all messages in the page
		$thankyous = \Kunena\Forum\Libraries\Forum\Message\Thankyou\Helper::getByMessage($this->messages);

		// First collect ids and users.
		$threaded       = ($this->layout == 'indented' || $this->layout == 'threaded');
		$userlist       = [];
		$this->threaded = [];
		$location       = $this->pagination->limitstart;

		foreach ($this->messages as $message)
		{
			$message->replynum = ++$location;

			if ($threaded)
			{
				// Threaded ordering
				if (isset($this->messages[$message->parent]))
				{
					$this->threaded[$message->parent][] = $message->id;
				}
				else
				{
					$this->threaded[0][] = $message->id;
				}
			}

			$userlist[(int) $message->userid]      = (int) $message->userid;
			$userlist[(int) $message->modified_by] = (int) $message->modified_by;

			$thankyou_list     = $thankyous[$message->id]->getList();
			$message->thankyou = [];

			if (!empty($thankyou_list))
			{
				$message->thankyou = $thankyou_list;
			}
		}

		if (!isset($this->messages[$mesid]) && !empty($this->messages))
		{
			$this->message = reset($this->messages);
		}

		if ($threaded)
		{
			if (!isset($this->messages[$this->topic->first_post_id]))
			{
				$this->messages = $this->getThreadedOrdering(0, ['edge']);
			}
			else
			{
				$this->messages = $this->getThreadedOrdering();
			}
		}

		// Prefetch all users/avatars to avoid user by user queries during template iterations
		\Kunena\Forum\Libraries\User\Helper::loadUsers($userlist);

		// Prefetch attachments.
		Helper::getByMessage($this->messages);
	}

	/**
	 * Change ordering of the displayed messages and apply threading.
	 *
	 * @param   int    $parent  Parent Id.
	 * @param   array  $indent  Indent for the current object.
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function getThreadedOrdering($parent = 0, $indent = [])
	{
		$list = [];

		if (count($indent) == 1 && $this->topic->getTotal() > $this->pagination->limitstart + $this->pagination->limit)
		{
			$last = -1;
		}
		else
		{
			$last = end($this->threaded[$parent]);
		}

		foreach ($this->threaded[$parent] as $mesid)
		{
			$message = $this->messages[$mesid];
			$skip    = $message->id != $this->topic->first_post_id
				&& $message->parent != $this->topic->first_post_id && !isset($this->messages[$message->parent]);

			if ($mesid != $last)
			{
				// Default sibling edge
				$indent[] = 'crossedge';
			}
			else
			{
				// Last sibling edge
				$indent[] = 'lastedge';
			}

			end($indent);
			$key = key($indent);

			if ($skip)
			{
				$indent[] = 'gap';
			}

			$list[$mesid]         = $this->messages[$mesid];
			$list[$mesid]->indent = $indent;

			if (empty($this->threaded[$mesid]))
			{
				// No children node
				// FIXME: $mesid == $message->thread
				$list[$mesid]->indent[] = ($mesid == $message->thread) ? 'single' : 'leaf';
			}
			else
			{
				// Has children node
				// FIXME: $mesid == $message->thread
				$list[$mesid]->indent[] = ($mesid == $message->thread) ? 'root' : 'node';
			}

			if (!empty($this->threaded[$mesid]))
			{
				// Fix edges
				if ($mesid != $last)
				{
					$indent[$key] = 'edge';
				}
				else
				{
					$indent[$key] = 'empty';
				}

				if ($skip)
				{
					$indent[$key + 1] = 'empty';
				}

				$list += $this->getThreadedOrdering($mesid, $indent);
			}

			if ($skip)
			{
				array_pop($indent);
			}

			array_pop($indent);
		}

		return $list;
	}

	/**
	 * After render update topic data for the user.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	protected function after()
	{
		parent::after();

		$this->topic->hit();

		$this->topic->markRead();

		// Check if subscriptions have been sent and reset the value.
		if ($this->topic->isAuthorised('subscribe') && $this->userTopic->subscribed == 2)
		{
			$this->userTopic->subscribed = 1;
			$this->userTopic->save();
		}
	}

	/**
	 * Prepare document.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	protected function prepareDocument()
	{
		$image = '';
		$doc   = Factory::getApplication()->getDocument();
		$this->setMetaData('og:url', Uri::current(), 'property');
		$this->setMetaData('og:type', 'article', 'property');
		$this->setMetaData('og:title', $this->topic->displayField('subject'), 'property');
		$this->setMetaData('profile:username', $this->topic->getAuthor()->username, 'property');

		$image = $this->docImage();

		$message = Parser::parseText($this->topic->first_post_message);
		$matches = preg_match("/\[img]http(s?):\/\/.*\/\img]/iu", $message, $title);

		if ($matches)
		{
			$image = substr($title[0], 5, -6);
		}

		if ($this->topic->attachments > 0)
		{
			$attachments = Helper::getByMessage($this->topic->first_post_id);
			$item        = [];

			foreach ($attachments as $attach)
			{
				$object           = new stdClass;
				$object->path     = $attach->getUrl();
				$object->image    = $attach->isImage();
				$object->filename = $attach->filename;
				$object->folder   = $attach->folder;
				$item             = $object;
			}

			$attach = $item;

			if ($attach)
			{
				if (File::exists(JPATH_SITE . '/' . $attach->folder . '/' . $attach->filename))
				{
					if ($attach->image)
					{
						if ($this->config->attachment_protection)
						{
							$url      = $attach->path;
							$protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';
							$image    = $protocol . $_SERVER['SERVER_NAME'] . $url;
						}
						else
						{
							$image = $attach->path;
						}
					}
				}
			}
		}

		$first = Parser::stripBBCode($this->topic->first_post_message, 160);

		if (!$first)
		{
			$first = $this->topic->subject;
		}

		$this->setMetaData('og:description', $first, 'property');
		$this->setMetaData('og:image', $image, 'property');
		$this->setMetaData('article:published_time', $this->topic->getFirstPostTime()->toISO8601(), 'property');
		$this->setMetaData('article:section', $this->topic->getCategory()->name, 'property');
		$this->setMetaData('twitter:card', 'summary', 'name');
		$this->setMetaData('twitter:title', $this->topic->displayField('subject'), 'name');
		$this->setMetaData('twitter:image', $image, 'property');
		$this->setMetaData('twitter:description', $first);

		$config = Factory::getConfig();
		$robots = $config->get('robots');

		if ($robots == '')
		{
			$this->setMetaData('robots', 'index, follow');
		}
		elseif ($robots == 'noindex, follow')
		{
			$this->setMetaData('robots', 'noindex, follow');
		}
		elseif ($robots == 'index, nofollow')
		{
			$this->setMetaData('robots', 'index, nofollow');
		}
		else
		{
			$this->setMetaData('robots', 'nofollow, noindex');
		}

		$page       = $this->pagination->pagesCurrent;
		$total      = $this->pagination->pagesTotal;
		$headerText = $this->headerText . ($total > 1 && $page > 1 ? " - " . Text::_('COM_KUNENA_PAGES') . " {$page}" : '');

		$pagdata = $this->pagination->getData();

		if ($pagdata->previous->link)
		{
			$pagdata->previous->link = str_replace('?limitstart=0', '', $pagdata->previous->link);
			$doc->addHeadLink($pagdata->previous->link, 'prev');
		}

		if ($pagdata->next->link)
		{
			$doc->addHeadLink($pagdata->next->link, 'next');
		}

		if ($page > 1)
		{
			foreach ($doc->_links as $key => $value)
			{
				if (is_array($value))
				{
					if (array_key_exists('relation', $value))
					{
						if ($value['relation'] == 'canonical')
						{
							$canonicalUrl               = KunenaRoute::_();
							$doc->_links[$canonicalUrl] = $value;
							unset($doc->_links[$key]);
							break;
						}
					}
				}
			}
		}

		$menu_item = $this->app->getMenu()->getActive();

		if ($menu_item)
		{
			$params          = $menu_item->getParams();
			$params_keywords = $params->get('menu-meta_keywords');
			$headerText      = Parser::stripBBCode($this->topic->subject, 0, true);
			$this->setTitle($headerText);

			if (!empty($params_keywords))
			{
				$keywords = $params->get('menu-meta_keywords');
				$this->setKeywords($keywords);
				$this->setMetaData('article:tag', $keywords, 'property');
			}
			else
			{
				$this->setKeywords($headerText);
			}

			if ($total > 1 && $page > 1)
			{
				$small = Parser::stripBBCode($this->topic->first_post_message, 130);

				if (empty($small))
				{
					$small = $headerText;
				}

				$this->setDescription($small . " - " . Text::_('COM_KUNENA_PAGES') . " {$page}");
			}
			else
			{
				$small = Parser::stripBBCode($this->topic->first_post_message, 160);

				if (empty($small))
				{
					$small = $headerText;
				}

				$this->setDescription($small);
			}
		}
	}

	/**
	 * Prepare document.
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	protected function docImage()
	{
		if (File::exists(JPATH_SITE . '/media/kunena/avatars/' . KunenaFactory::getUser($this->topic->getAuthor()->id)->avatar))
		{
			$image = Uri::root() . 'media/kunena/avatars/' . KunenaFactory::getUser($this->topic->getAuthor()->id)->avatar;
		}
		elseif ($this->topic->getAuthor()->avatar == null)
		{
			if (File::exists(JPATH_SITE . '/' . $this->config->emailheader))
			{
				$image = Uri::base() . $this->config->emailheader;
			}
			else
			{
				$image = Uri::base() . '/media/kunena/email/hero-wide.png';
			}
		}
		else
		{
			$image = $this->topic->getAuthor()->getAvatarURL('Profile', '200');
		}

		return $image;
	}
}
