<?php
/**
* @version $Id:
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

/**
* Kunena poll class
* @package com_kunena
*/
class CKunenaPolls {
   /**
	 * Get the datas for a poll
	 * @return array
	 */
	 function get_polls_datas($threadid){
    $kunena_db = &JFactory::getDBO();
    $kunena_db->setQuery("SELECT * FROM #__fb_polls AS a JOIN #__fb_polls_options AS b ON a.threadid=b.pollid WHERE a.threadid=$threadid");
    return count($kunena_db->loadObjectList()) > 0 ? $kunena_db->loadObjectList() : array();
   }
   /**
	 * Get the parent of a thread
	 * @return array
	 */
	 function get_parent($threadid){
    $kunena_db = &JFactory::getDBO();
    $kunena_db->setQuery("SELECT parent FROM #__fb_messages WHERE id=$threadid");
    return count($kunena_db->loadObject()) > 0 ? $kunena_db->loadObject() : array();
   }
   /**
	 * Get the users which have voted for a poll
	 * @return array
	 */
	 function get_users_voted($threadid){
	  $kunena_db = &JFactory::getDBO();
    //To show the usernames of the users which have voted for this poll
    $kunena_db->setQuery("SELECT pollid,userid,name,username FROM #__fb_polls_users AS a JOIN #__users AS b ON a.userid=b.id WHERE pollid=$threadid");
    $kunena_db->query() or check_dberror('Unable to load users poll.');
    return count($kunena_db->loadObjectList()) > 0 ? $kunena_db->loadObjectList() : array();
   }
   /**
	 * Get the total number of voters in a poll
	 * @return int
	 */
	 function get_number_total_voters($pollid){
    $kunena_db = &JFactory::getDBO();
    $kunena_db->setQuery("SELECT SUM(votes) FROM #__fb_polls_options WHERE pollid=$pollid");
    return intval($kunena_db->loadResult());
   }
   /**
	 * Get the number options of an poll
	 * @return int
	 */
	 function get_total_options($pollid){
    $kunena_db = &JFactory::getDBO();
    $kunena_db->setQuery("SELECT COUNT(*) FROM #__fb_polls_options WHERE pollid=$pollid");
    return intval($kunena_db->loadResult());
   }
   /**
	 * Insert javascript and ajax for vote
	 */
	 function call_javascript_vote(){
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
     //-->
     </script>
		');
   }
   /**
	 * Insert javascript for form of new post
	 */
	 function call_javascript_form(){
    $document =& JFactory::getDocument();
    $document->addScript(KUNENA_DIRECTURL . '/template/default/plugin/poll/js/kunena.poll.js');

		JApplication::addCustomHeadTag('
    <script type="text/javascript">
	   <!--
	   var boardclass = "'.KUNENA_BOARD_CLASS.'";
	   var KUNENA_POLL_OPTION_NAME = "'._KUNENA_POLL_OPTION_NAME.'";
	   var KUNENA_POLL_NUMBER_OPTIONS_MAX_NOW = "'._KUNENA_POLL_NUMBER_OPTIONS_MAX_NOW.'";
      //-->
      </script>
		');
   }
   /**
	 * Save a new poll
	 */
	 function save_new_poll($polltimetolive,$polltitle,$catid,$pid,$optionvalue){
	  $kunena_db = &JFactory::getDBO();
	  $kunena_db->setQuery("UPDATE #__fb_messages SET poll_exist=1 WHERE id=$pid");
    $kunena_db->query();
    $kunena_db->setQuery("INSERT INTO #__fb_polls (title,threadid,catid,polltimetolive) VALUES(".$kunena_db->quote($polltitle).",'$pid','$catid','$polltimetolive')");
    $kunena_db->query();
    for($i = 0; $i < sizeof($optionvalue); $i++){
      $kunena_db->setQuery("INSERT INTO #__fb_polls_options (text,pollid,votes) VALUES(".$kunena_db->quote($optionvalue[$i]).",'$pid','0')");
      $kunena_db->query();
    }
   }
   /**
	 * Save the results of a poll to prevent spam
	 * @return array
	 */
	 function get_datas_polls_users($userid,$threadid){
    $kunena_db = &JFactory::getDBO();
    $kunena_db->setQuery("SELECT pollid,userid,lasttime,votes,TIMEDIFF(CURTIME(),DATE_FORMAT(lasttime, '%H:%i:%s')) AS timediff FROM #__fb_polls_users WHERE pollid=$threadid AND userid=$userid");
    $kunena_db->query() or check_dberror('Unable to load users poll.');
    return count($kunena_db->loadObjectList()) > 0 ? $kunena_db->loadObjectList() : array();
   }
   /**
	 * Get the five better votes in polls
	 * @return int
	 */
	 function get_five_top_votes(){
    $kunena_db = &JFactory::getDBO();
    $kunena_db->setQuery("SELECT SUM(o.votes) AS total FROM #__fb_polls AS p LEFT JOIN #__fb_polls_options AS o ON p.threadid=o.pollid GROUP BY p.threadid ORDER BY total DESC LIMIT 1");
    $kunena_db->query();
    return intval($kunena_db->loadResult());
   }
   /**
	 * Get the five better polls
	 * @return Array
	 */
	 function get_five_top_polls($PopPollsCount){
    $kunena_db = &JFactory::getDBO();
    $kunena_db->setQuery("SELECT p.*, SUM(o.votes) AS total FROM #__fb_polls AS p LEFT JOIN #__fb_polls_options AS o ON p.threadid=o.pollid GROUP BY p.threadid ORDER BY total DESC",0,$PopPollsCount);
    $kunena_db->query();
    return count($kunena_db->loadObjectList()) > 0 ? $kunena_db->loadObjectList() : array();
   }
   /**
	 * Save the results of a poll
	 */
	 function save_results($pollid,$userid,$value_choosed){
    $kunena_db = &JFactory::getDBO();
    $kunena_config =& CKunenaConfig::getInstance();
    $pollusers = CKunenaPolls::get_datas_polls_users($userid,$pollid);
    $nonewvote = "0";
    if($kunena_config->pollallowvoteone){
      if($pollusers[0]->userid == $userid){
        $nonewvote = "1";
      }
    }
    if($nonewvote == "0"){
      if($pollusers[0]->timediff > $kunena_config->polltimebtvotes || $pollusers[0]->timediff == null){
        echo "<script language = \"JavaScript\" type = \"text/javascript\">var infoserver=\"1\";</script>";
        $kunena_db->setQuery("SELECT * FROM #__fb_polls_options WHERE pollid=$pollid AND id=$value_choosed");
        $kunena_db->query() or check_dberror('Unable to load poll.');
        $polloption = $kunena_db->loadObject();
        if (!$polloption) break; // OPTION DOES NOT EXIST
          $kunena_db->setQuery("SELECT votes FROM #__fb_polls_users WHERE pollid=$pollid AND userid=$userid");
          $kunena_db->query() or check_dberror('Unable to load datas from poll users.');
          $votes = $kunena_db->loadResult();
          if(empty($votes)) {
            $kunena_db->setQuery("INSERT INTO #__fb_polls_users (pollid,userid,votes) VALUES('$pollid','{$userid}',1)");
            $kunena_db->query();
            if($votes == null){ //need this if because when the $votes is null the thing SET votes=votes+1 doesn't work
              $kunena_db->setQuery("UPDATE #__fb_polls_options SET votes=1 WHERE id=$value_choosed");
              $kunena_db->query();
            }else {
              $kunena_db->setQuery("UPDATE #__fb_polls_options SET votes=votes+1 WHERE id=$value_choosed");
              $kunena_db->query();
            }
            echo "<script language = \"JavaScript\" type = \"text/javascript\">var infoserver=\"1\";</script>";
          }else if($votes = $kunena_config->pollnbvotesbyuser) {
            $kunena_db->setQuery("UPDATE #__fb_polls_users SET votes=votes+1 WHERE pollid=$pollid AND userid={$userid}");
            $kunena_db->query();
            $kunena_db->setQuery("UPDATE #__fb_polls_options SET votes=votes+1 WHERE id=$value_choosed");
            $kunena_db->query();
            echo "<script language = \"JavaScript\" type = \"text/javascript\">var infoserver=\"1\";</script>";
         }
      }elseif($pollusers[0]->timediff <= $kunena_config->polltimebtvotes){
         echo "<script language = \"JavaScript\" type = \"text/javascript\">var infoserver=\"2\";</script>";
      }
     }else{
        echo "<script language = \"JavaScript\" type = \"text/javascript\">var infoserver=\"3\";</script>";
     }
   }
   /**
	 * Update poll during edit
	 */
	 function update_poll_edit($polltimetolive,$threadid,$polltitle,$optvalue,$optionsnumbers){
    $kunena_db = &JFactory::getDBO();
    $polloptions = CKunenaPolls::get_total_options($threadid);
    $pollsdatas = CKunenaPolls::get_polls_datas($threadid); //Need this to update/delete the right option in the database
    $kunena_db->setQuery("UPDATE #__fb_polls SET title=".$kunena_db->quote($polltitle).",polltimetolive=$polltimetolive WHERE threadid={$threadid}");
    $kunena_db->query();
    if($polloptions == $optionsnumbers){ //When users just do an update of the polls fields
      for($i = 0; $i < sizeof($optvalue); $i++){
         $kunena_db->setQuery("UPDATE #__fb_polls_options SET text=".$kunena_db->quote($optvalue[$i])." WHERE id={$pollsdatas[$i]->id} AND pollid={$threadid}");
         $kunena_db->query();
      }
    }elseif($optionsnumbers > $polloptions){//When users add new polls options
      for($i = 0; $i < sizeof($optvalue); $i++){
        if($i < $polloptions){
          $kunena_db->setQuery("UPDATE #__fb_polls_options SET text=".$kunena_db->quote($optvalue[$i])." WHERE id={$pollsdatas[$i]->id} AND pollid={$threadid}");
          $kunena_db->query();
        }else{
          $kunena_db->setQuery("INSERT INTO #__fb_polls_options (text,pollid,votes) VALUES(".$kunena_db->quote($optvalue[$i]).",'$threadid','0')");
          $kunena_db->query();
        }
      }
    }elseif($optionsnumbers < $polloptions){//When users remove polls options
      for($i = 0; $i < $polloptions; $i++){
        if($i < $optionsnumbers){
          $kunena_db->setQuery("UPDATE #__fb_polls_options SET text=".$kunena_db->quote($optvalue[$i])." WHERE id={$pollsdatas[$i]->id} AND pollid={$threadid}");
          $kunena_db->query();
        }else{
          $kunena_db->setQuery("DELETE FROM #__fb_polls_options WHERE pollid=$threadid AND id={$pollsdatas[$i]->id}");
          $kunena_db->query();
        }
      }
    }
   }
   /**
	 * Reset poll
	 */
	 function reset_poll($threadid){
    $kunena_db = &JFactory::getDBO();
    $kunena_db->setQuery("UPDATE #__fb_polls_options SET votes=0 WHERE pollid=$threadid");
    $kunena_db->query();
    $kunena_db->setQuery("DELETE FROM #__fb_polls_users WHERE pollid=$threadid");
    $kunena_db->query();
   }
   /**
	 * Delete a poll
	 */
	 function delete_poll($threadid){
    $kunena_db = &JFactory::getDBO();
    $kunena_db->setQuery("DELETE FROM #__fb_polls WHERE threadid=$threadid");
    $kunena_db->query();
    $kunena_db->setQuery("DELETE FROM #__fb_polls_options WHERE pollid=$threadid");
    $kunena_db->query();
    $kunena_db->setQuery("DELETE FROM #__fb_polls_users WHERE pollid=$threadid");
    $kunena_db->query();
   }
}
?>