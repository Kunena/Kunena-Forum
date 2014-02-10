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
	 *	Return JSON results of smilies available
	 *
	 * @param string $tpl
	 *
	 * @since 3.1
	 *
	 * @return void
	 */
	public function displayListEmoji($tpl = null)
	{
		$response = array();

		if ($this->me->exists() )
		{
			$search = $this->app->input->get('search');

			$db = JFactory::getDBO();
			$kquery = new KunenaDatabaseQuery;
			$kquery->select('*')->from("{$db->qn('#__kunena_smileys')}")->where("code LIKE '%{$db->escape($search)}%' AND emoticonbar=1");
			$db->setQuery($kquery);
			$smileys = $db->loadObjectList();
			KunenaError::checkDatabaseError();

			foreach ($smileys as $smiley)
			{
				$emojis['key'] = $smiley->code;
				$emojis['name'] = $smiley->location;
				$emojis['url'] = JUri::root() . 'media/kunena/emoticons/' . $smiley->location;

				$response['emojis'][] = $emojis;
			}
		}

		// Set the MIME type and header for JSON output.
		$this->document->setMimeEncoding('application/json');
		JResponse::setHeader('Content-Disposition', 'attachment; filename="' . $this->getName() . '.' . $this->getLayout() . '.json"');

		echo json_encode($response);
	}
}
