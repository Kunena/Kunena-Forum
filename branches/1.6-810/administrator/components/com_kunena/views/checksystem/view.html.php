<?php
/**
 * @version		$Id: view.html.php 1014 2009-08-17 07:18:07Z louis $
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * The HTML Kunena configuration view.
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.6
 */
class KunenaViewChecksystem extends JView
{
	/**
	 * Method to display the view.
	 *
	 * @param	string	A template file to load.
	 * @return	mixed	JError object on failure, void on success.
	 * @throws	object	JError
	 * @since	1.6
	 */
	public function display($tpl = null)
	{
		// Initialize variables.
		$user	= JFactory::getUser();

		// Render the layout.
		parent::display($tpl);
	}
	
	protected function _displayMainToolbar()
	{
		JToolBarHelper::title(': '.JText::_('Check System'), 'generic');
		JToolBarHelper::divider();

	}
	function mainpage()
    {
    	$uri =& JURI::getInstance();
    	$systemcheck = new ControllerCheckSystem();
    	$systemcheck->DBStatus();
    }

	function MySQLVersion()
	{
		static $mysqlversion;
		if (!$mysqlversion)
		{
			$kunena_db = &JFactory::getDBO();
			$kunena_db->setQuery("SELECT VERSION() AS mysql_version");
			$mysqlversion = $kunena_db->loadResult();
			if (!$mysqlversion) $mysqlversion = 'unknown';
		}
		return $mysqlversion;
	}
	function syncusers($sync) {
    
}

function douserssync($douserssync)
{
    $app =& JFactory::getApplication();

    $kunena_db = &JFactory::getDBO();
	//reset access rights
	$kunena_db->setQuery("UPDATE #__kunena_sessions SET allowed='na'");
	$kunena_db->query() or trigger_dberror("Unable to update sessions.");

    //get userlist to remove from Kunena users list
    $kunena_db->setQuery("SELECT a.userid from #__kunena_users as a left join #__users as b on a.userid=b.id where b.username is null");
    $idlistR = $kunena_db->loadObjectList();
            check_dberror("Unable to load users.");

    $allIDsR = array ();
    $cidsR = count($idlistR);

    if ($cidsR > 0)
    {
        foreach ($idlistR as $idR) {
            $allIDsR[] = $idR->userid;
        }

        $idsR = implode(',', $allIDsR);
    }

    //get userlist to add into Kunena users list
    $kunena_db->setQuery("SELECT a.id from #__users as a left join #__kunena_users as b on b.userid=a.id where b.userid is null");
    $idlistA = $kunena_db->loadObjectList();
            check_dberror("Unable to load users.");

    $allIDsA = array ();
    $cidsA = count($idlistA);

    if ($cidsA > 0) {
        foreach ($idlistA as $idA) {
            $allIDsA[] = $idA->id;
        }
    }

	//fb_users update
    if ($cidsR or $cidsA) {
		// delete old users
		if ($cidsR)
		{
			$kunena_db->setQuery("DELETE FROM #__kunena_users WHERE userid in ($idsR)");
			$kunena_db->query() or trigger_dberror("Unable to delete old users.");
		}

		// add new users
		if ($cidsA)
		{
			for ($j = 0, $m = count($allIDsA); $j < $m; $j ++)
			{
				$kunena_db->setQuery("INSERT INTO #__kunena_users (userid) "."\nVALUES ($allIDsA[$j])");
				$kunena_db->query() or trigger_dberror("Unable to add new users.");
			}
		}
        $app->redirect( JURI::base() ."index2.php?option=$option&task=pruneusers", "" . _KUNENA_USERSSYNCDELETED . $cids . " " . _KUNENA_SYNCUSERPROFILES);
    }
    else
    {
        $cids = 0;
        $app->redirect( JURI::base() ."index2.php?option=$option&task=pruneusers", _KUNENA_NOPROFILESFORSYNC);
    }
}

function pruneforum()
{
    $forums_list = array ();
    //get forum list; locked forums are excluded from pruning
    $kunena_db->setQuery("SELECT a.id as value, a.name as text" . "\nFROM #__kunena_categories AS a" . "\nWHERE a.parent != '0'" . "\nAND a.locked != '1'" . "\nORDER BY parent, ordering");
    //get all subscriptions for this user
    $forums_list = $kunena_db->loadObjectList();
    	check_dberror("Unable to load unlocked forums.");
    $forumList['forum'] = JHTML::_('select.genericlist',$forums_list, 'prune_forum', 'class="inputbox" size="4"', 'value', 'text', '');
    html_Kunena::pruneforum($option, $forumList);
}

function doprune($kunena_db, $option)
{
	$app =& JFactory::getApplication();

	$catid = intval(JRequest::getVar( 'prune_forum', -1));
    $deleted = 0;

    if ($catid == -1)
    {
        echo "<script> alert('" . _KUNENA_CHOOSEFORUMTOPRUNE . "'); window.history.go(-1); </script>\n";
        $app->close();
    }

    $prune_days = intval(JRequest::getVar( 'prune_days', 0));
    //get the thread list for this forum
    $kunena_db->setQuery("SELECT DISTINCT a.thread AS thread, max(a.time) AS lastpost, c.locked AS locked " . "\n FROM #__kunena_messages AS a" . "\n JOIN #__kunena_categories AS b ON a.catid=b.id " . "\n JOIN #__kunena_messages   AS c ON a.thread=c.thread"
                            . "\n where a.catid=$catid " . "\n and b.locked != 1 " . "\n and a.locked != 1 " . "\n and c.locked != 1 " . "\n and c.parent = 0 " . "\n and c.ordering != 1 " . "\n group by thread");
    $threadlist = $kunena_db->loadObjectList();
        check_dberror("Unable to load thread list.");

    // Convert days to seconds for timestamp functions...
    $prune_date = CKunenaTools::fbGetInternalTime() - ($prune_days * 86400);

    if (count($threadlist) > 0)
    {
        foreach ($threadlist as $tl)
        {
            //check if thread is eligible for pruning
            if ($tl->lastpost < $prune_date)
            {
                //get the id's for all posts belonging to this thread
                $kunena_db->setQuery("SELECT id from #__kunena_messages WHERE thread=$tl->thread");
                $idlist = $kunena_db->loadObjectList();
                        check_dberror("Unable to load thread messages.");

                if (count($idlist) > 0)
                {
                    foreach ($idlist as $id)
                    {
                        //prune all messages belonging to the thread
                        $kunena_db->setQuery("DELETE FROM #__kunena_messages WHERE id=$id->id");
                        $kunena_db->query() or trigger_dberror("Unable to delete messages.");

                        $kunena_db->setQuery("DELETE FROM #__kunena_messages_text WHERE mesid=$id->id");
                        $kunena_db->query() or trigger_dberror("Unable to delete message texts.");

                        //delete all attachments
                        $kunena_db->setQuery("SELECT filelocation FROM #__kunena_attachments WHERE mesid=$id->id");
                        $fileList = $kunena_db->loadObjectList();
                                check_dberror("Unable to load attachments.");

                        if (count($fileList) > 0)
                        {
                            foreach ($fileList as $fl) {
                                unlink ($fl->filelocation);
                            }

                            $kunena_db->setQuery("DELETE FROM #__kunena_attachments WHERE mesid=$id->id");
                            $kunena_db->query() or trigger_dberror("Unable to delete attachments.");
                        }

                        $deleted++;
                    }
                }
            }

            //clean all subscriptions to these deleted threads
            $kunena_db->setQuery("DELETE FROM #__kunena_subscriptions WHERE thread=$tl->thread");
            $kunena_db->query() or trigger_dberror("Unable to delete subscriptions.");
        }
    }

    $app->redirect( JURI::base() ."index2.php?option=$option&task=pruneforum", "" . _KUNENA_FORUMPRUNEDFOR . " " . $prune_days . " " . _KUNENA_PRUNEDAYS . "; " . _KUNENA_PRUNEDELETED . $deleted . " " . _KUNENA_PRUNETHREADS);
}
/* End of file check.system.view.php */
/* Location: ./views/kunena/check.system.view.php */
}