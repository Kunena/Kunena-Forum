<?php
/**
* @version $Id: fb_auth.php 462 2007-12-10 00:05:53Z fxstein $
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
//
// Dont allow direct linking
defined ('_VALID_MOS') or die('Direct Access to this location is not allowed.');

class fb_auth
{
    function validate_user(&$forum, &$allow_forum, $groupid, &$acl)
    {
        if ($forum->pub_recurse)
        {
            $pub_recurse = "RECURSE";
        }
        else
        {
            $pub_recurse = "NO_RECURSE";
        }

        if ($forum->admin_recurse)
        {
            $admin_recurse = "RECURSE";
        }
        else
        {
            $admin_recurse = "NO_RECURSE";
        }

        if ($forum->pub_access == 0 || ($forum->pub_access == -1 && $groupid > 0) || in_array($forum->id, $allow_forum))
        {
            //this is a public forum; let 'Everybody' pass
            //or this forum is for all registered users and this is a registered user
            //or this forum->id is already in the cookie with allowed forums
            return 1;
        }
        else
        { //access restrictions apply; need to check
            if ($groupid == $forum->pub_access)
            {
                //the user has the same groupid as the access level requires; let pass
                return 1;
            }
            else
            {
                if ($pub_recurse == 'RECURSE')
                {
                    //check if there are child groups for the Access Level
                    $group_childs = array ();
                    $group_childs = $acl->get_group_children($forum->pub_access, 'ARO', $pub_recurse);

                    if (is_array($group_childs) && count($group_childs) > 0)
                    {
                        //there are child groups. See if user is in any ot them
                        if (in_array($groupid, $group_childs))
                        {
                            //user is in here; let pass
                            return 1;
                        }
                    }
                }
            }

            //no valid frontend users found to let pass; check if this is an Admin that should be let passed:
            if ($groupid == $forum->admin_access)
            {
                //the user has the same groupid as the access level requires; let pass
                return 1;
            }
            else
            {
                if ($admin_recurse == 'RECURSE')
                {
                    //check if there are child groups for the Access Level
                    $group_childs = array ();
                    $group_childs = $acl->get_group_children($forum->admin_access, 'ARO', $admin_recurse);

                    if (is_array($group_childs) && count($group_childs) > 0)
                    {
                        //there are child groups. See if user is in any ot them
                        if (in_array($groupid, $group_childs))
                        {
                            //user is in here; let pass
                            return 1;
                        }
                    }
                }
            }
        }

        //passage not allowed
        return 0;
    } //end validate function
}     //end class
?>