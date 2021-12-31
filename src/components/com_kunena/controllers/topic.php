<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;

/**
 * Kunena Topic Controller
 *
 * @since  2.0
 */
class KunenaControllerTopic extends KunenaController
{
	/**
	 * @param   array $config config
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->catid  = Factory::getApplication()->input->getInt('catid', 0);
		$this->return = Factory::getApplication()->input->getInt('return', $this->catid);
		$this->id     = Factory::getApplication()->input->getInt('id', 0);
		$this->mesid  = Factory::getApplication()->input->getInt('mesid', 0);
	}

	/**
	 * Get attachments attached to a message with AJAX.
	 *
	 * @return void
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	public function loadattachments()
	{
		// Only support JSON requests.
		if ($this->input->getWord('format', 'html') != 'json')
		{
			throw new RuntimeException(Text::_('Bad Request'), 400);
		}

		if (!Session::checkToken('request'))
		{
			throw new RuntimeException(Text::_('Forbidden'), 403);
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
			$object->inline  = $attach->isInline();
			$list['files'][] = $object;
		}

		header('Content-type: application/json');
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");

		if (ob_get_length())
		{
			ob_end_clean();
		}

		echo json_encode($list);

		jexit();
	}

	/**
	 * Set inline to 1 on the attachment object.
	 *
	 * @return void
	 * @throws Exception
	 * @since Kunena 5.1
	 */
	public function setinline()
	{
		$attachs_id = $this->input->getString('files_id', '');
		$attachs_id = json_decode($attachs_id);

		if ($attachs_id===null)
		{
			throw new RuntimeException(Text::_('Bad Request'), 400);
		}

		$attach_ids_final = array();
		foreach($attachs_id as $attach)
		{
			if (is_array($attach))
			{
				$attach_ids_final[] = $attach['0'];
			}
			else
			{
				$attach_ids_final[] = $attach;
			}
		}

		$instances  = KunenaAttachmentHelper::getById($attach_ids_final, 'none');

		$this->changeinline($instances,  '1');
	}

	/**
	 * Set inline to 0 on one attachment object.
	 *
	 * @return void
	 * @since Kunena 5.1
	 * @throws Exception
	 */
	public function removeinlineonattachment()
	{
		$attach_id = $this->input->getInt('file_id', 0);

		$instance  = KunenaAttachmentHelper::get($attach_id);

		$this->checkpermissions($instance->userid);

		$this->changeinline($instance, '0');
	}

	/**
	 * Set inline to 0 or 1 on the attachment object.
	 *
	 * @return void
	 * @since Kunena 5.1
	 * @throws Exception
	 */
	protected function changeinline($attachments, $inline)
	{
		// Only support JSON requests.
		if ($this->input->getWord('format', 'html') != 'json')
		{
			throw new RuntimeException(Text::_('Bad Request'), 400);
		}

		if (!Session::checkToken('request'))
		{
			throw new RuntimeException(Text::_('Forbidden'), 403);
		}

		$response  = array();

		if (is_object($attachments))
		{
			$editor_text = $this->input->get->get('editor_text', '', 'raw');
			$find             = array('/\[attachment='.$attachments->id.'\](.*?)\[\/attachment\]/su');
			$replace          = '';
			$text             = preg_replace($find, $replace, $editor_text);
			$response['text_prepared'] = $text;
		}
		else
		{
			foreach($attachments as $instance)
			{
				$response['result'] = $instance->setInline($inline);
				$response['value']  = $inline;
			}
		}

		unset($attachments);

		header('Content-type: application/json');
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");

		if (ob_get_length())
		{
			ob_end_clean();
		}

		echo json_encode($response);

		jexit();
	}

	/**
	 * Check permissions.
	 *
	 * @return void
	 * @since Kunena 5.1
	 * @throws Exception
	 */
	protected function checkpermissions($attachment_userid)
	{
		if (KunenaUserHelper::getMyself()->userid != $attachment_userid || !KunenaUserHelper::getMyself()->isAdmin() || !KunenaUserHelper::getMyself()->isModerator())
		{
			throw new RuntimeException(Text::_('Forbidden'), 403);
		}
	}

	/**
	 * Remove files with AJAX.
	 *
	 * @return void
	 * @throws Exception
	 * @since Kunena
	 */
	public function removeattachments()
	{
		// Only support JSON requests.
		if ($this->input->getWord('format', 'html') != 'json')
		{
			throw new RuntimeException(Text::_('Bad Request'), 400);
		}

		if (!Session::checkToken('request'))
		{
			throw new RuntimeException(Text::_('Forbidden'), 403);
		}

		$attachs_id = $this->input->getString('files_id_delete', '');
		$attachs_id = json_decode($attachs_id);

		if ($attachs_id===null)
		{
			throw new RuntimeException(Text::_('Bad Request'), 400);
		}

		$attach_ids_final = array();
		foreach($attachs_id as $attach)
		{
			if (is_array($attach))
			{
				$attach_ids_final[] = $attach['0'];
			}
			else
			{
				$attach_ids_final[] = $attach;
			}
		}

		$instances  = KunenaAttachmentHelper::getById($attach_ids_final, 'none');
		$success   = array();

		$editor_text = $this->app->input->get->get('editor_text', '', 'raw');
		$success['text_prepared'] = false;
		$find = array();

		foreach($instances as $instance)
		{
			if (!empty($editor_text) && $instance->inline)
			{
				$find[]             = '/\[attachment=' . $instance->id . '\](.*?)\[\/attachment\]/su';
			}

			$instance->delete();
		}

		$replace          = '';
		$text             = preg_replace($find, $replace, $editor_text);
		$success['text_prepared'] = $text;

		unset($instance);

		header('Content-type: application/json');
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");

		if (ob_get_length())
		{
			ob_end_clean();
		}

		echo json_encode($success);

		jexit();
	}

