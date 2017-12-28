<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.User
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\String\StringHelper;

/**
 * Class ComponentKunenaControllerUserEditAvatarDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerUserEditAvatarDisplay extends ComponentKunenaControllerUserEditDisplay
{
	protected $name = 'User/Edit/Avatar';

	protected $imageFilter = '(\.gif|\.png|\.jpg|\.jpeg)$';

	public $gallery;

	public $galleries;

	public $galleryOptions;

	public $galleryImages;

	public $headerText;

	/**
	 * Prepare avatar form.
	 *
	 * @return void
	 *
	 * @throws KunenaExceptionAuthorise
	 */
	protected function before()
	{
		parent::before();

		$avatar = KunenaFactory::getAvatarIntegration();

		if (!($avatar instanceof KunenaAvatarKunena))
		{
			throw new KunenaExceptionAuthorise(JText::_('COM_KUNENA_AUTH_ERROR_USER_EDIT_AVATARS'), 404);
		}

		$path = JPATH_ROOT . '/media/kunena/avatars/gallery';
		$this->gallery = $this->input->getString('gallery', '');
		$this->galleries = $this->getGalleries($path);
		$this->galleryOptions = $this->getGalleryOptions($path);
		$this->galleryImages = isset($this->galleries[$this->gallery])
			? $this->galleries[$this->gallery]
			: reset($this->galleries);
		$this->galleryUri = JUri::root(true) . '/media/kunena/avatars/gallery';

		$this->headerText = JText::_('COM_KUNENA_PROFILE_EDIT_AVATAR_TITLE');
	}

	/**
	 * Prepare document.
	 *
	 * @return void
	 */
	protected function prepareDocument()
	{
		$this->setTitle($this->headerText);
	}

	/**
	 * Get avatar gallery directories.
	 *
	 * @param   string  $path  Absolute path for the gallery.
	 *
	 * @return  array|string[]  List of directories.
	 */
	protected function getGalleries($path)
	{
		$files = array();
		$images = $this->getGallery($path);

		if ($images)
		{
			$files[''] = $images;
		}

		// TODO: Allow recursive paths.
		$folders = KunenaFolder::folders($path);

		foreach ($folders as $folder)
		{
			$images = $this->getGallery("{$path}/{$folder}");

			if ($images)
			{
				foreach ($images as $image)
				{
					$files[$folder][] = "{$folder}/{$image}";
				}
			}
		}

		return $files;
	}

	/**
	 * Get files from selected gallery.
	 *
	 * @param   string  $path  Absolute path for the gallery.
	 *
	 * @return  array
	 */
	protected function getGallery($path)
	{
		return KunenaFolder::files($path, $this->imageFilter);
	}

	/**
	 * Get avatar galleries and make them select option list.
	 *
	 * @return array|string[]  List of options.
	 */
	protected function getGalleryOptions()
	{
		$options = array();

		foreach ($this->galleries as $gallery => $files)
		{
			$text = $gallery ? StringHelper::ucwords(str_replace('/', ' / ', $gallery)) : JText::_('COM_KUNENA_DEFAULT_GALLERY');
			$options[] = JHtml::_('select.option', $gallery, $text);
		}

		return count($options) > 1 ? $options : array();
	}
}
