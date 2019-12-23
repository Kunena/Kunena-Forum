<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Attachment
 *
 * @copyright     Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Class KunenaAttachmentFinder
 *
 * @since 5.0
 */
class KunenaAttachmentFinder extends KunenaDatabaseObjectFinder
{
	/**
	 * @var string
	 * @since Kunena
	 */
	protected $table = '#__kunena_attachments';

	/**
	 * Get log entries.
	 *
	 * @return array|KunenaCollection
	 * @since Kunena
	 * @throws Exception|void
	 */
	public function find()
	{
		if ($this->skip)
		{
			return array();
		}

		$query = clone $this->query;
		$this->build($query);
		$query->select('a.*');
		$query->setLimit($this->limit, $this->start);
		$this->db->setQuery($query);

		try
		{
			$results = (array) $this->db->loadObjectList('id');
		}
		catch (RuntimeException $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		$instances = array();

		if (!empty($results))
		{
			foreach ($results as $id => $result)
			{
				$instances[$id] = KunenaAttachmentHelper::get($id);
			}
		}

		$instances = new KunenaCollection($instances);

		unset($results);

		return $instances;
	}
}
