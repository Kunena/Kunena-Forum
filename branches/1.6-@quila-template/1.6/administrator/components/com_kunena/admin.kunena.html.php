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
 * Based on Joomlaboard Component
 * @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @author TSMF & Jan de Graaff
 **/

defined( '_JEXEC' ) or die();


class html_Kunena {
	// Begin: HEADER FUNC
	function showFbHeader() {
		?>
<style type="text/css">
div.header {
	padding-left:148px !important;
}
div.icon-48-kunena {
	background-image:url(components/com_kunena/images/kunena-logo-48.png);
}

.hideable {
	position: relative;
	visibility: hidden;
}

#kadmin {
	text-align: left;
	width: 100%;
	min-height: 500px;
}

#kadmin-header {
	clear: both;
	width: 100%;
	margin-bottom: 15px;
}

.kadmin-container {
	width: 100%;
}
.kadmin-left {
	width: 190px;
	float:left;
	border-right: 1px solid #ccc;
	margin-right: 20px;
	position: absolute;
}

#kadmin table {
	margin: 0;
	padding 0;
	width:100%;
	border: collapse;
}
#kadmin table td {
	padding 5px;
}

/* Small icons  */
a.icon-cp-sm { background: url('components/com_kunena/images/icons/icon_controlpanel.png') 5px 5px no-repeat; }
a.icon-config-sm { background: url('components/com_kunena/images/icons/icon_config.png') 5px 5px no-repeat; }
a.icon-adminforum-sm { background: url('components/com_kunena/images/icons/icon_forumadmin.png') 5px 5px no-repeat; }
a.icon-profiles-sm { background: url('components/com_kunena/images/icons/icon_useradmin.png') 5px 5px no-repeat; }
a.icon-smilies-sm{ background: url('components/com_kunena/images/icons/icon_smilies.png') 5px 5px no-repeat; }
a.icon-ranks-sm { background: url('components/com_kunena/images/icons/icon_ranks.png') 5px 5px no-repeat; }
a.icon-files-sm { background: url('components/com_kunena/images/icons/icon_uploadedfiles.png') 5px 5px no-repeat; }
a.icon-images-sm { background: url('components/com_kunena/images/icons/icon_uploadedimages.png') 5px 5px no-repeat; }
a.icon-editcss-sm { background: url('components/com_kunena/images/icons/icon_editcss.png') 5px 5px no-repeat; }
a.icon-prune-sm { background: url('components/com_kunena/images/icons/icon_pruneforums.png') 5px 5px no-repeat; }
a.icon-syncusers-sm { background: url('components/com_kunena/images/icons/icon_syncusers.png') 5px 5px no-repeat; }
a.icon-recount-sm { background: url('components/com_kunena/images/icons/icon_recountstats.png') 5px 5px no-repeat; }
a.icon-trash-sm { background: url('components/com_kunena/images/icons/icon_trash.png') 5px 5px no-repeat; }
a.icon-systemreport-sm { background: url('components/com_kunena/images/icons/icon_reportconfig.png') 5px 5px no-repeat; }
a.icon-support-sm { background: url('components/com_kunena/images/icons/icon_supportsite.png') 5px 5px no-repeat; }

/* Large icons */
div.kadmin-functitle.icon-cpanel { background: url('components/com_kunena/images/kcontrolpanel.png') 5px 5px no-repeat; }
div.kadmin-functitle.icon-config { background: url('components/com_kunena/images/kconfig.png') 5px 5px no-repeat; }
div.kadmin-functitle.icon-adminforum { background: url('components/com_kunena/images/kforumadm.png') 5px 5px no-repeat; }
div.kadmin-functitle.icon-profiles { background: url('components/com_kunena/images/kuser.png') 5px 5px no-repeat; }
div.kadmin-functitle.icon-smilies{ background: url('components/com_kunena/images/ksmiley.png') 5px 5px no-repeat; }
div.kadmin-functitle.icon-ranks { background: url('components/com_kunena/images/kranks.png') 5px 5px no-repeat; }
div.kadmin-functitle.icon-files { background: url('components/com_kunena/images/kfiles.png') 5px 5px no-repeat; }
div.kadmin-functitle.icon-images { background: url('components/com_kunena/images/kimages.png') 5px 5px no-repeat; }
div.kadmin-functitle.icon-editcss { background: url('components/com_kunena/images/kcss.png') 5px 5px no-repeat; }
div.kadmin-functitle.icon-prune { background: url('components/com_kunena/images/ktable.png') 5px 5px no-repeat; }
div.kadmin-functitle.icon-syncusers { background: url('components/com_kunena/images/kusers.png') 5px 5px no-repeat; }
div.kadmin-functitle.icon-trash { background: url('components/com_kunena/images/trash.png') 5px 5px no-repeat; }
div.kadmin-functitle.icon-systemreport { background: url('components/com_kunena/images/report_conf.png') 5px 5px no-repeat; }
div.kadmin-functitle.icon-support { background: url('components/com_kunena/images/ktechsupport.png') 5px 5px no-repeat; }

div.kadmin-functitle.no-icon { text-indent: 5px !important; }

table.adminform,
form.adminform{
	margin-top: 10px !important;
}

#kadmin-menu {
	border-top: 1px solid #ccc;
}

#kadmin-menu a {
	display: block;
	font-size: 11px;
	border-left: 1px solid #ccc;
	border-bottom: 1px solid #ccc;
	height: 25px;
	line-height: 25px;
	text-indent: 30px;
}

.kadmin-mainmenu {
	background: #FBFBFB;
	padding: 3px;
}

.kadmin-activemenu {
	background: #fff;
	padding: 5px;
}

.kadmin-submenu {
	background: #fff;
	padding-left: 10px;
	padding: 5px 5px 5px 15px;
}

.kadmin-right {
	background: #fff;
	padding: 5px 5px 5px 220px;
}

.kadmin-footer {
	clear:both;
	font-size: 10px;
	text-align: right;
	padding: 5px;
	background: #FBFBFB;
}

.kadmin-functitle {
	font-size: 16px;
	text-align: left;
	padding: 5px;
	border: 1px solid #CCC;
	font-weight: bold;
	clear: both;
	display: block;
	height: 48px;
	line-height: 44px;
	text-indent: 60px;
}

.kadmin-funcsubtitle {
	font-size: 14px;
	text-align: left;
	padding: 5px;
	border-bottom: 3px solid #7F9DB9;
	font-weight: bold;
	color: #7F9DB9;
	margin: 10px 0 10px 0;
}

.krow0 td {
	padding: 8px 5px;
	text-align: left;
	border-bottom: 1px dotted #ccc;
}

.krow1 td {
	padding: 8px 5px;
	text-align: left;
	border-bottom: 1px dotted #ccc;
}

td.kadmin-tdtitle {
	font-weight: bold;
	padding-left: 10px;
	color: #666;
}

#kcongifcover fieldset {
	border: 1px solid #CFDCEB;
}

#kcongifcover fieldset legend {
	color: #666;
}

table.kadmin-sort td {
	height: 35px;
}

td.kadmin-paging {
	margin: 0 auto;
	background: #F3F3F3 !important;
}

select#pub_access,
select#latestcategory,
select#kforums {
	height: 110px;
}
select#admin_access {
	height: 70px;
}
select#kforums {
	width: 200px;
}

</style>

<!--[if IE]>
<style type="text/css">

table.kadmin-stat caption {
	display:block;
	font-size:12px !important;
	padding-top: 10px !important;
}

</style>
<![endif]-->



<div id="kadmin">
	<div class="kadmin-left">
		<div id="kadmin-menu">
			<?php $stask = JRequest::getVar ( 'task', null ); ?>
				<a class="kadmin-mainmenu icon-cp-sm" href="index.php?option=com_kunena"><?php echo JText::_('COM_KUNENA_CP'); ?></a>
				<a class="kadmin-mainmenu icon-config-sm" href="index.php?option=com_kunena&task=showconfig"><?php echo JText::_('COM_KUNENA_C_FBCONFIG'); ?></a>
				<a class="kadmin-mainmenu icon-adminforum-sm" href="index.php?option=com_kunena&task=showAdministration"><?php echo JText::_('COM_KUNENA_C_FORUM'); ?></a>
				<a class="kadmin-mainmenu icon-profiles-sm" href="index.php?option=com_kunena&task=showprofiles"><?php echo JText::_('COM_KUNENA_C_USER'); ?></a>
				<a class="kadmin-mainmenu icon-smilies-sm" href="index.php?option=com_kunena&task=showsmilies"><?php echo JText::_('COM_KUNENA_EMOTICONS_EMOTICON_MANAGER'); ?></a>
				<a class="kadmin-mainmenu icon-ranks-sm" href="index.php?option=com_kunena&task=ranks"><?php echo JText::_('COM_KUNENA_RANK_MANAGER'); ?></a>
				<a class="kadmin-mainmenu icon-files-sm" href="index.php?option=com_kunena&task=browseFiles"><?php echo JText::_('COM_KUNENA_C_FILES'); ?></a>
				<a class="kadmin-mainmenu icon-images-sm" href="index.php?option=com_kunena&task=browseImages"><?php echo JText::_('COM_KUNENA_C_IMAGES'); ?></a>
				<a class="kadmin-mainmenu icon-editcss-sm" href="index.php?option=com_kunena&task=showCss"><?php echo JText::_('COM_KUNENA_C_CSS'); ?></a>
				<a class="kadmin-mainmenu icon-prune-sm" href="index.php?option=com_kunena&task=pruneforum"><?php echo JText::_('COM_KUNENA_C_PRUNETAB'); ?></a>
				<a class="kadmin-mainmenu icon-syncusers-sm" href="index.php?option=com_kunena&task=syncusers"><?php echo JText::_('COM_KUNENA_SYNC_USERS'); ?></a>
				<a class="kadmin-mainmenu icon-recount-sm" href="index.php?option=com_kunena&task=recount"><?php echo JText::_('COM_KUNENA_RECOUNTFORUMS'); ?></a>
				<a class="kadmin-mainmenu icon-trash-sm" href="index.php?option=com_kunena&task=showtrashview"><?php echo JText::_('COM_KUNENA_TRASH_VIEW'); ?></a>
				<a class="kadmin-mainmenu icon-systemreport-sm" href="index.php?option=com_kunena&task=showsystemreport"><?php echo JText::_('COM_KUNENA_REPORT_SYSTEM'); ?></a>
				<a class="kadmin-mainmenu icon-support-sm" href="http://www.kunena.com" target="_blank"><?php echo JText::_('COM_KUNENA_C_SUPPORT'); ?></a>
		</div>
	</div>
	<div class="kadmin-right">
			<?php
			} // Finish: HEADER FUNC

			// Begin: FOOTER FUNC
			function showFbFooter() {
				$kunena_config = & CKunenaConfig::getInstance ();
				require_once (KUNENA_PATH_LIB . DS . 'kunena.version.php'); ?>
	</div>
	<div class="kadmin-footer">
		<?php echo CKunenaVersion::versionHTML (); ?>
	</div>
</div>

	<?php
	} // Finish: FOOTER FUNC

	function controlPanel() {
		?>

	<div class="kadmin-functitle icon-cpanel"><?php echo JText::_('COM_KUNENA_CP'); ?></div>
	<?php
		$path = JPATH_COMPONENT_ADMINISTRATOR . '/kunena.cpanel.php';

		if (file_exists ( $path )) {
			require $path;
		} else {
			echo '<br />mcap==: ' . JPATH_COMPONENT_ADMINISTRATOR . ' .... help!!';
		}
	}

	function showAdministration($rows, $children, $pageNav, $option) {
		?>
	<div class="kadmin-functitle icon-adminforum"><?php echo JText::_('COM_KUNENA_ADMIN'); ?></div>
	<form action="index.php" method="post" name="adminForm">
		<table class="kadmin-sort">
			<tr>
				<td align="right"><?php echo JText::_('COM_KUNENA_A_DISPLAY'); ?> <?php echo $pageNav->getLimitBox (); ?></td>
			</tr>
		</table>
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
		<tr>
		<th width="20">#</th>

		<th width="20"><input type="checkbox" name="toggle" value=""
			onclick="checkAll(<?php
		echo count ( $rows );
		?>);" /></th>

		<th class="title"><?php
		echo JText::_('COM_KUNENA_CATFOR');
		?></th>

		<th><small><?php
		echo JText::_('COM_KUNENA_CATID');
		?></small></th>

		<th><small><?php
		echo JText::_('COM_KUNENA_LOCKED');
		?></small></th>

		<th><small><?php
		echo JText::_('COM_KUNENA_MODERATED');
		?></small></th>

		<th><small><?php
		echo JText::_('COM_KUNENA_REVIEW');
		?></small></th>

		<th><small><?php
		echo JText::_('COM_KUNENA_CATEGORY_ANONYMOUS');
		?></small></th>

		<th><small><?php
		echo JText::_('COM_KUNENA_ADMIN_POLLS');
		?></small></th>

		<th><small><?php
		echo JText::_('COM_KUNENA_PUBLISHED');
		?></small></th>

		<th><small><?php
		echo JText::_('COM_KUNENA_PUBLICACCESS');
		?></small></th>

		<th><small><?php
		echo JText::_('COM_KUNENA_ADMINACCESS');
		?></small></th>

		<th><small><?php
		echo JText::_('COM_KUNENA_CHECKEDOUT');
		?></small></th>

		<th colspan="2"><small><?php
		echo JText::_('COM_KUNENA_REORDER');
		?></small></th>
	</tr>
	<?php
		$k = 0;
		$i = 0;
		for($i = 0, $n = count ( $rows ); $i < $n; $i ++) {
			$row = $rows [$i];
			?>
	<tr
		<?php
			echo 'class = "row' . $k . '"';
			?>>

		<td width="20" align="right"><?php
			echo $i + $pageNav->limitstart + 1;
			?>
		</td>
		<td width="20"><input type="checkbox" id="cb<?php
			echo $i;
			?>"
			name="cid[]" value="<?php
			echo $row->id;
			?>"
			onClick="isChecked(this.checked);"></td>
		<td width="70%"><a href="#edit"
			onclick="return listItemTask('cb<?php
			echo $i;
			?>','edit')">
		<?php
			//echo ($row->category ? "$row->category/$row->name" : "$row->name");
			echo ($row->treename);
			?>

		</a></td>
		<td align="center"><?php
			echo $row->id;
			?></td>
		<?php if (! $row->category): ?>
		<td colspan="5" align="center"><?php echo JText::_('COM_KUNENA_SECTION') ?></td>
		<?php else: ?>
		<td align="center"><?php
			echo ($row->locked == 1 ? "<img src=\"images/tick.png\">" : "<img src=\"images/publish_x.png\">");
			?>
		</td>
		<td align="center"><?php
			echo ($row->moderated == 1 ? "<img src=\"images/tick.png\">" : "<img src=\"images/publish_x.png\">");
			?>
		</td>
		<td align="center"><?php
			echo ($row->review == 1 ? "<img src=\"images/tick.png\">" : "<img src=\"images/publish_x.png\">");
			?>
		</td>
		<td align="center"><?php
			echo ($row->allow_anonymous == 1 ? "<img src=\"images/tick.png\">" : "<img src=\"images/publish_x.png\">");
			?>
		</td>

		<?php
			$polltask = $row->allow_polls ? 'pollunpublish' : 'pollpublish';
			?>

		<td width="10%" align="center">
			<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo $polltask; ?>')">
			<?php
			echo ($row->allow_polls == 1 ? "<img src=\"images/tick.png\">" : "<img src=\"images/publish_x.png\">");
			?>
			</a>
		</td>
		<?php endif; ?>
		<?php
			$task = $row->published ? 'unpublish' : 'publish';
			$img = $row->published ? 'publish_g.png' : 'publish_x.png';

			if ($row->pub_access == 0) {
				$groupname = JText::_('COM_KUNENA_EVERYBODY');
			} else if ($row->pub_access == - 1) {
				$groupname = JText::_('COM_KUNENA_ALLREGISTERED');
			} else if ($row->pub_access == 1) {
				$groupname = JText::_('COM_KUNENA_NOBODY');
			} else {
				$groupname = $row->groupname == "" ? "&nbsp;" : $row->groupname;
			}

			$adm_groupname = $row->admingroup == "" ? "&nbsp;" : $row->admingroup;
		?>
		<td width="10%" align="center"><a href="javascript: void(0);"
			onclick="return listItemTask('cb<?php
			echo $i;
			?>','<?php
			echo $task;
			?>')"> <img src="images/<?php
			echo $img;
			?>" width="12"
			height="12" border="0" alt="" /></a></td>
		<td width="" align="center"><?php
			echo $groupname;
			?></td>
		<td width="" align="center"><?php
			echo $adm_groupname;
			?></td>
		<td width="15%" align="center"><?php
			echo $row->editor;
			?>&nbsp;
		</td>
		<td class="order" ><span><?php
			echo $pageNav->orderUpIcon ( $i, isset ( $children [$row->parent] [$row->location - 1] ), 'orderup', 'Move Up', 1 );
			?></span> <span><?php
			echo $pageNav->orderDownIcon ( $i, $n, isset ( $children [$row->parent] [$row->location + 1] ), 'orderdown', 'Move Down', 1 );
			?></span></td>

		<?php
			$k = 1 - $k;
		}
		?>
	</tr>
	<tr>
		<td class="kadmin-paging" colspan="14">
			<div class="pagination">
				<div class="limit"><?php echo JText::_('COM_KUNENA_A_DISPLAY'); ?> <?php echo $pageNav->getLimitBox (); ?></div>
					<?php echo $pageNav->getPagesLinks (); ?>
				<div class="limit"><?php echo $pageNav->getResultsCounter (); ?></div>
			</div>
		</td>
	</tr>
