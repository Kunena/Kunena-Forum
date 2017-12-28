<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Site
 * @subpackage  Controllers
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Kunena Topic Controller
 *
 * @since  2.0
 */
class KunenaControllerTopic extends KunenaController
{
	/**
	 * @param   array $config
	 *
	 * @throws Exception
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->catid  = JFactory::getApplication()->input->getInt('catid', 0);
		$this->return = JFactory::getApplication()->input->getInt('return', $this->catid);
		$this->id     = JFactory::getApplication()->input->getInt('id', 0);
		$this->mesid  = JFactory::getApplication()->input->getInt('mesid', 0);
	}

	/**
	 * Get attachments attached to a message with AJAX.
	 *
	 * @throws RuntimeException
	 *
	 * @return string
	 */
	public function loadattachments()
	{
		// Only support JSON requests.
		if ($this->input->getWord('format', 'html') != 'json')
		{
			throw new RuntimeException(JText::_('Bad Request'), 400);
		}

		if (!JSession::checkToken('request'))
		{
			throw new RuntimeException(JText::_('Forbidden'), 403);
		}

		$mes_id      = $this->input->getInt('mes_id', 0);
		$attachments = KunenaAttachmentHelper::getByMessage($mes_id);
		$list        = array();

		foreach ($attachments as $attach)
		{
			$object          = new stdClass;
			$object->id      = $attach->id;
			$object->size    = round($attach->size / '1024', 0);
			$object->name    = $attach->filename;
			$object->folder  = $attach->folder;
			$object->caption = $attach->caption;
			$object->type    = $attach->filetype;
			$object->path    = $attach->getUrl();
			$object->image   = $attach->isImage();
			$list['files'][] = $object;
		}

		header('Content-type: application/json');
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");

		while (@ob_end_clean())
		{
		}

		echo json_encode($list);

		jexit();
	}

	/**
	 * Remove files with AJAX.
	 *
	 * @throws RuntimeException
	 *
	 * @return string
	 */
	public function removeattachments()
	{
		// Only support JSON requests.
		if ($this->input->getWord('format', 'html') != 'json')
		{
			throw new RuntimeException(JText::_('Bad Request'), 400);
		}

		if (!JSession::checkToken('request'))
		{
			throw new RuntimeException(JText::_('Forbidden'), 403);
		}

		$attach_id         = $this->input->getInt('file_id', 0);
		$success           = array();
		$instance          = KunenaAttachmentHelper::get($attach_id);
		$success['result'] = $instance->delete();
		unset($instance);
		header('Content-type: application/json');
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");

		while (@ob_end_clean())
		{
		}

		echo json_encode($success);

		jexit();
	}

	/**
	 * Upload files with AJAX.
	 *
	 * @throws RuntimeException
	 */
	public function upload()
	{
		// Only support JSON requests.
		if ($this->input->getWord('format', 'html') != 'json')
		{
			throw new RuntimeException(JText::_('Bad Request'), 400);
		}

		$upload = KunenaUpload::getInstance();

		// We are converting all exceptions into JSON.
		try
		{
			if (!JSession::checkToken('request'))
			{
				throw new RuntimeException(JText::_('Forbidden'), 403);
			}

			$me    = KunenaUserHelper::getMyself();
			$catid = $this->input->getInt('catid', 0);
			$mesid = $this->input->getInt('mesid', 0);

			if ($mesid)
			{
				$message = KunenaForumMessageHelper::get($mesid);
				$message->tryAuthorise('attachment.create');
				$category = $message->getCategory();
			}
			else
			{
				$category = KunenaForumCategoryHelper::get($catid);

				// TODO: Some room for improvements in here... (maybe ask user to pick up category first)
				if ($category->id)
				{
					if (stripos($this->input->getString('mime'), 'image/') !== false)
					{
						$category->tryAuthorise('topic.post.attachment.createimage');
					}
					else
					{
						$category->tryAuthorise('topic.post.attachment.createfile');
					}
				}
			}

			$caption = $this->input->getString('caption');
			$options = array(
				'filename'   => $this->input->getString('filename'),
				'size'       => $this->input->getInt('size'),
				'mime'       => $this->input->getString('mime'),
				'hash'       => $this->input->getString('hash'),
				'chunkStart' => $this->input->getInt('chunkStart', 0),
				'chunkEnd'   => $this->input->getInt('chunkEnd', 0),
			);

			// Upload!
			$upload->addExtensions(KunenaAttachmentHelper::getExtensions($category->id, $me->userid));
			$response = (object) $upload->ajaxUpload($options);

			if (!empty($response->completed))
			{
				// We have it all, lets create the attachment.
				$uploadFile = $upload->getProtectedFile();
				list($basename, $extension) = $upload->splitFilename();
				$attachment = new KunenaAttachment;
				$attachment->bind(
					array(
						'mesid'         => 0,
						'userid'        => (int) $me->userid,
						'protected'     => null,
						'hash'          => $response->hash,
						'size'          => $response->size,
						'folder'        => null,
						'filetype'      => $response->mime,
						'filename'      => null,
						'filename_real' => $response->filename,
						'caption'       => $caption,
					)
				);

				// Resize image if needed.
				if ($attachment->isImage())
				{
					$imageInfo = KunenaImage::getImageFileProperties($uploadFile);
					$config    = KunenaConfig::getInstance();

					if ($imageInfo->width > $config->imagewidth || $imageInfo->height > $config->imageheight)
					{
						// Calculate quality for both JPG and PNG.
						$quality = $config->imagequality;

						if ($quality < 1 || $quality > 100)
						{
							$quality = 70;
						}

						if ($imageInfo->type == IMAGETYPE_PNG)
						{
							$quality = intval(($quality - 1) / 10);
						}

						$image = new KunenaImage($uploadFile);
						$image = $image->resize($config->imagewidth, $config->imageheight, false);

						$options = array('quality' => $quality);
						$image->toFile($uploadFile, $imageInfo->type, $options);

						unset($image);

						$attachment->hash = md5_file($uploadFile);
						$attachment->size = filesize($uploadFile);
					}
				}

				$attachment->saveFile($uploadFile, $basename, $extension, true);

				// Set id and override response variables just in case if attachment was modified.
				$response->id       = $attachment->id;
				$response->hash     = $attachment->hash;
				$response->size     = $attachment->size;
				$response->mime     = $attachment->filetype;
				$response->filename = $attachment->filename_real;
			}
		}

		catch (Exception $response)
		{
			$upload->cleanup();

			// Use the exception as the response.
		}

		header('Content-type: application/json');
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");

		while (@ob_end_clean())
		{
		}

		echo $upload->ajaxResponse($response);

		jexit();
	}

