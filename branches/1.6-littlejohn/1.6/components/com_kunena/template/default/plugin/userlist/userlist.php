<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
* Based on FireBoard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Code borrowed from Userlist
* Created : 23-May-2004, Emir Sakic, http://www.sakic.net
* The "GNU General Public License" (GPL) is available at
* http://www.gnu.org/copyleft/gpl.html.
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die();


$kunena_config =& CKunenaConfig::getInstance();
$document=& JFactory::getDocument();
$jsSortable  = 'function tableOrdering( order, dir, task )';
$jsSortable  .= '{var form = document.adminForm;';
$jsSortable  .= 'form.filter_order.value = order;';
$jsSortable  .= 'form.filter_order_Dir.value = dir;';
$jsSortable  .= 'document.adminForm.submit( task );}';
$document->addScriptDeclaration( $jsSortable );
$option = JRequest::getCmd ( 'option' );

$document->setTitle(JText::_('COM_KUNENA_USRL_USERLIST') . ' - ' . stripslashes($kunena_config->board_title));

list_users($option);

function list_users($option)
{
    global $lang;

    $kunena_app = & JFactory::getApplication ();
    $kunena_config =& CKunenaConfig::getInstance();
    $kunena_db = &JFactory::getDBO();

    jimport('joomla.html.pagination');

    $filter_order		= $kunena_app->getUserStateFromRequest( $option.'filter_order',		'filter_order',		'registerDate', 'cmd' );
	$filter_order_Dir	= $kunena_app->getUserStateFromRequest( $option.'filter_order_Dir',	'filter_order_Dir',	'asc',			'word' );
	$order = JRequest::getVar ( 'order', '' );
	$orderby = ' ORDER BY '. $filter_order .' '. $filter_order_Dir;

    $search = JRequest::getVar('search', '');
    $limitstart = JRequest::getInt('limitstart', 0);
    $limit = JRequest::getInt('limit', $kunena_config->userlist_rows);

    // Total
    $kunena_db->setQuery("SELECT COUNT(*) FROM #__users WHERE block =0");
    $total_results = $kunena_db->loadResult();
    check_dberror ( "Unable to load user count." );

    // Search total
    $query = "SELECT COUNT(*) FROM #__users AS u INNER JOIN #__fb_users AS fu ON u.id=fu.userid";

    if ($search != "") {
        $query .= " WHERE (u.name LIKE '%$search%' OR u.username LIKE '%$search%')";
    }

    $kunena_db->setQuery($query);
    $total = $kunena_db->loadResult();
    check_dberror ( "Unable to load search user count." );

    if ($limit > $total) {
        $limitstart = 0;
    }

    $query_ext = "";
    // Select query
    $query
        = "SELECT u.id, u.name, u.username, u.usertype, u.email, u.registerDate, u.lastvisitDate, fu.userid, fu.showOnline, fu.group_id, fu.posts, fu.karma, fu.uhits "
        ." FROM #__users AS u INNER JOIN #__fb_users AS fu ON fu.userid = u.id WHERE block=0";

    if ($search != "")
    {
        $query .= " AND (name LIKE '%$search%' OR username LIKE
'%$search%') AND u.id NOT IN (62)";
        $query_ext .= "&amp;search=" . $search;
    } else {
        $query .= " AND u.id NOT IN (62)";
    }

    $query .= $orderby;

    $query .= " LIMIT $limitstart, $limit";

    $kunena_db->setQuery($query);
    $ulrows = $kunena_db->loadObjectList();
    check_dberror ( "Unable to load search result." );

    // table ordering
	$lists['order_Dir']	= $filter_order_Dir;
	$lists['order']		= $filter_order;

    $pageNav = new JPagination($total, $limitstart, $limit);
    HTML_userlist_content::showlist($option,$ulrows, $total_results, $pageNav, $limitstart, $query_ext, $search, $lists);
}

?>

