<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Views
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;

/**
 * Category View
 * @since Kunena
 */
class KunenaViewCategory extends KunenaView
{
	/**
	 * @param   null $tpl tpl
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	public function displayDefault($tpl = null)
	{
		$response              = array();
		$response['topiclist'] = array();

		if ($this->me->exists())
		{
			$this->category = $this->get('Category');

			if (!$this->category->isAuthorised('read'))
			{
				$response['error'] = $this->category->getError();
			}
			else
			{
				$topics = $this->get('Topics');

				foreach ($topics as $topic)
				{
					$item                    = new StdClass;
					$item->id                = $topic->id;
					$item->subject           = $topic->subject;
					$response['topiclist'][] = $item;
				}
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
}
