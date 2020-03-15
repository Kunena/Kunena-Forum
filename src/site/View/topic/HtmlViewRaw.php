<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Views
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\Exception\ExecutionFailureException;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\CategoryHelper;
use Kunena\Forum\Libraries\Forum\Topic\Rate\RateHelper;
use Kunena\Forum\Libraries\Forum\Topic\TopicHelper;
use Kunena\Forum\Libraries\Html\Parser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Libraries\View\View;

/**
 * Topic View
 *
 * @since   Kunena 6.0
 */
class KunenaViewTopic extends View
{
	/**
	 * @param   null  $tpl  tpl
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function displayEdit($tpl = null)
	{
		$body     = Factory::getApplication()->input->post->get('body', '', 'raw');
		$response = [];

		if ($this->me->exists() || $this->config->pubwrite)
		{
			$msgbody              = Parser::parseBBCode($body, $this);
			$response ['preview'] = $msgbody;
		}

		// Set the MIME type and header for JSON output.
		$this->document->setMimeEncoding('application/json');
		Factory::getApplication()->setHeader('Content-Disposition',
			'attachment; filename="' . $this->getName() . '.' . $this->getLayout() . '.json"'
		);
		Factory::getApplication()->sendHeaders();

		echo json_encode($response);
	}

	/**
	 * Return JSON results of smilies available
	 *
	 * @param   string  $tpl  tpl
	 *
	 * @return  void
	 *
	 * @since   Kunena 4.0
	 *
	 * @throws  Exception
	 */
	public function displayListEmoji($tpl = null)
	{
		$response = [];

		if ($this->me->exists())
		{
			$search = $this->app->input->get('search');

			$db     = Factory::getDBO();
			$kquery = $db->getQuery(true);
			$kquery->select('*')->from("{$db->quoteName('#__kunena_smileys')}")->where("code LIKE '%{$db->escape($search)}%' AND emoticonbar=1");
			$db->setQuery($kquery);

			try
			{
				$smileys = $db->loadObjectList();
			}
			catch (ExecutionFailureException $e)
			{
				KunenaError::displayDatabaseError($e);
			}

			foreach ($smileys as $smiley)
			{
				$emojis['key']  = $smiley->code;
				$emojis['name'] = $smiley->code;
				$emojis['url']  = Uri::root() . 'media/kunena/emoticons/' . $smiley->location;

				$response['emojis'][] = $emojis;
			}
		}

		// Set the MIME type and header for JSON output.
		$this->document->setMimeEncoding('application/json');
		Factory::getApplication()->setHeader('Content-Disposition',
			'attachment; filename="' . $this->getName() . '.' . $this->getLayout() . '.json"'
		);
		Factory::getApplication()->sendHeaders();

		echo json_encode($response);
	}