<?php
class HTML_userlist_content
{
    function showlist($option,$ulrows, $total_results, $pageNav, $limitstart, $query_ext, $search = "",$lists)
    {
		global $kunena_icons;

		$kunena_app =& JFactory::getApplication();
        $kunena_config =& CKunenaConfig::getInstance();
        $kunena_db = &JFactory::getDBO();

        if ($search == "") {
            $search = JText::_('COM_KUNENA_USRL_SEARCH');
        }
?>

        <script type = "text/javascript">
            <!--
            function validate()
            {
                if ((document.usrlform.search == "") || (document.usrlform.search.value == ""))
                {
                    alert('<?php echo JText::_('COM_KUNENA_USRL_SEARCH_ALERT'); ?>');
                    return false;
                }
                else
                {
                    return true;
                }
            }
                    //-->
        </script>
		<div class="k_bt_cvr1">
		<div class="k_bt_cvr2">
		<div class="k_bt_cvr3">
		<div class="k_bt_cvr4">
		<div class="k_bt_cvr5">
        <table class = "kblocktable" id ="kuserlist" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
            <thead>
                <tr>
                    <th>
                        <table width = "100%" border = "0" cellspacing = "0" cellpadding = "0">
                            <tr>
                                <td align = "left">
                                    <div class = "ktitle_cover  km">
                                        <span class="ktitle kl"> <?php echo JText::_('COM_KUNENA_USRL_USERLIST'); ?></span>&nbsp;

                                        <?php
                                        printf(JText::_('COM_KUNENA_USRL_REGISTERED_USERS'), $kunena_app->getCfg('sitename'), $total_results);
                                        ?>
                                    </div>
                                </td>

                                <td align = "right">
                                    <form name = "usrlform" method = "post" action = "<?php echo CKunenaLink::GetUserlistURL(); ?>" onsubmit = "return validate()">
                                        <input type = "text"
                                            name = "search"
                                            class = "inputbox"
                                            style = "width:150px"
                                            maxlength = "100" value = "<?php echo $search; ?>" onblur = "if(this.value=='') this.value='<?php echo $search; ?>';" onfocus = "if(this.value=='<?php echo $search; ?>') this.value='';" />

                                        <input type = "image" src = "<?php echo KUNENA_TMPLTMAINIMGURL .'/images/usl_search_icon.png' ;?>" alt = "<?php echo JText::_('COM_KUNENA_USRL_SEARCH'); ?>" align = "top" style = "border: 0px;"/>
                                    </form>
                                </td>
                            </tr>
                        </table>
                    </th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td class = "k-userlistinfo">
                        <!-- Begin: Listing -->
                        <form action="<?php echo CKunenaLink::GetUserlistURL(); ?>" method="POST" name="adminForm">
                        <table width = "100%" border = "0" cellspacing = "0" cellpadding = "0">
                            <tr class = "ksth ks">
                                <th class = "th-1 frst ksectiontableheader" align="center">
                                </th>

                                <?php
                                if ($kunena_config->userlist_online)
                                {
                                ?>

                                    <th class = "th-2 ksectiontableheader" align="center">
<?php echo JText::_('COM_KUNENA_USRL_ONLINE'); ?>
                                    </th>

                                <?php
                                }
                                ?>

                                <?php
                                if ($kunena_config->userlist_avatar)
                                {
                                ?>

                                    <th class = "th-3 ksectiontableheader" align="center">
<?php echo JText::_('COM_KUNENA_USRL_AVATAR'); ?>
                                    </th>

                                <?php
                                }
                                ?>

                                <?php
                                if ($kunena_config->userlist_name)
                                {
                                ?>

                                    <th class = "th-4 ksectiontableheader usersortable" align="center"><?php
					echo  JHTML::_( 'grid.sort', JText::_('COM_KUNENA_USRL_NAME'), 'name', $lists['order_Dir'], $lists['order']);
					?>
                                    </th>

                                <?php
                                }
                                ?>

                                <?php
                                if ($kunena_config->userlist_username)
                                {
                                ?>

                                    <th class = "th-5 ksectiontableheader usersortable" align="center"><?php
					echo  JHTML::_( 'grid.sort', JText::_('COM_KUNENA_USRL_USERNAME'), 'username', $lists['order_Dir'], $lists['order']);
					?>
                                    </th>

                                <?php
                                }
                                ?>

                                <?php
                                if ($kunena_config->userlist_posts)
                                {
                                ?>

                                    <th class = "th-7 ksectiontableheader usersortable" align="center"><?php
					echo  JHTML::_( 'grid.sort', JText::_('COM_KUNENA_USRL_POSTS'), 'posts', $lists['order_Dir'], $lists['order']);
					?>
                                    </th>

                                <?php
                                }
                                ?>

                                <?php
                                if ($kunena_config->userlist_karma)
                                {
                                ?>

                                    <th class = "th-7 ksectiontableheader usersortable" align="center"><?php
					echo  JHTML::_( 'grid.sort', JText::_('COM_KUNENA_USRL_KARMA'), 'karma', $lists['order_Dir'], $lists['order']);
					?>
                                    </th>

                                <?php
                                }
                                ?>

                                <?php
                                if ($kunena_config->userlist_email)
                                {
                                ?>

                                    <th class = "th-8 ksectiontableheader usersortable" align="center"><?php
					echo  JHTML::_( 'grid.sort', JText::_('COM_KUNENA_USRL_EMAIL'), 'email', $lists['order_Dir'], $lists['order']);
					?>
                                    </th>

                                <?php
                                }
                                ?>

                                <?php
                                if ($kunena_config->userlist_usertype)
                                {
                                ?>

                                    <th class = "th-9 ksectiontableheader usersortable" align="center"><?php
					echo  JHTML::_( 'grid.sort', JText::_('COM_KUNENA_USRL_USERTYPE'), 'usertype', $lists['order_Dir'], $lists['order']);
					?>
                                    </th>

                                <?php
                                }
                                ?>

                                <?php
                                if ($kunena_config->userlist_joindate)
                                {
                                ?>

                                    <th class = "th-10 ksectiontableheader usersortable" align="center"><?php
					echo  JHTML::_( 'grid.sort', JText::_('COM_KUNENA_USRL_JOIN_DATE'), 'registerDate', $lists['order_Dir'], $lists['order']);
					?>
                                     </th>

                                <?php
                                }
                                ?>

                                <?php
                                if ($kunena_config->userlist_lastvisitdate)
                                {
                                ?>

                                    <th class = "th-11  ksectiontableheader usersortable" align="center"><?php
					echo  JHTML::_( 'grid.sort', JText::_('COM_KUNENA_USRL_LAST_LOGIN'), 'lastvisitDate', $lists['order_Dir'], $lists['order']);
					?>
                                    </th>

                                <?php
                                }
                                ?>

								  <?php
                                if ($kunena_config->userlist_userhits)
                                {
                                ?>
								<th class = "th-12 lst ksectiontableheader usersortable" align="center">
								<?php
					echo  JHTML::_( 'grid.sort', JText::_('COM_KUNENA_USRL_HITS'), 'uhits', $lists['order_Dir'], $lists['order']);
					?>
								</th>
                                <?php
                                }
                                ?>

                            </tr>

                            <?php
                            $i = 1;

                            foreach ($ulrows as $ulrow)
                            {
                                $evenodd = $i % 2;

                                if ($evenodd == 0) {
                                    $usrl_class = "sectiontableentry1";
                                }
                                else {
                                    $usrl_class = "sectiontableentry2";
                                }

                                $nr = $i + $limitstart;

                                // Avatar
                                $uslavatar = '';
                                if ($kunena_config->avatar_src == "cb")
                                {
                                	$kunenaProfile =& CKunenaCBProfile::getInstance();
									$uslavatar = $kunenaProfile->showAvatar($ulrow->id);
                                }
                                else if ($kunena_config->avatar_src == "aup") // integration AlphaUserPoints
                                {
                                	$api_AUP = JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php';
                                	if ( file_exists($api_AUP)) {
                                		( $kunena_config->fb_profile=='aup' ) ? $showlink=1 : $showlink=0;
                                		 $uslavatar = AlphaUserPointsHelper::getAupAvatar( $ulrow->id, $showlink, $kunena_config->avatarsmallwidth, $kunena_config->avatarsmallheight );
                                	} // end integration AlphaUserPoints
                                }
                                else
                                {
                                    $kunena_db->setQuery("SELECT avatar FROM #__fb_users WHERE userid='{$ulrow->id}'");
                                    $avatar = $kunena_db->loadResult();
                                    check_dberror ( "Unable to load avatar." );

                                    if ($avatar != '') {

									if(!file_exists(KUNENA_PATH_UPLOADED .DS. 'avatars/s_' . $avatar)) {
										$uslavatar = '<img  border="0" class="usl_avatar" src="' . KUNENA_LIVEUPLOADEDPATH . '/avatars/' . $avatar . '" alt="" />';
										}else {
                                        $uslavatar = '<img  border="0" class="usl_avatar" src="' . KUNENA_LIVEUPLOADEDPATH . '/avatars/s_' . $avatar . '" alt="" />';
										}
                                    }
                                    else {$uslavatar = '<img  border="0" class="usl_avatar" src="' . KUNENA_LIVEUPLOADEDPATH . '/avatars/s_nophoto.jpg" alt="" />'; }
                                }
                                //
                            ?>

                                <tr class = "k<?php echo $usrl_class ;?>  km">
                                    <td class = "td-1 frst ks" align="center">
<?php echo $nr; ?>
                                    </td>

                                    <?php
                                    if ($kunena_config->userlist_online)
                                    {
                                    ?>

                                        <td class = "td-2">
                                            <?php // online - ofline status
                                            $sql = "SELECT COUNT(userid) FROM #__session WHERE userid='{$ulrow->id}'";
                                            $kunena_db->setQuery($sql);
                                            $isonline = $kunena_db->loadResult();
											check_dberror ( "Unable to load online status." );

                                            if ($isonline && $ulrow->showOnline ==1 ) {
                                                echo isset($kunena_icons['onlineicon']) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons['onlineicon'] . '" border="0" alt="' . JText::_('COM_KUNENA_MODLIST_ONLINE') . '" />' : '  <img src="' . KUNENA_URLEMOTIONSPATH . 'onlineicon.gif" border="0"  alt="' . JText::_('COM_KUNENA_MODLIST_ONLINE') . '" />';
                                            }
                                            else {
                                                echo isset($kunena_icons['offlineicon']) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons['offlineicon'] . '" border="0" alt="' . JText::_('COM_KUNENA_MODLIST_OFFLINE') . '" />' : '  <img src="' . KUNENA_URLEMOTIONSPATH . 'offlineicon.gif" border="0"  alt="' . JText::_('COM_KUNENA_MODLIST_OFFLINE') . '" />';
                                            }
                                            ?>
                                        </td>

                                    <?php
                                    }
                                    ?>

                                    <?php
                                    if ($kunena_config->userlist_avatar)
                                    {
                                    ?>

                                        <td class = "td-3" align="center">
                                      <?php
                                      if(JString::strlen($uslavatar)) {
						echo CKunenaLink::GetProfileLink($kunena_config, $ulrow->id, $uslavatar);
                                      }
                                      else { echo '&nbsp;'; }
                                      ?>
                                        </td>

                                    <?php
                                    }
                                    ?>

                                    <?php
                                    if ($kunena_config->userlist_name)
                                    {
                                    ?>

                                        <td class = "td-4  km" align="center">
						<?php echo CKunenaLink::GetProfileLink($kunena_config, $ulrow->id, $ulrow->name); ?>
                                        </td>

                                    <?php
                                    }
                                    ?>

                                    <?php
                                    if ($kunena_config->userlist_username)
                                    {
                                    ?>

                                        <td class = "td-5  km" align="center">
						<?php echo CKunenaLink::GetProfileLink($kunena_config, $ulrow->id, $ulrow->username); ?>
                                        </td>

                                    <?php
                                    }
                                    ?>

                                    <?php
                                    if ($kunena_config->userlist_posts)
                                    {
                                    ?>

                                        <td class = "td-7  ks" align="center">
<?php echo $ulrow->posts; ?>
                                        </td>

                                    <?php
                                    }
                                    ?>

                                    <?php
                                    if ($kunena_config->userlist_karma)
                                    {
                                    ?>

                                        <td class = "td-7 ks" align="center">
<?php echo $ulrow->karma; ?>
                                        </td>

                                    <?php
                                    }
                                    ?>

                                    <?php
                                    if ($kunena_config->userlist_email) {
                                        echo "\t\t<td class=\"td-8 ks\"  align=\"center\"><a href=\"mailto:$ulrow->email\">$ulrow->email</a></td>\n";
                                    }

                                    if ($kunena_config->userlist_usertype) {
                                        echo "\t\t<td  class=\"td-9 ks\"  align=\"center\">$ulrow->usertype</td>\n";
                                    }

                                    if ($kunena_config->userlist_joindate) {
                                        echo "\t\t<td  class=\"td-10 ks\"  align=\"center\" title=\"".CKunenaTimeformat::showDate($ulrow->registerDate, 'ago', 'utc')."\">" . CKunenaTimeformat::showDate($ulrow->registerDate, 'datetime_today', 'utc') . "</td>\n";
                                    }

                                    if ($kunena_config->userlist_lastvisitdate) {
                                        echo "\t\t<td  class=\"td-11 ks\"  align=\"center\" title=\"".CKunenaTimeformat::showDate($ulrow->lastvisitDate, 'ago', 'utc')."\">" . CKunenaTimeformat::showDate($ulrow->lastvisitDate, 'datetime_today', 'utc') . "</td>\n";
                                    }
                                    ?>

                                    <td class = "td-12 lst ks" align="center">
									 <?php
                                    if ($kunena_config->userlist_userhits)
                                    {
                                    ?>
									<?php echo $ulrow->uhits; ?>

                                    <?php
                                    }
                                    ?>
									 </td>

                            <?php
                                    echo "\t</tr>\n";
                                    $i++;
                            }
                            ?>

        </table>
        <input type="hidden" name="option" value="<?php echo $option; ?>">
        <input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />
        </form>

		<form name = "usrlform" method = "post" action = "<?php echo CKunenaLink::GetUserlistURL(); ?>" onsubmit = "return false;">
        <table width = "100%"  class="kuserlist_pagenav" border = "0" cellspacing = "0" cellpadding = "0">
            <tr class = "ksth  ks">
                <th class = "th-1  km" align = "center" style = "text-align:center;">

                            <?php
                            // TODO: fxstein - Need to perform SEO cleanup
                            echo $pageNav->getPagesLinks(CKunenaLink::GetUserlistURL($query_ext)); ?>
                </th>
            </tr>
        </table>



        <table class = "kblocktable" id="kuserlist_bottom" style="border-bottom:0px;margin:0;" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
                <tr>
                    <th  class = "th-right  ks" align="right" style="text-align:right">
                     <?php echo $pageNav->getPagesCounter(); ?> | <?php echo JText::_('COM_KUNENA_USRL_DISPLAY_NR'); ?> <?php echo $pageNav->getLimitBox(CKunenaLink::GetUserlistURL($query_ext)); ?>
                </th>
            </tr>
        </table>
		</form>

        </td>
	</tr>
  </tbody>
</table>
        <!-- Finish: Listing -->

</div>
</div>
</div>
</div>
</div>
        <?php
        //(JJ) BEGIN: WHOISONLINE
        if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/who/whoisonline.php')) {
            include(KUNENA_ABSTMPLTPATH . '/plugin/who/whoisonline.php');
        }
        else {
            include(KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/who/whoisonline.php');
        }
        //(JJ) FINISH: WHOISONLINE
        ?>
        <!-- Begin: Forum Jump -->
<div class="k_bt_cvr1">
<div class="k_bt_cvr2">
<div class="k_bt_cvr3">
<div class="k_bt_cvr4">
<div class="k_bt_cvr5">
        <table class = "kblocktable" id="kbottomarea"   border = "0" cellspacing = "0" cellpadding = "0">
            <thead>
                <tr>
                    <th  class = "th-right">
                        <?php
                        //(JJ) FINISH: CAT LIST BOTTOM
						if ($kunena_config->enableforumjump) {
							CKunenaTools::loadTemplate('/forumjump.php');
						}
                        ?>
                    </th>
                </tr>
            </thead>
			<tbody><tr><td></td></tr></tbody>
		</table>
        </div>
</div>
</div>
</div>
</div>
        <!-- Finish: Forum Jump -->

<?php
    }
}
?>
