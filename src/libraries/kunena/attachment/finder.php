<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    Attachment
 *
 * @copyright     Copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          http://www.kunena.org
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
		$this->db->setQuery($query, $this->start, $this->limit);

		try
		{
			$results = new KunenaCollection((array) $this->db->loadObjectList('id', 'KunenaAttachment'));
		}
		catch (RuntimeException $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		return $results;
	}
}