	/**
	 * @throws Exception
	 */
	public function post()
	{
		$this->id = JFactory::getApplication()->input->getInt('parentid', 0);
		$fields   = array(
			'catid'             => $this->catid,
			'name'              => JFactory::getApplication()->input->getString('authorname', $this->me->getName()),
			'email'             => JFactory::getApplication()->input->getString('email', null),
			'subject'           => JFactory::getApplication()->input->post->get('subject', '', 'raw'),
			'message'           => JFactory::getApplication()->input->post->get('message', '', 'raw'),
			'icon_id'           => JFactory::getApplication()->input->getInt('topic_emoticon', null),
			'anonymous'         => JFactory::getApplication()->input->getInt('anonymous', 0),
			'poll_title'        => JFactory::getApplication()->input->getString('poll_title', ''),
			'poll_options'      => JFactory::getApplication()->input->get('polloptionsID', array(), 'post', 'array'),
			'poll_time_to_live' => JFactory::getApplication()->input->getString('poll_time_to_live', 0),
			'subscribe'         => JFactory::getApplication()->input->getInt('subscribeMe', 0)
		);

		$this->app->setUserState('com_kunena.postfields', $fields);

		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		if (!$this->id)
		{
			// Create topic
			$category = KunenaForumCategoryHelper::get($this->catid);

			if (!$category->authorise('topic.create'))
			{
				$this->app->enqueueMessage($category->getError(), 'notice');
				$this->setRedirectBack();

				return;
			}

			list ($topic, $message) = $category->newTopic($fields);
		}
		else
		{
			// Reply topic
			$parent = KunenaForumMessageHelper::get($this->id);

			if (!$parent->authorise('reply'))
			{
				$this->app->enqueueMessage($parent->getError(), 'notice');
				$this->setRedirectBack();

				return;
			}

			list ($topic, $message) = $parent->newReply($fields);
			$category = $topic->getCategory();
		}

		if ($this->me->canDoCaptcha())
		{
			if (JPluginHelper::isEnabled('captcha'))
			{
				$plugin = JPluginHelper::getPlugin('captcha');
				$params = new JRegistry($plugin[0]->params);

				$captcha_pubkey = $params->get('public_key');
				$catcha_privkey = $params->get('private_key');

				if (!empty($captcha_pubkey) && !empty($catcha_privkey))
				{
					JPluginHelper::importPlugin('captcha');
					$dispatcher = JEventDispatcher::getInstance();

					$captcha_response = $this->app->input->getString('g-recaptcha-response');

					if (!empty($captcha_response))
					{
						// For ReCaptcha API 2.0
						$res = $dispatcher->trigger('onCheckAnswer', $this->app->input->getString('g-recaptcha-response'));
					}
					else
					{
						// For ReCaptcha API 1.0
						$res = $dispatcher->trigger('onCheckAnswer', $this->app->input->getString('recaptcha_response_field'));
					}

					if (!$res[0])
					{
						$this->setRedirectBack();

						return;
					}
				}
			}
		}

		$isNew = !$topic->exists();

		// Redirect to full reply instead.
		if (JFactory::getApplication()->input->getString('fullreply'))
		{
			$this->setRedirect(KunenaRoute::_("index.php?option=com_kunena&view=topic&layout=reply&catid={$fields->catid}&id={$parent->getTopic()->id}&mesid={$parent->id}", false));

			return;
		}

		// Flood protection
		if ($this->config->floodprotection && !$this->me->isModerator($category) && $isNew)
		{
			$timelimit = JFactory::getDate()->toUnix() - $this->config->floodprotection;
			$ip        = $_SERVER ["REMOTE_ADDR"];

			$db = JFactory::getDBO();
			$db->setQuery("SELECT COUNT(*) FROM #__kunena_messages WHERE ip={$db->Quote($ip)} AND time>{$db->quote($timelimit)}");

			try
			{
				$count = $db->loadResult();
			}
			catch(JDatabaseExceptionExecuting $e)
			{
				KunenaError::displayDatabaseError($e);
			}

			if ($count)
			{
				$this->app->enqueueMessage(JText::sprintf('COM_KUNENA_POST_TOPIC_FLOOD', $this->config->floodprotection), 'error');
				$this->setRedirectBack();

				return;
			}
		}

		// Set topic icon if permitted
		if ($this->config->topicicons && isset($fields['icon_id']) && $topic->authorise('edit', null, false))
		{
			$topic->icon_id = $fields['icon_id'];
		}

		// Remove IP address
		// TODO: Add administrator tool to remove all tracked IP addresses (from the database)
		if (!$this->config->iptracking)
		{
			$message->ip = '';
		}

		// If requested: Make message to be anonymous
		if ($fields['anonymous'] && $message->getCategory()->allow_anonymous)
		{
			$message->makeAnonymous();
		}

		// If configured: Hold posts from guests
		if (!$this->me->userid && $this->config->hold_guest_posts)
		{
			$message->hold = 1;
		}

		// If configured: Hold posts from users
		if ($this->me->userid && !$this->me->isModerator($category) && $this->me->posts < $this->config->hold_newusers_posts)
		{
			$message->hold = 1;
		}

		// Prevent user abort from this point in order to maintain data integrity.
		@ignore_user_abort(true);

		// Mark attachments to be added or deleted.
		$attachments = JFactory::getApplication()->input->get('attachments', array(), 'post', 'array');
		$attachment  = JFactory::getApplication()->input->get('attachment', array(), 'post', 'array');
		$message->addAttachments(array_keys(array_intersect_key($attachments, $attachment)));
		$message->removeAttachments(array_keys(array_diff_key($attachments, $attachment)));

		// Upload new attachments
		foreach ($_FILES as $key => $file)
		{
			$intkey = 0;

			if (preg_match('/\D*(\d+)/', $key, $matches))
			{
				$intkey = (int) $matches[1];
			}

			if ($file['error'] != UPLOAD_ERR_NO_FILE)
			{
				$message->uploadAttachment($intkey, $key, $this->catid);
			}
		}

		$url_subject = $this->checkURLInSubject($message->subject);

		if ($url_subject && $this->config->url_subject_topic)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_TOPIC_MESSAGES_ERROR_URL_IN_SUBJECT'), 'error');
			$this->setRedirectBack();

			return;
		}

