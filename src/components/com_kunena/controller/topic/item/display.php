<?php
/**
 * Kunena Component
 * @package         Kunena.Site
 * @subpackage      Controller.Topic
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Object\CMSObject;

/**
 * Class ComponentKunenaControllerTopicItemDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerTopicItemDisplay extends KunenaControllerDisplay
{
	/**
	 * @var string
	 * @since Kunena
	 */
	protected $name = 'Topic/Item';

	/**
	 * @var KunenaUser
	 * @since Kunena
	 */
	public $me;

	/**
	 * @var KunenaForumCategory
	 * @since Kunena
	 */
	public $category;

	/**
	 * @var KunenaForumTopic
	 * @since Kunena
	 */
	public $topic;

	/**
	 * @var KunenaPagination
	 * @since Kunena
	 */
	public $pagination;

	/**
	 * @var string
	 * @since Kunena
	 */
	public $headerText;

	/**
	 * Prepare topic display.
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @throws null
	 * @since Kunena
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

		if (!$Itemid && $format != 'feed' && KunenaConfig::getInstance()->sef_redirect)
		{
			$itemid     = KunenaRoute::fixMissingItemID();
			$controller = BaseController::getInstance("kunena");
			$controller->setRedirect(KunenaRoute::_("index.php?option=com_kunena&view=topic&catid={$catid}&id={$id}&Itemid={$itemid}", false));
			$controller->redirect();
		}

		if ($limit < 1 || $limit > 100)
		{
			$limit = $this->config->messages_per_page;
		}

		$this->me = KunenaUserHelper::getMyself();

		$allowed = md5(serialize(KunenaAccess::getInstance()->getAllowedCategories()));
		$cache   = Factory::getCache('com_kunena', 'output');

		/*
		if ($cache->start("{$this->ktemplate->name}.common.jump.{$allowed}", 'com_kunena.template'))
		 {
		 return;
		 }*/

		$options            = array();
		$options []         = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FORUM_TOP'));
		$cat_params         = array('sections' => 1, 'catid' => 0);
		$this->categorylist = HTMLHelper::_('kunenaforum.categorylist', 'catid', 0, $options, $cat_params, 'class="inputbox fbs" size="1" onchange = "this.form.submit()"', 'value', 'text');

		// Load topic and message.
		if ($mesid)
		{
			// If message was set, use it to find the current topic.
			$this->message = KunenaForumMessageHelper::get($mesid);
			$this->topic   = $this->message->getTopic();
		}
		else
		{
			// Note that redirect loops throw RuntimeException because of we added KunenaForumTopic::getTopic() call!
			$this->topic   = KunenaForumTopicHelper::get($id)->getTopic();
			$this->message = KunenaForumMessageHelper::get($this->topic->first_post_id);
		}

		// Load also category (prefer the URI variable if available).
		if ($catid && $catid != $this->topic->category_id)
		{
			$this->category = KunenaForumCategoryHelper::get($catid);
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
		$hold   = KunenaAccess::getInstance()->getAllowedHold($this->me, $this->category->id, false);
		$finder = new KunenaForumMessageFinder;
		$finder
			->where('thread', '=', $this->topic->id)
			->filterByHold($hold);

		$start            = $mesid ? $this->topic->getPostLocation($mesid) : $start;
		$this->pagination = new KunenaPagination($finder->count(), $start, $limit);

		$this->messages = $finder
			->order('time', $this->me->getMessageOrdering() == 'asc' ? 1 : -1)
			->start($this->pagination->limitstart)
			->limit($this->pagination->limit)
			->find();

		$this->prepareMessages($mesid);
		$doc = Factory::getDocument();

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
		$params = new \Joomla\Registry\Registry;
		$params->set('ksource', 'kunena');
		$params->set('kunena_view', 'topic');
		$params->set('kunena_layout', 'default');

		\Joomla\CMS\Plugin\PluginHelper::importPlugin('kunena');
		KunenaHtmlParser::prepareContent($content, 'topic_top');
		Factory::getApplication()->triggerEvent('onKunenaPrepare', array('kunena.topic', &$this->topic, &$params, 0));
		Factory::getApplication()->triggerEvent('onKunenaPrepare', array('kunena.messages', &$this->messages, &$params, 0));

		// Get user data, captcha & quick reply.
		$this->userTopic  = $this->topic->getUserTopic();
		$this->quickReply = $this->topic->isAuthorised('reply') && $this->me->exists() && KunenaConfig::getInstance()->quickreply;

		$subject = KunenaHtmlParser::parseText($this->topic->displayField('subject'));

		$data                           = new CMSObject;
		$data->{'@context'}             = "https://schema.org";
		$data->{'@type'}                = "DiscussionForumPosting";
		$data->{'id'}                   = Joomla\CMS\Uri\Uri::getInstance()->toString(array('scheme', 'host', 'port')) . $this->topic->getPermaUrl();
		$data->{'discussionUrl'}        = $this->topic->getPermaUrl();
		$data->{'headline'}             = $subject;
		$data->{'image'}                = $this->docImage();
		$data->{'datePublished'}        = $this->topic->getFirstPostTime()->toISO8601();
		$data->{'dateModified'}         = Factory::getDate($this->message->modified_time)->toISO8601();
		$data->{'author'}               = array();
		$tmp                            = new CMSObject;
		$tmp->{'@type'}                 = "Person";
		$tmp->{'name'}                  = $this->topic->getLastPostAuthor()->username;
		$data->{'author'}               = $tmp;
		$data->interactionStatistic     = array();
		$tmp2                           = new CMSObject;
		$tmp2->{'@type'}                = "InteractionCounter";
		$tmp2->{'interactionType'}      = "InteractionCounter";
		$tmp2->{'userInteractionCount'} = $this->topic->getReplies();
		$data->interactionStatistic     = $tmp2;
		$tmp3                           = new CMSObject;
		$tmp3->{'@type'}                = "ImageObject";
		$tmp3->{'url'}                  = $this->docImage();
		$data->publisher                = array();
		$tmp4                           = new CMSObject;
		$tmp4->{'@type'}                = "Organization";
		$tmp4->{'name'}                 = $this->config->board_title;
		$tmp4->{'logo'}                 = $tmp3;
		$data->publisher                = $tmp4;
		$data->mainEntityOfPage         = array();
		$tmp5                           = new CMSObject;
		$tmp5->{'@type'}                = "WebPage";
		$tmp5->{'name'}                 = Joomla\CMS\Uri\Uri::getInstance()->toString(array('scheme', 'host', 'port')) . $this->topic->getPermaUrl();
		$data->mainEntityOfPage         = $tmp5;

		if ($this->category->allow_ratings && $this->config->ratingenabled && KunenaForumTopicRateHelper::getCount($this->topic->id) > 0)
		{
			$data->aggregateRating  = array();
			$tmp3                   = new CMSObject;
			$tmp3->{'@type'}        = "AggregateRating";
			$tmp3->{'itemReviewed'} = $this->headerText;
			$tmp3->{'ratingValue'}  = KunenaForumTopicRateHelper::getSelected($this->topic->id) > 0 ? KunenaForumTopicRateHelper::getSelected($this->topic->id) : 5;
			$tmp3->{'reviewCount'}  = KunenaForumTopicRateHelper::getCount($this->topic->id);
			$data->aggregateRating  = $tmp3;
		}

		KunenaTemplate::getInstance()->addScriptDeclaration(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), 'application/ld+json');
	}

	/**
	 * Prepare messages for display.
	 *
	 * @param   int  $mesid  Selected message Id.
	 *
	 * @return  void
	 * @throws Exception
	 * @since Kunena
	 */
	protected function prepareMessages($mesid)
	{
		// Get thank yous for all messages in the page
		$thankyous = KunenaForumMessageThankyouHelper::getByMessage($this->messages);

		// First collect ids and users.
		$threaded       = ($this->layout == 'indented' || $this->layout == 'threaded');
		$userlist       = array();
		$this->threaded = array();
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
			$message->thankyou = array();

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
				$this->messages = $this->getThreadedOrdering(0, array('edge'));
			}
			else
			{
				$this->messages = $this->getThreadedOrdering();
			}
		}

		// Prefetch all users/avatars to avoid user by user queries during template iterations
		KunenaUserHelper::loadUsers($userlist);

		// Prefetch attachments.
		KunenaAttachmentHelper::getByMessage($this->messages);
	}

	/**
	 * Change ordering of the displayed messages and apply threading.
	 *
	 * @param   int    $parent  Parent Id.
	 * @param   array  $indent  Indent for the current object.
	 *
	 * @return  array
	 * @throws Exception
	 * @since Kunena
	 */
	protected function getThreadedOrdering($parent = 0, $indent = array())
	{
		$list = array();

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
	 * @return void
	 * @throws Exception
	 * @throws null
	 * @since Kunena
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
	 * @return void
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 */
	protected function prepareDocument()
	{
		$image = '';
		$doc   = Factory::getDocument();
		$this->setMetaData('og:url', Uri::current(), 'property');
		$this->setMetaData('og:type', 'article', 'property');
		$this->setMetaData('og:title', $this->topic->displayField('subject'), 'property');
		$this->setMetaData('profile:username', $this->topic->getAuthor()->username, 'property');

		$image = $this->docImage();

		$message = KunenaHtmlParser::parseText($this->topic->first_post_message);
		$matches = preg_match("/\[img]http(s?):\/\/.*\/img]/iu", $message, $title);

		if ($matches)
		{
			$image = substr($title[0], 5, -6);
		}

		if ($this->topic->attachments > 0)
		{
			$attachments = KunenaAttachmentHelper::getByMessage($this->topic->first_post_id);
			$item        = array();

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
				if (JFile::exists(JPATH_SITE . '/' . $attach->folder . '/' . $attach->filename))
				{
					if ($attach->image)
					{
						if (KunenaConfig::getInstance()->attachment_protection)
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

		$first = KunenaHtmlParser::stripBBCode($this->topic->first_post_message, 160);

		if (!$first)
		{
			$first = $this->topic->subject;
		}

		$multispaces_replaced = preg_replace('/\s+/', ' ', $first);

		$this->setMetaData('og:description', $multispaces_replaced, 'property');
		$this->setMetaData('og:image', $image, 'property');
		$this->setMetaData('article:published_time', $this->topic->getFirstPostTime()->toISO8601(), 'property');
		$this->setMetaData('article:section', $this->topic->getCategory()->name, 'property');
		$this->setMetaData('twitter:card', 'summary', 'name');
		$this->setMetaData('twitter:title', $this->topic->displayField('subject'), 'name');
		$this->setMetaData('twitter:image', $image, 'property');
		$this->setMetaData('twitter:description', $multispaces_replaced);

		$config = Factory::getConfig();
		$robots = $config->get('robots');

		if ($robots == 'noindex, follow')
		{
			$this->setMetaData('robots', 'noindex, follow');
		}
		elseif ($robots == 'index, nofollow')
		{
			$this->setMetaData('robots', 'index, nofollow');
		}
		elseif ($robots == 'noindex, nofollow')
		{
			$this->setMetaData('robots', 'noindex, nofollow');
		}
		else
		{
			$this->setMetaData('robots', 'index, follow');
		}

		$page       = (int) $this->pagination->pagesCurrent;
		$total      = (int) $this->pagination->pagesTotal;
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

		$app       = Factory::getApplication();
		$menu_item = $app->getMenu()->getActive();

		if ($menu_item)
		{
			$params          = $menu_item->params;
			$params_keywords = $params->get('menu-meta_keywords');
			$subject         = $this->topic->subject . $headerText;

			$this->setTitle($subject);

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

			$multispaces_replaced_desc = preg_replace('/\s+/', ' ', $this->topic->first_post_message);

			if ($total > 1 && $page > 1)
			{
				$small = KunenaHtmlParser::stripBBCode($multispaces_replaced_desc, 130);

				if (empty($small))
				{
					$small = $headerText;
				}

				$this->setDescription($small . " - " . Text::_('COM_KUNENA_PAGES') . " {$page}");
			}
			else
			{
				$small = KunenaHtmlParser::stripBBCode($multispaces_replaced_desc, 160);

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
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 */
	protected function docImage()
	{
		$image = '';

		if (JFile::exists(JPATH_SITE . '/media/kunena/avatars/' . KunenaFactory::getUser($this->topic->getAuthor()->id)->avatar))
		{
			$image = Uri::root() . 'media/kunena/avatars/' . KunenaFactory::getUser($this->topic->getAuthor()->id)->avatar;
		}
		elseif ($this->topic->getAuthor()->avatar == null)
		{
			if (JFile::exists(JPATH_SITE . '/' . KunenaConfig::getInstance()->emailheader))
			{
				$image = Uri::base() . KunenaConfig::getInstance()->emailheader;
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
