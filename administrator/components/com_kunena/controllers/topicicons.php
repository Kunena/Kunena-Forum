<?php
/**
 * Kunena Component
 * @package Kunena.Administrator
 * @subpackage Controllers
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Kunena Topicicons Controller
 *
 * @since 2.0
 */
class KunenaAdminControllerTopicicons extends KunenaController {
	protected $baseurl = null;

	public function __construct($config = array()) {
		parent::__construct($config);
		$this->baseurl = 'administrator/index.php?option=com_kunena&view=topicicons';
	}

	function add() {
		if (! JRequest::checkToken ()) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl."&layout=add", false));
	}

	function edit() {
		if (! JRequest::checkToken ()) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$id = array_shift($cid);
		if (!$id) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_A_NO_TOPICICON_SELECTED' ), 'notice' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		} else {
			$this->setRedirect(KunenaRoute::_($this->baseurl."&layout=add&id={$id}", false));
		}
	}

	function save() {
		$db = JFactory::getDBO ();
		if (!JRequest::checkToken()) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
			return;
		}

		$iconname = JRequest::getString ( 'topiciconname' );
		$filename = JRequest::getString ( 'topiciconslist' );
		$published = JRequest::getInt ( 'published' );
		$ordering = JRequest::getInt ( 'ordering', 0 );
		$topiciconid = JRequest::getInt( 'topiciconid', 0 );

		// TODO: escape all variables going to SQL query
		/*if ( !$topiciconid ) {
			$db->setQuery ( "INSERT INTO #__kunena_topics_icons SET name = '$iconname', filename = '$filename', published = '$published', ordering ='$ordering'" );
			$db->query ();
			if (KunenaError::checkDatabaseError()) return;
		} else {
			$db->setQuery ( "UPDATE #__kunena_topics_icons SET name = '$iconname', filename = '$filename', published = '$published', ordering ='$ordering' WHERE id = '$topiciconid'" );
			$db->query ();
			if (KunenaError::checkDatabaseError()) return;
		}*/

		$this->app->enqueueMessage ( JText::_('COM_KUNENA_TOPICICON_SAVED') );
		$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}

	function orderup() {
		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$this->orderUpDown ( array_shift($cid), -1 );
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	function orderdown() {
		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$this->orderUpDown ( array_shift($cid), 1 );
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	protected function orderUpDown($id, $direction) {
		/*
		require_once(JPATH_ADMINISTRATOR.'/components/com_kunena/libraries/tables/kunenatopicicons.php');
		KunenaFactory::loadLanguage('com_kunena', 'admin');

		if (!$id) return;

		if (! JRequest::checkToken ()) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			return;
		}

		$db = JFactory::getDBO ();
		$row = new TableKunenaTopicsIcons ( $db );
		$row->load ( $id );

		// Ensure that we have the right ordering
		$row->reorder ( );
		$row->move ( $direction );
		*/
	}

	function publish() {
		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$this->setVariable($cid, 'published', 1);
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}
	function unpublish() {
		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$this->setVariable($cid, 'published', 0);
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	protected function setVariable($cid, $variable, $value) {
		KunenaFactory::loadLanguage('com_kunena', 'admin');

		$db = JFactory::getDBO ();
		if (! JRequest::checkToken ()) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			return;
		}

		$id = array_shift($cid);
		if (empty ( $id )) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_A_NO_TOPICICON_SELECTED' ), 'notice' );
			return;
		}

		$topicicons_xml = simplexml_load_file(JPATH_ADMINISTRATOR.'/components/com_kunena/libraries/topicicons/topicicons2.xml');
		$id = (Int) $id-1;
		$topicicons_xml->icons->icon[$id]['published'] = $value;

	  $topicicons_xml->asXML(JPATH_ADMINISTRATOR.'/components/com_kunena/libraries/topicicons/topicicons2.xml');

		if ( $value ) $status = JText::_ ( 'COM_KUNENA_A_TOPICICON_PUBLISHED' );
		else $status = JText::_ ( 'COM_KUNENA_A_TOPICICON_UNPUBLISHED' );

		$this->app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_TOPICICON', ' '.$status ) );
		$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}

	function bydefault() {
		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$this->setDefault($cid, 1);
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	function notbydefault() {
		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$this->setDefault($cid, 0);
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	protected function setDefault($cid, $value) {
		$db = JFactory::getDBO ();
		if (! JRequest::checkToken ()) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			return;
		}

		$id = array_shift($cid);
		if (empty ( $id )) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_A_NO_TOPICICON_SELECTED' ), 'notice' );
			return;
		}

    $id = (Int) $id-1;

		$defaultexist = 0;
		if ($value == 1) {
		  $topicicons_xml = simplexml_load_file(JPATH_ADMINISTRATOR.'/components/com_kunena/libraries/topicicons/topicicons2.xml');

		  $defaultexist = $topicicons_xml->icons->icon[$id]['isdefault'];
		}

		if ( $defaultexist == 1) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_A_TOPICICON_ALREADY_DEFAULT' ) );
		} else {
				$topicicons_xml = simplexml_load_file(JPATH_ADMINISTRATOR.'/components/com_kunena/libraries/topicicons/topicicons2.xml');

		    $topicicons_xml->icons->icon[$id]['isdefault'] = $value;

	     $topicicons_xml->asXML(JPATH_ADMINISTRATOR.'/components/com_kunena/libraries/topicicons/topicicons2.xml');

			if ( $value ) $status = JText::_ ( 'COM_KUNENA_A_TOPICICON_DEFAULT' );
			else $status = JText::_ ( 'COM_KUNENA_A_TOPICICON_NOTDEFAULT' );

			$this->app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_TOPICICON', ' '.$status ) );
		}

		$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}

	function parseXMLTopiciconFile($topiciconBaseDir) {
		// Check if the xml file exists
		if(!is_file($topiciconBaseDir.'topicicons.xml')) {
			return false;
		}
		$data = $this->parseKunenaInstallFile($topiciconBaseDir.'topicicons.xml');
		if ($data->type != 'kunena-topicicons') {
			return false;
		}

		return $data;
	}

	function parseKunenaInstallFile($path) {
		// FIXME : deprecated under Joomla! 1.6
		$xml = JFactory::getXMLParser ( 'Simple' );
		if (! $xml->loadFile ( $path )) {
			unset ( $xml );
			return false;
		}
		if (! is_object ( $xml->document ) || ($xml->document->name () != 'kunena-topicicons')) {
			unset ( $xml );
			return false;
		}

		$data = new stdClass ();
		$element = & $xml->document->name [0];
		$data->name = $element ? $element->data () : '';
		$data->type = $element ? $xml->document->attributes ( "type" ) : '';

		$element = & $xml->document->creationDate [0];
		$data->creationdate = $element ? $element->data () : JText::_ ( 'Unknown' );

		$element = & $xml->document->author [0];
		$data->author = $element ? $element->data () : JText::_ ( 'Unknown' );

		$element = & $xml->document->copyright [0];
		$data->copyright = $element ? $element->data () : '';

		$element = & $xml->document->authorEmail [0];
		$data->authorEmail = $element ? $element->data () : '';

		$element = & $xml->document->authorUrl [0];
		$data->authorUrl = $element ? $element->data () : '';

		$element = & $xml->document->version [0];
		$data->version = $element ? $element->data () : '';

		$element = & $xml->document->description [0];
		$data->description = $element ? $element->data () : '';

		$element = & $xml->document->thumbnail [0];
		$data->thumbnail = $element ? $element->data () : '';

		return $data;
	}

	function topiciconupload() {
		jimport ( 'joomla.filesystem.folder' );
		jimport ( 'joomla.filesystem.file' );
		jimport ( 'joomla.filesystem.archive' );
		$tmp = JPATH_ROOT . '/tmp/kinstall/';
		$dest = JPATH_ROOT . '/media/kunena/topicicons/';
		$file = JRequest::getVar ( 'install_package', NULL, 'FILES', 'array' );

		if (! JRequest::checkToken ()) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		if (!$file || !is_uploaded_file ( $file ['tmp_name'])) {
			$this->app->enqueueMessage ( JText::sprintf('COM_KUNENA_A_TOPICON_MANAGER_INSTALL_EXTRACT_MISSING', $file ['name']), 'notice' );
		}
		else {
			$success = JFile::upload($file ['tmp_name'], $tmp . $file ['name']);
			$success = JArchive::extract ( $tmp . $file ['name'], $tmp );
			if (! $success) {
				$this->app->enqueueMessage ( JText::sprintf('COM_KUNENA_A_TOPICON_MANAGER_INSTALL_EXTRACT_FAILED', $file ['name']), 'notice' );
			}
			// Delete the tmp install directory
			if (JFolder::exists($tmp)) {
				$topicicons = $this->parseXMLTopiciconFile($tmp);
				if (!empty($topicicons)) {
          	// Never overwrite existing topic icon set
						if (!JFolder::exists($dest.(String)$topicicons->name)) {
					 	   $error = JFolder::move($tmp, $dest.(String)$topicicons->name);
						    if ($error !== true) $this->app->enqueueMessage ( JText::_('COM_KUNENA_A_TOPICON_MANAGER_ERROR_INSTALL_FAILED').': ' . $error, 'notice' );

					       JFile::delete($dest.(String)$topicicons->name.'/'.$file['name']);

				        	if(JFolder::exists ($tmp)) $retval = JFolder::delete($tmp);
					       $this->app->enqueueMessage ( JText::sprintf('COM_KUNENA_A_TOPICON_MANAGER_INSTALL_EXTRACT_SUCCESS', $file ['name']) );
					}
				} else {
					JError::raiseWarning(100, JText::_('COM_KUNENA_A_TOPICON_MANAGER_TEMPLATE_MISSING_FILE'));
					$retval = false;
				}
			} else {
				JError::raiseWarning(100, JText::_('COM_KUNENA_A_TOPICON_MANAGER_DIR_NOT_EXIST'));
				$retval = false;
			}
		}
		$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}

	function delete() {
		$db = JFactory::getDBO ();

		if (!JRequest::checkToken()) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
			return;
		}

		$cids = JRequest::getVar ( 'cid', array (), 'post', 'array' );

		if ($cids) {
		  $topicicons_xml = simplexml_load_file(JPATH_ADMINISTRATOR.'/components/com_kunena/libraries/topicicons/topicicons2.xml');
		  foreach( $cids as $id ) {
		    $id = (Int) $id-1;
        unset($topicicons_xml->icons->icon[$id]);
      }
			$topicicons_xml->asXML(JPATH_ADMINISTRATOR.'/components/com_kunena/libraries/topicicons/topicicons2.xml');
		}

		$this->app->enqueueMessage (JText::_('COM_KUNENA_TOPICICONS_DELETED') );
		$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}

	function saveorder() {
		/*
		$db = JFactory::getDBO ();
		require_once(JPATH_ADMINISTRATOR.'/components/com_kunena/libraries/tables/kunenatopicicons.php');

		if (! JRequest::checkToken ()) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$order = JRequest::getVar ( 'order', array (), 'post', 'array' );

		if (empty ( $cid )) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_A_NO_TOPICICONS_SELECTED' ), 'notice' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$success = false;

		$db = JFactory::getDBO ();
		$row = new TableKunenaTopicsIcons ( $db );

		$cids = implode(',',$cid); // TODO: Convert all values in array to integers.
		$query = "SELECT id,ordering FROM #__kunena_topics_icons WHERE id IN ($cids)";
		$db->setQuery ( $query );
		$topicicons = $db->loadObjectlist();
		if (KunenaError::checkDatabaseError()) return;


		foreach ( $topicicons as $icon ) {
			if (! isset ( $order [$icon->id] ) || $icon->ordering == $order [$icon->id])
				continue;

			$row->load( (int) $icon->id );
			$row->ordering = $order [$icon->id];
			if (!$row->store()) {
				$this->app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_ORDERING_SAVE_FAILED', $this->escape ( $category->getError () ) ), 'notice' );
			}
		}
		$row->reorder ( );

		if ($success) {
			$this->app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_NEW_ORDERING_SAVED' ) );
		}
		*/
		$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}
}