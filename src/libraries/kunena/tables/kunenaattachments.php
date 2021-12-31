<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    Tables
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;

require_once __DIR__ . '/kunena.php';

/**
 * Kunena Attachments Table
 * Provides access to the #__kunena_attachments table
 * @since Kunena
 */
class TableKunenaAttachments extends KunenaTable
{
	/**
	 * @var null
	 * @since Kunena
	 */
	public $id = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $userid = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $mesid = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $protected = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $hash = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $size = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $folder = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $filetype = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $filename = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $filename_real = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $comment = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $inline = null;

	/**
	 * @param   JDatabaseDriver $db Database driver
	 *
	 * @since Kunena
	 */
	public function __construct($db)
	{
		parent::__construct('#__kunena_attachments', 'id', $db);
	}

	/**
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 */
	public function check()
	{
		$user    = KunenaUserHelper::get($this->userid);
		$message = KunenaForumMessageHelper::get($this->mesid);

		if ($this->userid != 0 && !$user->exists())
		{
			$this->setError(Text::sprintf('COM_KUNENA_LIB_TABLE_ATTACHMENTS_ERROR_USER_INVALID', (int) $user->userid));
		}

		if ($message->id && !$message->exists())
		{
			$this->setError(Text::sprintf('COM_KUNENA_LIB_TABLE_ATTACHMENTS_ERROR_MESSAGE_INVALID', (int) $message->id));
		}

		$this->folder = trim($this->folder, '/');

		if (!$this->folder)
		{
			$this->setError(Text::_('COM_KUNENA_LIB_TABLE_ATTACHMENTS_ERROR_NO_FOLDER'));
		}

		if (!$this->filename)
		{
			$this->setError(Text::_('COM_KUNENA_LIB_TABLE_ATTACHMENTS_ERROR_NO_FILENAME'));
		}

		if (!$this->filename_real)
		{
			$this->filename_real = $this->filename;
		}

		$file = JPATH_ROOT . "/{$this->folder}/{$this->filename}";

		if (!is_file($file))
		{
			$this->setError(Text::sprintf('COM_KUNENA_LIB_TABLE_ATTACHMENTS_ERROR_FILE_MISSING', "{$this->folder}/{$this->filename}"));
		}
		else
		{
			if (!$this->hash)
			{
				$this->hash = md5_file($file);
			}

			if (!$this->size)
			{
				$this->size = filesize($file);
			}

			if (!$this->filetype)
			{
				$this->filetype = KunenaFile::getMime($file);
			}
		}

		return $this->getError() == '';
	}
}
