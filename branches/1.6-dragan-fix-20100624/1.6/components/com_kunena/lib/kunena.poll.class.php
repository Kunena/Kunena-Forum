<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die();

/**
 * @author Xillibit
 *
 */
class CKunenaPolls {
	protected $_db = null;
	protected $_app = null;
	public $config = null;
	public $my = null;
	public $document = null;

	protected function __construct($db, $config) {
		$this->do = JRequest::getCmd ( 'do', '' );
		$this->_db = $db;
		$this->my = $this->my = &JFactory::getUser ();
		$this->_app = & JFactory::getApplication ();
		$this->config = $config;
		$this->document =& JFactory::getDocument();

		$this->id = JRequest::getInt ( 'id', 0 );
		$this->catid = JRequest::getInt ( 'catid', 0 );
		$this->do = JRequest::getCmd ( 'do', '' );
	}

	public function &getInstance() {
		static $instance = NULL;
		if (! $instance) {
			$kunena_db = & JFactory::getDBO ();
			$kunena_config = KunenaFactory::getConfig ();

			$instance = new CKunenaPolls ( $kunena_db, $kunena_config );
		}
		return $instance;
	}
	
	/**
	* Escapes a value for output in a view script.
	*
	* If escaping mechanism is one of htmlspecialchars or htmlentities, uses
	* {@link $_encoding} setting.
	*
	* @param  mixed $var The output to escape.
	* @return mixed The escaped value.
	*/
	function escape($var)
	{
		return htmlspecialchars($var, ENT_COMPAT, 'UTF-8');
	}

	/**
	 * Get the datas for a poll
	 * @return array
	 */
	function get_poll_data($threadid)
	{
    	$query = "SELECT *
    				FROM #__kunena_polls AS a
    				INNER JOIN #__kunena_polls_options AS b ON a.threadid=b.pollid
    				WHERE a.threadid=$threadid";
    	$this->_db->setQuery($query);
    	$polldata = $this->_db->loadObjectList();
    	KunenaError::checkDatabaseError();

    	return $polldata;
	}

	/**
	 * Get the users which have voted for a poll
	 * @return array
	 */
	function get_users_voted($threadid)
	{
		//To show the usernames of the users which have voted for this poll
		$query = "SELECT pollid,userid,name,username
					FROM #__kunena_polls_users AS a
					INNER JOIN #__users AS b ON a.userid=b.id
					WHERE pollid=$threadid";
    	$this->_db->setQuery($query);
    	$uservotedata = $this->_db->loadObjectList();
    	KunenaError::checkDatabaseError();

    	return $uservotedata;
	}
	/**
	 * Get the total number of voters in a poll
	 * @return int
	 */
	function get_number_total_voters($pollid)
	{
    	$query = "SELECT SUM(votes) FROM #__kunena_polls_users WHERE pollid=$pollid";
    	$this->_db->setQuery($query);
    	$numvotes = $this->_db->loadResult();
    	KunenaError::checkDatabaseError();

    	return $numvotes;
	}
	/**
	 * Get the number options of an poll
	 * @return int
	 */
	function get_total_options($pollid)
	{
    	$query = "SELECT COUNT(*) FROM #__kunena_polls_options WHERE pollid=$pollid";
    	$this->_db->setQuery($query);
    	$numoptions = $this->_db->loadResult();
    	KunenaError::checkDatabaseError();

    	return $numoptions;
	}
	/**
	* Get if the poll is allowed to be displayed
	*/
	function get_poll_allowed($id, $parent=0, $kunena_editmode, $allow_cat=0) {
		$poll_allowed = '';
		if ( $allow_cat ) {
			if ( $kunena_editmode ) {
				if ( $parent == '0' ) $poll_allowed = '1';
			} else {
				if ( $id == '0' ) $poll_allowed = '1';
			}
		} else {
			if ( $id == '0' ) $poll_allowed = '1';
		}
		return $poll_allowed;
	}