	/**
	 * Send list of topic icons in JSON for the category set selected
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function displayTopicIcons()
	{
		$catid = $this->app->input->getInt('catid', 0);

		$category         = CategoryHelper::get($catid);
		$category_iconset = $category->iconset;
		$app              = Factory::getApplication();

		if (empty($category_iconset))
		{
			$response = [];

			// Set the MIME type and header for JSON output.
			$this->document->setMimeEncoding('application/json');
			$app->setHeader('Content-Disposition', 'attachment; filename="' . $this->getName() . '.' . $this->getLayout() . '.json"');
			Factory::getApplication()->sendHeaders();

			echo json_encode($response);
		}

		$topicIcons = [];

		$template = KunenaFactory::getTemplate();

		$xmlfile = JPATH_ROOT . '/media/kunena/topic_icons/' . $category_iconset . '/topicicons.xml';

		if (is_file($xmlfile))
		{
			$xml = simplexml_load_file($xmlfile);

			foreach ($xml->icons as $icons)
			{
				$type   = (string) $icons->attributes()->type;
				$width  = (int) $icons->attributes()->width;
				$height = (int) $icons->attributes()->height;

				foreach ($icons->icon as $icon)
				{
					$attributes = $icon->attributes();
					$icon       = new stdClass;
					$icon->id   = (int) $attributes->id;
					$icon->type = (string) $attributes->type ? (string) $attributes->type : $type;
					$icon->name = (string) $attributes->name;

					if ($icon->type != 'user')
					{
						$icon->id = $icon->type . '_' . $icon->name;
					}

					$icon->iconset   = $category_iconset;
					$icon->published = (int) $attributes->published;
					$icon->title     = (string) $attributes->title;
					$icon->b2        = (string) $attributes->b2;
					$icon->b3        = (string) $attributes->b3;
					$icon->fa        = (string) $attributes->fa;
					$icon->filename  = (string) $attributes->src;
					$icon->width     = (int) $attributes->width ? (int) $attributes->width : $width;
					$icon->height    = (int) $attributes->height ? (int) $attributes->height : $height;
					$icon->path      = Uri::root() . 'media/kunena/topic_icons/' . $category_iconset . '/' . $icon->filename;
					$icon->relpath   = $template->getTopicIconPath("{$icon->filename}", false);
					$topicIcons[]    = $icon;
				}
			}
		}

		// Set the MIME type and header for JSON output.
		$this->document->setMimeEncoding('application/json');
		$app->setHeader('Content-Disposition', 'attachment; filename="' . $this->getName() . '.' . $this->getLayout() . '.json"');
		Factory::getApplication()->sendHeaders();

		echo json_encode($topicIcons);
	}

	/**
	 * Load global rate for the topic
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function displayGetrate()
	{
		$user = Factory::getApplication()->getIdentity();

		$topicid  = $this->app->input->get('topic_id', 0, 'int');
		$response = [];
		$app      = Factory::getApplication();

		if ($user->id == 0 || TopicHelper::get($topicid)->first_post_userid == $this->me->userid)
		{
			$response = RateHelper::getSelected($topicid);
		}
		else
		{
			$response = RateHelper::getRate($topicid, $user->id);
		}

		// Set the MIME type and header for JSON output.
		$this->document->setMimeEncoding('application/json');
		$app->setHeader('Content-Disposition', 'attachment; filename="' . $this->getName() . '.' . $this->getLayout() . '.json"');
		Factory::getApplication()->sendHeaders();

		echo json_encode($response);
	}

	/**
	 * Save rate for user logged in by JSON call
	 *
	 * @param   null  $tpl  tpl
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function displayRate($tpl = null)
	{
		$starid   = $this->app->input->get('starid', 0, 'int');
		$topicid  = $this->app->input->get('topic_id', 0, 'int');
		$response = [];
		$app      = Factory::getApplication();
		$user     = KunenaUserHelper::getMyself();

		if ($user->exists() || $this->config->ratingenabled)
		{
			$rate           = RateHelper::get($topicid);
			$rate->stars    = $starid;
			$rate->topic_id = $topicid;

			$response = $rate->save($this->me);

			$selected = RateHelper::getSelected($topicid);

			$topic         = TopicHelper::get($topicid);
			$topic->rating = $selected;
			$topic->save();
		}

		// Set the MIME type and header for JSON output.
		$this->document->setMimeEncoding('application/json');
		$app->setHeader('Content-Disposition', 'attachment; filename="' . $this->getName() . '.' . $this->getLayout() . '.json"');
		Factory::getApplication()->sendHeaders();

		echo $response;
	}

	/**
	 * Return the template text corresponding to the category selected
	 *
	 * @param   null  $tpl  tpl
	 *
	 * @return  void
	 *
	 * @since   Kunena 5.1
	 *
	 * @throws  Exception
	 */
	public function displayCategorytemplatetext($tpl = null)
	{
		$app      = Factory::getApplication();
		$catid    = $this->app->input->getInt('catid', 0);
		$response = '';

		$category = CategoryHelper::get($catid);

		$response = $category->topictemplate;

		// Set the MIME type and header for JSON output.
		$this->document->setMimeEncoding('application/json');
		$app->setHeader('Content-Disposition', 'attachment; filename="' . $this->getName() . '.' . $this->getLayout() . '.json"');
		Factory::getApplication()->sendHeaders();

		echo json_encode($response);
	}
}