	/**
	 * Upload files with AJAX.
	 *
	 * @throws null
	 * @since Kunena
	 */
	public function upload()
	{
		// Only support JSON requests.
		if ($this->input->getWord('format', 'html') != 'json')
		{
			throw new RuntimeException(Text::_('Bad Request'), 400);
		}

		$upload = KunenaUpload::getInstance();

		// We are converting all exceptions into JSON.
		try
		{
			if (!Session::checkToken('request'))
			{
				throw new RuntimeException(Text::_('Forbidden'), 403);
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
						'inline'        => null,
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
				$response->inline   = $attachment->inline;
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

		if (ob_get_length())
		{
			ob_end_clean();
		}

		echo $upload->ajaxResponse($response);

		jexit();
	}

	/**
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 */
	public function post()
	{
		$this->id = Factory::getApplication()->input->getInt('parentid', 0);
		$fields   = array(
			'catid'             => $this->catid,
		    'name'              => Factory::getApplication()->input->getString('authorname', $this->me->getName()),
			'email'             => Factory::getApplication()->input->getString('email', null),
			'subject'           => Factory::getApplication()->input->post->get('subject', '', 'raw'),
			'message'           => Factory::getApplication()->input->post->get('message', '', 'raw'),
			'icon_id'           => Factory::getApplication()->input->getInt('topic_emoticon', null),
			'anonymous'         => Factory::getApplication()->input->getInt('anonymous', 0),
			'poll_title'        => Factory::getApplication()->input->getString('poll_title', ''),
			'poll_options'      => Factory::getApplication()->input->get('polloptionsID', array(), 'post', 'array'),
			'poll_time_to_live' => Factory::getApplication()->input->getString('poll_time_to_live', 0),
			'subscribe'         => Factory::getApplication()->input->getInt('subscribeMe', 0),
		);

		$this->app->setUserState('com_kunena.postfields', $fields);

		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		if (!$this->id)
		{
			// Create topic
			$category = KunenaForumCategoryHelper::get($this->catid);

			try
			{
				$category->isAuthorised('topic.create');
			}
			catch (\Exception $e)
			{
				$this->app->enqueueMessage($e->getMessage(), 'notice');
				$this->setRedirectBack();

				return;
			}

			list($topic, $message) = $category->newTopic($fields);
		}
		else
		{
			// Reply topic
			$parent = KunenaForumMessageHelper::get($this->id);

			try
			{
				$parent->isAuthorised('reply');
			}
			catch (\Exception $e)
			{
				$this->app->enqueueMessage($e->getMessage(), 'notice');
				$this->setRedirectBack();

				return;
			}

			list($topic, $message) = $parent->newReply($fields);
			$category = $topic->getCategory();
		}

		if ($this->me->canDoCaptcha())
		{
			if (\Joomla\CMS\Plugin\PluginHelper::isEnabled('captcha'))
			{
				$plugin = \Joomla\CMS\Plugin\PluginHelper::getPlugin('captcha');
				$params = new \Joomla\Registry\Registry($plugin[0]->params);

				$captcha_pubkey  = $params->get('public_key');
				$captcha_privkey = $params->get('private_key');

				if (!empty($captcha_pubkey) && !empty($captcha_privkey))
				{
					\Joomla\CMS\Plugin\PluginHelper::importPlugin('captcha');

					$captcha_response = $this->app->input->getString('g-recaptcha-response');

					if (!empty($captcha_response))
					{
						// For ReCaptcha API 2.0
						$res = Factory::getApplication()->triggerEvent('onCheckAnswer', array($this->app->input->getString('g-recaptcha-response')));
					}
					else
					{
						// For ReCaptcha API 1.0
						$res = Factory::getApplication()->triggerEvent('onCheckAnswer', array($this->app->input->getString('recaptcha_response_field')));
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
		if (Factory::getApplication()->input->getString('fullreply'))
		{
			$this->setRedirect(KunenaRoute::_("index.php?option=com_kunena&view=topic&layout=reply&catid={$fields->catid}&id={$parent->getTopic()->id}&mesid={$parent->id}", false));

			return;
		}

		// Flood protection
		if ($this->config->floodprotection && !$this->me->isModerator($category) && $isNew)
		{
			$timelimit = Factory::getDate()->toUnix() - $this->config->floodprotection;
			$ip        = $_SERVER ["REMOTE_ADDR"];

			$db = Factory::getDBO();
			$db->setQuery("SELECT COUNT(*) FROM #__kunena_messages WHERE ip={$db->Quote($ip)} AND time>{$db->quote($timelimit)}");

			try
			{
				$count = $db->loadResult();
			}
			catch (JDatabaseExceptionExecuting $e)
			{
				KunenaError::displayDatabaseError($e);
			}

			if ($count)
			{
				$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_POST_TOPIC_FLOOD', $this->config->floodprotection), 'error');
				$this->setRedirectBack();

				return;
			}
		}

		// Ignore identical for 5 minutes
		$duplicatetimewindow = Factory::getDate()->toUnix() - 1 * 60;
		$lastTopic           = $topic->getCategory()->getLastTopic();

		if ($lastTopic->subject == $topic->subject && $lastTopic->last_post_time >= $duplicatetimewindow
			&& $lastTopic->category_id == $topic->category_id && $lastTopic->last_post_id == $topic->last_post_id
			&& $lastTopic->id == $topic->id && $lastTopic->last_post_message == $message->message)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_POST_DUPLICATE_IGNORED'), 'error');

			return $this->setRedirect(KunenaRoute::_("index.php?option=com_kunena&view=topic&catid={$topic->getCategory()->id}&id={$lastTopic->id}&mesid={$lastTopic->last_post_id}", false));
		}

		// Set topic icon if permitted
		if ($this->config->topicicons && isset($fields['icon_id']) && $topic->isAuthorised('edit', null, false))
		{
			$topic->icon_id = $fields['icon_id'];
		}

		// Remove IP address
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
		$attachments = Factory::getApplication()->input->get('attachments', array(), 'post', 'array');
		$attachment  = Factory::getApplication()->input->get('attachment', array(), 'post', 'array');
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
			$this->app->enqueueMessage(Text::_('COM_KUNENA_TOPIC_MESSAGES_ERROR_URL_IN_SUBJECT'), 'error');
			$this->setRedirectBack();

			return;
		}

		if ($this->me->checkUserAllowedLinksImages())
		{
			$message->message = $this->removeLinksInMessage($message->message);

			if (!$message->message)
			{
				$this->app->enqueueMessage(Text::_('COM_KUNENA_TOPIC_MESSAGE_EMPTY_LINKS_IMAGES_REMOVED_NOT_ALLOWED'), 'error');
				$this->setRedirectBack();

				return;
			}
		}

		// Make sure that message has visible content (text, images or objects) to be shown.
		$text = KunenaHtmlParser::parseBBCode($message->message);

		if (!preg_match('!(<img |<object |<iframe )!', $text))
		{
			$text = trim(\Joomla\CMS\Filter\OutputFilter::cleanText($text));
		}

		if (!$text)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_LIB_TABLE_MESSAGES_ERROR_NO_MESSAGE'), 'error');
			$this->setRedirectBack();

			return;
		}

		$maxlinks = $this->checkMaxLinks($text, $topic);

		if (!$maxlinks)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_TOPIC_SPAM_LINK_PROTECTION'), 'error');
			$this->setRedirectBack();

			return;
		}

		if (!$this->catid)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ACTION_NO_CATEGORY_SELECTED'), 'error');
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
		else
		{
			$activity->onBeforeHold($message);
		}

		// Save message
		$success = $message->save();

		// Save IP address of user
		if ($this->config->iptracking)
		{
			$this->me->ip = $message->ip;
			$this->me->save();
		}

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
			if ($topic->isAuthorised('poll.create', null, false))
			{
				$poll        = $topic->getPoll();
				$poll->title = $poll_title;

				if (!empty($fields['poll_time_to_live']))
				{
					$polltimetolive       = new \Joomla\CMS\Date\Date($fields['poll_time_to_live']);
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
					$this->app->enqueueMessage(Text::_('COM_KUNENA_POLL_CREATED'));
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
				$this->app->enqueueMessage(Text::_('COM_KUNENA_POST_SUBSCRIBED_TOPIC'));

				// Activity integration
				$activity = KunenaFactory::getActivityIntegration();
				$activity->onAfterSubscribe($topic, 1);
			}
			else
			{
				$this->app->enqueueMessage(Text::_('COM_KUNENA_POST_NO_SUBSCRIBED_TOPIC') . ' ' . $topic->getError());
			}
		}

		if ($message->hold == 1)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_POST_SUCCES_REVIEW'));
		}
		else
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_POST_SUCCESS_POSTED'));
		}

		$category = KunenaForumCategoryHelper::get($this->return);

		if ($message->isAuthorised('read', null, false) && $this->id)
		{
			$this->setRedirect($message->getUrl($category, false));
		}
		elseif ($topic->isAuthorised('read', null, false))
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
	 * @throws null
	 * @since Kunena
	 */
	public function edit()
	{
		$this->id = Factory::getApplication()->input->getInt('mesid', 0);

		$message = KunenaForumMessageHelper::get($this->id);
		$topic   = $message->getTopic();
		$fields  = array(
			'name'              => Factory::getApplication()->input->getString('authorname', $message->name),
			'email'             => Factory::getApplication()->input->getString('email', $message->email),
			'subject'           => Factory::getApplication()->input->post->get('subject', '', 'raw'),
			'message'           => Factory::getApplication()->input->post->get('message', '', 'raw'),
			'modified_reason'   => Factory::getApplication()->input->getString('modified_reason', $message->modified_reason),
			'icon_id'           => Factory::getApplication()->input->getInt('topic_emoticon', $topic->icon_id),
			'anonymous'         => Factory::getApplication()->input->getInt('anonymous', 0),
			'poll_title'        => Factory::getApplication()->input->getString('poll_title', null),
			'poll_options'      => Factory::getApplication()->input->get('polloptionsID', array(), 'post', 'array'),
			'poll_time_to_live' => Factory::getApplication()->input->getString('poll_time_to_live', 0),
			'subscribe'         => Factory::getApplication()->input->getInt('subscribeMe', 0),
		);

		if (!Session::checkToken('post'))
		{
			$this->app->setUserState('com_kunena.postfields', $fields);
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		try
		{
			$message->isAuthorised('edit');
		}
		catch (\Exception $e)
		{
			$this->app->setUserState('com_kunena.postfields', $fields);
			$this->app->enqueueMessage($e->getMessage(), 'notice');
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
		$attachments = Factory::getApplication()->input->get('attachments', array(), 'post', 'array');
		$attachment  = Factory::getApplication()->input->get('attachment', array(), 'post', 'array');

		$addList    = array_keys(array_intersect_key($attachments, $attachment));
		$addList    = ArrayHelper::toInteger($addList);
		$removeList = array_keys(array_diff_key($attachments, $attachment));
		$removeList = ArrayHelper::toInteger($removeList);

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
			$this->app->enqueueMessage(Text::_('COM_KUNENA_TOPIC_MESSAGES_ERROR_URL_IN_SUBJECT'), 'error');
			$this->setRedirectBack();

			return;
		}

		// Set topic icon if permitted
		if ($this->config->topicicons && isset($fields['icon_id']) && $topic->isAuthorised('edit', null))
		{
			$topic->icon_id = $fields['icon_id'];
		}

		// Check if we are editing first post and update topic if we are!
		if ($topic->first_post_id == $message->id || KunenaConfig::getInstance()->allow_change_subject && $topic->first_post_userid == $message->userid || KunenaUserHelper::getMyself()->isModerator())
		{
			$topic->subject = $fields['subject'];
		}

		if ($this->me->checkUserAllowedLinksImages())
		{
			$message->message = $this->removeLinksInMessage($message->message);

			if (!$message->message)
			{
				$this->app->enqueueMessage(Text::_('COM_KUNENA_TOPIC_MESSAGE_EMPTY_LINKS_IMAGES_REMOVED_NOT_ALLOWED'), 'error');
				$this->setRedirectBack();

				return;
			}
		}

		// If user removed all the text and message doesn't contain images or objects, delete the message instead.
		$text = KunenaHtmlParser::parseBBCode($message->message);

		if (!preg_match('!(<img |<object |<iframe )!', $text))
		{
			$text = trim(\Joomla\CMS\Filter\OutputFilter::cleanText($text));
		}

		if (!$text && $this->config->userdeletetmessage == 1)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_LIB_TABLE_MESSAGES_ERROR_NO_MESSAGE'), 'error');

			return;
		}
		elseif (!$text)
		{
			// Reload message (we don't want to change it).
			$message->load();

			try
			{
				$message->publish(KunenaForum::DELETED);
			}
			catch (\Exception $e)
			{
				$this->app->enqueueMessage($e->getMessage(), 'notice');
			}

			$isMine = $this->me->userid == $message->userid;

			if ($message->publish(KunenaForum::DELETED))
			{
				if ($this->config->log_moderation)
				{
					KunenaLog::log(
						$isMine ? KunenaLog::TYPE_ACTION : KunenaLog::TYPE_MODERATION,
						KunenaLog::LOG_POST_DELETE,
						array('mesid' => $message->id, 'reason' => $fields['modified_reason']),
						$topic->getCategory(),
						$topic,
						!$isMine ? $message->getAuthor() : null
					);
				}

				$this->app->enqueueMessage(Text::_('COM_KUNENA_POST_SUCCESS_DELETE'));
			}

			$this->setRedirect($message->getUrl($this->return, false));

			return;
		}

		$maxlinks = $this->checkMaxLinks($text, $topic);

		if (!$maxlinks)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_TOPIC_SPAM_LINK_PROTECTION'), 'error');
			$this->setRedirectBack();

			return;
		}

		// Activity integration
		$activity = KunenaFactory::getActivityIntegration();
		$activity->onBeforeEdit($message);

		// Save message
		try
		{
			$message->save();
		}
		catch (\Exception $e)
		{
			$this->app->setUserState('com_kunena.postfields', $fields);
			$this->app->enqueueMessage($e->getMessage(), 'error');
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

		$subscribe = Factory::getApplication()->input->getInt('subscribeMe');
		$usertopic = $topic->getUserTopic();

		if ($topic->isAuthorised('subscribe'))
		{
			if ($subscribe)
			{
				$usertopic->subscribed = 1;
			}
			else
			{
				$usertopic->subscribed = 0;
			}

			$usertopic->save();
		}

		$poll_title = $fields['poll_title'];

		if ($poll_title !== null)
		{
			// Save changes into poll
			$poll_options = $fields['poll_options'];
			$poll         = $topic->getPoll();

			if (!empty($poll_options) && !empty($poll_title))
			{
				$poll->title = $poll_title;

				if (!empty($fields['poll_time_to_live']))
				{
					$polltimetolive       = new \Joomla\CMS\Date\Date($fields['poll_time_to_live']);
					$poll->polltimetolive = $polltimetolive->toSql();
				}

				$poll->setOptions($poll_options);

				if (!$topic->poll_id)
				{
					// Create a new poll
					if (!$topic->isAuthorised('poll.create'))
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
						$this->app->enqueueMessage(Text::_('COM_KUNENA_POLL_CREATED'));
					}
				}
				else
				{
					if ($this->config->allow_edit_poll || (!$this->config->allow_edit_poll && !$poll->getUserCount()))
					{
						// Edit existing poll
						if (!$topic->isAuthorised('poll.edit'))
						{
							$this->app->enqueueMessage($topic->getError(), 'notice');
						}
						elseif (!$poll->save())
						{
							$this->app->enqueueMessage($poll->getError(), 'notice');
						}
						else
						{
							$this->app->enqueueMessage(Text::_('COM_KUNENA_POLL_EDITED'));
						}
					}
				}
			}
			elseif ($poll->exists() && $topic->isAuthorised('poll.edit'))
			{
				// Delete poll
				if (!$topic->isAuthorised('poll.delete'))
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
					$this->app->enqueueMessage(Text::_('COM_KUNENA_POLL_DELETED'));
				}
			}
		}

