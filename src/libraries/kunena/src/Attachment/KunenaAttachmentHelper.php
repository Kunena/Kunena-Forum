<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Attachment
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Attachment;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\Database\Exception\ExecutionFailureException;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use RuntimeException;

/**
 * Kunena Attachment Helper Class
 *
 * @since   Kunena 6.0
 */
abstract class KunenaAttachmentHelper
{
	/**
	 * @var     KunenaAttachment[]
	 * @since   Kunena 6.0
	 */
	protected static $_instances = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected static $_messages = [];

	/**
	 * @param   bool|array|int  $ids        ids
	 * @param   string          $authorise  authorise
	 *
	 * @return  KunenaAttachment[]
	 *
	 * @throws  Exception
	 * @throws  null
	 * @since   Kunena 6.0
	 */
	public static function getById($ids = false, $authorise = 'read'): array
	{
		if ($ids === false)
		{
			return self::$_instances;
		}

		if (\is_array($ids))
		{
			$ids = array_unique($ids);
		}
		else
		{
			$ids = [$ids];
		}

		if (empty($ids))
		{
			return [];
		}

		self::loadById($ids);

		$list = [];

		foreach ($ids as $id)
		{
			if (!empty(self::$_instances [$id]) && self::$_instances [$id]->isAuthorised($authorise))
			{
				$list [$id] = self::$_instances [$id];
			}
		}

		return $list;
	}

	/**
	 * @param   array  $ids  ids
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	protected static function loadById(array $ids): void
	{
		foreach ($ids as $i => $id)
		{
			if (isset(self::$_instances [$id]))
			{
				unset($ids[$i]);
			}
		}

		if (empty($ids))
		{
			return;
		}

		$idlist = implode(',', $ids);
		$db     = Factory::getContainer()->get('DatabaseDriver');
		$query  = $db->getQuery(true);
		$query->select('*')
			->from($db->quoteName('#__kunena_attachments'))
			->where($db->quoteName('id') . ' IN (' . $idlist . ')');
		$db->setQuery($query);

		try
		{
			$results = (array) $db->loadObjectList('id');
		}
		catch (RuntimeException $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		foreach ($ids as $id)
		{
			if (isset($results[$id]))
			{
				$instance                               = self::get($id);
				self::$_instances[$id]                  = $instance;
				self::$_messages[$instance->mesid][$id] = $instance;
			}
			else
			{
				self::$_instances[$id] = null;
			}
		}

		unset($results);
	}

	/**
	 * Returns Attachment object.
	 *
	 * @param   int   $identifier  The attachment to load - Can be only an integer.
	 * @param   bool  $reload      reloaded
	 *
	 * @return  KunenaAttachment
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public static function get($identifier = null, $reload = false): KunenaAttachment
	{
		if ($identifier instanceof KunenaAttachment)
		{
			return $identifier;
		}

		$id = (int) $identifier;

		if ($id < 1)
		{
			return new KunenaAttachment;
		}

		if (empty(self::$_instances[$id]))
		{
			$instance = new KunenaAttachment;

			// Only load messages which haven't been preloaded before (including missing ones).
			$instance->load(!\array_key_exists($id, self::$_instances) ? $id : null);
			$instance->id          = $id;
			self::$_instances[$id] = $instance;
		}
		elseif ($reload)
		{
			self::$_instances[$id]->load();
		}

		return self::$_instances[$id];
	}

	/**
	 * Get the number of the attachments in the message
	 *
	 * @param   bool|string  $ids  ids
	 *
	 * @return  KunenaAttachment[]
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public static function getNumberAttachments($ids = false): array
	{
		$ids = [$ids];

		self::loadByMessage($ids);

		$list = [];

		foreach ($ids as $id)
		{
			if (!empty(self::$_messages [$id]))
			{
				$list = self::$_messages [$id];
			}
		}

		return $list;
	}

	/**
	 * @param   array  $ids  ids
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	protected static function loadByMessage(array $ids): void
	{
		foreach ($ids as $i => $id)
		{
			$id = \intval($id);

			if (!empty(self::$_messages[$id]))
			{
				unset($ids[$i]);
			}

			if (!$id)
			{
				unset($ids[$i]);
			}
		}

		if (empty($ids))
		{
			return;
		}

		$idlist = implode(',', $ids);
		$db     = Factory::getContainer()->get('DatabaseDriver');
		$query  = $db->getQuery(true);
		$query->select('*')
			->from($db->quoteName('#__kunena_attachments'))
			->where($db->quoteName('mesid') . ' IN (' . $idlist . ')');
		$db->setQuery($query);

		try
		{
			$results = (array) $db->loadObjectList('id');
		}
		catch (RuntimeException $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		foreach ($ids as $mesid)
		{
			if (!isset(self::$_messages [$mesid]))
			{
				self::$_messages [$mesid] = [];
			}
		}

		if (!empty($results))
		{
			foreach ($results as $id => $instance)
			{
				$instance                                = self::get($id);
				self::$_instances [$id]                  = $instance;
				self::$_messages [$instance->mesid][$id] = $instance;
			}
		}

		unset($results);
	}

	/**
	 * @param   bool|array|int  $ids        ids
	 * @param   string          $authorise  authorise
	 *
	 * @return  KunenaAttachment[]
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public static function getByMessage($ids = false, $authorise = 'read'): array
	{
		if ($ids === false)
		{
			return self::$_instances;
		}

		if (\is_array($ids))
		{
			$ids2 = [];

			foreach ($ids as $id)
			{
				if ($id instanceof KunenaMessage)
				{
					$id = $id->id;
				}

				$ids2[(int) $id] = (int) $id;
			}

			$ids = $ids2;
		}
		else
		{
			$ids = [$ids];
		}

		if (empty($ids))
		{
			return [];
		}

		self::loadByMessage($ids);

		$list = [];

		foreach ($ids as $id)
		{
			if (!empty(self::$_messages [$id]))
			{
				foreach (self::$_messages [$id] as $instance)
				{
					if ($instance->isAuthorised($authorise))
					{
						$list [$instance->id] = $instance;
					}
				}
			}
		}

		return $list;
	}

	/**
	 * Find filename which isn't already taken in the filesystem.
	 *
	 * @param   string  $folder     Relative path from JPATH_ROOT.
	 * @param   string  $basename   Filename without extension.
	 * @param   string  $extension  File extension.
	 * @param   null    $protected  True to randomize the filename. If not given, uses Kunena configuration setting.
	 *
	 * @return  string
	 *
	 * @throws Exception
	 * @since   Kunena 4.0
	 */
	public static function getAvailableFilename(string $folder, string $basename, string $extension, $protected = null): string
	{
		if (\is_null($protected))
		{
			$protected = (bool) KunenaConfig::getInstance()->attachmentProtection;
		}

		if ($protected)
		{
			// Ignore proposed filename and return totally random and unique name without file extension.
			do
			{
				$name = md5(rand());
			}
			while (file_exists(JPATH_ROOT . "/$folder/$name"));

			return $name;
		}

		// Lets find out if we need to rename the filename.
		$basename  = preg_replace('/[[:space:]]/', '', File::makeSafe($basename));
		$extension = trim($extension, '.');

		if (empty($basename))
		{
			$basename = 'file_' . substr(md5(rand()), 2, 7);
		}

		$newName = "{$basename}.{$extension}";
		$date    = date('Y-m-d');

		// Rename file if there is already one with the same name
		if (file_exists(JPATH_ROOT . "/{$folder}/{$newName}"))
		{
			$newName = "{$basename}_{$date}.{$extension}";

			for ($i = 2; file_exists(JPATH_ROOT . "/{$folder}/{$newName}"); $i++)
			{
				$newName = "{$basename}_{$date}-{$i}.{$extension}";
			}
		}

		return $newName;
	}

