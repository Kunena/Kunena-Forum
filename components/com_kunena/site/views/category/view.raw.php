<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Site
 * @subpackage    Views
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die ();

/**
 * Category View
 */
class KunenaViewCategory extends KunenaView
{
	function displayDefault($tpl = null)
	{
		$response              = array();
		$response['topiclist'] = array();

		if ($this->me->exists())
		{
			$this->category = $this->get('Category');

			if (!$this->category->authorise('read'))
			{
				$response['error'] = $this->category->getError();
			}
			else
			{
				$topics = $this->get('Topics');

				foreach ($topics as $topic)
				{
					$item                    = new StdClass();
					$item->id                = $topic->id;
					$item->subject           = $topic->subject;
					$response['topiclist'][] = $item;
				}
			}
		}

		// Set the MIME type and header for JSON output.
		$this->document->setMimeEncoding('application/json');
		JResponse::setHeader('Content-Disposition', 'attachment; filename="' . $this->getName() . '.' . $this->getLayout() . '.json"');

		echo json_encode($response);
	}
}