		$activity->onAfterEdit($message);

		$this->app->enqueueMessage(Text::_('COM_KUNENA_POST_SUCCESS_EDIT'));

		if ($message->hold == 1)
		{
			// If user cannot approve message by himself, send email to moderators.
			if (!$topic->isAuthorised('approve'))
			{
				$message->sendNotification();
			}

			$this->app->enqueueMessage(Text::_('COM_KUNENA_GEN_MODERATED'));
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
	 * Remove links in message content
	 *
	 * @param $text
	 *
	 * @since Kunena 5.2.0
	 */
	protected function removeLinksInMessage($text)
	{
		$text = preg_replace('/\[url=(.*?)\](.*?)\[\/url\]/su', '', $text);
		$text = preg_replace('/\[img=(.*?)\](.*?)\[\/img\]/su', '', $text);

		// When the bbcode urls and images are removed just remove the others links
		$text = preg_replace('/(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)(#?[\w \.-]*)(\??[\w \.-]*)(\=?[\w \.-]*)/i', '', $text);

		$this->app->enqueueMessage(Text::_('COM_KUNENA_POST_SAVED_WITHOUT_LINKS_AND_IMAGES'));

		return $text;
	}

	/**
	 * Check if title of topic or message contains URL to limit part of spam
	 *
	 * @param $subject
	 *
	 * @return boolean
	 * @internal param string $usbject
	 * @since    Kunena
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
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 */
	protected function checkMaxLinks($text, $topic)
	{
		$category = $topic->getCategory();

		if ($this->me->isAdmin() || $this->me->isModerator($category))
		{
			return true;
		}

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

			// Ignore internal links
			foreach ($matches[1] as $link)
			{
				$uri  = Uri::getInstance($link);
				$host = $uri->getHost();

				// The cms will catch most of these well
				if (empty($host) || Uri::isInternal($link))
				{
					$countlink--;
				}
			}

			if (!$topic->isAuthorised('approve') && $countlink >= $this->config->max_links + 1)
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 */
	public function thankyou()
	{
		$type = Factory::getApplication()->input->getString('task');
		$this->setThankyou($type);
	}

	/**
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 */
	public function unthankyou()
	{
		$type = Factory::getApplication()->input->getString('task');
		$this->setThankyou($type);
	}

	/**
	 * @param $type
	 *
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 */
	protected function setThankyou($type)
	{
		if (!Session::checkToken('get'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$message = KunenaForumMessageHelper::get($this->mesid);

		if (!$message->isAuthorised($type))
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
			try
			{
				$thankyou->save($this->me);
			}
			catch (Exception $e)
			{
				$this->app->enqueueMessage($e->getMessage());
				$this->setRedirectBack();

				return;
			}

			$this->app->enqueueMessage(Text::_('COM_KUNENA_THANKYOU_SUCCESS'));

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
			$userid = Factory::getApplication()->input->getInt('userid', '0');

			try
			{
				$thankyou->delete($userid);
			}
			catch (Exception $e)
			{
				$this->app->enqueueMessage($e->getMessage());
				$this->setRedirectBack();

				return;
			}

			$this->app->enqueueMessage(Text::_('COM_KUNENA_THANKYOU_REMOVED_SUCCESS'));

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
	 * @since Kunena
	 * @throws Exception
	 * @throws null
	 */
	public function subscribe()
	{
		if (!Session::checkToken('get'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$topic = KunenaForumTopicHelper::get($this->id);

		if ($topic->isAuthorised('read') && $topic->subscribe(1))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_POST_SUBSCRIBED_TOPIC'));

			// Activity integration
			$activity = KunenaFactory::getActivityIntegration();
			$activity->onAfterSubscribe($topic, 1);
		}
		else
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_POST_NO_SUBSCRIBED_TOPIC') . ' ' . $topic->getError(), 'notice');
		}

		$this->setRedirectBack();
	}

	/**
	 * @since Kunena
	 * @throws Exception
	 * @throws null
	 */
	public function unsubscribe()
	{
		if (!Session::checkToken('get'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$topic = KunenaForumTopicHelper::get($this->id);

		if ($topic->isAuthorised('read') && $topic->subscribe(0))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_POST_UNSUBSCRIBED_TOPIC'));

			// Activity integration
			$activity = KunenaFactory::getActivityIntegration();
			$activity->onAfterSubscribe($topic, 0);
		}
		else
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_POST_NO_UNSUBSCRIBED_TOPIC') . ' ' . $topic->getError(), 'notice');
		}

		$this->setRedirectBack();
	}

	/**
	 * @since Kunena
	 * @throws Exception
	 * @throws null
	 */
	public function favorite()
	{
		if (!Session::checkToken('get'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$topic = KunenaForumTopicHelper::get($this->id);

		if ($topic->isAuthorised('read') && $topic->favorite(1))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_POST_FAVORITED_TOPIC'));

			// Activity integration
			$activity = KunenaFactory::getActivityIntegration();
			$activity->onAfterFavorite($topic, 1);
		}
		else
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_POST_NO_FAVORITED_TOPIC') . ' ' . $topic->getError(), 'notice');
		}

		$this->setRedirectBack();
	}

	/**
	 * @since Kunena
	 * @throws Exception
	 * @throws null
	 */
	public function unfavorite()
	{
		if (!Session::checkToken('get'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$topic = KunenaForumTopicHelper::get($this->id);

		if ($topic->isAuthorised('read') && $topic->favorite(0))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_POST_UNFAVORITED_TOPIC'));

			// Activity integration
			$activity = KunenaFactory::getActivityIntegration();
			$activity->onAfterFavorite($topic, 0);
		}
		else
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_POST_NO_UNFAVORITED_TOPIC') . ' ' . $topic->getError(), 'notice');
		}

		$this->setRedirectBack();
	}

	/**
	 * @since Kunena
	 * @throws Exception
	 * @throws null
	 */
	public function sticky()
	{
		if (!Session::checkToken('get'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$topic = KunenaForumTopicHelper::get($this->id);

		if (!$topic->isAuthorised('sticky'))
		{
			$this->app->enqueueMessage($topic->getError(), 'notice');
		}
		elseif ($topic->sticky(1))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_POST_STICKY_SET'));

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
			$this->app->enqueueMessage(Text::_('COM_KUNENA_POST_STICKY_NOT_SET'));
		}

		$this->setRedirectBack();
	}

	/**
	 * @since Kunena
	 * @throws Exception
	 * @throws null
	 */
	public function unsticky()
	{
		if (!Session::checkToken('get'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$topic = KunenaForumTopicHelper::get($this->id);

		if (!$topic->isAuthorised('sticky'))
		{
			$this->app->enqueueMessage($topic->getError(), 'notice');
		}
		elseif ($topic->sticky(0))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_POST_STICKY_UNSET'));

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
			$this->app->enqueueMessage(Text::_('COM_KUNENA_POST_STICKY_NOT_UNSET'));
		}

		$this->setRedirectBack();
	}

	/**
	 * @since Kunena
	 * @throws Exception
	 * @throws null
	 */
	public function lock()
	{
		if (!Session::checkToken('get'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$topic = KunenaForumTopicHelper::get($this->id);

		if (!$topic->isAuthorised('lock'))
		{
			$this->app->enqueueMessage($topic->getError(), 'notice');
		}
		elseif ($topic->lock(1))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_POST_LOCK_SET'));

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
			$this->app->enqueueMessage(Text::_('COM_KUNENA_POST_LOCK_NOT_SET'));
		}

		$this->setRedirectBack();
	}

	/**
	 * @since Kunena
	 * @throws Exception
	 * @throws null
	 */
	public function unlock()
	{
		if (!Session::checkToken('get'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$topic = KunenaForumTopicHelper::get($this->id);

		if (!$topic->isAuthorised('lock'))
		{
			$this->app->enqueueMessage($topic->getError(), 'notice');
		}
		elseif ($topic->lock(0))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_POST_LOCK_UNSET'));

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
			$this->app->enqueueMessage(Text::_('COM_KUNENA_POST_LOCK_NOT_UNSET'));
		}

		$this->setRedirectBack();
	}

	/**
	 * @since Kunena
	 * @throws Exception
	 * @throws null
	 */
	public function delete()
	{
		if (!Session::checkToken('get'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		if ($this->mesid)
		{
			// Delete message
			$message = $target = KunenaForumMessageHelper::get($this->mesid);
			$topic   = $message->getTopic();
			$log     = KunenaLog::LOG_POST_DELETE;
			$hold    = KunenaForum::DELETED;
			$msg     = Text::_('COM_KUNENA_POST_SUCCESS_DELETE');
		}
		else
		{
			// Delete topic
			$topic = $target = KunenaForumTopicHelper::get($this->id);
			$log   = KunenaLog::LOG_TOPIC_DELETE;
			$hold  = KunenaForum::TOPIC_DELETED;
			$msg   = Text::_('COM_KUNENA_TOPIC_SUCCESS_DELETE');
		}

		$category = $topic->getCategory();

		if ($target->isAuthorised('delete') && $target->publish($hold))
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

		if (!$target->isAuthorised('read'))
		{
			if ($target instanceof KunenaForumMessage && $target->getTopic()->isAuthorised('read'))
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
	 * @since Kunena
	 * @throws Exception
	 * @throws null
	 */
	public function undelete()
	{
		if (!Session::checkToken('get'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		if ($this->mesid)
		{
			// Undelete message
			$message = $target = KunenaForumMessageHelper::get($this->mesid);
			$topic   = $message->getTopic();
			$log     = KunenaLog::LOG_POST_UNDELETE;
			$msg     = Text::_('COM_KUNENA_POST_SUCCESS_UNDELETE');
		}
		else
		{
			// Undelete topic
			$topic = $target = KunenaForumTopicHelper::get($this->id);
			$log   = KunenaLog::LOG_TOPIC_UNDELETE;
			$msg   = Text::_('COM_KUNENA_TOPIC_SUCCESS_UNDELETE');
		}

		$category = $topic->getCategory();

		if ($target->isAuthorised('undelete') && $target->publish(KunenaForum::PUBLISHED))
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
	 * @since Kunena
	 * @throws Exception
	 * @throws null
	 */
	public function permdelete()
	{
		if (!Session::checkToken('get'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		if ($this->mesid)
		{
			// Delete message
			$message = $target = KunenaForumMessageHelper::get($this->mesid);
			$topic   = $message->getTopic();
			$log     = KunenaLog::LOG_POST_DESTROY;
			$topic   = KunenaForumTopicHelper::get($target->getTopic());

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
			$log   = KunenaLog::LOG_TOPIC_DESTROY;
		}

		$category = $topic->getCategory();

		if ($topic->isAuthorised('permdelete') && $target->delete())
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
				$this->app->enqueueMessage(Text::_('COM_KUNENA_POST_SUCCESS_DELETE'));
				$url = $topic->getUrl($this->return, false);
			}
			else
			{
				$this->app->enqueueMessage(Text::_('COM_KUNENA_TOPIC_SUCCESS_DELETE'));
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
	 * @since Kunena
	 * @throws Exception
	 * @throws null
	 */
	public function approve()
	{
		if (!Session::checkToken('get'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
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
			$log     = KunenaLog::LOG_POST_APPROVE;
		}
		else
		{
			// Approve topic
			$target  = KunenaForumTopicHelper::get($this->id);
			$message = KunenaForumMessageHelper::get($target->first_post_id);
			$log     = KunenaLog::LOG_TOPIC_APPROVE;
		}

		$topic    = $message->getTopic();
		$category = $topic->getCategory();

		if ($target->isAuthorised('approve') && $target->publish(KunenaForum::PUBLISHED))
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

			$this->app->enqueueMessage(Text::_('COM_KUNENA_MODERATE_APPROVE_SUCCESS'));

			// Only email if message wasn't modified by the author before approval
			// TODO: this is just a workaround for #1862, we need to find better solution.

			$modifiedByAuthor = ($message->modified_by == $message->userid);

			if (!$modifiedByAuthor)
			{
				$target->sendNotification(null, true);
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
	 * @since Kunena
	 * @throws null
	 */
	public function move()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$topicId        = Factory::getApplication()->input->getInt('id', 0);
		$messageId      = Factory::getApplication()->input->getInt('mesid', 0);
		$targetCategory = Factory::getApplication()->input->getInt('targetcategory', 0);
		$targetTopic    = Factory::getApplication()->input->getInt('targettopic', 0);

		if ($targetTopic < 0)
		{
			$targetTopic = Factory::getApplication()->input->getInt('targetid', 0);
		}

		if ($messageId)
		{
			$message = $object = KunenaForumMessageHelper::get($messageId);
			$topic   = $message->getTopic();
		}
		else
		{
			$topic   = $object = KunenaForumTopicHelper::get($topicId);
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

		if (!$object->isAuthorised('move'))
		{
			$error = $object->getError();
		}
		elseif (!$target->isAuthorised('read'))
		{
			$error = $target->getError();
		}
		else
		{
			$changesubject  = Factory::getApplication()->input->getBool('changesubject', false);
			$subject        = Factory::getApplication()->input->getString('subject', '');
			$shadow         = Factory::getApplication()->input->getBool('shadow', false);
			$topic_emoticon = Factory::getApplication()->input->getInt('topic_emoticon', null);
			$keep_poll      = Factory::getApplication()->input->getInt('keep_poll', false);

			if ($object instanceof KunenaForumMessage)
			{
				$mode = Factory::getApplication()->input->getWord('mode', 'selected');

				switch ($mode)
				{
					case 'newer':
						$ids = new \Joomla\CMS\Date\Date($object->time);
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

			$targetobject = $topic->move($target, $ids, $shadow, $subject, $changesubject, $topic_emoticon, $keep_poll);

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
						'move'    => array('id' => $topicId, 'mesid' => $messageId, 'mode' => isset($mode) ? $mode : 'topic'),
						'target'  => array('category_id' => $targetCategory, 'topic_id' => $targetTopic),
						'options' => array('emo' => $topic_emoticon, 'subject' => $subject, 'changeAll' => $changesubject, 'shadow' => $shadow),
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
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ACTION_TOPIC_SUCCESS_MOVE'));
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
	 * @since Kunena
	 * @throws null
	 */
	public function report()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		if (!$this->me->exists() || $this->config->reportmsg == 0)
		{
			// Deny access if report feature has been disabled or user is guest
			$this->app->enqueueMessage(Text::_('COM_KUNENA_NO_ACCESS'), 'notice');
			$this->setRedirectBack();

			return;
		}

		if (!$this->config->get('send_emails'))
		{
			// Emails have been disabled
			$this->app->enqueueMessage(Text::_('COM_KUNENA_EMAIL_DISABLED'), 'notice');
			$this->setRedirectBack();

			return;
		}

		if (!$this->config->getEmail() || !\Joomla\CMS\Mail\MailHelper::isEmailAddress($this->config->getEmail()))
		{
			// Error: email address is invalid
			$this->app->enqueueMessage(Text::_('COM_KUNENA_EMAIL_INVALID'), 'error');
			$this->setRedirectBack();

			return;
		}

		// Get target object for the report
		if ($this->mesid)
		{
			$message = $target = KunenaForumMessageHelper::get($this->mesid);
			$topic   = $target->getTopic();
			$log     = KunenaLog::LOG_POST_REPORT;
		}
		else
		{
			$topic   = $target = KunenaForumTopicHelper::get($this->id);
			$message = KunenaForumMessageHelper::get($topic->first_post_id);
			$log     = KunenaLog::LOG_TOPIC_REPORT;
		}

		if (!$target->isAuthorised('read'))
		{
			// Deny access if user cannot read target
			$this->app->enqueueMessage($target->getError(), 'notice');
			$this->setRedirectBack();

			return;
		}

		$reason = Factory::getApplication()->input->getString('reason');
		$text   = Factory::getApplication()->input->getString('text');

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
					'mesid'   => $message->id,
					'reason'  => $reason,
					'message' => $text,
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
			$this->app->enqueueMessage(Text::_('COM_KUNENA_REPORT_FORG0T_SUB_MES'));
			$this->setRedirectBack();

			return;
		}
		else
		{
			$acl         = KunenaAccess::getInstance();
			$emailToList = $acl->getSubscribers($topic->category_id, $topic->id, false, true, false);

			if (!empty($emailToList))
			{
				$mailnamesender  = !empty($this->config->email_sender_name) ? \Joomla\CMS\Mail\MailHelper::cleanAddress($this->config->email_sender_name) : \Joomla\CMS\Mail\MailHelper::cleanAddress($this->config->board_title . ': ' . $this->me->getName());
				$mailsubject = "[" . $this->config->board_title . " " . Text::_('COM_KUNENA_FORUM') . "] " . Text::_('COM_KUNENA_REPORT_MSG') . ": ";

				if ($reason)
				{
					$mailsubject .= $reason;
				}
				else
				{
					$mailsubject .= $topic->subject;
				}

				jimport('joomla.environment.uri');
				$msglink = Uri::getInstance()->toString(array('scheme', 'host', 'port')) . $target->getPermaUrl(null, false);

				$mail = Factory::getMailer();
				$mail->setSender(array($this->config->getEmail(), $mailnamesender));
				$mail->setSubject($mailsubject);
				$mail->addReplyTo($this->me->email, $this->me->username);

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
					if (!Joomla\CMS\Mail\MailHelper::isEmailAddress($emailTo->email))
					{
						continue;
					}
					else
					{
						$receivers[] = $emailTo->email;
					}
				}

				KunenaEmail::send($mail, $receivers);

				$this->app->enqueueMessage(Text::_('COM_KUNENA_REPORT_SUCCESS'));
			}
			else
			{
				$this->app->enqueueMessage(Text::_('COM_KUNENA_REPORT_NOT_SEND'));
			}
		}

		$this->setRedirect($target->getUrl($this->return, false));
	}

	/**
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 */
	public function vote()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$vote  = Factory::getApplication()->input->getInt('kpollradio', '');
		$id    = Factory::getApplication()->input->getInt('id', 0);
		$catid = Factory::getApplication()->input->getInt('catid', 0);

		$topic = KunenaForumTopicHelper::get($id);
		$poll  = $topic->getPoll();

		if (!$topic->isAuthorised('poll.vote'))
		{
			$this->app->enqueueMessage($topic->getError(), 'error');
		}
		elseif (!$poll->getMyVotes())
		{
			// Give a new vote
			$success = $poll->vote($vote);

			if (!$success)
			{
				$this->app->enqueueMessage($poll->getError(), 'error');
			}
			else
			{
				$this->app->enqueueMessage(Text::_('COM_KUNENA_TOPIC_VOTE_SUCCESS'));
			}
		}
		elseif (!$this->config->pollallowvoteone)
		{
			// Change existing vote
			$success = $poll->vote($vote, true);

			if (!$success)
			{
				$this->app->enqueueMessage($poll->getError(), 'error');
			}
			else
			{
				$this->app->enqueueMessage(Text::_('COM_KUNENA_TOPIC_VOTE_CHANGED_SUCCESS'));
			}
		}

		$this->setRedirect($topic->getUrl($this->return, false));
	}

	/**
	 * @since Kunena
	 * @throws Exception
	 * @throws null
	 */
	public function resetvotes()
	{
		if (!Session::checkToken('get'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
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

		$this->app->enqueueMessage(Text::_('COM_KUNENA_TOPIC_VOTE_RESET_SUCCESS'));
		$this->setRedirect($topic->getUrl($this->return, false));
	}
}
