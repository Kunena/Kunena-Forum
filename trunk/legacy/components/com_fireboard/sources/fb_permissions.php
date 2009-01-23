<?php
/**
* @version $Id: fb_permissions.php 688 2008-06-17 03:10:29Z fxstein $
* Fireboard Component
* @package Fireboard
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * Checks if a user has postpermission in given thread
 * @param database object
 * @param int
 * @param int
 * @param int
 * @param boolean
 * @param boolean
 *
 * @pre: fb_has_read_permission()
 */
function fb_has_post_permission(&$database,$catid,$replyto,$userid,$pubwrite,$ismod) {
    global $fbConfig;
    if ($ismod)
        return 1; // moderators always have post permission
    if($replyto != 0) {
        $database->setQuery("select thread from #__fb_messages where id='$replyto'");
        $topicID=$database->loadResult();
        if ($topicID != 0) //message replied to is not the topic post; check if the topic post itself is locked
            $sql='select locked from #__fb_messages where id='.$topicID;
        else
            $sql='select locked from #__fb_messages where id='.$replyto;
        $database->setQuery($sql);
        if ($database->loadResult()==1)
        return -1; // topic locked
    }

    //topic not locked; check if forum is locked
    $database->setQuery("select locked from #__fb_categories where id=$catid");
    if ($database->loadResult()==1)
        return -2; // forum locked

    if ($userid != 0|| $pubwrite)
        return 1; // post permission :-)
    return 0; // no public writing allowed
}
/**
 * Checks if user is a moderator in given forum
 * @param dbo
 * @param int
 * @param int
 * @param bool
 */

function fb_has_moderator_permission(&$database,&$obj_fb_cat,$int_fb_uid,$bool_fb_isadmin) {
    if ($bool_fb_isadmin)
        return 1;
    if ($obj_fb_cat!='' && $obj_fb_cat->getModerated() && $int_fb_uid != 0) {
        $database->setQuery('SELECT userid FROM #__fb_moderation WHERE catid='.$obj_fb_cat->getId().' AND userid='.$int_fb_uid);
        
        if ($database->loadResult()!='')
            return 1;
     }
// Check if we have forum wide moderators - not limited to particular categories 
    $database->setQuery('SELECT moderator FROM #__fb_users WHERE userid='.$int_fb_uid);
    if ($database->loadResult()==1) // moderator YES
    {
        $database->setQuery('SELECT userid FROM #__fb_moderation WHERE userid='.$int_fb_uid);
        if ($database->loadResult()=='') // not limited to a specific category - as we checked for those above
        {
            return 1;
        }
    }         
    return 0;
}


/**
 * Checks if user has read permission in given forum
 * @param object
 * @param array
 * @param int
 * @param obj
 */
function fb_has_read_permission(&$obj_fbcat,&$allow_forum,$groupid,&$acl) {
    if ($obj_fbcat->getPubRecurse())
        $pub_recurse="RECURSE";
    else
        $pub_recurse="NO_RECURSE";

    if ($obj_fbcat->getAdminRecurse())
        $admin_recurse="RECURSE";
    else
        $admin_recurse="NO_RECURSE";
      if ($obj_fbcat->getPubAccess() == 0 || ($obj_fbcat->getPubAccess() == -1 && $groupid > 0) || (sizeof($allow_forum)> 0 && in_array($obj_fbcat->getId(),$allow_forum))) {
      //this is a public forum; let 'Everybody' pass
      //or this forum is for all registered users and this is a registered user
      //or this forum->id is already in the cookie with allowed forums
         return 1;
      }
      else {
          //access restrictions apply; need to check

        if( $groupid == $obj_fbcat->getPubAccess()) {
            //the user has the same groupid as the access level requires; let pass
            return 1;
        }
        else {
            if ($pub_recurse=='RECURSE') {
                //check if there are child groups for the Access Level
                $group_childs=array();
                $group_childs=$acl->get_group_children( $obj_fbcat->getPubAccess(), 'ARO', $pub_recurse );

                if ( is_array( $group_childs ) && count( $group_childs ) > 0) {
                    //there are child groups. See if user is in any ot them
                    if ( in_array($groupid, $group_childs) ) {
                        //user is in here; let pass
                        return 1;
                    }
               }
            }
        }//esle
        //no valid frontend users found to let pass; check if this is an Admin that should be let passed:

        if( $groupid == $obj_fbcat->getAdminAccess() ) {
            //the user has the same groupid as the access level requires; let pass
            return 1;
        }
        else {
            if ($admin_recurse=='RECURSE') {
                //check if there are child groups for the Access Level
                $group_childs=array();
                $group_childs=$acl->get_group_children( $obj_fbcat->getAdminAccess(), 'ARO', $admin_recurse );

                if (is_array( $group_childs ) && count( $group_childs ) > 0) {
                    //there are child groups. See if user is in any ot them
                      if ( in_array($groupid, $group_childs) ) {
                        //user is in here; let pass
                         return 1;
                    }
                }
            }
         }    //esle
    } // esle
    //passage not allowed
    return 0;
}