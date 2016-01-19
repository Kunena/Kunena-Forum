
<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Site
 * @subpackage    Views
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          http://www.kunena.org
 **/
defined('_JEXEC') or die ();

/**
 * Topic View
 */
class KunenaViewTopic extends KunenaView
{
	function displayEdit($tpl = null)
	{
		$body     = JRequest::getVar('body', '', 'post', 'string', JREQUEST_ALLOWRAW); // RAW input
		$response = array();

		if ($this->me->exists() || $this->config->pubwrite)
		{
			$msgbody              = KunenaHtmlParser::parseBBCode($body, $this);
			$response ['preview'] = $msgbody;
		}

		// Set the MIME type and header for JSON output.
		$this->document->setMimeEncoding('application/json');
		JResponse::setHeader('Content-Disposition', 'attachment; filename="' . $this->getName() . '.' . $this->getLayout() . '.json"');

		echo json_encode($response);
	}

	/**
	 *    Return JSON results of smilies available
	 *
	 * @param string $tpl
	 *
	 * @since K4.0
	 *
	 * @return void
	 */
	public function displayListEmoji($tpl = null)
	{
		$response = array();

		if ($this->me->exists())
		{
			$search = $this->app->input->get('search');

			$db     = JFactory::getDBO();
			$kquery = new KunenaDatabaseQuery;
			$kquery->select('*')->from("{$db->qn('#__kunena_smileys')}")->where("code LIKE '%{$db->escape($search)}%' AND emoticonbar=1");
			$db->setQuery($kquery);
			$smileys = $db->loadObjectList();
			KunenaError::checkDatabaseError();

			foreach ($smileys as $smiley)
			{
				$emojis['key']  = $smiley->code;
				$emojis['name'] = $smiley->code;
				$emojis['url']  = JUri::root() . 'media/kunena/emoticons/' . $smiley->location;

				$response['emojis'][] = $emojis;
			}
		}

		// Set the MIME type and header for JSON output.
		$this->document->setMimeEncoding('application/json');
		JResponse::setHeader('Content-Disposition', 'attachment; filename="' . $this->getName() . '.' . $this->getLayout() . '.json"');

		echo json_encode($response);
	}

	/**
	 * Send list of topic icons in JSON for the category set selected
	 *
	 * @return string
	 */
	public function displayTopicIcons()
	{
		jimport('joomla.filesystem.folder');

		$catid = $this->app->input->getInt('catid', 0);

		$category = KunenaForumCategoryHelper::get($catid);
		$category_iconset = $category->iconset;

		if ( empty($category_iconset) )
		{
			$response = array();

			// Set the MIME type and header for JSON output.
			$this->document->setMimeEncoding('application/json');
			JResponse::setHeader('Content-Disposition', 'attachment; filename="' . $this->getName() . '.' . $this->getLayout() . '.json"');

			echo json_encode($response);
		}

		$topicIcons = array();

		$template = KunenaFactory::getTemplate();

		$xmlfile = JPATH_ROOT . '/media/kunena/topic_icons/'. $category_iconset .'/topicicons.xml';

		if (is_file($xmlfile))
		{
			$xml = simplexml_load_file($xmlfile);

			foreach($xml->icons as $icons)
			{
				$type = (string) $icons->attributes()->type;
				$width = (int) $icons->attributes()->width;
				$height = (int) $icons->attributes()->height;

				foreach($icons->icon as $icon)
				{
					$attributes = $icon->attributes();
					$icon = new stdClass();
					$icon->id = (int) $attributes->id;
					$icon->type = (string) $attributes->type ? (string) $attributes->type : $type;
					$icon->name = (string) $attributes->name;

					if ($icon->type != 'user')
					{
						$icon->id = $icon->type.'_'.$icon->name;
					}

					$icon->iconset = $category_iconset;
					$icon->published = (int) $attributes->published;
					$icon->title = (string) $attributes->title;
					$icon->b2 = (string) $attributes->b2;
					$icon->b3  = (string) $attributes->b3;
					$icon->fa  = (string) $attributes->fa;
					$icon->filename = (string) $attributes->src;
					$icon->width = (int) $attributes->width ? (int) $attributes->width : $width;
					$icon->height = (int) $attributes->height ? (int) $attributes->height : $height;
					$icon->path = JURI::root() . 'media/kunena/topic_icons/' . $category_iconset . '/' . $icon->filename;
					$icon->relpath = $template->getTopicIconPath("{$icon->filename}", false, $category_iconset);
					$topicIcons[] = $icon;
				}

			}
		}

		// Set the MIME type and header for JSON output.
		$this->document->setMimeEncoding('application/json');
		JResponse::setHeader('Content-Disposition', 'attachment; filename="' . $this->getName() . '.' . $this->getLayout() . '.json"');

		echo json_encode($topicIcons);

	}
}