	/**
	 * @param   mixed  $category  category
	 * @param   null   $user      user
	 *
	 * @return  array|boolean
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public static function getExtensions($category, $user = null)
	{
		$imageTypes = self::getImageExtensions($category, $user);
		$fileTypes  = self::getFileExtensions($category, $user);

		if ($imageTypes === false && $fileTypes === false)
		{
			return false;
		}

		return (array) array_merge((array) $imageTypes, (array) $fileTypes);
	}

	/**
	 * @param   mixed  $category  category
	 * @param   mixed  $user      user
	 *
	 * @return  array|boolean
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public static function getImageExtensions($category = null, $user = null)
	{
		if ($category !== null)
		{
			$category = KunenaCategoryHelper::get($category);
		}

		$user   = KunenaUserHelper::get($user);
		$config = KunenaFactory::getConfig();
		$types  = explode(',', $config->imageTypes);

		foreach ($types as &$type)
		{
			$type = trim($type);

			if (empty($type))
			{
				unset($type);
			}
		}

		// Check if attachments are allowed at all
		if (!$config->imageUpload)
		{
			return false;
		}

		if ($config->imageUpload == 'everybody')
		{
			return $types;
		}

		// For now on we only allow registered users
		if (!$user->exists())
		{
			return false;
		}

		if ($config->imageUpload == 'registered')
		{
			return $types;
		}

		// For now on we only allow moderators
		if (!$user->isModerator($category))
		{
			return false;
		}

		if ($config->imageUpload == 'moderator')
		{
			return $types;
		}

		// For now on we only allow administrators
		if (!$user->isAdmin($category))
		{
			return false;
		}

		if ($config->imageUpload == 'admin')
		{
			return $types;
		}

		return false;
	}

	/**
	 * @param   mixed  $category  category
	 * @param   mixed  $user      user
	 *
	 * @return  array|boolean
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public static function getFileExtensions($category = null, $user = null)
	{
		$category = KunenaCategoryHelper::get($category);
		$user     = KunenaUserHelper::get($user);
		$config   = KunenaFactory::getConfig();
		$types    = explode(',', $config->fileTypes);

		foreach ($types as &$type)
		{
			$type = trim($type);

			if (empty($type))
			{
				unset($type);
			}
		}

		// Check if attachments are allowed at all
		if (!$config->fileUpload)
		{
			return false;
		}

		if ($config->fileUpload == 'everybody')
		{
			return $types;
		}

		// For now on we only allow registered users
		if (!$user->exists())
		{
			return false;
		}

		if ($config->fileUpload == 'registered')
		{
			return $types;
		}

		// For now on we only allow moderators
		if (!$user->isModerator($category))
		{
			return false;
		}

		if ($config->fileUpload == 'moderator')
		{
			return $types;
		}

		// For now on we only allow administrators
		if (!$user->isAdmin($category))
		{
			return false;
		}

		if ($config->fileUpload == 'admin')
		{
			return $types;
		}

		return false;
	}

	/**
	 * @return bool
	 *
	 * @since   Kunena 6.0
	 * @throws \Exception
	 */
	public static function cleanup(): bool
	{
		// Find up to 50 orphan attachments and delete them
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->select('a.*')
			->from($db->quoteName('#__kunena_attachments', 'a'))
			->leftJoin($db->quoteName('#__kunena_messages', 'm') . ' ON a.mesid = m.id')
			->where($db->quoteName('m.id') . ' IS NULL');
		$query->setLimit(50);
		$db->setQuery($query);

		try
		{
			$results = (array) $db->loadObjectList('id', 'Attachment');
		}
		catch (ExecutionFailureException $e)
		{
			KunenaError::displayDatabaseError($e);

			return false;
		}

		if (empty($results))
		{
			return true;
		}

		foreach ($results as $instance)
		{
			$instance->exists(false);
			unset($instance);
		}

		$ids = implode(',', array_keys($results));
		unset($results);
		$query = $db->getQuery(true);
		$query->from($db->quoteName('#__kunena_attachments'))
			->where($db->quoteName('id') . 'IN (' . $ids . ')');
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (ExecutionFailureException $e)
		{
			KunenaError::displayDatabaseError($e);

			return false;
		}

		return true;
	}