   /**
	* Insert javascript and ajax for vote
	*/
   function call_javascript_vote()
   {
    	CKunenaTools::addScript(KUNENA_DIRECTURL . 'template/default/plugin/poll/js/kunena.poll.ajax-min.js');
		JApplication::addCustomHeadTag('
      <script type="text/javascript">
	   <!--
	   var KUNENA_POLL_SAVE_ALERT_OK = "'.JText::_('COM_KUNENA_POLL_SAVE_ALERT_OK').'";
	   var KUNENA_POLL_SAVE_ALERT_ERROR = "'.JText::_('COM_KUNENA_POLL_SAVE_ALERT_ERROR').'";
	   var KUNENA_POLL_SAVE_VOTE_ALREADY = "'.JText::_('COM_KUNENA_POLL_SAVE_VOTE_ALREADY').'";
	   var KUNENA_POLL_SAVE_ALERT_ERROR_NOT_CHECK = "'.JText::_('COM_KUNENA_POLL_SAVE_ALERT_ERROR_NOT_CHECK').'";
	   var KUNENA_POLL_WAIT_BEFORE_VOTE = "'.JText::_('COM_KUNENA_POLL_WAIT_BEFORE_VOTE').'";
	   var KUNENA_POLL_CANNOT_VOTE_NEW_TIME = "'.JText::_('COM_KUNENA_POLL_CANNOT_VOTE_NEW_TIME').'";
	   var KUNENA_ICON_ERROR = "'.JURI::root(). 'administrator/images/publish_x.png'.'";
	   var KUNENA_ICON_INFO = "'.JURI::root(). 'images/M_images/con_info.png'.'";
     //-->
     </script>
		');
   }
	/**
	* Get poll input when you are in editmode
	*/
	function get_input_poll($kunena_editmode, $id, $polldatasedit) {
		$html_poll_edit = '';
		if ($kunena_editmode) {
			$polloptions  = $this->get_total_options($id);
			if (isset($polloptions)) {
        		$nboptions = '1';

        		for ($i=0;$i < $polloptions;$i++) {
        			if(empty($html_poll_edit)) {
						$html_poll_edit = "<div id=\"option".$nboptions."\">Option ".$nboptions."&nbsp;<input type=\"text\" maxlength = \"25\" id=\"field_option".$i."\" name=\"field_option".$i."\" value=\"".$polldatasedit[$i]->text."\" onmouseover=\"
						javascript:$('helpbox').set('value', '"
				. JText::_('COM_KUNENA_EDITOR_HELPLINE_ADDPOLLOPTION'). "')\" /></div>";
        			} else {
						$html_poll_edit .= "<div id=\"option".$nboptions."\">Option ".$nboptions."&nbsp;<input type=\"text\" maxlength = \"25\" id=\"field_option".$i."\" name=\"field_option".$i."\" value=\"".$polldatasedit[$i]->text."\" onmouseover=\"
						javascript:$('helpbox').set('value', '"
				. JText::_('COM_KUNENA_EDITOR_HELPLINE_ADDPOLLOPTION'). "')\" /></div>";
        			}
        			$nboptions++;
        		}
			}
		}
		return $html_poll_edit;
	}
	/**
	* Insert javascript for form for editing a poll
	*/
	function call_js_poll_edit($kunena_editmode, $id) {
		$polloptions  = $this->get_total_options($id);
		$polloptionsstart = $polloptions+1;

		if ($kunena_editmode) {
			$this->document->addScriptDeclaration('
	   		var number_field = "'.$polloptionsstart.'";
			');
		} else {
			$this->document->addScriptDeclaration('
   			var number_field = 1;
			');
		}
	}
   /**
	* Insert javascript for form of new post
	*/
   function call_javascript_form()
   {
    	CKunenaTools::addScript(KUNENA_DIRECTURL . 'template/default/plugin/poll/js/kunena.poll-min.js');
		$this->document->addScriptDeclaration('
	   var KUNENA_POLL_CATS_NOT_ALLOWED = "'.JText::_('COM_KUNENA_POLL_CATS_NOT_ALLOWED').'";
	   var KUNENA_EDITOR_HELPLINE_OPTION = "'.JText::_('COM_KUNENA_EDITOR_HELPLINE_OPTION').'";
	   var KUNENA_POLL_OPTION_NAME = "'.JText::_('COM_KUNENA_POLL_OPTION_NAME').'";
	   var KUNENA_POLL_NUMBER_OPTIONS_MAX_NOW = "'.JText::_('COM_KUNENA_POLL_NUMBER_OPTIONS_MAX_NOW').'";
	   var KUNENA_ICON_ERROR = "'.JURI::root(). 'administrator/images/publish_x.png'.'";
	   var kunena_ajax_url_poll = "'.CKunenaLink::GetJsonURL('pollcatsallowed', '', false).'";
		');
   }
   /**
	* Save a new poll
	*/
   function save_new_poll($polltimetolive,$polltitle,$pid,$optionvalue)
   {
		if (isset($polltitle) && sizeof($optionvalue) > '0') {
			$query = "INSERT INTO #__kunena_polls (title,threadid,polltimetolive)
						VALUES(".$this->_db->quote($polltitle).",'$pid','$polltimetolive')";
    		$this->_db->setQuery($query);
    		$this->_db->query();
    		if (KunenaError::checkDatabaseError()) return;

    		for ($i = 0; $i < sizeof($optionvalue); $i++)
    		{
    			$query = "INSERT INTO #__kunena_polls_options (text,pollid,votes)
    						VALUES(".$this->_db->quote($optionvalue[$i]).",'$pid','0')";
        		$this->_db->setQuery($query);
        		$this->_db->query();
    			if (KunenaError::checkDatabaseError()) return;
    		}
		}
   }
   /**
	* Save the results of a poll to prevent spam
	* @return array
	*/
   function get_data_poll_users($userid,$threadid)
   {
		$query = "SELECT pollid,userid,lasttime,votes,
						TIMEDIFF(CURTIME(),DATE_FORMAT(lasttime, '%H:%i:%s')) AS timediff
					FROM #__kunena_polls_users
					WHERE pollid=$threadid AND userid=$userid";
		$this->_db->setQuery($query);
		$polluserdata = $this->_db->loadObjectList();
		KunenaError::checkDatabaseError();

		return $polluserdata;
   }
   /**
	* Get the five better votes in polls
	* @return int
	*/
   function get_top_five_votes($PopPollsCount)
   {
		$query = "SELECT SUM(o.votes) AS total
					FROM #__kunena_polls AS p
					LEFT JOIN #__kunena_polls_options AS o ON p.threadid=o.pollid
					GROUP BY p.threadid
					ORDER BY total
					DESC ";
		$this->_db->setQuery($query,0,$PopPollsCount);
		$votecount = $this->_db->loadResult();
		KunenaError::checkDatabaseError();

		return $votecount;
   }
   /**
	* Get the five better polls
	* @return Array
	*/
	function get_top_five_polls($PopPollsCount)
	{
    	$query = "SELECT q.catid,p.*, SUM(o.votes) AS total
    				FROM #__kunena_polls AS p
    				INNER JOIN #__kunena_polls_options AS o ON p.threadid=o.pollid
    				INNER JOIN #__kunena_messages AS q ON p.threadid = q.thread
    				GROUP BY p.threadid
    				ORDER BY total DESC";
    	$this->_db->setQuery($query,0,$PopPollsCount);
	    $toppolls = $this->_db->loadObjectList();
	    KunenaError::checkDatabaseError();

    	return $toppolls;
   }
   /**
	* Save the results of a poll
	*/
   function save_results($pollid,$userid,$vote)
   {
   		// Sanitize parameters!
   		$pollid = intval ( $pollid );
   		$userid = intval ( $userid );
   		$vote   = intval ( $vote );

    	$pollusers = $this->get_data_poll_users($userid,$pollid);
    	$nonewvote = "0";
    	$data = array();
    	if ($this->config->pollallowvoteone)
    	{
      		if(!empty($pollusers)){
    			if ($pollusers[0]->userid == $userid)
      			{
        			$nonewvote = "1";
      			}
      		}
    	}
    	if ($nonewvote == "0")
    	{
    		if(empty($pollusers)){
				$poll_timediff = false;
				$pollusers[0]->timediff = null;
    		} else {
    			$poll_timediff = true;
    		}
      		if ($poll_timediff || $pollusers[0]->timediff == null)
      		{
        		$query = "SELECT * FROM #__kunena_polls_options WHERE pollid='$pollid' AND id='$vote';";
        		$this->_db->setQuery($query);
        		$polloption = $this->_db->loadObject();
        		if (KunenaError::checkDatabaseError()) return;

        		if (!$polloption) break; // OPTION DOES NOT EXIST

        		$query = "SELECT votes FROM #__kunena_polls_users WHERE pollid='$pollid' AND userid='$userid';";
        		$this->_db->setQuery($query);
          		$votes = $this->_db->loadResult();
          		if (KunenaError::checkDatabaseError()) return;

          		if (empty($votes))
          		{
          			$query = "INSERT INTO #__kunena_polls_users (pollid,userid,votes,lastvote) VALUES('$pollid','$userid','1','$vote');";
            		$this->_db->setQuery($query);
            		$this->_db->query();
            		if (KunenaError::checkDatabaseError()) return;

            		$query = "UPDATE #__kunena_polls_options SET votes=votes+1 WHERE id='$vote';";
             		$this->_db->setQuery($query);
             		$this->_db->query();
            		if (KunenaError::checkDatabaseError()) return;

            		$data['results'] = '1';
          		}
         		else if ($votes < $this->config->pollnbvotesbyuser)
         		{
         			$query = "UPDATE #__kunena_polls_users SET votes=votes+1,lastvote='$vote' WHERE pollid='$pollid' AND userid='$userid';";
            		$this->_db->setQuery($query);
            		$this->_db->query();
            		if (KunenaError::checkDatabaseError()) return;

            		$query = "UPDATE #__kunena_polls_options SET votes=votes+1 WHERE id='$vote';";
            		$this->_db->setQuery($query);
            		$this->_db->query();
            		if (KunenaError::checkDatabaseError()) return;

					$data['results'] = '1';
         		}
      		}
      		elseif ($pollusers[0]->timediff <= $this->config->polltimebtvotes)
      		{
      			$data['results'] = '2';
      		}
     	}
     	else
     	{
     		$data['results'] = '3';
     	}

     	return $data;
   }
   /**
	* Update poll during edit
	*/
   function save_changevote($threadid,$userid,$vote)
   {
   		// Sanitize parameters!
   		$threadid = intval ( $threadid );
   		$userid   = intval ( $userid );
   		$vote     = intval ( $vote );

		$pollusers = $this->get_data_poll_users($userid,$threadid);
		$data = array();

		if ($pollusers[0]->timediff > $this->config->polltimebtvotes)
      	{
      		// call reset vote
      		$this->reset_vote($userid,$threadid);

			$query = "UPDATE #__kunena_polls_options SET votes=votes+1 WHERE id='$vote';";
        	$this->_db->setQuery($query);
        	$this->_db->query();
        	if (KunenaError::checkDatabaseError()) return;

        	$query = "UPDATE #__kunena_polls_users SET votes=votes+1, lastvote='$vote', lasttime=now() WHERE pollid='$threadid' AND userid='$userid';";
        	$this->_db->setQuery($query);
        	$this->_db->query();
        	if (KunenaError::checkDatabaseError()) return;

        	$data['results'] = '1';
      	}
      	elseif ($pollusers[0]->timediff <= $this->config->polltimebtvotes)
      	{
        	$data['results'] = '3';
      	}
      	return $data;
   }
   /**
	* Update poll during edit
	*/
   function update_poll_edit($polltimetolive,$threadid,$polltitle,$optvalue,$optionsnumbers)
   {
    	$polloptions = $this->get_total_options($threadid);
    	$pollsdatas = $this->get_poll_data($threadid); //Need this to update/delete the right option in the database

    	$query = "UPDATE #__kunena_polls
    				SET title=".$this->_db->quote($polltitle).",
    				polltimetolive=".$this->_db->quote($polltimetolive)."
    				WHERE threadid=$threadid";
    	$this->_db->setQuery($query);
    	$this->_db->query();
    	if (KunenaError::checkDatabaseError()) return;

    	if ($polloptions == $optionsnumbers)//When users just do an update of the polls fields
    	{
      		for ($i = 0; $i < sizeof($optvalue); $i++)
      		{
      			$query = "UPDATE #__kunena_polls_options
      						SET text=".$this->_db->quote($optvalue[$i])."
      						WHERE id={$pollsdatas[$i]->id} AND pollid={$threadid}";
         		$this->_db->setQuery($query);
         		$this->_db->query();
             	if (KunenaError::checkDatabaseError()) return;
      		}
    	}
    	elseif($optionsnumbers > $polloptions)//When users add new polls options
    	{
      		for ($i = 0; $i < sizeof($optvalue); $i++)
      		{
        		if ($i < $polloptions)
        		{
        			$query = "UPDATE #__kunena_polls_options
        						SET text=".$this->_db->quote($optvalue[$i])."
        						WHERE id={$pollsdatas[$i]->id} AND pollid={$threadid}";
          			$this->_db->setQuery($query);
          			$this->_db->query();
          	    	if (KunenaError::checkDatabaseError()) return;
        		}
        		else
        		{
					$query = "INSERT INTO #__kunena_polls_options (text,pollid,votes)
								VALUES(".$this->_db->quote($optvalue[$i]).",'$threadid','0')";
        			$this->_db->setQuery($query);
          			$this->_db->query();
          	    	if (KunenaError::checkDatabaseError()) return;
        		}
      		}
    	}
    	elseif($optionsnumbers < $polloptions)//When users remove polls options
    	{
      		for ($i = 0; $i < $polloptions; $i++)
      		{
        		if ($i < $optionsnumbers)
        		{
        			$query = "UPDATE #__kunena_polls_options
        						SET text=".$this->_db->quote($optvalue[$i])."
        						WHERE id={$pollsdatas[$i]->id} AND pollid=$threadid";
          			$this->_db->setQuery($query);
          			$this->_db->query();
          			if (KunenaError::checkDatabaseError()) return;
        		}
        		else
        		{
        			$query = "DELETE FROM #__kunena_polls_options
        						WHERE pollid=$threadid AND id={$pollsdatas[$i]->id}";
          			$this->_db->setQuery($query);
          			$this->_db->query();
          			if (KunenaError::checkDatabaseError()) return;
        		}
      		}
    	}
   }
   /**
	* To get the last vote id in fb_users
	*/
   function get_last_vote_id($userid,$pollid)
   {

		$query = "SELECT lastvote FROM #__kunena_polls_users
				WHERE pollid='$pollid' AND userid='$userid';";
    	$this->_db->setQuery($query);
    	$id_last_vote = $this->_db->loadResult();
    	KunenaError::checkDatabaseError();

    	return $id_last_vote;
   }
   /**
	* For the user can vote a new once, need to remove one vote
	*/
   function reset_vote($userid,$threadid)
   {
		$query = "SELECT a.id,a.votes AS option_votes, b.votes AS user_votes, b.lastvote FROM #__kunena_polls_options AS a
				INNER JOIN #__kunena_polls_users AS b ON a.pollid=b.pollid
				WHERE a.pollid=$threadid";
    	$this->_db->setQuery($query);
    	$poll_options_user = $this->_db->loadObjectList();
		if (KunenaError::checkDatabaseError()) return;

		foreach ($poll_options_user as $row) {
			if ($row->id == $row->lastvote) {
				if($row->option_votes > '0' && $row->user_votes > '0') {
					$query = "UPDATE #__kunena_polls_options SET votes=votes-1 WHERE id={$row->lastvote} AND pollid='$threadid';";
    				$this->_db->setQuery($query);
    				$this->_db->query();
					if (KunenaError::checkDatabaseError()) return;

					$query = "UPDATE #__kunena_polls_users SET votes=votes-1 WHERE userid='$userid' AND pollid='$threadid';";
    				$this->_db->setQuery($query);
    				$this->_db->query();
					if (KunenaError::checkDatabaseError()) return;
				}
			}
		}
   }
   /**
	* Delete a poll
	*/
   function delete_poll($threadid)
   {
    	$query = "DELETE FROM #__kunena_polls WHERE threadid=$threadid";
    	$this->_db->setQuery($query);
    	$this->_db->query();
    	if (KunenaError::checkDatabaseError()) return;

    	$query = "DELETE FROM #__kunena_polls_options WHERE pollid=$threadid";
    	$this->_db->setQuery($query);
    	$this->_db->query();
    	if (KunenaError::checkDatabaseError()) return;

    	$query = "DELETE FROM #__kunena_polls_users WHERE pollid=$threadid";
    	$this->_db->setQuery($query);
    	$this->_db->query();
    	if (KunenaError::checkDatabaseError()) return;
   }

	/*
	 * Function to do things without json
	*/

	public function polldo() {
		$vote	= JRequest::getInt('kpollradio', '');
		$id = JRequest::getInt ( 'kpoll-id', 0 );
		$catid = JRequest::getInt ( 'catid', 0 );

		switch ( $this->do ) {
			case 'vote' :
			case 'pollvote' :
				$result = $this->save_results($id,$this->my->id,$vote);

				if ($result['results'] == 1) {
					$message = JText::_('COM_KUNENA_POLL_SAVE_ALERT_OK');
				} elseif($result['results'] == 2) {
					$message = JText::_('COM_KUNENA_POLL_SAVE_VOTE_ALREADY');
				} elseif($result['results'] == 3) {
					$message = JText::_('COM_KUNENA_POLL_WAIT_BEFORE_VOTE');
				}

				$this->_app->enqueueMessage ( $message );
				$this->_app->redirect ( CKunenaLink::GetThreadPageURL('view', $catid, $id, 1, $this->config->messages_per_page, $id, false) );
				break;
			case 'pollchangevote' :
				$result = $this->save_changevote($id,$this->my->id,$vote);

				if($result['results'] == 1) {
					$message = JText::_('COM_KUNENA_POLL_SAVE_ALERT_OK');
				} elseif($result['results'] == 3) {
					$message = JText::_('COM_KUNENA_POLL_WAIT_BEFORE_VOTE');
				}

				$this->_app->enqueueMessage ( $message );
				$this->_app->redirect ( CKunenaLink::GetThreadPageURL(CKunenaLink::GetThreadPageURL('view', $catid, $id, 1, $this->config->messages_per_page, $id, false)) );
				break;
		}
	}

   /**
    *  Show pollbox
    */

    public function showPollbox() {
		CKunenaTools::loadTemplate('/plugin/poll/pollbox.php');
    }

    public function display() {
    	switch ($this->do) {
			case 'vote' :
				$this->changevote = '';
				CKunenaTools::loadTemplate('/plugin/poll/pollvote.php');
				break;
			case 'changevote' :
				$this->changevote = '1';
				CKunenaTools::loadTemplate('/plugin/poll/pollvote.php');
				break;
    	}
    }
}
?>