<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Views
 *
 * @copyright       Copyright (C) 2008 - 2017 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Topic View
 * @since Kunena
 */
class KunenaViewTopic extends KunenaView
{
	/**
	 * @param   null $tpl
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	function displayEdit($tpl = null)
	{
		$body     = \Joomla\CMS\Factory::getApplication()->input->post->get('body', '', 'raw');
		$response = array();

		if ($this->me->exists() || $this->config->pubwrite)
		{
			$msgbody              = KunenaHtmlParser::parseBBCode($body, $this);
			$response ['preview'] = $msgbody;
		}

		// Set the MIME type and header for JSON output.
		$this->document->setMimeEncoding('application/json');
		\Joomla\CMS\Factory::getApplication()->setHeader('Content-Disposition',
            'attachment; filename="' . $this->getName() . '.' . $this->getLayout() . '.json"'
        );
		\Joomla\CMS\Factory::getApplication()->sendHeaders();

		echo json_encode($response);
	}

	/**
	 *    Return JSON results of smilies available
	 *
	 * @param   string $tpl
	 *
	 * @return void
	 * @throws Exception
	 * @since K4.0
	 *
	 * @since Kunena
	 */
	public function displayListEmoji($tpl = null)
	{
		$response = array();

		if ($this->me->exists())
		{
			$search = $this->app->input->get('search');

			$db     = \Joomla\CMS\Factory::getDBO();
			$kquery = new KunenaDatabaseQuery;
			$kquery->select('*')->from("{$db->qn('#__kunena_smileys')}")->where("code LIKE '%{$db->escape($search)}%' AND emoticonbar=1");
			$db->setQuery($kquery);

			try
			{
				$smileys = $db->loadObjectList();
			}
			catch (JDatabaseExceptionExecuting $e)
			{
				KunenaError::displayDatabaseError($e);
			}

			foreach ($smileys as $smiley)
			{
				$emojis['key']  = $smiley->code;
				$emojis['name'] = $smiley->code;
				$emojis['url']  = \Joomla\CMS\Uri\Uri::root() . 'media/kunena/emoticons/' . $smiley->location;

				$response['emojis'][] = $emojis;
			}
		}

		// Set the MIME type and header for JSON output.
		$this->document->setMimeEncoding('application/json');
		\Joomla\CMS\Factory::getApplication()->setHeader('Content-Disposition',
            'attachment; filename="' . $this->getName() . '.' . $this->getLayout() . '.json"'
        );
		\Joomla\CMS\Factory::getApplication()->sendHeaders();

		echo json_encode($response);
	}

	/**
	 * Send list of topic icons in JSON for the category set selected
	 *
	 * @return void
	 * @throws Exception
	 * @since Kunena
	 */
	public function displayTopicIcons()
	{
		jimport('joomla.filesystem.folder');

		$catid = $this->app->input->getInt('catid', 0);

		$category         = KunenaForumCategoryHelper::get($catid);
		$category_iconset = $category->iconset;
		$app              = \Joomla\CMS\Factory::getApplication();

		if (empty($category_iconset))
		{
			$response = array();

			// Set the MIME type and header for JSON output.
			$this->document->setMimeEncoding('application/json');
			$app->setHeader('Content-Disposition', 'attachment; filename="' . $this->getName() . '.' . $this->getLayout() . '.json"');

			echo json_encode($response);
		}

		$topicIcons = array();

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
					$icon->path      = \Joomla\CMS\Uri\Uri::root() . 'media/kunena/topic_icons/' . $category_iconset . '/' . $icon->filename;
					$icon->relpath   = $template->getTopicIconPath("{$icon->filename}", false);
					$topicIcons[]    = $icon;
				}
			}
		}

		// Set the MIME type and header for JSON output.
		$this->document->setMimeEncoding('application/json');
		$app->setHeader('Content-Disposition', 'attachment; filename="' . $this->getName() . '.' . $this->getLayout() . '.json"');

		echo json_encode($topicIcons);
	}

	/**
	 * Load global rate for the topic
	 * @since Kunena
	 * @throws Exception
	 */
	public function displayGetrate()
	{
		$user = \Joomla\CMS\Factory::getUser();

		$topicid  = $this->app->input->get('topic_id', 0, 'int');
		$response = array();
		$app      = \Joomla\CMS\Factory::getApplication();

		if ($user->id == 0)
		{
			$response = KunenaForumTopicRateHelper::getSelected($topicid);
		}
		else
		{
			$rating = KunenaForumTopicRate::getInstance($topicid);

			$response = $rating->getTopicUserRate();
		}

		// Set the MIME type and header for JSON output.
		$this->document->setMimeEncoding('application/json');
		$app->setHeader('Content-Disposition', 'attachment; filename="' . $this->getName() . '.' . $this->getLayout() . '.json"');

		echo json_encode($response);
	}

	/**
	 * Save rate for user logged in by JSON call
	 *
	 * @param   null $tpl
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	public function displayRate($tpl = null)
	{
		$starid   = $this->app->input->get('starid', 0, 'int');
		$topicid  = $this->app->input->get('topic_id', 0, 'int');
		$response = array();
		$app      = \Joomla\CMS\Factory::getApplication();

		if ($this->me->exists() || $this->config->ratingenabled)
		{
			$rate           = KunenaForumTopicRateHelper::get($topicid);
			$rate->stars    = $starid;
			$rate->topic_id = $topicid;

			$response = $rate->save($this->me);

			$selected = KunenaForumTopicRateHelper::getSelected($topicid);

			$topic         = KunenaForumTopicHelper::get($topicid);
			$topic->rating = $selected;
			$topic->save();
		}

		// Set the MIME type and header for JSON output.
		$this->document->setMimeEncoding('application/json');
		$app->setHeader('Content-Disposition', 'attachment; filename="' . $this->getName() . '.' . $this->getLayout() . '.json"');

		echo $response;
	}

	/**
	 * Return the template text corresponding to the category selected
	 *
	 * @param   null $tpl
	 *
	 * @throws Exception
	 * @since Kunena 5.1
	 */
	public function displayCategorytemplatetext($tpl = null)
	{
		$app      = \Joomla\CMS\Factory::getApplication();
		$catid    = $this->app->input->getInt('catid', 0);
		$response = '';

		$category = KunenaForumCategoryHelper::get($catid);

		$response = $category->topictemplate;

		// Set the MIME type and header for JSON output.
		$this->document->setMimeEncoding('application/json');
		$app->setHeader('Content-Disposition', 'attachment; filename="' . $this->getName() . '.' . $this->getLayout() . '.json"');

		echo json_encode($response);
	}
}