	/**
	 * This function shortens long file names for display purposes.
	 * The first 8 characters of the filename, followed by three dots
	 * and the last 5 character of the filename.
	 *
	 * @param   string  $filename  Filename to be shortened.
	 * @param   int     $front     front
	 * @param   int     $back      back
	 * @param   string  $filler    filler
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public static function shortenFilename(string $filename, $front = 10, $back = 8, $filler = '...')
	{
		$len = mb_strlen($filename);

		if ($len > ($front + \strlen($filler) + $back))
		{
			$output = substr($filename, 0, $front) . $filler . substr($filename, $len - $back, $back);
		}
		else
		{
			$output = $filename;
		}

		return $output;
	}

	/**
	 * @param   mixed  $user    user
	 * @param   array  $params  params
	 *
	 * @return  KunenaAttachment[]
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public static function getByUserid($user, array $params): array
	{
		if ($params['file'] == '1' && $params['image'] != '1')
		{
			$filetype = " AND filetype=''";
		}
		elseif ($params['image'] == '1' && $params['file'] != '1')
		{
			$filetype = " AND filetype!=''";
		}
		elseif ($params['file'] == '1' && $params['image'] == '1')
		{
			$filetype = '';
		}
		else
		{
			return [];
		}

		if ($params['orderby'] == 'desc')
		{
			$orderby = ' ORDER BY id DESC';
		}
		else
		{
			$orderby = ' ORDER BY id ASC';
		}

		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->select('*')
			->from($db->quoteName('#__kunena_attachments'))
			->where($db->quoteName('userid') . ' = ' . $db->quote($user->userid . $filetype . $orderby));
		$query->setLimit($params['limit']);
		$db->setQuery($query);

		try
		{
			$results = $db->loadObjectList('id', 'Attachment');
		}
		catch (RuntimeException $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		$list = [];

		if (!empty($results))
		{
			foreach ($results as $instance)
			{
				if (!isset(self::$_instances[$instance->id]))
				{
					self::$_instances [$instance->id]                  = $instance;
					self::$_messages [$instance->mesid][$instance->id] = $instance;
				}

				$list[] = self::$_instances[$instance->id];
			}
		}

		return $list;
	}

	/**
	 * Load the total count of attachments
	 *
	 * @return bool|null
	 *
	 * @throws \Exception
	 * @since   Kunena 5.1
	 */
	public static function getTotalAttachments(): ?bool
	{
		$attachments = null;

		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query
			->select('COUNT(*)')
			->from($db->quoteName('#__kunena_attachments'));
		$db->setQuery($query);

		try
		{
			$attachments = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		return $attachments;
	}

	/**
	 * Check if mime type is image.
	 *
	 * @param   string  $mime  mime
	 *
	 * @return  boolean  True if mime is image.
	 *
	 * @since   Kunena 4.0
	 */
	public function isImageMime(string $mime): bool
	{
		return stripos($mime, 'image/') !== false;
	}
}
