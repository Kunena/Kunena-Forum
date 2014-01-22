<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Views
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Topic View
 */
class KunenaViewTopic extends KunenaView {
	function displayEdit($tpl = null) {
		$body = JRequest::getVar('body', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$response = array();
		if ($this->me->exists() || $this->config->pubwrite) {
			$msgbody = KunenaHtmlParser::parseBBCode( $body, $this );
			$response ['preview'] = $msgbody;
		}

		// Set the MIME type and header for JSON output.
		$this->document->setMimeEncoding( 'application/json' );
		JResponse::setHeader( 'Content-Disposition', 'attachment; filename="'.$this->getName().'.'.$this->getLayout().'.json"' );

		echo json_encode( $response );
	}

	/**
	 * Method to handle files ajax upload on topic edition
	 *
	 * @since 3.1
	 *
	 * @return void
	 */
	public function displayUpload()
	{
		$me = KunenaUserHelper::getMyself();
		$catid = JFactory::getApplication()->input->get('catid', 0, 'int');
		$path = JPATH_ROOT . '/media/kunena/attachments/' . $me->id;

		$category = KunenaForumCategoryHelper::get($catid);
		// TODO: Some room for improvements in here... (maybe ask user to pick up category first)
		if ($category->id) $category->tryAuthorise('topic.post.attachment.create');

		require 'UploadHandler.php';
		$upload_handler = new KunenaUploadHandler;
	}
}