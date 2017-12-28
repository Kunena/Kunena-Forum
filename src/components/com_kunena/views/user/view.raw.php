<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Site
 * @subpackage  Views
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Users View
 */
class KunenaViewUser extends KunenaView
{
	/**
	 * @param   null $tpl
	 *
	 * @throws Exception
	 */
	function displayList($tpl = null)
	{
		$response = array();

		if ($this->me->exists())
		{
			$users = $this->get('Items');

			foreach ($users as $user)
			{
				if ($this->config->username)
				{
					$response[] = $user->username;
				}
				else
				{
					$response[] = $user->name;
				}
			}
		}

		// Set the MIME type and header for JSON output.
		$this->document->setMimeEncoding('application/json');
		JFactory::getApplication()->sendHeaders('Content-Disposition', 'attachment; filename="' . $this->getName() . '.' . $this->getLayout() . '.json"');

		echo json_encode($response);
	}

	/**
	 * Method to return list of users by ajax request for userlist and search user
	 *
	 * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @since K4.0
	 * @return JSon
	 */
	public function displayListMention($tpl = null)
	{
		$response = array();

		if ($this->me->exists())
		{
			$users = $this->get('Items');

			foreach ($users as $user)
			{
				$user_obj = new stdClass;

				$user_obj->id    = $user->id;
				$user_obj->photo = $user->getAvatarURL();
				$user_obj->name  = $user->username;

				$response[] = $user_obj;
			}
		}

		// Set the MIME type and header for JSON output.
		$this->document->setMimeEncoding('application/json');
		JFactory::getApplication()->sendHeaders('Content-Disposition', 'attachment; filename="' . $this->getName() . '.' . $this->getLayout() . '.json"');

		echo json_encode($response);
	}

	/**
	 * Return the list of files for the avatar gallery selected by the user
	 *
	 * @since K5.0
	 * @return JSON
	 */
	public function displayGalleryimages()
	{
		$response = array();

		$gallery_name = $this->app->input->get('gallery_name', null, 'string');

		jimport( 'joomla.filesystem.folder' );

		$list_files = JFolder::files(JPATH_BASE . '/media/kunena/avatars/gallery/' . $gallery_name);

		foreach($list_files as $key => $file)
		{
			$response[$key]['filename'] = $file;
			$response[$key]['url'] = JUri::root() . 'media/kunena/avatars/gallery/' . $gallery_name . '/' . $file;
		}

		// Set the MIME type and header for JSON output.
		$this->document->setMimeEncoding('application/json');
		JFactory::getApplication()->sendHeaders('Content-Disposition', 'attachment; filename="' . $this->getName() . '.' . $this->getLayout() . '.json"');

		echo json_encode($response);
	}
}
