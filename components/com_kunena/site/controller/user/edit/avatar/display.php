<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Controllers.User
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class ComponentKunenaControllerUserEditAvatarDisplay
 */
class ComponentKunenaControllerUserEditAvatarDisplay extends ComponentKunenaControllerUserEditDisplay
{
	protected $name = 'User/Edit/Avatar';
	protected $imageFilter = '(\.gif|\.png|\.jpg|\.jpeg)$';

	public $gallery;
	public $galleries;
	public $galleryOptions;
	public $headerText;

	protected function before()
	{
		parent::before();

		$avatar = KunenaFactory::getAvatarIntegration();
		if (!($avatar instanceof KunenaAvatarKunena))
		{
			throw new KunenaExceptionAuthorise(JText::_('COM_KUNENA_AUTH_ERROR_USER_EDIT_AVATARS'), 404);
		}

		jimport('joomla.filesystem.folder');

		$path = JPATH_ROOT . '/media/kunena/avatars/gallery';
		$this->gallery = $this->input->getString('gallery', '');
		$this->galleries = $this->getGalleries($path);
		$this->galleryOptions = $this->getGalleryOptions($path);
		$this->galleryImages = isset($this->galleries[$this->gallery]) ? $this->galleries[$this->gallery] : reset($this->galleries);
		$this->galleryUri = JUri::root(true) . '/media/kunena/avatars/gallery';

		$this->headerText = JText::_('COM_KUNENA_PROFILE_EDIT_AVATAR_TITLE');
	}

	protected function prepareDocument()
	{
		$this->setTitle($this->headerText);
	}

	protected function getGalleries($path)
	{
		$files = array();
		$images = $this->getGallery($path);
		if ($images) $files[''] = $images;

		// TODO: Allow recursive paths.
		$folders = JFolder::folders($path);
		foreach($folders as $folder)
		{
			$images = $this->getGallery($folder);
			if ($images)
			{
				foreach($images as $image) $files[$folder][] = "{$folder}/{$image}";
			}
		}
		return $files;
	}

	protected function getGallery($path)
	{
		return JFolder::files($path, $this->imageFilter);
	}

	protected function getGalleryOptions()
	{
		$options = array();
		foreach ($this->galleries as $gallery=>$files)
		{
			$text = $gallery ? JString::ucwords(str_replace('/', ' / ', $gallery)) : JText::_('COM_KUNENA_DEFAULT_GALLERY');
			$options[] = JHtml::_('select.option', $gallery, $text);
		}

		return count($options) > 1 ? $options : array();
	}
}
