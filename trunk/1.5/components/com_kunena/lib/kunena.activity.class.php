<?php
/**
* @version $Id: kunena.activity.class.php 653 2009-04-27 02:28:22Z mahagr $
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/
//
// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

class CKunenaActivity
{
	protected function act($params)
	{

	}

	public function newThread($userid, $threadid, $subject, $catid, $catname)
	{
		// construct params based on this event type

		act($params);
	}

	public function replyThread($userid, $threadid, $postid, $catid, $catname, $subject)
	{
		// construct params based on this event type

		act($params);
	}

	public function editPost($userid, $threadid, $postid, $catid, $catname, $subject)
	{
		// construct params based on this event type

		act($params);
	}

	public function attachImage($userid, $threadid, $postid, $catid, $catname)
	{
		// construct params based on this event type

		act($params);
	}

	public function attachFile($userid, $threadid, $postid, $catid, $catname)
	{
		// construct params based on this event type

		act($params);
	}

// More ....

}
?>