</table>

<input type="hidden" name="option" value="<?php
		echo $option;
		?>"> <input type="hidden" name="task" value="showAdministration"> <input
	type="hidden" name="boxchecked" value="0"> <?php
		echo '<input type = "hidden" name = "limitstart" value = "0">';
		?>
</form>

<?php
	}

	function editForum(&$row, $categoryList, $moderatorList, $lists, $accessLists, $option, $kunena_config) {
		?>
        <script language="javascript" type="text/javascript">
            function submitbutton(pressbutton)
            {
                var form = document.adminForm;
                if (pressbutton == 'cancel')
                {
                    submitform(pressbutton);
                    return;
                }
                // do field validation
                if (typeof form.onsubmit == "function") form.onsubmit();
                if (form.name.value == "")
                {
                    alert("<?php
				echo JText::_('COM_KUNENA_ERROR1');
		?>");
                }
                else
                {
                    submitform(pressbutton);
                }
            }
        </script>
		<div class="kadmin-functitle icon-adminforum"><?php echo JText::_('COM_KUNENA_A_CONFIG') ?></div>
		<form action="index.php?option=<?php echo $option; ?>" method="POST" name="adminForm">

		<?php jimport('joomla.html.pane');
			$myTabs = &JPane::getInstance('tabs', array('startOffset'=>0));
			?>
	<dl class="tabs" id="pane">
	<dt><?php echo $row->id ? JText::_('COM_KUNENA_EDIT') : JText::_('COM_KUNENA_ADD'); ?> <?php echo JText::_('COM_KUNENA_CATFOR'); ?></dt>
<dd>
	<fieldset>
			<legend><?php echo JText::_('COM_KUNENA_BASICSFORUMINFO'); ?></legend>
			<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
				<tr>
					<td width="200" valign="top"><?php echo JText::_('COM_KUNENA_PARENT'); ?></td>
					<td><?php echo $categoryList; ?><br /><br /><?php echo JText::_('COM_KUNENA_PARENTDESC'); ?></td>
				</tr>
				<tr>
					<td width="200"><?php echo JText::_('COM_KUNENA_NAMEADD'); ?></td>
					<td><input class="inputbox" type="text" name="name" size="120" maxlength="<?php echo $kunena_config->maxsubject; ?>" value="<?php echo kescape ( $row->name ); ?>"></td>
				</tr>
				<tr>
					<td valign="top"><?php echo JText::_('COM_KUNENA_DESCRIPTIONADD'); ?></td>
					<td>
						<textarea class="inputbox" cols="50" rows="6" name="description" id="description" style="width: 500px"><?php echo kescape ( $row->description ); ?></textarea>
					</td>
				</tr>
				<tr>
					<td valign="top"><?php echo JText::_('COM_KUNENA_HEADERADD'); ?></td>
					<td>
						<textarea class="inputbox" cols="50" rows="6" name="headerdesc" id="headerdesc" style="width: 500px"><?php echo kescape ( $row->headerdesc ); ?></textarea>
					</td>
				</tr>
			</table>
		</fieldset>
		</dd>
		<dt><?php echo JText::_('COM_KUNENA_ADVANCEDDESC'); ?></dt>
		<dd>
			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_ADVANCEDDESCINFO'); ?></legend>
				<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
					<?php if (!$row->id || $row->parent): ?>
					<tr>
						<td><?php echo JText::_('COM_KUNENA_LOCKED1'); ?></td>
 						<td><?php echo $lists ['forumLocked']; ?></td>
						<td><?php echo JText::_('COM_KUNENA_LOCKEDDESC'); ?></td>
					</tr>
					<?php endif; ?>
					<tr>
						<td nowrap="nowrap" valign="top"><?php echo JText::_('COM_KUNENA_PUBACC'); ?></td>
						<td valign="top"><?php echo $accessLists ['pub_access']; ?></td>
						<td><?php echo JText::_('COM_KUNENA_PUBACCDESC'); ?></td>
					</tr>
					<tr>
						<td nowrap="nowrap" valign="top"><?php echo JText::_('COM_KUNENA_CGROUPS'); ?></td>
						<td valign="top"><?php echo $lists ['pub_recurse']; ?></td>
						<td valign="top"><?php echo JText::_('COM_KUNENA_CGROUPSDESC'); ?></td>
					</tr>
					<tr>
						<td valign="top"><?php echo JText::_('COM_KUNENA_ADMINLEVEL'); ?></td>
						<td valign="top"><?php echo $accessLists ['admin_access']; ?></td>
						<td valign="top"><?php echo JText::_('COM_KUNENA_ADMINLEVELDESC'); ?></td>
					</tr>
					<tr>
						<td nowrap="nowrap" valign="top"><?php echo JText::_('COM_KUNENA_CGROUPS1'); ?></td>
						<td valign="top"><?php echo $lists ['admin_recurse']; ?></td>
						<td valign="top"><?php echo JText::_('COM_KUNENA_CGROUPS1DESC'); ?></td>
					</tr>
					<?php if (!$row->id || $row->parent): ?>
					<tr>
						<td nowrap="nowrap" valign="top"><?php echo JText::_('COM_KUNENA_REV'); ?></td>
						<td valign="top"><?php echo $lists ['forumReview']; ?></td>
						<td valign="top"><?php echo JText::_('COM_KUNENA_REVDESC'); ?></td>
					</tr>
					<tr>
						<td nowrap="nowrap" valign="top"><?php echo JText::_('COM_KUNENA_CATEGORY_ANONYMOUS_ALLOW'); ?>:</td>
						<td valign="top"><?php echo $lists ['allow_anonymous']; ?></td>
						<td valign="top"><?php echo JText::_('COM_KUNENA_CATEGORY_ANONYMOUS_ALLOW_DESC'); ?></td>
					</tr>
					<tr>
						<td nowrap="nowrap" valign="top"><?php echo JText::_('COM_KUNENA_CATEGORY_ANONYMOUS_DEFAULT'); ?>:</td>
						<td valign="top"><?php echo $lists ['post_anonymous']; ?></td>
						<td valign="top"><?php echo JText::_('COM_KUNENA_CATEGORY_ANONYMOUS_DEFAULT_DESC'); ?></td>
					</tr>
					<tr>
						<td nowrap="nowrap" valign="top"><?php echo JText::_('COM_KUNENA_A_POLL_CATEGORIES_ALLOWED'); ?>:</td>
						<td valign="top"><?php echo $lists ['allow_polls']; ?></td>
						<td valign="top"><?php echo JText::_('COM_KUNENA_A_POLL_CATEGORIES_ALLOWED_DESC'); ?></td>
					</tr>
					<?php endif; ?>
				</table>
			</fieldset>

			<?php if (!$row->id || $row->parent): ?>

			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_ADVANCEDDISPINFO'); ?></legend>
				<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
					<tr>
						<td><?php echo JText::_('COM_KUNENA_CLASS_SFX'); ?></td>
						<td><input class="inputbox" type="text" name="class_sfx" size="20" maxlength="20" value="<?php echo $row->class_sfx; ?>"></td>
						<td><?php echo JText::_('COM_KUNENA_CLASS_SFXDESC'); ?></td>
					</tr>
				</table>
			</fieldset>
			</dd>
			<dt><?php echo JText::_('COM_KUNENA_MODNEWDESC'); ?></dt>
			<dd>
			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_MODHEADER'); ?></legend>
				   <table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
						<tr>
						<td nowrap="nowrap" valign="top"><?php echo JText::_('COM_KUNENA_MOD'); ?></td>
						<td valign="top"><?php echo $lists ['forumModerated']; ?></td>
						<td valign="top"><?php echo JText::_('COM_KUNENA_MODDESC'); ?></td>
					</tr>
				</table>

				<?php 	if ($row->moderated) {
				?>

				<div class="kadmin-funcsubtitle"><?php echo JText::_('COM_KUNENA_MODSASSIGNED'); ?></div>

				<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
					<tr>
						<th width="20">#</th>
						<th width="20"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count ( $moderatorList ); ?>);" /></th>
						<th align="left"><?php echo JText::_('COM_KUNENA_USRL_NAME'); ?></th>
						<th align="left"><?php echo JText::_('COM_KUNENA_USRL_USERNAME'); ?></th>
						<th align="left"><?php echo JText::_('COM_KUNENA_USRL_EMAIL'); ?></th>
						<th align="center"><?php echo JText::_('COM_KUNENA_PUBLISHED'); ?></th>
					</tr>

					<?php if (count ( $moderatorList ) == 0) {
							echo "<tr><td colspan=\"5\">" . JText::_('COM_KUNENA_NOMODS') . "</td></tr>";
						} else {
							$k = 1;
							$i = 0;
								foreach ( $moderatorList as $ml ) {
								$k = 1 - $k;
								?>
						<tr class="row<?php 	echo $k;
								?>">
					<td width="20"><?php echo $i + 1;
								?></td>
						<td width="20"><input type="checkbox"
						id="cb<?php echo $i;
								?>" name="cid[]"
						value="<?php echo $ml->id;
								?>"
						onClick="isChecked(this.checked);"></td>
							<td><?php echo kescape($ml->name);
								?></td>
							<td><?php echo kescape($ml->username);
								?></td>
							<td><?php echo kescape($ml->email);
								?></td>
							<td align="center"><img src="images/tick.png" alt=""></td>
						</tr>
									<?php 	$i ++;
							}
						}
						?>
			</table>
			<?php 	} ?>
		<?php endif; ?>
		<input type="hidden" name="id" value="<?php echo $row->id; ?>">
		<input type="hidden" name="option"
			value="<?php
			echo $option;
			?>">
		<input type="hidden" name="task" value="showAdministration"> <?php
			if ($row->ordering != 0) {
				echo '<input type="hidden" name="ordering" value="' . $row->ordering . '">';
			}
			?>
	</fieldset>
</dd>
</dl>
</form>


	<?php
		}

		function showConfig(&$kunena_config, &$lists, $option) {
			jimport('joomla.html.pane');
			$myTabs = &JPane::getInstance('tabs', array('startOffset'=>0));
			?>

	<div id="kadmin-configtabs">
		<div class="kadmin-functitle icon-config"><?php echo JText::_('COM_KUNENA_A_CONFIG') ?></div>
		<form action="index.php" method="post" name="adminForm">

		<dl class="tabs" id="pane">

		<dt><?php echo JText::_('COM_KUNENA_A_BASICS') ?></dt>
		<dd>
			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_A_BASIC_SETTINGS') ?></legend>

				<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
							<tr align="center" valign="middle">
						<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_A_BOARD_TITLE') ?>
						</td>
								<td align="left" valign="top" width="25%"><input type="text"
							name="cfg_board_title"
							value="<?php echo kescape(stripslashes ( $kunena_config->board_title ));
						?>" /></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_BOARD_TITLE_DESC') ?>
						</td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_EMAIL') ?></td>
								<td align="left" valign="top"><input type="text" name="cfg_email"
							value="<?php echo kescape($kunena_config->email);
						?>" /></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_EMAIL_DESC') ?></td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_BOARD_OFFLINE') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['board_offline'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_BOARD_OFFLINE_DESC') ?>
						</td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_KUNENA_SESSION_TIMEOUT') ?>
						</td>
								<td align="left" valign="top"><input type="text"
							name="cfg_fbsessiontimeout"
							value="<?php echo kescape($kunena_config->fbsessiontimeout);
						?>" /></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_KUNENA_SESSION_TIMEOUT_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_BOARD_OFFLINE_MES') ?></td>
						<td align="left" valign="top" colspan="2">
							<textarea name="cfg_offline_message" rows="3" cols="50"><?php echo kescape(stripslashes ( $kunena_config->offline_message )); ?></textarea>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_RSS') ?></td>
								<td align="left" valign="top"><?php echo $lists ['enablerss'];
						?>
						</td>
								<td align="left" valign="top"><img
							src="<?php echo JURI::root ();
						?>/images/M_images/livemarks.png"
							alt="" /> <?php echo JText::_('COM_KUNENA_A_RSS_DESC') ?></td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_PDF') ?></td>
								<td align="left" valign="top"><?php echo $lists ['enablepdf'];
						?>
						</td>
						<td align="left" valign="top"><img src="<?php echo JURI::root (); ?>/images/M_images/pdf_button.png" alt="" /> <?php echo JText::_('COM_KUNENA_A_PDF_DESC') ?></td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_DEBUG_MODE') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['debug'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_DEBUG_MODE_DESC') ?>
						</td>
					</tr>
				</table>
			</fieldset>

			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_SEO_SETTINGS') ?></legend>
				<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
					<tr align="center" valign="middle">
						<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_SEF') ?></td>
						<td align="left" valign="top" width="25%"><?php echo $lists ['sef']; ?></td>
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_SEF_DESC') ?></td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_SEF_CATS') ?></td>
						<td align="left" valign="top" width="25%"><?php echo $lists ['sefcats']; ?></td>
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_SEF_CATS_DESC') ?></td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_SEF_UTF8') ?></td>
						<td align="left" valign="top" width="25%"><?php echo $lists ['sefutf8']; ?></td>
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_SEF_UTF8_DESC') ?></td>
					</tr>
				</table>
			</fieldset>
			</dd>
			<dt><?php echo JText::_('COM_KUNENA_A_FRONTEND') ?></dt>
			<dd>
			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_A_LOOKS') ?></legend>
				<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
					<tr align="center" valign="middle">
						<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_A_THREADS') ?>
						</td>
								<td align="left" valign="top" width="25%"><input type="text"
							name="cfg_threads_per_page"
							value="<?php echo kescape($kunena_config->threads_per_page);
						?>" /></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_THREADS_DESC') ?></td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_MESSAGES') ?></td>
								<td align="left" valign="top"><input type="text"
							name="cfg_messages_per_page"
							value="<?php echo kescape($kunena_config->messages_per_page);
						?>" /></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_MESSAGES_DESC') ?>
						</td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_MESSAGES_SEARCH') ?>
						</td>
								<td align="left" valign="top"><input type="text"
							name="cfg_messages_per_page_search"
							value="<?php echo kescape($kunena_config->messages_per_page_search);
						?>" /></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_MESSAGES_DESC_SEARCH') ?>
						</td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_HISTORY') ?></td>
								<td align="left" valign="top"><?php echo $lists ['showhistory'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_HISTORY_DESC') ?></td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_HISTLIM') ?></td>
								<td align="left" valign="top"><input type="text"
							name="cfg_historylimit"
							value="<?php echo kescape($kunena_config->historylimit);
						?>" /></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_HISTLIM_DESC') ?></td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_CFG_POST_DATEFORMAT') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['post_dateformat'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_CFG_POST_DATEFORMAT_DESC') ?>
						</td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_CFG_POST_DATEFORMAT_HOVER') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['post_dateformat_hover'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_CFG_POST_DATEFORMAT_HOVER_DESC') ?>
						</td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_SHOWNEW') ?></td>
								<td align="left" valign="top"><?php echo $lists ['shownew'];
						?></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_SHOWNEW_DESC') ?></td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_NEWCHAR') ?></td>
								<td align="left" valign="top"><input type="text" name="cfg_newchar"
							value="<?php echo kescape(stripslashes ( $kunena_config->newchar ));
						?>" /></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_NEWCHAR_DESC') ?></td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_MAMBOT_SUPPORT') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['jmambot'];
						?></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_MAMBOT_SUPPORT_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_TEMPLATE') ?></td>
								<td align="left" valign="top"><?php echo $lists ['template'];
						?></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_TEMPLATE_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_TEMPLATE_IMAGE_PATH') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['templateimagepath'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_TEMPLATE_IMAGE_PATH_DESC') ?>
						</td>
					</tr>
					<?php if ($kunena_config->template == 'default') { ?>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_TEMPLATE_COLOR_SELECT') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['theme'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_TEMPLATE_COLOR_SELECT_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_TEMPLATE_BUTTONSTYLE_SELECT') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['buttonstyle'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_TEMPLATE_BUTTONSTYLE_SELECT_DESC') ?>
						</td>
					</tr>
					<?php } ?>
							<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_AV_POSITION') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['avposition'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_AV_POSITION_DESC') ?>
						</td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_FBDEFAULT_PAGE') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['fbdefaultpage'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_FBDEFAULT_PAGE_DESC') ?>
						</td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_SHOW_ANNOUNCEMENT') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['showannouncement'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_SHOW_ANNOUNCEMENT_DESC') ?>
						</td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_SHOW_AVATAR_ON_CAT') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['avataroncat'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_SHOW_AVATAR_ON_CAT_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_CATIMAGEPATH') ?>
						</td>
								<td align="left" valign="top"><input type="text"
							name="cfg_catimagepath"
							value="<?php echo kescape($kunena_config->catimagepath);
						?>" /></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_CATIMAGEPATH_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['showchildcaticon'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST_DESC') ?>
						</td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_ANN_MODID') ?></td>
								<td align="left" valign="top"><input type="text" name="cfg_annmodid"
							value="<?php echo kescape($kunena_config->annmodid);
						?>" /></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_ANN_MODID_DESC') ?>
						</td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_TAWIDTH') ?></td>
								<td align="left" valign="top"><input type="text" name="cfg_rtewidth"
							value="<?php echo kescape($kunena_config->rtewidth);
						?>" /></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_TAWIDTH_DESC') ?></td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_TAHEIGHT') ?></td>
								<td align="left" valign="top"><input type="text" name="cfg_rteheight"
							value="<?php echo kescape($kunena_config->rteheight);
						?>" /></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_TAHEIGHT_DESC') ?>
						</td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_RULESPAGE') ?></td>
								<td align="left" valign="top"><?php echo $lists ['enablerulespage'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_RULESPAGE_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_RULESPAGE_IN_FB') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['rules_infb'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_RULESPAGE_IN_KUNENA_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_RULESPAGE_CID') ?>
						</td>
								<td align="left" valign="top"><input type="text" name="cfg_rules_cid"
							value="<?php echo kescape($kunena_config->rules_cid);
						?>" /></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_RULESPAGE_CID_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_RULESPAGE_LINK') ?>
						</td>
								<td align="left" valign="top"><input type="text" name="cfg_rules_link"
							value="<?php echo kescape($kunena_config->rules_link);
						?>" /></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_RULESPAGE_LINK_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_HELPPAGE') ?></td>
								<td align="left" valign="top"><?php echo $lists ['enablehelppage'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_HELPPAGE_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_HELPPAGE_IN_FB') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['help_infb'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_HELPPAGE_IN_KUNENA_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_HELPPAGE_CID') ?>
						</td>
								<td align="left" valign="top"><input type="text" name="cfg_help_cid"
							value="<?php echo kescape($kunena_config->help_cid);
						?>" /></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_HELPPAGE_CID_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_HELPPAGE_LINK') ?>
						</td>
								<td align="left" valign="top"><input type="text" name="cfg_help_link"
							value="<?php echo kescape($kunena_config->help_link);
						?>" /></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_HELPPAGE_LINK_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_FORUM_JUMP') ?></td>
								<td align="left" valign="top"><?php echo $lists ['enableforumjump'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_FORUM_JUMP_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_COM_A_REPORT') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['reportmsg'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_COM_A_REPORT_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_COM_A_ORDERING_SYSTEM') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['ordering_system'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_COM_A_REPORT_ORDERING_SYSTEM_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_COM_A_HIDE_IP') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['hide_ip'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_COM_A_HIDE_IP_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_COM_A_LATESTCATEGORY_IN') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['latestcategory_in'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_COM_A_LATESTCATEGORY_IN_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_COM_A_LATESTCATEGORY') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['latestcategory'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_COM_A_LATESTCATEGORY_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_COM_A_TOPICICONS') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['topicicons'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_COM_A_TOPCIICONS_DESC') ?>
						</td>
					</tr>
				</table>
			</fieldset>
			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_A_LENGTHS') ?></legend>
				<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
					<tr align="center" valign="middle">
						<td align="left" width="25%" valign="top"><?php echo JText::_('COM_KUNENA_A_SUBJECTLENGTH') ?>
						</td>
								<td align="left" width="25%" valign="top"><input type="text"
							name="cfg_maxsubject"
							value="<?php echo kescape($kunena_config->maxsubject);
						?>" /></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_SUBJECTLENGTH_DESC') ?>
						</td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_SIGNATURE') ?></td>
								<td align="left" valign="top"><input type="text" name="cfg_maxsig"
							value="<?php echo kescape($kunena_config->maxsig);
						?>" /></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_SIGNATURE_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_PESONNALTEXT') ?></td>
								<td align="left" valign="top"><input type="text"
							name="cfg_maxpersotext"
							value="<?php echo kescape($kunena_config->maxpersotext);
						?>" /></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_PESONNALTEXT_DESC') ?>
						</td>
					</tr>
				</table>
			</fieldset>
			</dd>

			<dt><?php echo JText::_('COM_KUNENA_A_USERS') ?></dt>
			<dd>
			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_A_USER_RELATED') ?></legend>
				<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
					<tr align="center" valign="middle">
						<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_A_USERNAME') ?>
						</td>
								<td align="left" valign="top" width="25%"><?php echo $lists ['username'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_USERNAME_DESC') ?>
						</td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_ASK_EMAIL') ?></td>
								<td align="left" valign="top"><?php echo $lists ['askemail'];
						?></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_ASK_EMAIL_DESC') ?>
						</td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_SHOWMAIL') ?></td>
								<td align="left" valign="top"><?php echo $lists ['showemail'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_SHOWMAIL_DESC') ?>
						</td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_USERSTATS') ?></td>
								<td align="left" valign="top"><?php echo $lists ['showuserstats'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_USERSTATS_DESC') ?>
						</td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_KARMA') ?></td>
								<td align="left" valign="top"><?php echo $lists ['showkarma'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_KARMA_DESC') ?></td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_USER_EDIT') ?></td>
								<td align="left" valign="top"><?php echo $lists ['useredit'];
						?></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_USER_EDIT_DESC') ?>
						</td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_USER_EDIT_TIME') ?>
						</td>
								<td align="left" valign="top"><input type="text"
							name="cfg_useredittime"
							value="<?php echo kescape($kunena_config->useredittime);
						?>" /></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_USER_EDIT_TIME_DESC') ?>
						</td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_USER_EDIT_TIMEGRACE') ?>
						</td>
								<td align="left" valign="top"><input type="text"
							name="cfg_useredittimegrace"
							value="<?php echo kescape($kunena_config->useredittimegrace);
						?>" /></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_USER_EDIT_TIMEGRACE_DESC') ?>
						</td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_USER_MARKUP') ?></td>
								<td align="left" valign="top"><?php echo $lists ['editmarkup'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_USER_MARKUP_DESC') ?>
						</td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_SUBSCRIPTIONS') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['allowsubscriptions'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_SUBSCRIPTIONS_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_SUBSCRIPTIONSCHECKED') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['subscriptionschecked'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_SUBSCRIPTIONSCHECKED_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_FAVORITES') ?></td>
								<td align="left" valign="top"><?php echo $lists ['allowfavorites'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_FAVORITES_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_GHOSTMESSAGE') ?></td>
								<td align="left" valign="top"><?php echo $lists ['boxghostmessage'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_GHOSTMESSAGE_DESC') ?>
						</td>
					</tr>
				</table>
			</fieldset>
			</dd>
			<dt><?php echo JText::_('COM_KUNENA_A_SECURITY') ?></dt>
			<dd>
			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_A_SECURITY_SETTINGS') ?></legend>
				<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">

					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_PUBWRITE') ?></td>
								<td align="left" valign="top"><?php echo $lists ['pubwrite'];
						?></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_PUBWRITE_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_A_REGISTERED_ONLY') ?>
						</td>
								<td align="left" valign="top" width="25%"><?php echo $lists ['regonly'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_REG_ONLY_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_DELETEMESSAGE') ?></td>
								<td align="left" valign="top"><?php echo $lists ['userdeletetmessage'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_DELETEMESSAGE_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_ALLOW_USERNAME_CHANGE'); ?></td>
						<td align="left" valign="top" width="25%"><?php echo $lists ['usernamechange']; ?></td>
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_ALLOW_USERNAME_CHANGE_DESC'); ?></td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_ALLOW_NICKNAME') ?></td>
								<td align="left" valign="top"><?php echo $lists ['changename'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_ALLOW_NICKNAME_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_FLOOD') ?></td>
								<td align="left" valign="top"><input type="text"
							name="cfg_floodprotection"
							value="<?php echo kescape($kunena_config->floodprotection);
						?>" /></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_FLOOD_DESC') ?></td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_MODERATION') ?></td>
								<td align="left" valign="top"><?php echo $lists ['mailmod'];
						?></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_MODERATION_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_MAIL_ADMIN') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['mailadmin'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_MAIL_ADMIN_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_CAPTCHA_ON') ?></td>
								<td align="left" valign="top"><?php echo $lists ['captcha'];
						?></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_CAPTCHA_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_MAILFULL');
						?></td>
						<td align="left" valign="top"><?php echo $lists ['mailfull'];
						?></td>
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_MAILFULL_DESC');
						?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_SHOW_ONLINE_USERS');
						?></td>
						<td align="left" valign="top"><?php echo $lists ['onlineusers'];
						?></td>
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_SHOW_ONLINE_USERS_DESC');
						?>
						</td>
					</tr>
				</table>
			</fieldset>
			</dd>
			<dt><?php echo JText::_('COM_KUNENA_A_AVATARS') ?></dt>
			<dd>
			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_A_AVATAR_SETTINGS') ?></legend>
				<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_AVATARUPLOAD') ?></td>
								<td align="left" valign="top"><?php echo $lists ['allowavatarupload'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_AVATARUPLOAD_DESC') ?>
						</td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_AVATARGALLERY') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['allowavatargallery'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_AVATARGALLERY_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_IMAGE_PROCESSOR') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['imageprocessor'];
						?>
						</td>
						<td align="left" valign="top"><?php 				$fb_gd = intval ( KUNENA_gdVersion () );
						if ($fb_gd > 0) {
							$fbmsg = JText::_('COM_KUNENA_GD_INSTALLED') . $fb_gd;
						} elseif ($fb_gd == - 1) {
							$fbmsg = JText::_('COM_KUNENA_GD_NO_VERSION');
						} else {
							$fbmsg = JText::_('COM_KUNENA_GD_NOT_INSTALLED') . '<a href="http://www.php.net/gd" target="_blank">http://www.php.net/gd</a>';
						}
								echo $fbmsg;
								?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_AVSIZE') ?></td>
								<td align="left" valign="top"><input type="text" name="cfg_avatarsize"
							value="<?php echo kescape($kunena_config->avatarsize);
						?>" /></td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_AVATAR_QUALITY') ?>
						</td>
								<td align="left" valign="top"><input type="text"
							name="cfg_avatarquality"
							value="<?php echo kescape($kunena_config->avatarquality);
						?>" /> %</td>
					</tr>
				</table>
			</fieldset>
			</dd>
			<dt><?php echo JText::_('COM_KUNENA_A_UPLOADS') ?></dt>
			<dd>
			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_A_IMAGE') ?></legend>
				<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
					<tr align="center" valign="middle">
						<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_A_IMAGEUPLOAD') ?>
						</td>
								<td align="left" valign="top" width="25%"><?php echo $lists ['allowimageupload'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_IMAGEUPLOAD_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_IMAGEREGUPLOAD') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['allowimageregupload'];
						?>
						</td>
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_IMAGEREGUPLOAD_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_SHOWIMGFORGUEST') ?>
						</td>
								<td align="left" valign="top" width="25%"><?php echo $lists ['showimgforguest'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_SHOWIMGFORGUEST_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_IMAGEALLOWEDTYPES') ?>
						</td>
								<td align="left" valign="top"><input type="text" name="cfg_imagetypes"
							value="<?php echo kescape($kunena_config->imagetypes);
						?>" /></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_IMAGEALLOWEDTYPES_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_IMAGECHECKMIMETYPES') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['checkmimetypes'];
						?>
						</td>
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_IMAGECHECKMIMETYPES_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_IMAGEALLOWEDMIMETYPES') ?>
						</td>
								<td align="left" valign="top"><input type="text" name="cfg_imagemimetypes"
							value="<?php echo kescape($kunena_config->imagemimetypes);
						?>" /></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_IMAGEALLOWEDMIMETYPES_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_IMGSIZE') ?></td>
								<td align="left" valign="top"><input type="text" name="cfg_imagesize"
							value="<?php echo kescape($kunena_config->imagesize);
						?>" /></td>
						<td align="left" valign="top">
							<?php echo JText::sprintf('COM_KUNENA_A_IMGSIZE_DESC',
														ini_get('post_max_size'), ini_get('upload_max_filesize'),
														function_exists('php_ini_loaded_file') ? php_ini_loaded_file() : '') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_IMGWIDTH') ?></td>
								<td align="left" valign="top"><input type="text" name="cfg_imagewidth"
							value="<?php echo kescape($kunena_config->imagewidth);
						?>" /></td>
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_IMGWIDTH_DESC') ?></td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_IMGHEIGHT') ?></td>
								<td align="left" valign="top"><input type="text"
							name="cfg_imageheight"
							value="<?php echo kescape($kunena_config->imageheight);
						?>" /></td>
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_IMGHEIGHT_DESC') ?></td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_IMGTHUMBWIDTH') ?></td>
								<td align="left" valign="top"><input type="text" name="cfg_thumbwidth"
							value="<?php echo kescape($kunena_config->thumbwidth);
						?>" /></td>
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_IMGTHUMBWIDTH_DESC') ?></td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_IMGTHUMBHEIGHT') ?></td>
								<td align="left" valign="top"><input type="text"
							name="cfg_thumbheight"
							value="<?php echo kescape($kunena_config->thumbheight);
						?>" /></td>
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_IMGTHUMBHEIGHT_DESC') ?></td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_IMGQUALITY') ?></td>
								<td align="left" valign="top"><input type="text" name="cfg_imagequality"
							value="<?php echo kescape($kunena_config->imagequality);
						?>" /></td>
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_IMGQUALITY_DESC') ?></td>
					</tr>
				</table>
			</fieldset>
			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_A_FILE') ?></legend>
				<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
					<tr align="center" valign="middle">
						<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_A_FILEUPLOAD') ?>
						</td>
								<td align="left" valign="top" width="25%"><?php echo $lists ['allowfileupload'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_FILEUPLOAD_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_FILEREGUPLOAD') ?>
						</td>
						<td align="left" valign="top"><?php echo $lists ['allowfileregupload'];
						?>
						</td>
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_FILEREGUPLOAD_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_SHOWFILEFORGUEST') ?>
						</td>
								<td align="left" valign="top" width="25%"><?php echo $lists ['showfileforguest'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_SHOWFILEFORGUEST_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_FILEALLOWEDTYPES') ?>
						</td>
								<td align="left" valign="top"><input type="text" name="cfg_filetypes"
							value="<?php echo kescape($kunena_config->filetypes);
						?>" /></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_FILEALLOWEDTYPES_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_FILESIZE') ?></td>
						<td align="left" valign="top"><input type="text" name="cfg_filesize"
							value="<?php echo kescape($kunena_config->filesize);
						?>" /></td>
						<td align="left" valign="top">
							<?php echo JText::sprintf('COM_KUNENA_A_FILESIZE_DESC',
														ini_get('post_max_size'), ini_get('upload_max_filesize'),
														function_exists('php_ini_loaded_file') ? php_ini_loaded_file() : '') ?>
						</td>
					</tr>
				</table>
			</fieldset>
			</dd>
			<dt><?php echo JText::_('COM_KUNENA_A_RANKING') ?></dt>
			<dd>
			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_A_RANKING_SETTINGS') ?></legend>
				<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
					<tr align="center" valign="middle">
						<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_A_RANKING') ?>
						</td>
						<td align="left" valign="top" width="25%"><?php echo $lists ['showranking'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_RANKING_DESC') ?></td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_RANKINGIMAGES') ?>
						</td>
						<td align="left" valign="top"><?php echo $lists ['rankimages'];
						?>
						</td>
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_RANKINGIMAGES_DESC') ?>
						</td>
					</tr>
				</table>
			</fieldset>
			</dd>
			<dt><?php echo JText::_('COM_KUNENA_A_BBCODE') ?></dt>
			<dd>
			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_A_BBCODE_SETTINGS') ?></legend>
				<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_DISEMOTICONS') ?></td>
								<td align="left" valign="top"><?php echo $lists ['disemoticons'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_DISEMOTICONS_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_A_SHOWSPOILERTAG') ?>
						</td>
								<td align="left" valign="top" width="25%"><?php echo $lists ['showspoilertag'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_SHOWSPOILERTAG_DESC') ?>
						</td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_A_SHOWVIDEOTAG') ?>
						</td>
								<td align="left" valign="top" width="25%"><?php echo $lists ['showvideotag'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_SHOWVIDEOTAG_DESC') ?>
						</td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_A_SHOWEBAYTAG') ?>
						</td>
								<td align="left" valign="top" width="25%"><?php echo $lists ['showebaytag'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_SHOWEBAYTAG_DESC') ?>
						</td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_EBAYLANGUAGECODE') ?>
						</td>
								<td align="left" valign="top"><input type="text"
							name="cfg_ebaylanguagecode"
							value="<?php echo kescape($kunena_config->ebaylanguagecode);
						?>" /></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_EBAYLANGUAGECODE_DESC') ?>
						</td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_A_TRIMLONGURLS') ?>
						</td>
								<td align="left" valign="top" width="25%"><?php echo $lists ['trimlongurls'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_TRIMLONGURLS_DESC') ?>
						</td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_TRIMLONGURLSFRONT') ?>
						</td>
								<td align="left" valign="top"><input type="text"
							name="cfg_trimlongurlsfront"
							value="<?php echo kescape($kunena_config->trimlongurlsfront);
						?>" /></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_TRIMLONGURLSFRONT_DESC') ?>
						</td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_TRIMLONGURLSBACK') ?>
						</td>
								<td align="left" valign="top"><input type="text"
							name="cfg_trimlongurlsback"
							value="<?php echo kescape($kunena_config->trimlongurlsback);
						?>" /></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_TRIMLONGURLSBACK_DESC') ?>
						</td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_AUTOEMBEDYOUTUBE') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['autoembedyoutube'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_AUTOEMBEDYOUTUBE_DESC') ?>
						</td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_AUTOEMBEDEBAY') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['autoembedebay'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_AUTOEMBEDEBAY_DESC') ?>
						</td>
					</tr>
							<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_HIGHLIGHTCODE') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['highlightcode'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_HIGHLIGHTCODE_DESC') ?>
						</td>
					</tr>
				</table>
			</fieldset>
			</dd>
			<dt><?php echo JText::_('COM_KUNENA_A_INTEGRATION') ?></dt>
			<dd>
				<fieldset>
					<legend><?php echo JText::_('COM_KUNENA_INTEGRATION_TITLE') ?></legend>
					<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
						<tr align="center" valign="middle">
							<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_INTEGRATION_AVATAR') ?></td>
							<td align="left" valign="top" width="25%"><?php echo $lists ['integration_avatar']; ?></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_INTEGRATION_AVATAR_DESC') ?></td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_INTEGRATION_PROFILE') ?></td>
							<td align="left" valign="top" width="25%"><?php echo $lists ['integration_profile']; ?></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_INTEGRATION_PROFILE_DESC') ?></td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_INTEGRATION_LOGIN') ?></td>
							<td align="left" valign="top" width="25%"><?php echo $lists ['integration_login']; ?></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_INTEGRATION_LOGIN_DESC') ?></td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_INTEGRATION_PRIVATE') ?></td>
							<td align="left" valign="top" width="25%"><?php echo $lists ['integration_private']; ?></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_INTEGRATION_PRIVATE_DESC') ?></td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_INTEGRATION_ACTIVITY') ?></td>
							<td align="left" valign="top" width="25%"><?php echo $lists ['integration_activity']; ?></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_INTEGRATION_ACTIVITY_DESC') ?></td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_INTEGRATION_ACCESS') ?></td>
							<td align="left" valign="top" width="25%"><?php echo $lists ['integration_access']; ?></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_INTEGRATION_ACCESS_DESC') ?></td>
						</tr>
					</table>
				</fieldset>
				<fieldset>
					<legend><?php echo JText::_('COM_KUNENA_AUP_ALPHAUSERPOINTS'); ?></legend>
					<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
						<tr align="center" valign="middle">
							<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_AUP_ENABLED_POINTS_IN_PROFILE'); ?></td>
							<td align="left" valign="top" width="25%"><?php echo $lists ['alphauserpoints']; ?></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_AUP_ENABLED_POINTS_IN_PROFILE_DESC'); ?></td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_AUP_ENABLED_RULES'); ?></td>
							<td align="left" valign="top" width="25%"><?php echo $lists ['alphauserpointsrules']?></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_AUP_ENABLED_RULES_DESC'); ?></td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_AUP_MINIMUM_POINTS_ON_REPLY'); ?></td>
							<td align="left" valign="top" width="25%"><input type="text"
								name="cfg_alphauserpointsnumchars"
								value="<?php echo kescape($kunena_config->alphauserpointsnumchars); ?>" /></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_AUP_MINIMUM_POINTS_ON_REPLY_DESC'); ?></td>
						</tr>
					</table>
				</fieldset>
				<fieldset>
					<legend><?php echo JText::_('COM_KUNENA_A_JS_ACTIVITYSTREAM_INTEGRATION') ?></legend>
					<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
						<tr align="center" valign="middle">
							<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_A_JS_ACTIVITYSTREAM_INTEGRATION') ?> </td>
							<td align="left" valign="top" width="25%"><?php echo $lists ['js_actstr_integration']; ?> </td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_JS_ACTIVITYSTREAM_INTEGRATION_DESC') ?></td>
						</tr>
					</table>
				</fieldset>
				</dd>
				<dt><?php echo JText::_('COM_KUNENA_ADMIN_RSS') ?></dt>
				<dd>
				<fieldset>
					<legend><?php echo JText::_('COM_KUNENA_ADMIN_RSS_SETTINGS') ?></legend>
					<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
						<tr align="center" valign="middle">
							<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_A_RSS_TYPE') ?></td>
							<td align="left" valign="top" width="25%"><?php echo $lists ['rss_type']; ?></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_RSS_TYPE_DESC') ?></td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_A_RSS_SPEC') ?></td>
							<td align="left" valign="top" width="25%"><?php echo $lists ['rss_specification']; ?></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_RSS_SPEC_DESC') ?></td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_A_RSS_TIMELIMIT') ?></td>
							<td align="left" valign="top" width="25%"><?php echo $lists ['rss_timelimit']; ?></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_RSS_TIMELIMIT_DESC') ?></td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_A_RSS_LIMIT') ?></td>
							<td align="left" valign="top" width="25%"><input type="text"
								name="cfg_rss_limit"
								value="<?php echo kescape($kunena_config->rss_limit); ?>" /></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_RSS_LIMIT_DESC') ?></td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_A_RSS_INCLUDED_CATEGORIES') ?></td>
							<td align="left" valign="top" width="25%"><input type="text"
								name="cfg_rss_included_categories"
								value="<?php echo kescape($kunena_config->rss_included_categories); ?>" /></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_RSS_INCLUDED_CATEGORIES_DESC') ?></td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_A_RSS_EXCLUDED_CATEGORIES') ?></td>
							<td align="left" valign="top" width="25%"><input type="text"
								name="cfg_rss_excluded_categories"
								value="<?php echo kescape($kunena_config->rss_excluded_categories); ?>" /></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_RSS_EXCLUDED_CATEGORIES_DESC') ?></td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_A_RSS_ALLOW_HTML') ?></td>
							<td align="left" valign="top" width="25%"><?php echo $lists ['rss_allow_html']; ?></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_RSS_ALLOW_HTML_DESC') ?></td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_A_RSS_AUTHOR_FORMAT') ?></td>
							<td align="left" valign="top" width="25%"><?php echo $lists ['rss_author_format']; ?></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_RSS_AUTHOR_FORMAT_DESC') ?></td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_A_RSS_WORD_COUNT') ?></td>
							<td align="left" valign="top" width="25%"><?php echo $lists ['rss_word_count']; ?></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_RSS_WORD_COUNT_DESC') ?></td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_A_RSS_OLD_TITLES') ?></td>
							<td align="left" valign="top" width="25%"><?php echo $lists ['rss_old_titles']; ?></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_RSS_OLD_TITLES_DESC') ?></td>
						</tr>
					</table>
				</fieldset>
				</dd>
				<dt><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_PLUGINS') ?></dt>
				<dd>
				<fieldset>
					<legend><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST') ?></legend>
					<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
						<tr align="center" valign="middle">
							<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_ROWS') ?></td>
							<td align="left" valign="top" width="25%"><input type="text"
								name="cfg_userlist_rows"
								value="<?php echo kescape($kunena_config->userlist_rows); ?>" /></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_ROWS_DESC') ?></td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE') ?></td>
							<td align="left" valign="top"><?php echo $lists ['userlist_online']; ?></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE_DESC') ?></td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_AVATAR') ?></td>
							<td align="left" valign="top"><?php echo $lists ['userlist_avatar']; ?></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_USERLIST_AVATAR_DESC') ?></td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_NAME') ?></td>
							<td align="left" valign="top"><?php echo $lists ['userlist_name']; ?></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_NAME_DESC') ?></td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME') ?></td>
							<td align="left" valign="top"><?php echo $lists ['userlist_username']; ?></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME_DESC') ?></td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_POSTS') ?></td>
							<td align="left" valign="top"><?php echo $lists ['userlist_posts']; ?></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_POSTS_DESC') ?></td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_KARMA') ?></td>
							<td align="left" valign="top"><?php echo $lists ['userlist_karma']; ?></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_KARMA_DESC') ?></td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL') ?></td>
							<td align="left" valign="top"><?php echo $lists ['userlist_email']; ?></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL_DESC') ?></td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE') ?></td>
							<td align="left" valign="top"><?php echo $lists ['userlist_usertype']; ?></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE_DESC') ?></td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE') ?></td>
							<td align="left" valign="top"><?php echo $lists ['userlist_joindate']; ?></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE_DESC') ?></td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE') ?></td>
							<td align="left" valign="top"><?php echo $lists ['userlist_lastvisitdate']; ?></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE_DESC') ?></td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_HITS') ?></td>
							<td align="left" valign="top"><?php echo $lists ['userlist_userhits']; ?></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_HITS_DESC') ?></td>
						</tr>
					</table>
				</fieldset>
				<fieldset>
					<legend><?php echo JText::_('COM_KUNENA_STATS') ?></legend>
					<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
						<tr align="center" valign="middle">
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_SHOWWHOIS');
							?></td>
							<td align="left" valign="top"><?php echo $lists ['showwhoisonline'];
							?>
							</td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_SHOWWHOISDESC');
							?>
							</td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_SHOWSTATS');
							?>
							</td>
							<td align="left" valign="top" width="25%"><?php echo $lists ['showstats'];
							?>
							</td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_SHOWSTATSDESC');
							?>
							</td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_STATSGENERAL');
							?>
							</td>
							<td align="left" valign="top"><?php echo $lists ['showgenstats'];
							?>
							</td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_STATSGENERALDESC');
							?>
							</td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_USERSTATS');
							?></td>
							<td align="left" valign="top"><?php echo $lists ['showpopuserstats'];
							?>
							</td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_USERSTATSDESC');
							?>
							</td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_USERNUM');
							?></td>
							<td align="left" valign="top"><input type="text"
								name="cfg_popusercount"
								value="<?php echo kescape($kunena_config->popusercount);
							?>" /></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_USERNUM');
							?></td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_USERPOPULAR');
							?>
							</td>
							<td align="left" valign="top"><?php echo $lists ['showpopsubjectstats'];
							?>
							</td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_USERPOPULARDESC');
							?>
							</td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_NUMPOP');
							?></td>
							<td align="left" valign="top"><input type="text"
								name="cfg_popsubjectcount"
								value="<?php echo kescape($kunena_config->popsubjectcount);
							?>" /></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_NUMPOP');
							?></td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_POLLSSTATS');
							?></td>
							<td align="left" valign="top"><?php echo $lists ['showpoppollstats'];
							?>
							</td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_POLLSTATSDESC');
							?>
							</td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_POLLSPOP');
							?></td>
							<td align="left" valign="top"><input type="text"
								name="cfg_popsubjectcount"
								value="<?php echo kescape($kunena_config->poppollscount);
							?>" /></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_POLLSPOP');
							?></td>
						</tr>
					</table>
				</fieldset>
				<fieldset>
					<legend><?php echo JText::_('COM_KUNENA_A_POLL_TITLE'); ?></legend>
					<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
						<tr align="center" valign="middle">
							<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_A_POLL_ENABLED');
							?>
							</td>
							<td align="left" valign="top" width="25%"><?php echo $lists ['pollenabled']?>
							</td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_ENABLED_DESC');
							?>
							</td>
						</tr>
									<tr align="center" valign="middle">
							<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_A_POLL_NUMBER_OPTIONS');
							?>
							</td>
							<td align="left" valign="top" width="25%"><input type="text"
								name="cfg_pollnboptions"
								value="<?php echo kescape($kunena_config->pollnboptions);
							?>" /></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_POLL_NUMBER_OPTIONS_DESC');
							?>
							</td>
						</tr>
									<tr align="center" valign="middle">
							<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_A_POLL_TIME_VOTES');
							?>
							</td>
							<td align="left" valign="top" width="25%"><input type="text"
								name="cfg_polltimebtvotes"
								value="<?php echo kescape($kunena_config->polltimebtvotes);
							?>" /></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_POLL_TIME_VOTES_DESC');
							?>
							</td>
						</tr>
									<tr align="center" valign="middle">
							<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_A_POLL_NUMBER_VOTES_BY_USER');
							?>
							</td>
							<td align="left" valign="top" width="25%"><input type="text"
								name="cfg_pollnbvotesbyuser"
								value="<?php echo kescape($kunena_config->pollnbvotesbyuser);
							?>" /></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_POLL_NUMBER_VOTES_BY_DESC');
							?>
							</td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_A_POLL_ALLOW_ONE_VOTE');
							?>
							</td>
							<td align="left" valign="top" width="25%"><?php echo $lists ['pollallowvoteone']?>
							</td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_POLL_ALLOW_ONE_VOTE_DESC');
							?>
							</td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_A_POLL_SHOW_USER_LIST');
							?>
							</td>
							<td align="left" valign="top" width="25%"><?php echo $lists ['pollresultsuserslist']?>
							</td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_POLL_SHOW_USER_LIST_DESC');
							?>
							</td>
						</tr>
					</table>
				</fieldset>
				<input type="hidden" name="task" value="showConfig" />
				<input type="hidden" name="option" value="<?php echo $option; ?>" />
				</dd>
			</dl>
			</form>
		</div>
		<?php
			}
			function showInstructions($kunena_db, $option) {
		?>
		<table width="100%" border="0" cellpadding="2" cellspacing="2" class="adminheading">
			<tr>
				<th class="info">&nbsp;<?php echo JText::_('COM_KUNENA_INSTRUCTIONS'); ?></th>
			</tr>
		</table>

		<table width="100%" border="0" cellpadding="2" cellspacing="2" class="adminform">
			<tr>
				<th><?php echo JText::_('COM_KUNENA_FINFO'); ?></th>
			</tr>
			<tr>
				<td><?php echo JText::_('COM_KUNENA_INFORMATION'); ?></td>
			</tr>
		</table>
		<?php
			} //end function showInstructions

			function showCss($file, $option) {
				$f = fopen ( $file, "r" );
				$content = fread ( $f, filesize ( $file ) );
				$content = kunena_htmlspecialchars ( $content );
		?>
		<div class="kadmin-functitle icon-editcss"><?php echo JText::_('COM_KUNENA_CSSEDITOR'); ?></div>
		<form action="index.php?" method="post" name="adminForm" class="adminForm" id="adminForm">
			<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminform">
				<tr>
					<th colspan="4"><?php echo JText::_('COM_KUNENA_PATH'); ?> <?php echo $file; ?></th>
				</tr>
				<tr>
					<td><textarea cols="100" rows="20" name="csscontent"><?php echo $content; ?></textarea></td>
				</tr>
				<tr>
					<td class="error"><?php echo JText::_('COM_KUNENA_CSSERROR'); ?></td>
				</tr>
			</table>
			<input type="hidden" name="file" value="<?php echo $file; ?>" />
			<input type="hidden" name="option" value="<?php echo $option; ?>">
			<input type="hidden" name="task" value=""> <input type="hidden" name="boxchecked" value="0"></form>
		<?php
			} //end function showCss
			function showProfiles($option, &$profileList, $countPL, $pageNavSP, $order, $search) {
		?>
		<div class="kadmin-functitle icon-profiles"><?php echo JText::_('COM_KUNENA_FUM'); ?></div>
		<form action="index.php" method="POST" name="adminForm">
		<table class="kadmin-sort">
			<tr>
				<td align="left"><?php echo JText::_('COM_KUNENA_USRL_SEARCH_BUTTON'); ?>:
				<input type="text" name="search" value="<?php echo $search; ?>" class="inputbox" onChange="document.adminForm.submit();" /></td>
				<td align="right"><?php echo JText::_('COM_KUNENA_A_DISPLAY'); ?> <?php echo $pageNavSP->getLimitBox (); ?></td>
			</tr>
			<tr>
				<td colspan="4" nowrap>
					<a href="index.php?option=com_kunena&task=profiles&order=0"><?php echo JText::_('COM_KUNENA_SORTID'); ?></a> | <a href="index.php?option=com_kunena&task=profiles&order=2"><?php echo JText::_('COM_KUNENA_SORTNAME'); ?></a> | <a href="index.php?option=com_kunena&task=profiles&order=3"><?php echo JText::_('COM_KUNENA_SORTREALNAME'); ?></a> | <a href="index.php?option=com_kunena&task=profiles&order=1"><?php echo JText::_('COM_KUNENA_SORTMOD'); ?></a>
				</td>
			</tr>
		</table>
		<table class="adminlist" border="0" cellspacing="0" cellpadding="3" width="100%">
			<tr>
				<th align="left" width="20"><input type="checkbox" name="toggle"
					value=""
					onclick="checkAll(<?php echo count ( $profileList ); ?>);" /></th>
				<th align="left" width="10"><?php echo JText::_('COM_KUNENA_ANN_ID'); ?></th>
				<th align="left" width="10"><?php echo JText::_('COM_KUNENA_USRL_AVATAR'); ?></th>
				<th align="left" width="10"><?php echo JText::_('COM_KUNENA_USRL_NAME'); ?></th>
				<th align="left" width="10"><?php echo JText::_('COM_KUNENA_USRL_REALNAME'); ?></th>
				<th align="left" width="10"><?php echo JText::_('COM_KUNENA_USRL_LOGGEDIN'); ?></th>
				<th align="left" width="10"><?php echo JText::_('COM_KUNENA_USRL_ENABLED'); ?></th>
				<th align="left" width="10"><?php echo JText::_('COM_KUNENA_USRL_BANNED'); ?></th>
				<th align="left" width="100"><?php echo JText::_('COM_KUNENA_GEN_EMAIL'); ?></th>
				<th align="left" width="100"><?php echo JText::_('COM_KUNENA_GEN_USERGROUP'); ?></th>
				<th align="left" width="15"><?php echo JText::_('COM_KUNENA_VIEW_MODERATOR'); ?></th>
				<th align="left" width="10"><?php echo JText::_('COM_KUNENA_VIEW'); ?></th>
				<th align="left" width="*"><?php echo JText::_('COM_KUNENA_GEN_SIGNATURE'); ?></th>
			</tr>
			<?php
			if ($countPL > 0) {
					$k = 0;
					//foreach ($profileList as $pl)
					$i = 0;
					for($i = 0, $n = count ( $profileList ); $i < $n; $i ++) {
						$pl = &$profileList [$i];
						$k = 1 - $k;
						$userLogged = $pl->session_id ? '<img src="images/tick.png" width="16" height="16" border="0" alt="" />': '';
						$userEnabled = ($pl->enabled && $pl->bantype ==1) ? 'publish_x.png' : 'tick.png';
						$altUserEnabled = ($pl->enabled && $pl->bantype==1) ? JText::_( 'Enabled' ) : JText::_( 'Blocked' );
						$userBlockTask = $pl->block ? 'userunblock' : 'userblock';
						$userbanned = ($pl->enabled && $pl->bantype==2) ? 'tick.png' : 'publish_x.png';
						$userBannedTask = ($pl->enabled && $pl->bantype==2) ? 'userunban' : 'userban';
						$altUserBanned = ($pl->enabled && $pl->bantype==2) ? JText::_( 'Banned' ) : JText::_( 'Not banned' );
						$kunena_user = KunenaFactory::getUser($pl->userid);
					?>
			<tr class="row<?php echo $k;
						?>">
				<td width="20"><input type="checkbox" id="cb<?php echo $i;
						?>"
					name="uid[]" value="<?php echo $pl->id;
						?>"
					onClick="isChecked(this.checked);"></td>
				<td width="10"><a href="#edit"
					onclick="return listItemTask('cb<?php echo $i;
						?>','userprofile')"><?php echo kescape($pl->userid);
						?></a></td>
					<td width="100"><?php echo $kunena_user->getAvatarLink('', '48' ,'48');
						?></td>
				<td width="100"><a href="#edit"
					onclick="return listItemTask('cb<?php echo $i;
						?>','userprofile')"><?php echo kescape($pl->username);
						?></a></td>
				<td width="100"><a href="#edit"
					onclick="return listItemTask('cb<?php echo $i;
						?>','userprofile')"><?php echo kescape($pl->name);
						?></a></td>
				<td width="100" align="center"><?php echo $userLogged;
						?>&nbsp;</td>
				<td width="100" align="center"><a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $userBlockTask; ?>')">
						<img src="images/<?php echo $userEnabled;?>" width="16" height="16" border="0" alt="<?php echo $altUserEnabled; ?>" /></a>&nbsp;</td>
				<td width="100" align="center"><a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $userBannedTask; ?>')">
						<img src="images/<?php echo $userbanned;?>" width="16" height="16" border="0" alt="<?php echo $altUserBanned; ?>" /></a>&nbsp;</td>
				<td width="100"><?php echo kescape($pl->email);
						?>&nbsp;</td>
				<td width="100"><?php echo kescape($pl->usertype);
						?>&nbsp;</td>
				<td align="center" width="15"><?php 		if ($pl->moderator) {
							echo JText::_('COM_KUNENA_ANN_YES');
						} else {
							echo JText::_('COM_KUNENA_ANN_NO');
						}
						;
						?>
				&nbsp;</td>
				<td align="center" width="10"><?php echo kescape($pl->view);
						?>&nbsp;</td>
				<td width="*"><?php echo kescape(stripslashes ( $pl->signature ) );
						?>&nbsp;
				</td>
			</tr>
			<?php }
				} else { ?>
			<tr><td colspan="13"><?php echo JText::_('COM_KUNENA_NOUSERSFOUND') ?></td></tr>
			<?php } ?>
			<tr>
				<th align="center" colspan="13"><?php echo $pageNavSP->getLimitBox () . $pageNavSP->getResultsCounter () . $pageNavSP->getPagesLinks (); ?>
				</th>
			</tr>
		</table>
		<input type="hidden" name="order" value="<?php echo $order; ?>" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="showprofiles" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="limitstart" value="0" />
	</form>
	<?php
		} //end function showProfiles
		function newModerator($option, $id, $moderators, &$modIDs, $forumName, &$userList, $countUL, $pageNav) {
	?>

	<form action="index.php" method="post" name="adminForm">
		<table cellpadding="4" class="adminheading" cellspacing="0" border="0" width="100%">
			<tr>
				<th width="100%" class="user"><?php echo JText::_('COM_KUNENA_ADDMOD'); ?> <?php echo $forumName; ?>
				</th>
				<td><?php echo JText::_('COM_KUNENA_A_DISPLAY'); ?></td>
				<td><?php echo $pageNav->getLimitBox (); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>
		<table class="adminlist" border="0" cellspacing="0" cellpadding="3" width="100%">
			<tr>
				<th width="20">#</th>
				<th width="20"><input type="checkbox" name="toggle" value=""
					onclick="checkAll(<?php echo count ( $userList ); ?>);" /></th>
				<th><?php echo JText::_('COM_KUNENA_ANN_ID'); ?></th>
				<th align="left"><?php echo JText::_('COM_KUNENA_USRL_NAME'); ?></th>
				<th align="left"><?php echo JText::_('COM_KUNENA_GEN_EMAIL'); ?></th>
				<th><?php echo JText::_('COM_KUNENA_PUBLISHED'); ?></th>
				<th>&nbsp;</th>
			</tr>
			<?php if ($countUL > 0) {
					$k = 0;
					$i = 0;

					for($i = 0, $n = count ( $userList ); $i < $n; $i ++) {
						$pl = &$userList [$i];
						$k = 1 - $k;
			?>
			<tr class="row<?php echo $k;
						?>">
				<td width="20" align="right"><?php echo $i + $pageNav->limitstart + 1;
						?></td>
				<td width="20"><input type="checkbox" id="cb<?php echo $i;
						?>"
					name="cid[]" value="<?php echo $pl->id;
						?>"
					onClick="isChecked(this.checked);"></td>
				<td width="20"><a
					href="index.php?option=com_kunena&task=userprofile&do=show&user_id=<?php echo kescape($pl->id);
						?>"><?php echo kescape($pl->id);
						?></a>&nbsp;</td>
				<td><?php echo kescape($pl->name);
						?>&nbsp;</td>
				<td><?php echo kescape($pl->email);
						?>&nbsp;</td>
				<td align="center"><?php 		if ($moderators) {
							if (in_array ( $pl->id, $modIDs )) {
								echo "<img src=\"images/tick.png\">";
							} else {
								echo "<img src=\"images/publish_x.png\">";
							}
						} else {
							echo "<img src=\"images/publish_x.png\">";
						}
						?></td>
				<td>&nbsp;</td>
			</tr>

			<?php 	}
				} else {
					echo "<tr><td align='left' colspan='7'>" . JText::_('COM_KUNENA_NOMODSAV') . "</td></tr>";
				}
				?>

			<tr>
				<td class="kadmin-paging" colspan="6">
					<div class="pagination">
						<div class="limit"><?php echo JText::_('COM_KUNENA_A_DISPLAY'); ?> <?php echo $pageNav->getLimitBox (); ?></div>
							<?php echo $pageNav->getPagesLinks (); ?>
						<div class="limit"><?php echo $pageNav->getResultsCounter (); ?></div>
					</div>
				</td>
			</tr>

			<tr>
				<td colspan="7"><?php echo JText::_('COM_KUNENA_NOTEUS'); ?></td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>">
		<input type="hidden" name="id" value="<?php echo $id; ?>">
		<input type="hidden" name="boxchecked" value="0">
		<input type="hidden" name="task" value="newmoderator">
		<input type="hidden" name="limitstart" value="0"></form>

		<?php
			}
			//   function showUserProfile ($kunena_db,$user_id,$do,$deleteSig,$signature,$newview,$user_id,$thread,$moderator)
			//   {
			//
			//      include ('components/com_kunena/moderate_user.php');
			//   }
			function editUserProfile($option, $user, $subslist, $selectRank, $selectPref, $selectMod, $selectOrder, $uid, $modCats, $useriplist) {
				$kunena_config = & CKunenaConfig::getInstance ();
				$kunena_db = &JFactory::getDBO ();
				//fill the variables needed later
				$signature = stripslashes ($user->signature );
				$username = $user->username;
				$avatarint = KunenaFactory::getAvatarIntegration();
				$editavatar = is_a($avatarint, 'KunenaAvatarKunena') ? true : false;
				$avatar = $avatarint->getLink($user->id, '', 'profile');
				$ordering = $user->ordering;
				//that's what we got now; later the 'future_use' columns can be used..

				$csubslist = count ( $subslist );
				//        include_once ('components/com_kunena/bb_adm.js'); ?>
		<div class="kadmin-functitle icon-profiles"> <?php echo JText::_('COM_KUNENA_PROFFOR'); ?>: <?php echo kescape($user->name) .' ('. kescape($user->username) .')'; ?></div>
		<form action="index.php?option=<?php echo $option; ?>" method="POST" name="adminForm">
		<?php jimport('joomla.html.pane');
			$myTabs = &JPane::getInstance('tabs', array('startOffset'=>0));
			?>
		<dl class="tabs" id="pane">

		<dt><?php echo JText::_('COM_KUNENA_A_BASIC_SETTINGS') ?></dt>
		<dd>
		<fieldset>
		<legend><?php echo JText::_('COM_KUNENA_A_BASIC_SETTINGS') ?></legend>
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
			<tr>
				<th colspan="3" class="title"><?php echo JText::_('COM_KUNENA_GENPROF'); ?></th>
			</tr>
			<tr>
				<td width="150" class="contentpane"><?php echo JText::_('COM_KUNENA_PREFOR'); ?></td>
				<td align="left" valign="top" class="contentpane"><?php echo $selectOrder; ?></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td width="150" class="contentpane"><?php echo JText::_('COM_KUNENA_RANKS'); ?></td>
				<td align="left" valign="top" class="contentpane"><?php echo $selectRank; ?></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td width="150" valign="top" class="contentpane"><?php echo JText::_('COM_KUNENA_GEN_SIGNATURE'); ?>:
				</td>
				<td align="left" valign="top" class="contentpane">

<script type="text/javascript">
var current_count = <?php echo JString::strlen($signature) ?>;
var max_count = <?php echo $kunena_config->maxsig; ?>;

function textCounter(field, target) {
	if (field.value.length > max_count) {
		field.value = field.value.substring(0, max_count);
	} else {
		current_count = max_count - field.value.length;
		target.value = current_count;
	}
}
</script>

	<textarea class="inputbox" name="message" cols="50" rows="6"
	onkeyup="textCounter(this, this.form.current_count);"><?php echo kescape( $signature ); ?></textarea>
	<div><?php echo JText::sprintf('COM_KUNENA_SIGNATURE_LENGTH_COUNTER', intval($kunena_config->maxsig),
			'<input readonly="readonly" type="text" name="current_count" value="'.(intval($kunena_config->maxsig)-JString::strlen($signature)).'" size="3" />');?>
	</div>

	<div> <input type="checkbox" value="1" name="deleteSig"> <em><?php echo JText::_('COM_KUNENA_DELSIG'); ?></em></div>

	</td>
	</table>
</fieldset>
</dd>
<dt><?php echo JText::_('COM_KUNENA_A_AVATARS') ?></dt>
			<dd>
			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_A_AVATARS') ?></legend>
				<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
				<tr>
				<th colspan="2" class="title"><?php echo JText::_('COM_KUNENA_MOD_NEW'); ?></th>
			</tr>
			<tr>
				<td width="150" class="contentpane">
				<?php
				if ($editavatar) {
					?>
				<?php echo JText::_('COM_KUNENA_UAVATAR'); ?>
				<?php echo $avatar; ?>
					<p><input type="checkbox" value="1"
					name="deleteAvatar"> <em><?php echo JText::_('COM_KUNENA_DELAV'); ?></em></p></td>
				<?php } else {
					echo "<td>&nbsp;</td>";
					echo '<input type="hidden" value="" name="avatar">';
				}
				?>

				<td><?php if ($editavatar) {
					?>
				<?php } else {
					echo "<td>&nbsp;</td>";
				}
				?></td>
			</tr>
		</table>

	</fieldset>
</dd>

<dt><?php echo JText::_('COM_KUNENA_MOD_NEW') ?></dt>
			<dd>
			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_MOD_NEW') ?></legend>
				<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
				<tr>
				<th colspan="2" class="title"><?php echo JText::_('COM_KUNENA_MOD_NEW'); ?></th>
			</tr>
			<tr>
				<td width="150" class="contentpane"><?php echo JText::_('COM_KUNENA_ISMOD');
				echo $selectMod;
				?>
				</td>
				<td><?php echo $modCats; ?></td>
			</tr>
		</table>
		<input type="hidden" name="uid" value="<?php echo $uid; ?>">
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="option" value="com_kunena" />
		<input type="hidden" name="boxchecked" value="1" />
	</fieldset>
</dd>
<dt><?php echo JText::_('COM_KUNENA_SUBSCRIPTIONS') ?></dt>
			<dd>
			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_SUBSCRIPTIONS') ?></legend>
				<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
				<tr>
			<th colspan="2" class="title"><?php
			echo JText::_('COM_KUNENA_SUBFOR');
			?> <?php
			echo $username;
			?>
			</th>
		</tr>
		<?php
			$enum = 1; //reset value
			$k = 0; //value for alternating rows


			if ($csubslist > 0) {
				foreach ( $subslist as $subs ) { //get all message details for each subscription
					$kunena_db->setQuery ( "select * from #__fb_messages where id=$subs->thread" );
					$subdet = $kunena_db->loadObjectList ();
					check_dberror ( "Unable to load subscription messages." );

					foreach ( $subdet as $sub ) {
						$k = 1 - $k;
						echo "<tr class=\"row$k\">";
						echo "  <td>$enum: " . kescape( stripslashes ( $sub->subject ) ) . " by " . kescape( stripslashes ( $sub->name ) );
						echo "  <td>&nbsp;</td>";
						echo "</tr>";
						$enum ++;
					}
				}
			} else {
				echo "<tr><td class=\"message\">" . JText::_('COM_KUNENA_NOSUBS') . "</td></tr>";
			}
			?>
	</table>
    </fieldset>
</dd>
<dt><?php echo JText::_('COM_KUNENA_TRASH_IP') ?></dt>
			<dd>
			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_TRASH_IP') ?></legend>
				<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
				<tr>
			<th colspan="2" class="title"><?php
			echo JText::sprintf('COM_KUNENA_IPFOR', $username);
			?>
			</th>
		</tr>
		<?php
		$i = 0;
		$k = 0; //value for alternating rows

		$userids='';
		foreach ($useriplist as $ip => $list) {
			$k = 1 - $k;
			$i++;
			$userlist = array();
			$mescnt = 0;
			foreach ($list as $curuser) {
				if ($curuser->userid == $user->id) {
					$mescnt += intval($curuser->mescnt);
					continue;
				}
				$userlist[] = kescape($curuser->username).' ('.kescape($curuser->mescnt).')';
			}
			$userlist = implode(', ', $userlist);
			echo "<tr class=\"row$k\">";
			echo "  <td>".$i.":".$ip." - ".JText::sprintf('COM_KUNENA_IP_OCCURENCES', $mescnt).(!empty($userlist)?" ".JText::sprintf('COM_KUNENA_USERIDUSED', $userlist):'')."</td>";
			echo "  <td>&nbsp;</td>";
			echo "</tr>";
		}
		?>
	</table>
	</fieldset>
</dd>
</dl>
</form>
	<?php
		}
		function moveUserMessages ( $option, $return, $uid, $lists ) {
	?>
			<div class="kadmin-functitle icon-profiles"><?php echo JText::_('COM_KUNENA_A_MOVE_USERMESSAGES'); ?></div>
			<form action="index.php" method="post" name="adminForm">
	<?php
			echo $lists;
	?>
			<input type="hidden" name="boxchecked" value="1">
			<input type="hidden" name="return" value="<?php echo $return;?>" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="uid" value="<?php echo $uid[0]; ?>" />
			</form>
	<?php
		}
		//**************************
		// Prune Forum
		//**************************
		function pruneforum($option, $forumList) {
	?>
	<div class="kadmin-functitle icon-prune"><?php echo JText::_('COM_KUNENA_A_PRUNE'); ?></div>
	<form action="index.php" method="post" name="adminForm">
		<table class="adminform" cellpadding="4" cellspacing="0" border="0" width="100%">
			<tr>
				<th width="100%" colspan="2">&nbsp;</th>
			</tr>
			<tr>
				<td colspan="2"><?php echo JText::_('COM_KUNENA_A_PRUNE_DESC') ?></td>
			</tr>
			<tr>
				<td width="20%"><?php echo JText::_('COM_KUNENA_A_PRUNE_NAME') ?></td>
				<td><?php echo $forumList ['forum']?></td>
			</tr>
			<tr>
				<td width="20%"><?php echo JText::_('COM_KUNENA_A_PRUNE_NOPOSTS') ?></td>
				<td><input type="text" name="prune_days" value="30"><?php echo JText::_('COM_KUNENA_A_PRUNE_DAYS') ?></td>
			</tr>
		</table>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" /></form>
		<?php
			}

			//**************************
			// Sync Users
			//**************************
			function syncusers($option) {
		?>
		<div id="kadmin-congifcover">
			<div class="kadmin-functitle icon-syncusers"><?php echo JText::_('COM_KUNENA_SYNC_USERS'); ?></div>
			<form action="index.php" method="post" name="adminForm" class="adminform">
				<fieldset>
					<legend><?php echo JText::_('COM_KUNENA_SYNC_USERS_OPTIONS'); ?></legend>
					<table cellpadding="4" class="kadmin-adminform" cellspacing="0" border="0" width="100%">
						<tr>
							<td><?php echo JText::_('COM_KUNENA_SYNC_USERS_CACHE'); ?></td>
							<td><input type="checkbox" name="usercache" value="1" checked="checked" /></td>
							<td><?php echo JText::_('COM_KUNENA_SYNC_USERS_CACHE_DESC'); ?></td>
						</tr>
						<tr>
							<td><?php echo JText::_('COM_KUNENA_SYNC_USERS_ADD'); ?></td>
							<td><input type="checkbox" name="useradd" value="1" /></td>
							<td><?php echo JText::_('COM_KUNENA_SYNC_USERS_ADD_DESC'); ?></td>
						</tr>
						<tr>
							<td><?php echo JText::_('COM_KUNENA_SYNC_USERS_DEL'); ?></td>
							<td><input type="checkbox" name="userdel" value="1" /></td>
							<td><?php echo JText::_('COM_KUNENA_SYNC_USERS_DEL_DESC'); ?></td>
						</tr>
						<tr>
							<td><?php echo JText::_('COM_KUNENA_SYNC_USERS_RENAME'); ?></td>
							<td><input type="checkbox" name="userrename" value="1" /></td>
							<td><?php echo JText::_('COM_KUNENA_SYNC_USERS_RENAME_DESC'); ?></td>
						</tr>
					</table>
				</fieldset>
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="option" value="<?php echo $option; ?>" />
			</form>
		</div>
		<?php
			}

			//***************************************
			// Uploaded Image Browser
			//***************************************
			function browseUploaded($option, $attachments, $type) {
				$kunena_db = &JFactory::getDBO ();
				$map = JPATH_ROOT;
				?>
			<script language="javascript" type="text/javascript">
					<!---
					function decision(message, url)
					{
						if (confirm(message))
							location.href = url;
					}
					//--->
			</script>
		<?php
			echo $type ? '<div class="kadmin-functitle icon-images">' : '<div class="kadmin-functitle icon-files">';
			echo $type ? JText::_('COM_KUNENA_A_IMGB_IMG_BROWSE') : JText::_('COM_KUNENA_A_IMGB_FILE_BROWSE');
			echo '</div>';
			echo '<table class="adminform"><tr><td>';
			echo $type ? JText::_('COM_KUNENA_A_IMGB_TOTAL_IMG') : JText::_('COM_KUNENA_A_IMGB_TOTAL_FILES');
			echo ': ' . count ( $attachments ) . '</td></tr>';
			echo '<tr><td>';
			echo $type ? JText::_('COM_KUNENA_A_IMGB_ENLARGE') : JText::_('COM_KUNENA_A_IMGB_DOWNLOAD');
			echo '</td></tr></table>';
			echo '<table class="adminform"><tr>';

			$i=0;
			foreach($attachments as $attachment) {
				$i++;
				$filename = $attachment->filename;
				$attach_live_path = JURI::root().'/'.$attachment->folder.'/'.$attachment->filename;
				$attach_path = JPATH_ROOT.'/'.$attachment->folder.'/'.$attachment->filename;

				echo '<td>';
				echo '<table style="border: 1px solid #ccc;"><tr><td height="90" width="130" style="text-align: center">';
				echo $type ? '<a href="' . $attach_live_path . '" target="_blank" title="' . JText::_('COM_KUNENA_A_IMGB_ENLARGE') . '" alt="' . JText::_('COM_KUNENA_A_IMGB_ENLARGE') . '"><img src="' . kescape($attach_live_path) . '" width="80" heigth="80" border="0"></a>' : '<a href="' . kescape($attach_live_path) . '" title="' . JText::_('COM_KUNENA_A_IMGB_DOWNLOAD') . '" alt="' . JText::_('COM_KUNENA_A_IMGB_DOWNLOAD') . '"><img src="../administrator/components/com_kunena/images/kfile.png" border="0"></a>';
				echo '</td></tr><tr><td style="text-align: center">';
				echo '<br /><small>';
				echo '<strong>' . JText::_('COM_KUNENA_A_IMGB_NAME') . ': </strong> ' . kescape($filename) . '<br />';
				if (is_file($attach_path)) {
					echo '<strong>' . JText::_('COM_KUNENA_A_IMGB_SIZE') . ': </strong> ' . @filesize ( $attach_path ) . ' bytes<br />';
					$type ? list ( $width, $height ) = @getimagesize ( $attach_path ) : '';
					echo $type ? '<strong>' . JText::_('COM_KUNENA_A_IMGB_DIMS') . ': </strong> ' . $width . 'x' . $height . '<br />' : '';
				}
				echo $type ? '<a href="javascript:decision(\'' . JText::_('COM_KUNENA_A_IMGB_CONFIRM') . '\',\'index.php?option=' . $option . '&task=deleteImage&id=' . $attachment->id . '\')">' . JText::_('COM_KUNENA_A_IMGB_REMOVE') . '</a><br />' : '<a href="javascript:decision(\'' . JText::_('COM_KUNENA_A_IMGB_CONFIRM') . '\',\'index.php?option=' . $option . '&task=deleteFile&id=' . $attachment->id . '\')">' . JText::_('COM_KUNENA_A_IMGB_REMOVE') . '</a><br />';

				if ($attachment->catid > 0) {
					echo '<a href="../index.php?option=' . $option . '&func=view&catid=' . $attachment->catid . '&id=' . $attachment->mesid . '#' . $attachment->mesid . '" target="_blank">' . JText::_('COM_KUNENA_A_IMGB_VIEW') . '</a>';
				} else {
					echo JText::_('COM_KUNENA_A_IMGB_NO_POST');
				}

				echo '</td></tr></table>';
				echo '</td>';

				if (! fmod ( $i, 5 )) {
					echo '</tr><tr align="center" valign="middle">';
				}
			}

			echo '</tr></table>';
		}
			//***************************************
			// Show Smilies
			//***************************************
		function showsmilies($option, &$smileytmp, $pageNavSP, $smileypath) {
		?>
		<div class="kadmin-functitle icon-smilies"><?php echo JText::_('COM_KUNENA_EMOTICONS_EMOTICON_MANAGER'); ?></div>
		<form action="index.php" method="POST" name="adminForm">
			<table class="kadmin-sort" cellpadding="4" cellspacing="0" border="0" width="100%">
				<tr>
					<td align="right"><?php echo JText::_('COM_KUNENA_A_DISPLAY'); ?><?php echo $pageNavSP->getLimitBox (); ?></td>
				</tr>
			</table>
			<table class="adminlist" border="0" cellspacing="0" cellpadding="3" width="100%">
				<tr>
					<th width="20" align="center">#</th>
					<th align="center" width="20"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count ( $smileytmp ); ?>);" /></th>
					<th align="center" width="50"><?php echo JText::_('COM_KUNENA_EMOTICON'); ?></th>
					<th align="center" width="50"><?php echo JText::_('COM_KUNENA_EMOTICONS_CODE'); ?></th>
					<th align="left" width="200"><?php echo JText::_('COM_KUNENA_EMOTICONS_URL'); ?></th>
					<th width="*">&nbsp;</th>
				</tr>
				<?php
					$k = 0;
					$i = 0;
					for($i = 0, $n = count ( $smileytmp ); $i < $n; $i ++) {
						$k = 1 - $k;
						$s = &$smileytmp [$i];
				?>
				<tr class="row<?php echo $k; ?>" align="center">
					<td width="10" align="center"><a href="#edit"
						onclick="return listItemTask('cb<?php
						echo $i;
						?>','editsmiley')"><?php
						echo $s->id;
						?></a></td>
					<td width="20" align="center"><input type="checkbox"
						id="cb<?php
						echo $i;
						?>" name="cid[]"
						value="<?php
						echo kescape($s->id);
						?>"
						onClick="isChecked(this.checked);"></td>
					<td width="50" align="center"><a href="#edit"
						onclick="return listItemTask('cb<?php
						echo $i;
						?>','editsmiley')"><img
						src="<?php
						echo kescape($smileypath ['live'] . $s->location);
						?>"
						alt="<?php
						echo kescape($s->location);
						?>" border="0" /></a></td>
					<td width="50" align="center"><?php echo kescape($s->code); ?>&nbsp;</td>
					<td width="200" align="left"><?php echo kescape($s->location); ?>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<?php
					}
					?>
				<tr>
					<td class="kadmin-paging" colspan="6">
						<div class="pagination">
							<div class="limit"><?php echo JText::_('COM_KUNENA_A_DISPLAY'); ?> <?php echo $pageNavSP->getLimitBox (); ?></div>
							<?php echo $pageNavSP->getPagesLinks (); ?>
							<div class="limit"><?php echo $pageNavSP->getResultsCounter (); ?></div>
						</div>
					</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="<?php echo $option; ?>">
			<input type="hidden" name="task" value="showsmilies">
			<input type="hidden" name="boxchecked" value="0">
			<?php echo '<input type = "hidden" name = "limitstart" value = "0">'; ?>
		</form>
		<?php
			} //end function showsmilies

			function editsmiley($option, $smiley_edit_img, $filename_list, $smileypath, $smileycfg) {
			?>
		<script language="javascript" type="text/javascript">
			<!--
			function update_smiley(newimage)
			{
				document.smiley_image.src = "<?php
				echo $smileypath;
				?>" + newimage;
			}
			//-->
		</script>
		<div class="kadmin-functitle icon-smilies"><?php echo JText::_('COM_KUNENA_EMOTICONS_EDIT_SMILEY'); ?></div>
		<form action="index.php" method="POST" name="adminForm">
			<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminform">
				<tr align="center">
					<td width="100"><?php
					echo JText::_('COM_KUNENA_EMOTICONS_CODE');
					?></td>
					<td width="200"><input class="post" type="text" name="smiley_code"
						value="<?php
					echo kescape($smileycfg ['code']);
					?>" /></td>
					<td rowspan="3" width="50"><img name="smiley_image"
						src="<?php
					echo $smiley_edit_img;
					?>" border="0" alt="" /> &nbsp;</td>
					<td rowspan="3">&nbsp;</td>
				</tr>
				<tr align="center">
					<td width="100"><?php
					echo JText::_('COM_KUNENA_EMOTICONS_URL');
					?></td>
					<td><select name="smiley_url"
						onchange="update_smiley(this.options[selectedIndex].value);">
						<?php
					echo $filename_list;
					?>
					</select> &nbsp;</td>
				</tr>
				<tr>
					<td width="100"><?php
					echo JText::_('COM_KUNENA_EMOTICONS_EMOTICONBAR');
					?></td>
					<td><input type="checkbox" name="smiley_emoticonbar" value="1"
						<?php
					if ($smileycfg ['emoticonbar'] == 1) {
						echo 'checked="checked"';
					}
					?> /></td>
				</tr>
				<tr>
					<td colspan="2" align="center"><input type="hidden" name="option"
						value="<?php
					echo $option;
					?>"> <input type="hidden" name="task" value="showsmilies"> <input
						type="hidden" name="boxchecked" value="0"><input type="hidden"
						name="id" value="<?php
					echo $smileycfg ['id'];
					?>"></td>
				</tr>
			</table>
		</form>
		<?php
			} //end function editmilies
			function newsmiley($option, $filename_list, $smileypath) {
		?>
		<script language="javascript" type="text/javascript">
			<!--
			function update_smiley(newimage)
			{
				document.smiley_image.src = "<?php
				echo $smileypath;
				?>" + newimage;
			}
		//-->
		</script>
		<div class="kadmin-functitle icon-smilies"><?php echo JText::_('COM_KUNENA_EMOTICONS_NEW_SMILEY'); ?></div>
		<form action="index.php" method="POST" name="adminForm">
			<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminform">
				<tr align="center">
					<td width="100"><?php
					echo JText::_('COM_KUNENA_EMOTICONS_CODE');
					?></td>
					<td width="200"><input class="post" type="text" name="smiley_code"
						value="" /></td>
					<td rowspan="3" width="50"><img name="smiley_image" src="" border="0"
						alt="" /> &nbsp;</td>
					<td rowspan="3">&nbsp;</td>
				</tr>
				<tr align="center">
					<td width="100"><?php
					echo JText::_('COM_KUNENA_EMOTICONS_URL');
					?></td>
					<td><select name="smiley_url"
						onchange="update_smiley(this.options[selectedIndex].value);" onmousemove="update_smiley(this.options[selectedIndex].value);">
						<?php
					echo $filename_list;
					?>
					</select> &nbsp;</td>
				</tr>
				<tr>
					<td width="100"><?php
					echo JText::_('COM_KUNENA_EMOTICONS_EMOTICONBAR');
					?></td>
					<td><input type="checkbox" name="smiley_emoticonbar" value="1" /></td>
				</tr>
				<tr>
					<td colspan="2" align="center"><input type="hidden" name="option"
						value="<?php
					echo $option;
					?>"> <input type="hidden" name="task" value="showsmilies"> <input
						type="hidden" name="boxchecked" value="0"></td>
				</tr>
			</table>
		</form>
		<?php
			} //end function newsmilies
			/// Rank Administration
			function showRanks($option, &$ranks, $pageNavSP, $order, $rankpath) {
			$kunena_db = &JFactory::getDBO ();
		?>
		<div class="kadmin-functitle icon-ranks"><?php echo JText::_('COM_KUNENA_RANK_MANAGER'); ?></div>
		<form action="index.php" method="POST" name="adminForm">
			<table class="kadmin-sort" cellpadding="4" cellspacing="0" border="0" width="100%">
				<tr>
					<td  align="right"><?php echo JText::_('COM_KUNENA_A_DISPLAY'); ?><?php echo $pageNavSP->getLimitBox (); ?></td>
				</tr>
			</table>
			<table class="adminlist" border="0" cellspacing="0" cellpadding="3" width="100%">
				<tr>
					<th width="20" align="center">#</th>
					<th align="left"><input type="checkbox" name="toggle" value=""
						onclick="checkAll(<?php
					echo count ( $ranks );
					?>);" /></th>
					<th align="left"><?php
					echo JText::_('COM_KUNENA_RANKSIMAGE');
					?></th>
					<th align="left" ><?php
					echo JText::_('COM_KUNENA_RANKS');
					?></th>
					<th align="left" ><?php
					echo JText::_('COM_KUNENA_RANKS_SPECIAL');
					?></th>
					<th align="center" ><?php
					echo JText::_('COM_KUNENA_RANKSMIN');
					?></th>
					<th width="100%">&nbsp;</th>
				</tr>
				<?php
					$k = 0;
					$i = 0;
					foreach ( $ranks as $id => $row ) {
						$k = 1 - $k;
						?>
				<tr class="row<?php
						echo $k;
						?>">
					<td width="20" align="center"><?php
						echo ($id + $pageNavSP->limitstart + 1);
						?></td>
					<td width="20" align="center"><input type="checkbox"
						id="cb<?php
						echo $id;
						?>" name="cid[]"
						value="<?php
						echo $row->rank_id;
						?>"
						onClick="isChecked(this.checked);"></td>
					<td><a href="#edit"
						onclick="return listItemTask('cb<?php
						echo $id;
						?>','editRank')"><img
						src="<?php
						echo kescape($rankpath ['live'] . $row->rank_image);
						?>"
						alt="<?php
						echo kescape($row->rank_image);
						?>" border="0" /></a></td>
					<td ><a href="#edit"
						onclick="return listItemTask('cb<?php
						echo $id;
						?>','editRank')"><?php
						echo kescape($row->rank_title);
						?></a></td>
					<td><?php
						if ($row->rank_special == 1) {
							echo JText::_('COM_KUNENA_ANN_YES');
						} else {
							echo JText::_('COM_KUNENA_ANN_NO');
						}
						?></td>
					<td align="center"><?php
						echo kescape($row->rank_min);
						?></td>
					<td width="100%">&nbsp;</td>
				</tr>
				<?php
					}
					?>
			<tr>
				<td class="kadmin-paging" colspan="7">
					<div class="pagination">
						<div class="limit"><?php echo JText::_('COM_KUNENA_A_DISPLAY'); ?> <?php echo $pageNavSP->getLimitBox (); ?></div>
							<?php echo $pageNavSP->getPagesLinks (); ?>
						<div class="limit"><?php echo $pageNavSP->getResultsCounter (); ?></div>
					</div>
				</td>
			</tr>

			</table>
			<input type="hidden" name="option" value="<?php echo $option; ?>">
			<input type="hidden" name="boxchecked" value="0">
			<input type="hidden" name="task" value="ranks"> <input type="hidden" name="limitstart" value="0">
		</form>
		<?php
			} //end function showRanks
			function newRank($option, $filename_list, $rankpath) {
		?>
		<script language="javascript" type="text/javascript">
			<!--
			function update_rank(newimage)
			{
				document.rank_image.src = "<?php
				echo $rankpath;
				?>" + newimage;
			}
			//-->
		</script>
		<div class="kadmin-functitle icon-ranks"><?php echo JText::_('COM_KUNENA_NEW_RANK'); ?></div>
		<form action="index.php" method="POST" name="adminForm">
			<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminform">

				<tr align="center">
					<td width="100"><?php
					echo JText::_('COM_KUNENA_RANKS');
					?></td>
					<td width="200"><input class="post" type="text" name="rank_title"
						value="" /></td>
				</tr>
				<tr>
					<td width="100"><?php
					echo JText::_('COM_KUNENA_RANKSIMAGE');
					?></td>
					<td><select name="rank_image"
						onchange="update_rank(this.options[selectedIndex].value);" onmousemove="update_rank(this.options[selectedIndex].value);">
						<?php
					echo $filename_list;
					?>
					</select> &nbsp; <img name="rank_image" src="" border="0" alt="" /></td>
				</tr>
				<tr>
					<td width="100"><?php
					echo JText::_('COM_KUNENA_RANKSMIN');
					?></td>
					<td><input class="post" type="text" name="rank_min" value="1" /></td>
				</tr>
				<tr>
					<td width="100"><?php
					echo JText::_('COM_KUNENA_RANKS_SPECIAL');
					?></td>
					<td><input type="checkbox" name="rank_special" value="1" /></td>
				</tr>
				<tr>
					<td colspan="2" align="center"><input type="hidden" name="option"
						value="<?php
					echo $option;
					?>"> <input type="hidden" name="task" value="showRanks"> <input
						type="hidden" name="boxchecked" value="0"></td>
				</tr>
			</table>
		</form>
		<?php
			} //end function edit rank
			function editrank($option, $edit_img, $filename_list, $path, $row) {
		?>
		<script language="javascript" type="text/javascript">
			<!--
			function update_rank(newimage)
			{
				document.rank_image.src = "<?php
				echo $path;
				?>" + newimage;
			}
			//-->
		</script>
		<div class="kadmin-functitle icon-ranks"><?php echo JText::_('COM_KUNENA_RANKS_EDIT'); ?></div>
		<form action="index.php" method="POST" name="adminForm">
			<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminform">
				<tr align="center">
					<td width="100"><?php
					echo JText::_('COM_KUNENA_RANKS');
					?></td>
					<td width="200"><input class="post" type="text" name="rank_title"
						value="<?php
					echo kescape($row->rank_title);
					?>" /></td>
				</tr>
				<tr align="center">
					<td width="100"><?php
					echo JText::_('COM_KUNENA_RANKSIMAGE');
					?></td>
					<td><select name="rank_image"
						onchange="update_rank(this.options[selectedIndex].value);">
						<?php
					echo $filename_list;
					?>
					</select> &nbsp; <img name="rank_image"
						src="<?php
					echo $edit_img;
					?>" border="0" alt="" /></td>
				</tr>
				<tr>
					<td width="100"><?php
					echo JText::_('COM_KUNENA_RANKSMIN');
					?></td>
					<td><input class="post" type="text" name="rank_min"
						value="<?php
					echo kescape($row->rank_min);
					?>" /></td>
				</tr>
				<tr>
					<td width="100"><?php
					echo JText::_('COM_KUNENA_RANKS_SPECIAL');
					?></td>
					<td><input type="checkbox" name="rank_special" value="1"
						<?php
					if ($row->rank_special == 1) {
						echo 'checked="checked"';
					}
					?> /></td>
				</tr>
				<tr>
					<td colspan="2" align="center"><input type="hidden" name="option"
						value="<?php
					echo $option;
					?>"> <input type="hidden" name="task" value="showRanks"> <input
						type="hidden" name="boxchecked" value="0"><input type="hidden"
						name="id" value="<?php
					echo $row->rank_id;
					?>"></td>
				</tr>
			</table>
		</form>
		<?php
			} //end function newrank

			//Start trash view
			function showtrashview($option, $trashitems, $pageNavSP, $lists) {
			?>
		<div class="kadmin-functitle icon-trash"><?php echo JText::_('COM_KUNENA_TRASH_VIEW'); ?></div>
		<form action="index.php" method="POST" name="adminForm" class="adminform">
			<table class="adminheading" cellpadding="4" cellspacing="0" border="0" width="100%"></table>
			<table>
				<tr>
					<td align="left" width="100%">
						<?php echo JText::_( 'Filter' ); ?>:
						<input type="text" name="search" id="search" value="<?php echo $lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
						<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
						<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
					</td>
					<td >
					</td>
				</tr>
			</table>
			<table class="adminlist" border="0" cellspacing="0" cellpadding="3" width="100%">
				<tr>
					<th width="20" align="center">#</th>
					<th align="left"><input type="checkbox" name="toggle" value=""
						onclick="checkAll(<?php
					echo count ( $trashitems );
					?>);" /></th>
					<th align="left"><?php
					echo  JHTML::_( 'grid.sort', JText::_('COM_KUNENA_TRASH_ID'), 'id', $lists['order_Dir'], $lists['order']);
					?></th>
					<th align="left" ><?php
					echo JHTML::_( 'grid.sort', JText::_('COM_KUNENA_TRASH_TITLE'), 'subject', $lists['order_Dir'], $lists['order']);
					?></th>
					<th align="left" ><?php
					echo JHTML::_( 'grid.sort', JText::_('COM_KUNENA_TRASH_CATEGORY'), 'cats_name', $lists['order_Dir'], $lists['order']);
					?></th>
					<th align="left" ><?php
					echo JHTML::_( 'grid.sort', JText::_('COM_KUNENA_TRASH_IP'), 'ip', $lists['order_Dir'], $lists['order']);
					?></th>
					<th align="left" ><?php
					echo JHTML::_( 'grid.sort', JText::_('COM_KUNENA_TRASH_AUTHOR_USERID'), 'userid', $lists['order_Dir'], $lists['order']);
					?></th>
					<th align="left" ><?php
					echo JHTML::_( 'grid.sort', JText::_('COM_KUNENA_TRASH_AUTHOR'), 'username', $lists['order_Dir'], $lists['order']);
					?></th>
					<th align="left" ><?php
					echo JHTML::_( 'grid.sort', JText::_('COM_KUNENA_TRASH_DATE'), 'time', $lists['order_Dir'], $lists['order']);
					?></th>
				</tr>
				<?php
					$k = 0;
					$i = 0;
					foreach ( $trashitems as $id => $row ) {
						$k = 1 - $k;
						?>
				<tr class="row<?php
						echo $k;
						?>">
					<td width="20" align="center"><?php
						echo ($id + $pageNavSP->limitstart + 1);
						?></td>
					<td width="20" align="center"><input type="checkbox"
						id="cb<?php
						echo $id;
						?>" name="cid[]"
						value="<?php
						echo $row->id;
						?>"
						onClick="isChecked(this.checked);"></td>
					<td >
						<?php
						echo $row->id;
						?>
						</td>
					<td ><?php
						echo kescape($row->subject);
						?></td>
					<td ><?php
						echo kescape($row->cats_name);
						?></td>
					<td ><?php
						echo kescape($row->ip);
						?></td>
					<td ><?php
						echo kescape($row->userid);
						?></td>
					<td ><?php
						if(empty($row->username)){
							echo JText::_('COM_KUNENA_VIEW_VISITOR');
						} else {
							echo kescape($row->username);
						}
						?></td>
					<td ><?php
						echo strftime('%Y-%m-%d %H:%M:%S',$row->time);
						?></td>
				</tr>
				<?php
					}
					?>
			<tr>
				<td class="kadmin-paging" colspan="9">
					<div class="pagination">
						<div class="limit"><?php echo JText::_('COM_KUNENA_A_DISPLAY'); ?> <?php echo $pageNavSP->getLimitBox (); ?></div>
							<?php echo $pageNavSP->getPagesLinks (); ?>
						<div class="limit"><?php echo $pageNavSP->getResultsCounter (); ?></div>
					</div>
				</td>
			</tr>
			</table>
			<input type="hidden" name="option" value="<?php echo $option; ?>">
			<input type="hidden" name="boxchecked" value="0">
			<input type="hidden" name="task" value="showtrashview">
			<input type="hidden" name="limitstart" value="0">
			<input type="hidden" name="return" value="showtrashview" />
			<input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />
		</form>
		<?php
			}
			function trashpurge($option, $return, $cid, $items) {
		?>
		<div class="kadmin-functitle"><?php echo JText::_('COM_KUNENA_TRASH_PURGE'); ?></div>
		<form action="index.php" method="POST" name="adminForm">
			<table class="adminheading" cellpadding="4" cellspacing="0" border="0" width="100%"></table>
			<table class="adminlist" border="0" cellspacing="0" cellpadding="3" width="100%">
				<tr>
					<td><strong><?php echo JText::_('COM_KUNENA_NUMBER_ITEMS'); ?>:</strong>
						<br />
						<font color="#000066"><strong><?php echo count( $cid ); ?></strong></font>
						<br /><br />
					</td>
					<td  valign="top" width="25%">
						<strong><?php echo JText::_('COM_KUNENA_ITEMS_BEING_DELETED'); ?>:</strong>
						<br />
						<?php echo "<ol>";
							foreach ( $items as $item ) {
								echo "<li>". kescape($item->subject) ."</li>";
							}
							echo "</ol>";
						?>
					</td>
					<td valign="top"><span style="color:red;"><strong><?php echo JText::_('COM_KUNENA_PERM_DELETE_ITEMS'); ?></strong></span>
					</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="1" />
			<input type="hidden" name="return" value="<?php echo $return;?>" />
			<?php
				foreach ($cid as $id) {
				echo "\n<input type=\"hidden\" name=\"cid[]\" value=\"$id\" />";
				}
			?>
		</form>
		<?php
			}
			//End trash view
			//Start report system
			function showSystemReport($option, $report) {
		?>
		<div class="kadmin-functitle icon-systemreport"><?php echo JText::_('COM_KUNENA_REPORT_SYSTEM'); ?></div>
		<script type="text/javascript">
			window.addEvent('domready', function(){
				$('link_sel_all').addEvent('click', function(e){
					$('report_final').select();
				});
			});
		</script>
		<form action="index.php" method="POST" name="adminForm" class="adminform">
		<fieldset><?php echo JText::_('COM_KUNENA_REPORT_SYSTEM_DESC'); ?><br /></fieldset>
		<fieldset>
			<div><a href="#" id="link_sel_all" ><?php echo JText::_('COM_KUNENA_REPORT_SELECT_ALL'); ?></a></div>
			<textarea id="report_final" name="report_final" cols="80" rows="15"><?php echo $report; ?></textarea>
		</fieldset>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="1" />
	</form>
<?php
	}//End report system
} //end class