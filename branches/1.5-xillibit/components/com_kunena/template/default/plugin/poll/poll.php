<?php
/**
* @version $Id:
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2009 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/
// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

$do = JRequest::getVar("do", "");
$id = intval(JRequest::getVar("id", ""));
CKunenaPolls::call_javascript_vote();
if($do == "results"){
    //Prevent spam from users
    CKunenaPolls::save_results($id,$kunena_my->id,$value_choosed);
}elseif($do == "pollreset"){
  //Reset all the votes for a poll and delete the users which have voted for this poll
  CKunenaPolls::reset_poll($id);
}
?>