		// Make sure that message has visible content (text, images or objects) to be shown.
		$text = KunenaHtmlParser::parseBBCode($message->message);

		if (!preg_match('!(<img |<object |<iframe )!', $text))
		{
			$text = trim(JFilterOutput::cleanText($text));
		}

		if (!$text)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_LIB_TABLE_MESSAGES_ERROR_NO_MESSAGE'), 'error');
			$this->setRedirectBack();

			return;
		}
		$maxlinks = $this->checkMaxLinks($text, $topic);

		if (!$maxlinks)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_TOPIC_SPAM_LINK_PROTECTION'), 'error');
			$this->setRedirectBack();

			return;
		}

		// Activity integration
		$activity = KunenaFactory::getActivityIntegration();

		if ($message->hold == 0)
		{
			if (!$topic->exists())
			{
				$activity->onBeforePost($message);
			}
			else
			{
				$activity->onBeforeReply($message);
			}
		}

		// Save message
		$success = $message->save();

		if ($this->me->isModerator($category) && $this->config->log_moderation)
		{
			KunenaLog::log(
					KunenaLog::TYPE_ACTION,
					$isNew ? KunenaLog::LOG_TOPIC_CREATE : KunenaLog::LOG_POST_CREATE,
					array('mesid' => $message->id, 'parent_id' => $this->id),
					$category,
					$topic
					);
		}

		if (!$success)
		{
			$this->app->enqueueMessage($message->getError(), 'error');
			$this->setRedirectBack();

			return;
		}

		// Message has been sent, we can now clear saved form
		$this->app->setUserState('com_kunena.postfields', null);

		// Display possible warnings (upload failed etc)
		foreach ($message->getErrors() as $warning)
		{
			$this->app->enqueueMessage($warning, 'notice');
		}

		// Create Poll
		$poll_title   = $fields['poll_title'];
		$poll_options = $fields['poll_options'];

		if (!empty($poll_options) && !empty($poll_title))
		{
			if ($topic->authorise('poll.create', null, false))
			{
				$poll                 = $topic->getPoll();
				$poll->title          = $poll_title;

				if (!empty($fields['poll_time_to_live']))
				{
					$polltimetolive        = new JDate($fields['poll_time_to_live']);
					$poll->polltimetolive = $polltimetolive->toSql();
				}

				$poll->setOptions($poll_options);

				if (!$poll->save())
				{
					$this->app->enqueueMessage($poll->getError(), 'notice');
				}
				else
				{
					$topic->poll_id = $poll->id;
					$topic->save();
					$this->app->enqueueMessage(JText::_('COM_KUNENA_POLL_CREATED'));
				}
			}
			else
			{
				$this->app->enqueueMessage($topic->getError(), 'notice');
			}
		}

		$message->sendNotification();

		// Now try adding any new subscriptions if asked for by the poster

		$usertopic = $topic->getUserTopic();

		if ($fields['subscribe'] && !$usertopic->subscribed)
		{
			if ($topic->subscribe(1))
			{
				$this->app->enqueueMessage(JText::_('COM_KUNENA_POST_SUBSCRIBED_TOPIC'));

				// Activity integration
				$activity = KunenaFactory::getActivityIntegration();
				$activity->onAfterSubscribe($topic, 1);
			}
			else
			{
				$this->app->enqueueMessage(JText::_('COM_KUNENA_POST_NO_SUBSCRIBED_TOPIC') . ' ' . $topic->getError());
			}
		}

		if ($message->hold == 1)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_POST_SUCCES_REVIEW'));
		}
		else
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_POST_SUCCESS_POSTED'));
		}

		$category = KunenaForumCategoryHelper::get($this->return);

		if ($message->authorise('read', null, false) && $this->id)
		{
			$this->setRedirect($message->getUrl($category, false));
		}
		elseif ($topic->authorise('read', null, false))
		{
			$this->setRedirect($topic->getUrl($category, false));
		}
		else
		{
			$this->setRedirect($category->getUrl(null, false));
		}
	}

	/**
	 * @throws Exception
	 */
	public function edit()
	{
		$this->id = JFactory::getApplication()->input->getInt('mesid', 0);

		$message = KunenaForumMessageHelper::get($this->id);
		$topic   = $message->getTopic();
		$fields  = array(
			'name'              => JFactory::getApplication()->input->getString('authorname', $message->name),
			'email'             => JFactory::getApplication()->input->getString('email', $message->email),
			'subject'           => JFactory::getApplication()->input->post->get('subject', '', 'raw'),
			'message'           => JFactory::getApplication()->input->post->get('message', '', 'raw'),
			'modified_reason'   => JFactory::getApplication()->input->getString('modified_reason', $message->modified_reason),
			'icon_id'           => JFactory::getApplication()->input->getInt('topic_emoticon', $topic->icon_id),
			'anonymous'         => JFactory::getApplication()->input->getInt('anonymous', 0),
			'poll_title'        => JFactory::getApplication()->input->getString('poll_title', null),
			'poll_options'      => JFactory::getApplication()->input->get('polloptionsID', array(), 'post', 'array'),
			'poll_time_to_live' => JFactory::getApplication()->input->getString('poll_time_to_live', 0)
		);

		if (!JSession::checkToken('post'))
		{
			$this->app->setUserState('com_kunena.postfields', $fields);
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		if (!$message->authorise('edit'))
		{
			$this->app->setUserState('com_kunena.postfields', $fields);
			$this->app->enqueueMessage($message->getError(), 'notice');
			$this->setRedirectBack();

			return;
		}

		// Load language file from the template.
		KunenaFactory::getTemplate()->loadLanguage();

		// Update message contents
		$message->edit($fields);

		// If requested: Make message to be anonymous
		if ($fields['anonymous'] && $message->getCategory()->allow_anonymous)
		{
			$message->makeAnonymous();
		}

		// Prevent user abort from this point in order to maintain data integrity.
		@ignore_user_abort(true);

		// Mark attachments to be added or deleted.
		$attachments = JFactory::getApplication()->input->get('attachments', array(), 'post', 'array');
		$attachment  = JFactory::getApplication()->input->get('attachment', array(), 'post', 'array');

		$addList = array_keys(array_intersect_key($attachments, $attachment));
		Joomla\Utilities\ArrayHelper::toInteger($addList);
		$removeList = array_keys(array_diff_key($attachments, $attachment));
		Joomla\Utilities\ArrayHelper::toInteger($removeList);

		$message->addAttachments($addList);
		$message->removeAttachments($removeList);

		// Upload new attachments
		foreach ($_FILES as $key => $file)
		{
			$intkey = 0;

			if (preg_match('/\D*(\d+)/', $key, $matches))
			{
				$intkey = (int) $matches[1];
			}

			if ($file['error'] != UPLOAD_ERR_NO_FILE)
			{
				$message->uploadAttachment($intkey, $key, $this->catid);
			}
		}

		$url_subject = $this->checkURLInSubject($message->subject);

		if ($url_subject && $this->config->url_subject_topic)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_TOPIC_MESSAGES_ERROR_URL_IN_SUBJECT'), 'error');
			$this->setRedirectBack();

			return;
		}

		// Set topic icon if permitted
		if ($this->config->topicicons && isset($fields['icon_id']) && $topic->authorise('edit', null, false))
		{
			$topic->icon_id = $fields['icon_id'];
		}

		// Check if we are editing first post and update topic if we are!
		if ($topic->first_post_id == $message->id)
		{
			$topic->subject = $fields['subject'];
		}

		// If user removed all the text and message doesn't contain images or objects, delete the message instead.
		$text = KunenaHtmlParser::parseBBCode($message->message);

		if (!preg_match('!(<img |<object |<iframe )!', $text))
		{
			$text = trim(JFilterOutput::cleanText($text));
		}

		if (!$text && $this->config->userdeletetmessage == 1)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_LIB_TABLE_MESSAGES_ERROR_NO_MESSAGE'), 'error');
			return;
		}
		elseif (!$text)
		{
			// Reload message (we don't want to change it).
			$message->load();

			if ($message->publish(KunenaForum::DELETED))
			{
				$this->app->enqueueMessage(JText::_('COM_KUNENA_POST_SUCCESS_DELETE'));
			}
			else
			{
				$this->app->enqueueMessage($message->getError(), 'notice');
			}

			$this->setRedirect($message->getUrl($this->return, false));

			return;
		}

		$maxlinks = $this->checkMaxLinks($text, $topic);

		if (!$maxlinks)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_TOPIC_SPAM_LINK_PROTECTION'), 'error');
			$this->setRedirectBack();

			return;
		}

		// Activity integration
		$activity = KunenaFactory::getActivityIntegration();
		$activity->onBeforeEdit($message);

		// Save message
		$success = $message->save();

		if (!$success)
		{
			$this->app->setUserState('com_kunena.postfields', $fields);
			$this->app->enqueueMessage($message->getError(), 'error');
			$this->setRedirectBack();

			return;
		}

		$isMine = $this->me->userid == $message->userid;

		if ($this->config->log_moderation)
		{
			KunenaLog::log(
				$isMine ? KunenaLog::TYPE_ACTION : KunenaLog::TYPE_MODERATION,
				KunenaLog::LOG_POST_EDIT,
				array('mesid' => $message->id, 'reason' => $fields['modified_reason']),
				$topic->getCategory(),
				$topic,
				!$isMine ? $message->getAuthor() : null
			);
		}

		// Display possible warnings (upload failed etc)
		foreach ($message->getErrors() as $warning)
		{
			$this->app->enqueueMessage($warning, 'notice');
		}

		$poll_title = $fields['poll_title'];

		if ($poll_title !== null)
		{
			// Save changes into poll
			$poll_options = $fields['poll_options'];
			$poll         = $topic->getPoll();

			if (!empty($poll_options) && !empty($poll_title))
			{
				$poll->title           = $poll_title;

				if (!empty($fields['poll_time_to_live']))
				{
					$polltimetolive        = new JDate($fields['poll_time_to_live']);
					$poll->polltimetolive = $polltimetolive->toSql();
				}

				$poll->setOptions($poll_options);

				if (!$topic->poll_id)
				{
					// Create a new poll
					if (!$topic->authorise('poll.create'))
					{
						$this->app->enqueueMessage($topic->getError(), 'notice');
					}
					elseif (!$poll->save())
					{
						$this->app->enqueueMessage($poll->getError(), 'notice');
					}
					else
					{
						$topic->poll_id = $poll->id;
						$topic->save();
						$this->app->enqueueMessage(JText::_('COM_KUNENA_POLL_CREATED'));
					}
				}
				else
				{
					// Edit existing poll
					if (!$topic->authorise('poll.edit'))
					{
						$this->app->enqueueMessage($topic->getError(), 'notice');
					}
					elseif (!$poll->save())
					{
						$this->app->enqueueMessage($poll->getError(), 'notice');
					}
					else
					{
						$this->app->enqueueMessage(JText::_('COM_KUNENA_POLL_EDITED'));
					}
				}
			}
			elseif ($poll->exists() && $topic->authorise('poll.edit'))
			{
				// Delete poll
				if (!$topic->authorise('poll.delete'))
				{
					// Error: No permissions to delete poll
					$this->app->enqueueMessage($topic->getError(), 'notice');
				}
				elseif (!$poll->delete())
				{
					$this->app->enqueueMessage($poll->getError(), 'notice');
				}
				else
				{
					$this->app->enqueueMessage(JText::_('COM_KUNENA_POLL_DELETED'));
				}
			}
		}

		$activity->onAfterEdit($message);

		$this->app->enqueueMessage(JText::_('COM_KUNENA_POST_SUCCESS_EDIT'));

		if ($message->hold == 1)
		{
			// If user cannot approve message by himself, send email to moderators.
			if (!$topic->authorise('approve'))
			{
				$message->sendNotification();
			}

			$this->app->enqueueMessage(JText::_('COM_KUNENA_GEN_MODERATED'));
		}

		// Redirect edit first message when category is under review
		if ($message->hold == 1 && $message->getCategory()->review && $topic->first_post_id == $message->id && !$this->me->isModerator())
		{
			$this->setRedirect($message->getCategory()->getUrl($this->return, false));
		}
		else
		{
			$this->setRedirect($message->getUrl($this->return, false));
		}
	}

	/**
	 * Check if title of topic or message contains URL to limit part of spam
	 *
	 * @param $subject
	 *
	 * @return bool
	 * @internal param string $usbject
	 *
	 */
	protected function checkURLInSubject($subject)
	{
		if ($this->config->url_subject_topic)
		{
			preg_match_all('/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i', $subject, $matches);

			$ignore = false;

			foreach ($matches as $match)
			{
				if (!empty($match))
				{
					$ignore = true;
				}
			}

			return $ignore;
		}

		return true;
	}

	/**
	 * Check in the text the max links
	 *
	 * @param $text
	 * @param $topic
	 *
	 * @return bool
	 */
	protected function checkMaxLinks($text, $topic)
	{
		preg_match_all('/<div class=\"kunena_ebay_widget\"(.*?)>(.*?)<\/div>/s', $text, $ebay_matches);

		$ignore = false;

		foreach ($ebay_matches as $match)
		{
			if (!empty($match))
			{
				$ignore = true;
			}
		}

		preg_match_all('/<div id=\"kunena_twitter_widget\"(.*?)>(.*?)<\/div>/s', $text, $twitter_matches);

		foreach ($twitter_matches as $match)
		{
			if (!empty($match))
			{
				$ignore = true;
			}
		}

		if (!$ignore)
		{
			preg_match_all('@\(((https?://)?([-\\w]+\\.[-\\w\\.]+)+\\w(:\\d+)?(/([-\\w/_\\.]*(\\?\\S+)?)?)*)\)@', $text, $matches);

			if (empty($matches[0]))
			{
				preg_match_all("/<a\s[^>]*href=\"([^\"]*)\"[^>]*>(.*)<\/a>/siU", $text, $matches);
			}

			$countlink = count($matches[0]);

			if (!$topic->authorise('approve') && $countlink >= $this->config->max_links + 1)
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * @throws Exception
	 */
	public function thankyou()
	{
		$type = JFactory::getApplication()->input->getString('task');
		$this->setThankyou($type);
	}

	/**
	 * @throws Exception
	 */
	public function unthankyou()
	{
		$type = JFactory::getApplication()->input->getString('task');
		$this->setThankyou($type);
	}

	/**
	 * @param $type
	 *
	 * @throws Exception
	 */
	protected function setThankyou($type)
	{
		if (!JSession::checkToken('get'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$message = KunenaForumMessageHelper::get($this->mesid);

		if (!$message->authorise($type))
		{
			$this->app->enqueueMessage($message->getError());
			$this->setRedirectBack();

			return;
		}

		$category            = KunenaForumCategoryHelper::get($this->catid);
		$thankyou            = KunenaForumMessageThankyouHelper::get($this->mesid);
		$activityIntegration = KunenaFactory::getActivityIntegration();

		if ($type == 'thankyou')
		{
			if (!$thankyou->save($this->me))
			{
				$this->app->enqueueMessage($thankyou->getError());
				$this->setRedirectBack();

				return;
			}

			$this->app->enqueueMessage(JText::_('COM_KUNENA_THANKYOU_SUCCESS'));

			if ($this->config->log_moderation)
			{
				KunenaLog::log(
					KunenaLog::TYPE_ACTION,
					KunenaLog::LOG_POST_THANKYOU,
					array('mesid' => $message->id),
					$category,
					$message->getTopic(),
					$message->getAuthor()
				);
			}

			$activityIntegration->onAfterThankyou($this->me->userid, $message->userid, $message);
		}
		else
		{
			$userid = JFactory::getApplication()->input->getInt('userid', '0');

			if (!$thankyou->delete($userid))
			{
				$this->app->enqueueMessage($thankyou->getError());
				$this->setRedirectBack();

				return;
			}

			$this->app->enqueueMessage(JText::_('COM_KUNENA_THANKYOU_REMOVED_SUCCESS'));

			if ($this->config->log_moderation)
			{
				KunenaLog::log(
					KunenaLog::TYPE_MODERATION,
					KunenaLog::LOG_POST_UNTHANKYOU,
					array('mesid' => $message->id, 'userid' => $userid),
					$category,
					$message->getTopic(),
					$message->getAuthor()
				);
			}

			$activityIntegration->onAfterUnThankyou($this->me->userid, $userid, $message);
		}

		$this->setRedirect($message->getUrl($category->exists() ? $category->id : $message->catid, false));
	}

	/**
	 *
	 */
	public function subscribe()
	{
		if (!JSession::checkToken('get'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$topic = KunenaForumTopicHelper::get($this->id);

		if ($topic->authorise('read') && $topic->subscribe(1))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_POST_SUBSCRIBED_TOPIC'));

			// Activity integration
			$activity = KunenaFactory::getActivityIntegration();
			$activity->onAfterSubscribe($topic, 1);
		}
		else
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_POST_NO_SUBSCRIBED_TOPIC') . ' ' . $topic->getError(), 'notice');
		}

		$this->setRedirectBack();
	}

	/**
	 *
	 */
	public function unsubscribe()
	{
		if (!JSession::checkToken('get'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$topic = KunenaForumTopicHelper::get($this->id);

		if ($topic->authorise('read') && $topic->subscribe(0))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_POST_UNSUBSCRIBED_TOPIC'));

			// Activity integration
			$activity = KunenaFactory::getActivityIntegration();
			$activity->onAfterSubscribe($topic, 0);
		}
		else
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_POST_NO_UNSUBSCRIBED_TOPIC') . ' ' . $topic->getError(), 'notice');
		}

		$this->setRedirectBack();
	}

	/**
	 *
	 */
	public function favorite()
	{
		if (!JSession::checkToken('get'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$topic = KunenaForumTopicHelper::get($this->id);

		if ($topic->authorise('read') && $topic->favorite(1))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_POST_FAVORITED_TOPIC'));

			// Activity integration
			$activity = KunenaFactory::getActivityIntegration();
			$activity->onAfterFavorite($topic, 1);
		}
		else
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_POST_NO_FAVORITED_TOPIC') . ' ' . $topic->getError(), 'notice');
		}

		$this->setRedirectBack();
	}

	/**
	 *
	 */
	public function unfavorite()
	{
		if (!JSession::checkToken('get'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$topic = KunenaForumTopicHelper::get($this->id);

		if ($topic->authorise('read') && $topic->favorite(0))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_POST_UNFAVORITED_TOPIC'));

			// Activity integration
			$activity = KunenaFactory::getActivityIntegration();
			$activity->onAfterFavorite($topic, 0);
		}
		else
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_POST_NO_UNFAVORITED_TOPIC') . ' ' . $topic->getError(), 'notice');
		}

		$this->setRedirectBack();
	}

	/**
	 *
	 */
	public function sticky()
	{
		if (!JSession::checkToken('get'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$topic = KunenaForumTopicHelper::get($this->id);

		if (!$topic->authorise('sticky'))
		{
			$this->app->enqueueMessage($topic->getError(), 'notice');
		}
		elseif ($topic->sticky(1))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_POST_STICKY_SET'));

			if ($this->config->log_moderation)
			{
				KunenaLog::log(
					KunenaLog::TYPE_MODERATION,
					KunenaLog::LOG_TOPIC_STICKY,
					array(),
					$topic->getCategory(),
					$topic
				);
			}

			// Activity integration
			$activity = KunenaFactory::getActivityIntegration();
			$activity->onAfterSticky($topic, 1);
		}
		else
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_POST_STICKY_NOT_SET'));
		}

		$this->setRedirectBack();
	}

	/**
	 *
	 */
	public function unsticky()
	{
		if (!JSession::checkToken('get'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$topic = KunenaForumTopicHelper::get($this->id);

		if (!$topic->authorise('sticky'))
		{
			$this->app->enqueueMessage($topic->getError(), 'notice');
		}
		elseif ($topic->sticky(0))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_POST_STICKY_UNSET'));

			if ($this->config->log_moderation)
			{
				KunenaLog::log(
					KunenaLog::TYPE_MODERATION,
					KunenaLog::LOG_TOPIC_UNSTICKY,
					array(),
					$topic->getCategory(),
					$topic
				);
			}

			// Activity integration
			$activity = KunenaFactory::getActivityIntegration();
			$activity->onAfterSticky($topic, 0);
		}
		else
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_POST_STICKY_NOT_UNSET'));
		}

		$this->setRedirectBack();
	}

	/**
	 *
	 */
	public function lock()
	{
		if (!JSession::checkToken('get'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$topic = KunenaForumTopicHelper::get($this->id);

		if (!$topic->authorise('lock'))
		{
			$this->app->enqueueMessage($topic->getError(), 'notice');
		}
		elseif ($topic->lock(1))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_POST_LOCK_SET'));

			if ($this->config->log_moderation)
			{
				KunenaLog::log(
					KunenaLog::TYPE_MODERATION,
					KunenaLog::LOG_TOPIC_LOCK,
					array(),
					$topic->getCategory(),
					$topic
				);
			}

			// Activity integration
			$activity = KunenaFactory::getActivityIntegration();
			$activity->onAfterLock($topic, 1);
		}
		else
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_POST_LOCK_NOT_SET'));
		}

		$this->setRedirectBack();
	}

	/**
	 *
	 */
	public function unlock()
	{
		if (!JSession::checkToken('get'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$topic = KunenaForumTopicHelper::get($this->id);

		if (!$topic->authorise('lock'))
		{
			$this->app->enqueueMessage($topic->getError(), 'notice');
		}
		elseif ($topic->lock(0))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_POST_LOCK_UNSET'));

			if ($this->config->log_moderation)
			{
				KunenaLog::log(
					KunenaLog::TYPE_MODERATION,
					KunenaLog::LOG_TOPIC_UNLOCK,
					array(),
					$topic->getCategory(),
					$topic
				);
			}

			// Activity integration
			$activity = KunenaFactory::getActivityIntegration();
			$activity->onAfterLock($topic, 0);
		}
		else
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_POST_LOCK_NOT_UNSET'));
		}

		$this->setRedirectBack();
	}

	/**
	 *
	 */
	public function delete()
	{
		if (!JSession::checkToken('get'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		if ($this->mesid)
		{
			// Delete message
			$message = $target = KunenaForumMessageHelper::get($this->mesid);
			$topic = $message->getTopic();
			$log = KunenaLog::LOG_POST_DELETE;
			$hold   = KunenaForum::DELETED;
			$msg    = JText::_('COM_KUNENA_POST_SUCCESS_DELETE');
		}
		else
		{
			// Delete topic
			$topic = $target = KunenaForumTopicHelper::get($this->id);
			$log = KunenaLog::LOG_TOPIC_DELETE;
			$hold   = KunenaForum::TOPIC_DELETED;
			$msg    = JText::_('COM_KUNENA_TOPIC_SUCCESS_DELETE');
		}

		$category = $topic->getCategory();
		if ($target->authorise('delete') && $target->publish($hold))
		{
			if ($this->config->log_moderation)
			{
				KunenaLog::log(
					$this->me->isModerator($category) ? KunenaLog::TYPE_MODERATION : KunenaLog::TYPE_ACTION,
					$log,
					isset($message) ? array('mesid' => $message->id) : array(),
					$category,
					$topic
				);
			}

			$this->app->enqueueMessage($msg);
		}
		else
		{
			$this->app->enqueueMessage($target->getError(), 'notice');
		}

		if (!$target->authorise('read'))
		{
			if ($target instanceof KunenaForumMessage && $target->getTopic()->authorise('read'))
			{
				$target = $target->getTopic();
				$target = KunenaForumMessageHelper::get($target->last_post_id);
			}
			else
			{
				$target = $target->getCategory();
			}
		}

		$this->setRedirect($target->getUrl($this->return, false));
	}

	/**
	 *
	 */
	public function undelete()
	{
		if (!JSession::checkToken('get'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		if ($this->mesid)
		{
			// Undelete message
			$message = $target = KunenaForumMessageHelper::get($this->mesid);
			$topic = $message->getTopic();
			$log = KunenaLog::LOG_POST_UNDELETE;
			$msg    = JText::_('COM_KUNENA_POST_SUCCESS_UNDELETE');
		}
		else
		{
			// Undelete topic
			$topic = $target = KunenaForumTopicHelper::get($this->id);
			$log = KunenaLog::LOG_TOPIC_UNDELETE;
			$msg    = JText::_('COM_KUNENA_TOPIC_SUCCESS_UNDELETE');
		}

		$category = $topic->getCategory();
		if ($target->authorise('undelete') && $target->publish(KunenaForum::PUBLISHED))
		{
			if ($this->config->log_moderation)
			{
				KunenaLog::log(
					$this->me->isModerator($category) ? KunenaLog::TYPE_MODERATION : KunenaLog::TYPE_ACTION,
					$log,
					isset($message) ? array('mesid' => $message->id) : array(),
					$category,
					$topic
				);
			}

			$this->app->enqueueMessage($msg);
		}
		else
		{
			$this->app->enqueueMessage($target->getError(), 'notice');
		}

		$this->setRedirect($target->getUrl($this->return, false));
	}

	/**
	 *
	 */
	public function permdelete()
	{
		if (!JSession::checkToken('get'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		if ($this->mesid)
		{
			// Delete message
			$message = $target = KunenaForumMessageHelper::get($this->mesid);
			$topic = $message->getTopic();
			$log = KunenaLog::LOG_POST_DESTROY;
			$topic  = KunenaForumTopicHelper::get($target->getTopic());

			if ($topic->attachments > 0)
			{
				$topic->attachments = $topic->attachments - 1;
				$topic->save(false);
			}
		}
		else
		{
			// Delete topic
			$topic = $target = KunenaForumTopicHelper::get($this->id);
			$log = KunenaLog::LOG_TOPIC_DESTROY;
		}

		$category = $topic->getCategory();
		if ($target->authorise('permdelete') && $target->delete())
		{
			if ($this->config->log_moderation)
			{
				KunenaLog::log(
					$this->me->isModerator($category) ? KunenaLog::TYPE_MODERATION : KunenaLog::TYPE_ACTION,
					$log,
					isset($message) ? array('mesid' => $message->id) : array(),
					$category,
					$topic
				);
			}

			if ($topic->exists())
			{
				$this->app->enqueueMessage(JText::_('COM_KUNENA_POST_SUCCESS_DELETE'));
				$url = $topic->getUrl($this->return, false);
			}
			else
			{
				$this->app->enqueueMessage(JText::_('COM_KUNENA_TOPIC_SUCCESS_DELETE'));
				$url = $topic->getCategory()->getUrl($this->return, false);
			}
		}
		else
		{
			$this->app->enqueueMessage($target->getError(), 'notice');
		}

		if (isset($url))
		{
			$this->setRedirect($url);
		}
	}

	/**
	 *
	 */
	public function approve()
	{
		if (!JSession::checkToken('get'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		// Load language file from the template.
		KunenaFactory::getTemplate()->loadLanguage();

		if ($this->mesid)
		{
			// Approve message
			$target  = KunenaForumMessageHelper::get($this->mesid);
			$message = $target;
			$log = KunenaLog::LOG_POST_APPROVE;
		}
		else
		{
			// Approve topic
			$target  = KunenaForumTopicHelper::get($this->id);
			$message = KunenaForumMessageHelper::get($target->first_post_id);
			$log = KunenaLog::LOG_TOPIC_APPROVE;
		}

		$topic = $message->getTopic();
		$category = $topic->getCategory();
		if ($target->authorise('approve') && $target->publish(KunenaForum::PUBLISHED))
		{
			if ($this->config->log_moderation)
			{
				KunenaLog::log(
						$this->me->isModerator($category) ? KunenaLog::TYPE_MODERATION : KunenaLog::TYPE_ACTION,
						$log,
						array('mesid' => $message->id),
						$category,
						$topic,
						$message->getAuthor()
				);
			}

			$this->app->enqueueMessage(JText::_('COM_KUNENA_MODERATE_APPROVE_SUCCESS'));

			// Only email if message wasn't modified by the author before approval
			// TODO: this is just a workaround for #1862, we need to find better solution.

			$modifiedByAuthor = ($message->modified_by == $message->userid);

			if (!$modifiedByAuthor)
			{
				$target->sendNotification();
			}
		}
		else
		{
			$this->app->enqueueMessage($target->getError(), 'notice');
		}

		$this->setRedirect($target->getUrl($this->return, false));
	}

	/**
	 * @throws Exception
	 */
	public function move()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$topicId        = JFactory::getApplication()->input->getInt('id', 0);
		$messageId      = JFactory::getApplication()->input->getInt('mesid', 0);
		$targetCategory = JFactory::getApplication()->input->getInt('targetcategory', 0);
		$targetTopic    = JFactory::getApplication()->input->getInt('targettopic', 0);

		if ($targetTopic < 0)
		{
			$targetTopic = JFactory::getApplication()->input->getInt('targetid', 0);
		}

		if ($messageId)
		{
			$message = $object = KunenaForumMessageHelper::get($messageId);
			$topic = $message->getTopic();
		}
		else
		{
			$topic = $object = KunenaForumTopicHelper::get($topicId);
			$message = KunenaForumMessageHelper::get($topic->first_post_id);
		}

		if ($targetTopic)
		{
			$target = KunenaForumTopicHelper::get($targetTopic);
		}
		else
		{
			$target = KunenaForumCategoryHelper::get($targetCategory);
		}

		$error        = null;
		$targetobject = null;

		if (!$object->authorise('move'))
		{
			$error = $object->getError();
		}
		elseif (!$target->authorise('read'))
		{
			$error = $target->getError();
		}
		else
		{
			$changesubject  = JFactory::getApplication()->input->getBool('changesubject', false);
			$subject        = JFactory::getApplication()->input->getString('subject', '');
			$shadow         = JFactory::getApplication()->input->getBool('shadow', false);
			$topic_emoticon = JFactory::getApplication()->input->getInt('topic_emoticon', null);

			if ($object instanceof KunenaForumMessage)
			{
				$mode = JFactory::getApplication()->input->getWord('mode', 'selected');

				switch ($mode)
				{
					case 'newer':
						$ids = new JDate($object->time);
						break;
					case 'selected':
					default:
						$ids = $object->id;
						break;
				}
			}
			else
			{
				$ids = false;
			}

			$targetobject = $topic->move($target, $ids, $shadow, $subject, $changesubject, $topic_emoticon);

			if (!$targetobject)
			{
				$error = $topic->getError();
			}

			if ($this->config->log_moderation)
			{
				KunenaLog::log(
					KunenaLog::TYPE_MODERATION,
					$messageId ? KunenaLog::LOG_POST_MODERATE : KunenaLog::LOG_TOPIC_MODERATE,
					array(
						'move' => array('id' => $topicId, 'mesid' => $messageId, 'mode' => isset($mode) ? $mode : 'topic'),
						'target' => array('category_id' => $targetCategory, 'topic_id' => $targetTopic),
						'options' => array('emo' => $topic_emoticon, 'subject' => $subject, 'changeAll' => $changesubject, 'shadow' => $shadow)
					),
					$topic->getCategory(),
					$topic,
					$message->getAuthor()
				);
			}
		}

		if ($error)
		{
			$this->app->enqueueMessage($error, 'notice');
		}
		else
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ACTION_TOPIC_SUCCESS_MOVE'));
		}

		if ($targetobject)
		{
			$this->setRedirect($targetobject->getUrl($this->return, false, 'last'));
		}
		else
		{
			$this->setRedirect($topic->getUrl($this->return, false, 'first'));
		}
	}

	/**
	 * @throws Exception
	 */
	function report()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		if (!$this->me->exists() || $this->config->reportmsg == 0)
		{
			// Deny access if report feature has been disabled or user is guest
			$this->app->enqueueMessage(JText::_('COM_KUNENA_NO_ACCESS'), 'notice');
			$this->setRedirectBack();

			return;
		}

		if (!$this->config->get('send_emails'))
		{
			// Emails have been disabled
			$this->app->enqueueMessage(JText::_('COM_KUNENA_EMAIL_DISABLED'), 'notice');
			$this->setRedirectBack();

			return;
		}

		if (!$this->config->getEmail() || !JMailHelper::isEmailAddress($this->config->getEmail()))
		{
			// Error: email address is invalid
			$this->app->enqueueMessage(JText::_('COM_KUNENA_EMAIL_INVALID'), 'error');
			$this->setRedirectBack();

			return;
		}

		// Get target object for the report
		if ($this->mesid)
		{
			$message = $target = KunenaForumMessageHelper::get($this->mesid);
			$topic   = $target->getTopic();
			$log = KunenaLog::LOG_POST_REPORT;
		}
		else
		{
			$topic   = $target = KunenaForumTopicHelper::get($this->id);
			$message = KunenaForumMessageHelper::get($topic->first_post_id);
			$log = KunenaLog::LOG_TOPIC_REPORT;
		}

		$messagetext = $message->message;
		$baduser     = KunenaFactory::getUser($message->userid);

		if (!$target->authorise('read'))
		{
			// Deny access if user cannot read target
			$this->app->enqueueMessage($target->getError(), 'notice');
			$this->setRedirectBack();

			return;
		}

		$reason = JFactory::getApplication()->input->getString('reason');
		$text   = JFactory::getApplication()->input->getString('text');

		$template = KunenaTemplate::getInstance();

		if (method_exists($template, 'reportMessage'))
		{
			$template->reportMessage($message, $reason, $text);
		}

		if ($this->config->log_moderation)
		{
			KunenaLog::log(
				KunenaLog::TYPE_REPORT,
				$log,
				array(
					'mesid' => $message->id,
					'reason' => $reason,
					'message' => $text
				),
				$topic->getCategory(),
				$topic,
				$message->getAuthor()
			);
		}

		// Load language file from the template.
		KunenaFactory::getTemplate()->loadLanguage();

		if (empty($reason) && empty($text))
		{
			// Do nothing: empty subject or reason is empty
			$this->app->enqueueMessage(JText::_('COM_KUNENA_REPORT_FORG0T_SUB_MES'));
			$this->setRedirectBack();

			return;
		}
		else
		{
			$acl         = KunenaAccess::getInstance();
			$emailToList = $acl->getSubscribers($topic->category_id, $topic->id, false, true, false);

			if (!empty($emailToList))
			{
				$mailsender  = JMailHelper::cleanAddress($this->config->board_title . ' ' . JText::_('COM_KUNENA_FORUM') . ': ' . $this->me->getName());
				$mailsubject = "[" . $this->config->board_title . " " . JText::_('COM_KUNENA_FORUM') . "] " . JText::_('COM_KUNENA_REPORT_MSG') . ": ";

				if ($reason)
				{
					$mailsubject .= $reason;
				}
				else
				{
					$mailsubject .= $topic->subject;
				}

				jimport('joomla.environment.uri');
				$msglink = JUri::getInstance()->toString(array('scheme', 'host', 'port')) . $target->getPermaUrl(null, false);

				$mail = JFactory::getMailer();
				$mail->setSender(array($this->me->username, $this->me->email));
				$mail->setSubject($mailsubject);

				// Render the email.
				$layout = KunenaLayout::factory('Email/Report')->debug(false)
					->set('mail', $mail)
					->set('message', $message)
					->set('me', $this->me)
					->set('title', $reason)
					->set('content', $text)
					->set('messageLink', $msglink);

				try
				{
					$body = trim($layout->render());
					$mail->setBody($body);
				}
				catch (Exception $e)
				{

				}

				$receivers = array();

				foreach ($emailToList as $emailTo)
				{
					if (!$emailTo->email || !JMailHelper::isEmailAddress($emailTo->email))
					{
						continue;
					}

					$receivers[] = $emailTo->email;
				}

				KunenaEmail::send($mail, $receivers);

				$this->app->enqueueMessage(JText::_('COM_KUNENA_REPORT_SUCCESS'));
			}
			else
			{
				$this->app->enqueueMessage(JText::_('COM_KUNENA_REPORT_NOT_SEND'));
			}
		}

		$this->setRedirect($target->getUrl($this->return, false));
	}

	/**
	 * @throws Exception
	 */
	public function vote()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$vote  = JFactory::getApplication()->input->getInt('kpollradio', '');
		$id    = JFactory::getApplication()->input->getInt('id', 0);
		$catid = JFactory::getApplication()->input->getInt('catid', 0);

		$topic = KunenaForumTopicHelper::get($id);
		$poll  = $topic->getPoll();

		if (!$topic->authorise('poll.vote'))
		{
			$this->app->enqueueMessage($topic->getError(), 'error');
		}
		elseif (!$this->config->pollallowvoteone || !$poll->getMyVotes())
		{
			// Give a new vote
			$success = $poll->vote($vote);

			if (!$success)
			{
				$this->app->enqueueMessage($poll->getError(), 'error');
			}
			else
			{
				$this->app->enqueueMessage(JText::_('COM_KUNENA_TOPIC_VOTE_SUCCESS'));
			}
		}
		else
		{
			// Change existing vote
			$success = $poll->vote($vote, true);

			if (!$success)
			{
				$this->app->enqueueMessage($poll->getError(), 'error');
			}
			else
			{
				$this->app->enqueueMessage(JText::_('COM_KUNENA_TOPIC_VOTE_CHANGED_SUCCESS'));
			}
		}

		$this->setRedirect($topic->getUrl($this->return, false));
	}

	/**
	 *
	 */
	public function resetvotes()
	{
		if (!JSession::checkToken('get'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$topic = KunenaForumTopicHelper::get($this->id);
		$topic->resetvotes();

		if ($this->config->log_moderation)
		{
			KunenaLog::log(
				KunenaLog::TYPE_MODERATION,
				KunenaLog::LOG_POLL_MODERATE,
				array(),
				$topic->getCategory(),
				$topic,
				null
			);
		}

		$this->app->enqueueMessage(JText::_('COM_KUNENA_TOPIC_VOTE_RESET_SUCCESS'));
		$this->setRedirect($topic->getUrl($this->return, false));
	}
}
