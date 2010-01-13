<?php
/**
* @version $Id: kunena.poll.class.php 1426 2010-01-02 09:13:33Z xillibit $
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die();


/**
* Kunena poll class
* @package com_kunena
*/
class CKunenaPolls {
	/**
	 * Get the datas for a poll
	 * @return array
	 */
	function get_poll_data($threadid)
	{
    	$kunena_db = &JFactory::getDBO();
    	$query = "SELECT *
    				FROM #__fb_polls AS a
    				JOIN #__fb_polls_options AS b ON a.threadid=b.pollid
    				WHERE a.threadid=$threadid";
    	$kunena_db->setQuery($query);
    	$polldata = $kunena_db->loadObjectList();
    	check_dberror('Unable to load poll data');

    	return $polldata;
	}
	/**
	 * Get the parent of a thread
	 * @return array
	 */
	function get_parent($threadid)
	{
    	$kunena_db = &JFactory::getDBO();
    	$query = "SELECT parent FROM #__fb_messages WHERE id=$threadid";
    	$kunena_db->setQuery($query);
    	$parent = $kunena_db->loadObject();
        check_dberror('Unable to load parent');

        return $parent;
	}
	/**
	 * Get the users which have voted for a poll
	 * @return array
	 */
	function get_users_voted($threadid)
	{
		$kunena_db = &JFactory::getDBO();
		//To show the usernames of the users which have voted for this poll
		$query = "SELECT pollid,userid,name,username
					FROM #__fb_polls_users AS a
					JOIN #__users AS b ON a.userid=b.id
					WHERE pollid=$threadid";
    	$kunena_db->setQuery($query);
    	$uservotedata = $kunena_db->loadObjectList();
    	check_dberror('Unable to load users voted');

    	return $uservotedata;
	}
	/**
	 * Get the total number of voters in a poll
	 * @return int
	 */
	function get_number_total_voters($pollid)
	{
    	$kunena_db = &JFactory::getDBO();
    	$query = "SELECT SUM(votes) FROM #__fb_polls_options WHERE pollid=$pollid";
    	$kunena_db->setQuery($query);
    	$numvotes = $kunena_db->loadResult();
    	check_dberror('Unable to count votes');

    	return $numvotes;
	}
	/**
	 * Get the number options of an poll
	 * @return int
	 */
	function get_total_options($pollid)
	{
    	$kunena_db = &JFactory::getDBO();
    	$query = "SELECT COUNT(*) FROM #__fb_polls_options WHERE pollid=$pollid";
    	$kunena_db->setQuery($query);
    	$numoptions = $kunena_db->loadResult();
    	check_dberror('Unable to count poll options');

    	return $numoptions;
	}
   /**
	* Insert javascript and ajax for vote
	*/
   function call_javascript_vote()
   {
    	$document =& JFactory::getDocument();
    	$document->addScript(KUNENA_DIRECTURL . '/template/default/plugin/poll/js/kunena.poll.ajax.js');
		JApplication::addCustomHeadTag('
      <script type="text/javascript">
	   <!--
	   var jliveurl ="'.KUNENA_JLIVEURL.'";
	   var KUNENA_POLL_SAVE_ALERT_OK = "'._KUNENA_POLL_SAVE_ALERT_OK.'";
	   var KUNENA_POLL_SAVE_ALERT_ERROR = "'._KUNENA_POLL_SAVE_ALERT_ERROR.'";
	   var KUNENA_POLL_SAVE_VOTE_ALREADY = "'._KUNENA_POLL_SAVE_VOTE_ALREADY.'";
	   var KUNENA_POLL_SAVE_ALERT_ERROR_NOT_CHECK = "'._KUNENA_POLL_SAVE_ALERT_ERROR_NOT_CHECK.'";
	   var KUNENA_POLL_WAIT_BEFORE_VOTE = "'._KUNENA_POLL_WAIT_BEFORE_VOTE.'";
	   var KUNENA_POLL_CANNOT_VOTE_NEW_TIME = "'._KUNENA_POLL_CANNOT_VOTE_NEW_TIME.'";
	   var KUNENA_ICON_ERROR = "'.JURI::root(). 'administrator/images/publish_x.png'.'";
	   var KUNENA_ICON_INFO = "'.JURI::root(). 'images/M_images/con_info.png'.'";
     //-->
     </script>
		');
   }
   /**
	* Insert javascript for form of new post
	*/
   function call_javascript_form()
   {
    	$document =& JFactory::getDocument();
    	$document->addScript(KUNENA_DIRECTURL . '/template/default/plugin/poll/js/kunena.poll.js');
		JApplication::addCustomHeadTag('
    <script type="text/javascript">
	   <!--
	   var boardclass = "'.KUNENA_BOARD_CLASS.'";
	   var KUNENA_POLL_OPTION_NAME = "'._KUNENA_POLL_OPTION_NAME.'";
	   var KUNENA_POLL_NUMBER_OPTIONS_MAX_NOW = "'._KUNENA_POLL_NUMBER_OPTIONS_MAX_NOW.'";
	   var KUNENA_ICON_ERROR = "'.JURI::root(). 'administrator/images/publish_x.png'.'";
      //-->
      </script>
		');
   }
	/**
	* Fill input field by javascript in kunena configuration panel
	*/
	function AdminFillCategoriesAllowed()
	{
		$document =& JFactory::getDocument();
		$document->addScript(KUNENA_DIRECTURL . '/template/default/plugin/poll/js/kunena.poll.admin.js');
	}
   /**
	* Save a new poll
	*/
   function save_new_poll($polltimetolive,$polltitle,$pid,$optionvalue)
   {
		$kunena_db = &JFactory::getDBO();
		$query = "INSERT INTO #__fb_polls (title,threadid,polltimetolive)
					VALUES(".$kunena_db->quote($polltitle).",'$pid','$polltimetolive')";
    	$kunena_db->setQuery($query);
    	$kunena_db->query();
    	check_dberror('Unable to insert poll data');

    	for ($i = 0; $i < sizeof($optionvalue); $i++)
    	{
    		$query = "INSERT INTO #__fb_polls_options (text,pollid,votes)
    					VALUES(".$kunena_db->quote($optionvalue[$i]).",'$pid','0')";
        	$kunena_db->setQuery($query);
        	$kunena_db->query();
    		check_dberror('Unable to insert poll options');
    	}
   }
   /**
	* Save the results of a poll to prevent spam
	* @return array
	*/
   function get_data_poll_users($userid,$threadid)
   {
		$kunena_db = &JFactory::getDBO();
		$query = "SELECT pollid,userid,lasttime,votes,
						TIMEDIFF(CURTIME(),DATE_FORMAT(lasttime, '%H:%i:%s')) AS timediff
					FROM #__fb_polls_users
					WHERE pollid=$threadid AND userid=$userid";
		$kunena_db->setQuery($query);
		$polluserdata = $kunena_db->loadObjectList();
		check_dberror('Unable to load poll user data');

		return $polluserdata;
   }
   /**
	* Get the five better votes in polls
	* @return int
	*/
   function get_top_five_votes($PopPollsCount)
   {
		$kunena_db = &JFactory::getDBO();
		$query = "SELECT SUM(o.votes) AS total
					FROM #__fb_polls AS p
					LEFT JOIN #__fb_polls_options AS o ON p.threadid=o.pollid
					GROUP BY p.threadid
					ORDER BY total
					DESC ";
		$kunena_db->setQuery($query,0,$PopPollsCount);
		$votecount = $kunena_db->loadResult();
		check_dberror('Unable to count votes');

		return $votecount;
   }
   /**
	* Get the five better polls
	* @return Array
	*/
	function get_top_five_polls($PopPollsCount)
	{
    	$kunena_db = &JFactory::getDBO();
    	$query = "SELECT q.catid,p.*, SUM(o.votes) AS total
    				FROM #__fb_polls AS p
    				INNER JOIN #__fb_polls_options AS o ON p.threadid=o.pollid
    				INNER JOIN #__fb_messages AS q ON p.threadid = q.thread
    				GROUP BY p.threadid
    				ORDER BY total DESC";
    	$kunena_db->setQuery($query,0,$PopPollsCount);
	    $toppolls = $kunena_db->loadObjectList();
	    check_dberror('Unable to count top five votes');

    	return $toppolls;
   }
   /**
	* Save the results of a poll
	*/
   function save_results($pollid,$userid,$vote)
   {
		$kunena_db = &JFactory::getDBO();
    	$kunena_config =& CKunenaConfig::getInstance();
    	$pollusers = CKunenaPolls::get_data_poll_users($userid,$pollid);
    	$nonewvote = "0";
    	if ($kunena_config->pollallowvoteone)
    	{
      		if ($pollusers[0]->userid == $userid)
      		{
        		$nonewvote = "1";
      		}
    	}
    	if ($nonewvote == "0")
    	{
      		if ($pollusers[0]->timediff > $kunena_config->polltimebtvotes || $pollusers[0]->timediff == null)
      		{
        		echo "<script language = \"JavaScript\" type = \"text/javascript\">var infoserver=\"1\";</script>";

        		$query = "SELECT * FROM #__fb_polls_options WHERE pollid=$pollid AND id=$vote";
        		$kunena_db->setQuery($query);
        		$polloption = $kunena_db->loadObject();
        		check_dberror('Unable to load poll options');

        		if (!$polloption) break; // OPTION DOES NOT EXIST

        		$query = "SELECT votes FROM #__fb_polls_users WHERE pollid=$pollid AND userid=$userid";
        		$kunena_db->setQuery($query);
          		$votes = $kunena_db->loadResult();
          		check_dberror('Unable to load votes');

          		if (empty($votes))
          		{
          			$query = "INSERT INTO #__fb_polls_users (pollid,userid,votes,lastvote) VALUES('$pollid','{$userid}',1,'{$vote}')";
            		$kunena_db->setQuery($query);
            		$kunena_db->query();
            		check_dberror('Unable to insert poll user');

            		$query = "UPDATE #__fb_polls_options SET votes=votes+1 WHERE id=$vote";
             		$kunena_db->setQuery($query);
             		$kunena_db->query();
            		check_dberror('Unable to update poll options');

            		echo "<script language = \"JavaScript\" type = \"text/javascript\">var infoserver=\"1\";</script>";
          		}
         		else if ($votes == $kunena_config->pollnbvotesbyuser)
         		{
         			$query = "UPDATE #__fb_polls_users SET votes=votes+1,lastvote=$vote WHERE pollid=$pollid AND userid={$userid}";
            		$kunena_db->setQuery($query);
            		$kunena_db->query();
            		check_dberror('Unable to ubdate poll users');

            		$query = "UPDATE #__fb_polls_options SET votes=votes+1 WHERE id=$vote";
            		$kunena_db->setQuery($query);
            		$kunena_db->query();
            		check_dberror('Unable to update poll options');

            		echo "<script language = \"JavaScript\" type = \"text/javascript\">var infoserver=\"1\";</script>";
         		}
      		}
      		elseif ($pollusers[0]->timediff <= $kunena_config->polltimebtvotes)
      		{
         		echo "<script language = \"JavaScript\" type = \"text/javascript\">var infoserver=\"2\";</script>";
      		}
     	}
     	else
     	{
        	echo "<script language = \"JavaScript\" type = \"text/javascript\">var infoserver=\"3\";</script>";
     	}
   }
   /**
	* Update poll during edit
	*/
   function save_changevote($threadid,$userid,$vote)
   {
		$kunena_db = &JFactory::getDBO();
		$kunena_config =& CKunenaConfig::getInstance();
		$pollusers = CKunenaPolls::get_data_poll_users($userid,$threadid);

		if ($pollusers[0]->timediff > $kunena_config->polltimebtvotes)
      	{
			$query = "UPDATE #__fb_polls_options SET votes=votes+1 WHERE id=$vote";
        	$kunena_db->setQuery($query);
        	$kunena_db->query();
        	check_dberror('Unable to update poll options');

        	$query = "UPDATE #__fb_polls_users SET votes=votes+1, lasttime=now() WHERE pollid=$threadid AND userid=$userid";
        	$kunena_db->setQuery($query);
        	$kunena_db->query();
        	check_dberror('Unable to update poll users');

        	echo "<script language = \"JavaScript\" type = \"text/javascript\">var infoserver=\"1\";</script>";
      	}
      	elseif ($pollusers[0]->timediff <= $kunena_config->polltimebtvotes)
      	{
        	echo "<script language = \"JavaScript\" type = \"text/javascript\">var infoserver=\"2\";</script>";
      	}
   }
   /**
	* Update poll during edit
	*/
   function update_poll_edit($polltimetolive,$threadid,$polltitle,$optvalue,$optionsnumbers)
   {
		$kunena_db = &JFactory::getDBO();
    	$polloptions = CKunenaPolls::get_total_options($threadid);
    	$pollsdatas = CKunenaPolls::get_poll_data($threadid); //Need this to update/delete the right option in the database

    	$query = "UPDATE #__fb_polls
    				SET title=".$kunena_db->quote($polltitle).",
    				polltimetolive=".$kunena_db->quote($polltimetolive)."
    				WHERE threadid=$threadid";
    	$kunena_db->setQuery($query);
    	$kunena_db->query();
    	check_dberror('Unable to Update Polls');

    	if ($polloptions == $optionsnumbers)//When users just do an update of the polls fields
    	{
      		for ($i = 0; $i < sizeof($optvalue); $i++)
      		{
      			$query = "UPDATE #__fb_polls_options
      						SET text=".$kunena_db->quote($optvalue[$i])."
      						WHERE id={$pollsdatas[$i]->id} AND pollid={$threadid}";
         		$kunena_db->setQuery($query);
         		$kunena_db->query();
             	check_dberror('Unable to Update Polls Options');
      		}
    	}
    	elseif($optionsnumbers > $polloptions)//When users add new polls options
    	{
      		for ($i = 0; $i < sizeof($optvalue); $i++)
      		{
        		if ($i < $polloptions)
        		{
        			$query = "UPDATE #__fb_polls_options
        						SET text=".$kunena_db->quote($optvalue[$i])."
        						WHERE id={$pollsdatas[$i]->id} AND pollid={$threadid}";
          			$kunena_db->setQuery($query);
          			$kunena_db->query();
          	    	check_dberror('Unable to Update Polls Options');
        		}
        		else
        		{
					$query = "INSERT INTO #__fb_polls_options (text,pollid,votes)
								VALUES(".$kunena_db->quote($optvalue[$i]).",'$threadid','0')";
        			$kunena_db->setQuery($query);
          			$kunena_db->query();
          	    	check_dberror('Unable to Insert Polls Options');
        		}
      		}
    	}
    	elseif($optionsnumbers < $polloptions)//When users remove polls options
    	{
      		for ($i = 0; $i < $polloptions; $i++)
      		{
        		if ($i < $optionsnumbers)
        		{
        			$query = "UPDATE #__fb_polls_options
        						SET text=".$kunena_db->quote($optvalue[$i])."
        						WHERE id={$pollsdatas[$i]->id} AND pollid=$threadid";
          			$kunena_db->setQuery($query);
          			$kunena_db->query();
          			check_dberror('Unable to Update Polls Options');
        		}
        		else
        		{
        			$query = "DELETE FROM #__fb_polls_options
        						WHERE pollid=$threadid AND id={$pollsdatas[$i]->id}";
          			$kunena_db->setQuery($query);
          			$kunena_db->query();
          			check_dberror('Unable to Delete Polls Options');
        		}
      		}
    	}
   }
   /**
	* For the user can vote a new once, need to remove one vote
	*/
   function change_vote($userid,$threadid,$lastvote)
   {
		$kunena_db = &JFactory::getDBO();

		$query = "SELECT a.id,a.votes AS option_votes, b.votes AS user_votes, b.lastvote FROM #__fb_polls_options AS a
				JOIN #__fb_polls_users AS b ON a.pollid=b.pollid
				WHERE a.pollid=$threadid";
    	$kunena_db->setQuery($query);
    	$poll_options_user = $kunena_db->loadObjectList();
		check_dberror('Unable to load Polls Options');

		foreach ($poll_options_user as $row) {
			if ($row->id == $row->lastvote) {
				if($row->option_votes > '0' && $row->user_votes > '0') {
					$query = "UPDATE #__fb_polls_options SET votes=votes-1 WHERE id=$lastvote AND pollid=$threadid";
    				$kunena_db->setQuery($query);
    				$kunena_db->query();
					check_dberror('Unable to Update Polls Options');

					$query = "UPDATE #__fb_polls_users SET votes=votes-1 WHERE userid=$userid AND pollid=$threadid";
    				$kunena_db->setQuery($query);
    				$kunena_db->query();
					check_dberror('Unable to Update Polls Users');
				}
			}
		}
   }
   /**
	* Delete a poll
	*/
   function delete_poll($threadid)
   {
    	$kunena_db = &JFactory::getDBO();
    	$query = "DELETE FROM #__fb_polls WHERE threadid=$threadid";
    	$kunena_db->setQuery($query);
    	$kunena_db->query();
    	check_dberror('Unable to Delete Poll');

    	$query = "DELETE FROM #__fb_polls_options WHERE pollid=$threadid";
    	$kunena_db->setQuery($query);
    	$kunena_db->query();
    	check_dberror('Unable to Delete Poll Options');

    	$query = "DELETE FROM #__fb_polls_users WHERE pollid=$threadid";
    	$kunena_db->setQuery($query);
    	$kunena_db->query();
    	check_dberror('Unable to Delete Poll Users');
   }
}
?>