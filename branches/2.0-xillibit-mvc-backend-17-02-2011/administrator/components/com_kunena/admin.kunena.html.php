<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 * Based on FireBoard Component
 * @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.bestofjoomla.com
 *
 * Based on Joomlaboard Component
 * @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @author TSMF & Jan de Graaff
 **/

defined( '_JEXEC' ) or die();


class html_Kunena {
	// Begin: HEADER FUNC
	function showFbHeader() {
		$document = JFactory::getDocument();
		$document->addStyleSheet ( JURI::base().'components/com_kunena/media/css/admin.css' );
		?>
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
	<div class="kadmin-left"><?php include KPATH_ADMIN.'/views/common/tmpl/menu.php'; ?></div>
	<div class="kadmin-right">
			<?php
			} // Finish: HEADER FUNC

			// Begin: FOOTER FUNC
			function showFbFooter() {
?>
	</div>
	<div class="kadmin-footer">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
</div>

	<?php
	} // Finish: FOOTER FUNC

	function controlPanel() {
		$kunena_config = KunenaFactory::getConfig ();
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

//*****************************************/
//			START TEMPLATE MANAGER
//*****************************************/

	function installKTemplate()
	{ ?>
		<div class="kadmin-functitle icon-template"><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER'); ?> - <?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_INSTALL_NEW'); ?></div><br />
		<form enctype="multipart/form-data" action="<?php echo KunenaRoute::_('index.php?option=com_kunena&task=installTemplate') ?>" method="post" name="adminForm">
			<table class="adminform">
				<tr>
					<th colspan="2"><?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_UPLOAD' ); ?></th>
				</tr>
				<tr>
					<td width="120">
						<label for="install_package"><?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_PACKAGE_FILE' ); ?>:</label>
					</td>
					<td>
						<input class="input_box" name="install_package" type="file" size="57" />
						<input class="button" type="submit" name="submit" value="<?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_UPLOAD_FILE' ); ?> &amp; <?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_INSTALL' ); ?>" />
					</td>
				</tr>
			</table>
			<?php echo JHTML::_( 'form.token' ); ?>
		</form>
	<?php
	}

	function showTemplates(& $rows, & $page, $option)
	{
		$kunena_app = & JFactory::getApplication ();
		$limitstart = JRequest :: getVar('limitstart', '0', '', 'int');
		$user = & JFactory :: getUser();
        if (isset($row->authorUrl) && $row->authorUrl != '') {
            $row->authorUrl = str_replace('http://', '', $row->authorUrl);
        }
		JHTML::_('behavior.tooltip');?>
	<div class="kadmin-functitle icon-template"><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER'); ?></div><br />
		<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" name="adminForm">
			<table class="adminlist">
			<thead>
				<tr>
					<th width="5" class="title"> # </th>
					<th class="title" colspan="2"><?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NAME' ); ?></th>
					<th width="5%"><?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_DEFAULT' ); ?></th>
					<th width="20%"  class="title"><?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_AUTHOR' ); ?></th>
					<th width="5%" align="center"><?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_VERSION' ); ?></th>
					<th width="7%" class="title"><?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_DATE' ); ?></th>
					<th width="20%"  class="title"><?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_AUTHOR_URL' ); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="8"><?php echo $page->getListFooter(); ?></td>
				</tr>
			</tfoot>
			<tbody>
			<?php
				$k = 0;
				for ($i = 0, $n = count($rows); $i < $n; $i++) {
				$row = & $rows[$i];
			?>
				<tr <?php echo 'class = "row' . $k . '"'; ?>>
					<td> <?php echo $page->getRowOffset( $i ); ?></td>
					<td width="5">
						<input type="radio" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo kescape($row->directory); ?>" onclick="isChecked(this.checked);" />
					</td>
					<td><?php $img_path = JURI::root().'components/com_kunena/template/'.$row->directory.'/images/template_thumbnail.png'; ?>
						<span class="editlinktip hasTip" title="<?php
							echo kescape($row->name . '::<img border="1" src="' . kescape($img_path) . '" name="imagelib" alt="' . JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_NO_PREVIEW' ) . '" width="200" height="145" />'); ?> ">
							<a href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=editKTemplate&amp;cid[]=<?php echo urlencode($row->directory);?>"><?php echo kescape($row->name);?></a>
						</span>
					</td>
					<td align="center">
						<?php if ($row->published == 1) { ?>
							<img src="<?php echo JURI::base(); ?>components/com_kunena/images/icons/default.png" alt="<?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_DEFAULT' ); ?>" />
						<?php } else { ?>
							<a href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=publishTemplate&amp;cid[]=<?php echo urlencode($row->directory);?>">
								<img src="<?php echo JURI::base(); ?>components/com_kunena/images/icons/default_off.png" alt="<?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_NO_DEFAULT' ); ?>" /></a>
						<?php } ?>
					</td>
					<td align="center">
						<span class="editlinktip" title="">
							<?php echo $row->authorEmail ? '<a href="mailto:' . kescape($row->authorEmail) . '">' . kescape($row->author) . '</a>' : kescape($row->author); ?>
						</span>
					</td>
					<td align="center">
						<?php echo kescape($row->version); ?>
					</td>
					<td align="center">
						<?php echo kescape($row->creationdate); ?>
					</td>
					<td align="center">
						<span class="editlinktip" title="">
							<a href="<?php echo substr($row->authorUrl, 0, 7) == 'http://' ? kescape($row->authorUrl) : 'http://' . kescape($row->authorUrl); ?>" target="_blank"><?php echo kescape($row->authorUrl); ?></a>
						</span>
					</td>
				</tr>
				<?php $k = 1 - $k; } ?>
			</tbody>
			</table>
	<input type="hidden" name="option" value="<?php echo $option;?>" />
	<input type="hidden" name="client" value="" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHTML::_( 'form.token' ); ?>
	</form>
	<?php
	}

	function editKTemplate($row, & $params, $option, & $ftp, & $template)
	{
		JHTML::_('behavior.tooltip');?>
		<div class="kadmin-functitle icon-template"><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_EDIT_TEMPLATE'); ?> - <?php echo JText::_($row->name); ?></div><br />
		<div style="border: 1px solid #ccc; padding: 10px 0 0;">
		<form action="index.php" method="post" name="adminForm">
		<div class="col width-50">
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_DETAILS' ); ?></legend>
				<table class="admintable">
				<tr>
					<td colspan="2" class="key" style="text-align:left; padding: 10px 0 0 10px;"><h1><?php echo JText::_($row->name); ?></h1></td>
				</tr>
				<tr>
					<td valign="top" class="key"><?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_AUTHOR' ); ?>:</td>
					<td><strong><?php echo JText::_($row->author); ?></strong></td>
				</tr>
				<tr>
					<td valign="top" class="key"><?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_DESCRIPTION' ); ?>:</td>
					<td><?php $path = KUNENA_PATH_TEMPLATE.DS.$template . '/images/template_thumbnail.png';
						if (file_exists ( $path )) : ?>
						<div class="tpl-thumbnail"><img src ="<?php echo JURI::root (); ?>/components/com_kunena/template/<?php echo kescape($template); ?>/images/template_thumbnail.png" alt="" /></div>
						<?php endif; ?>
						<div class="tpl-desc"><?php echo JText::_($row->description); ?></div>
					</td>
				</tr>
				</table>
			</fieldset>
		</div>
		<div class="col width-50">
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_PARAMETERS' ); ?></legend>
				<table class="admintable">
				<tr>
					<td colspan="2" class="key" style="text-align:left; padding: 10px">
						<?php $templatefile = KUNENA_PATH_TEMPLATE.DS.$template.DS.'params.ini';
							echo is_writable($templatefile) ? JText::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_PARAMSWRITABLE', kescape($templatefile)):JText::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_PARAMSUNWRITABLE', kescape($templatefile));
						?>
					</td>
				</tr>
				<tr>
					<td>
						<?php
							if (!is_null($params)) {
								echo $params->render();
							} else {
								echo '<em>' . JText :: _('COM_KUNENA_A_TEMPLATE_MANAGER_NO_PARAMETERS') . '</em>'; }
						?>
					</td>
				</tr>
				</table>
			</fieldset>
		</div>
		<div class="clr"></div>
		<input type="hidden" name="id" value="<?php echo kescape($row->directory); ?>" />
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="client" value="" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		</div>
		<?php
	}


	function chooseCSSFiles($template, $t_dir, $t_files, $option)
	{
 ?>
	<div class="kadmin-functitle icon-editcss"><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_CHOOSE_CSS_TEMPLATE'); ?></div><br />
	<form action="index.php" method="post" name="adminForm">
		<table cellpadding="1" cellspacing="1" border="0" width="100%">
		<tr>
			<td width="220"><span class="componentheading">&nbsp;</span></td>
		</tr>
		</table>
		<table class="adminlist">
		<tr>
			<th width="1%" align="left"> </th>
			<th width="85%" align="left"><?php echo kescape($t_dir); ?></th>
			<th width="10%"><?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_PARAMSWRITABLE' ); ?>/<?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_PARAMSUNWRITABLE' ); ?></th>
		</tr>
		<?php
		$k = 0;
		for ($i = 0, $n = count($t_files); $i < $n; $i++) {
			$file = & $t_files[$i]; ?>
			<tr class="<?php echo 'row'. $k; ?>">
				<td width="5%"><input type="radio" id="cb<?php echo $i;?>" name="filename" value="<?php echo kescape($file); ?>" onclick="isChecked(this.checked);" /></td>
				<td width="85%"><?php echo kescape($file); ?></td>
				<td width="10%"><?php echo is_writable($t_dir.DS.$file) ? '<font color="green"> '. JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_PARAMSWRITABLE' ) .'</font>' : '<font color="red"> '. JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_PARAMSUNWRITABLE' ) .'</font>' ?></td>
			</tr>
		<?php
			$k = 1 - $k; } ?>
		</table>
		<input type="hidden" name="id" value="<?php echo kescape($template); ?>" />
		<input type="hidden" name="cid[]" value="<?php echo kescape($template); ?>" />
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="chooseCSSFiles" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="client" value="" />
	</form>
		<?php

	}

	function editCSSSource($template, $filename, & $content, $option, & $ftp)
	{
		$css_path = KUNENA_PATH_TEMPLATE.DS.$template.DS.'css'.DS.$filename; ?>
	<div class="kadmin-functitle icon-editcss"><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_EDIT_CSS_TEMPLATE'); ?></div><br />
		<form action="index.php" method="post" name="adminForm">
		<?php if($ftp): ?>
		<fieldset title="<?php echo JText::_('DESCFTPTITLE'); ?>">
			<legend><?php echo JText::_('DESCFTPTITLE'); ?></legend>
			<?php echo JText::_('DESCFTP'); ?>
			<?php if(JError::isError($ftp)): ?>
				<p><?php echo JText::_($ftp->message); ?></p>
			<?php endif; ?>
			<table class="adminform nospace">
			<tbody>
			<tr>
				<td width="120"><label for="username"><?php echo JText::_('Username'); ?>:</label></td>
				<td><input type="text" id="username" name="username" class="input_box" size="70" value="" /></td>
			</tr>
			<tr>
				<td width="120"><label for="password"><?php echo JText::_('Password'); ?>:</label></td>
				<td><input type="password" id="password" name="password" class="input_box" size="70" value="" /></td>
			</tr>
			</tbody>
			</table>
		</fieldset>
		<?php endif; ?>
		<table class="adminform">
		<tr>
			<th>
				<?php echo kescape($css_path); ?>
			</th>
		</tr>
		<tr>
			<td><textarea style="width:100%;height:500px" cols="110" rows="25" name="filecontent" class="inputbox"><?php echo $content; ?></textarea></td>
		</tr>
		</table>
		<input type="hidden" name="id" value="<?php echo $template; ?>" />
		<input type="hidden" name="cid[]" value="<?php echo $template; ?>" />
		<input type="hidden" name="filename" value="<?php echo $filename; ?>" />
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="client" value="" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
	}

//*****************************************/
//			END TEMPLATE MANAGER
//******************************************/

			function showCss($file, $option) {
				$f = fopen ( $file, "r" );
				$content = fread ( $f, filesize ( $file ) );
		?>
		<div class="kadmin-functitle icon-editcss"><?php echo JText::_('COM_KUNENA_CSSEDITOR'); ?></div>
		<form action="index.php?" method="post" name="adminForm" class="adminForm" id="adminForm">
			<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminform">
				<tr>
					<th colspan="4"><?php echo JText::_('COM_KUNENA_PATH'). kescape($file); ?></th>
				</tr>
				<tr>
					<td><textarea cols="100" rows="20" name="csscontent"><?php echo kescape($content); ?></textarea></td>
				</tr>
				<tr>
					<td class="error"><?php echo JText::_('COM_KUNENA_CSSERROR'); ?></td>
				</tr>
			</table>
			<input type="hidden" name="file" value="<?php echo kescape($file); ?>" />
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value=""> <input type="hidden" name="boxchecked" value="0" /></form>
		<?php
			} //end function showCss

function showProfiles($option, &$users, $pageNav, $order, $lists) {
?>
<div class="kadmin-functitle icon-profiles"><?php echo JText::_('COM_KUNENA_FUM'); ?></div>
	<form action="index.php" method="post" name="adminForm">
		<table class="kadmin-sort">
			<tr>
				<td class="left" width="90%">
					<?php echo JText::_( 'COM_KUNENA_FILTER' ); ?>:
					<input type="text" name="search" id="search" value="<?php echo kescape($lists['search']);?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_( 'COM_KUNENA_GO' ); ?></button>
					<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'COM_KUNENA_RESET' ); ?></button>
				</td>
			</tr>
		</table>
		<table class="adminlist" border="0" cellspacing="0" cellpadding="3" width="100%">
			<thead>
				<tr>
					<th align="center" width="5">#</th>
					<th align="center" width="5"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count ( $users ); ?>);" /></th>
					<th align="center"><?php echo JText::_('COM_KUNENA_USRL_AVATAR'); ?></th>
					<th class="title" align="center"><?php echo JHTML::_('grid.sort', 'COM_KUNENA_ANN_ID', 'id', $lists['order_Dir'], $lists['order'] ); ?></th>
					<th align="left"><?php echo JHTML::_('grid.sort', 'COM_KUNENA_USRL_NAME', 'username', $lists['order_Dir'], $lists['order'] ); ?></th>
					<th align="left"><?php echo JHTML::_('grid.sort', 'COM_KUNENA_USRL_REALNAME', 'name', $lists['order_Dir'], $lists['order'] ); ?></th>
					<th align="center"><?php echo JText::_('COM_KUNENA_USRL_LOGGEDIN'); ?></th>
					<th align="center"><?php echo JText::_('COM_KUNENA_USRL_ENABLED'); ?></th>
					<th align="center"><?php echo JText::_('COM_KUNENA_USRL_BANNED'); ?></th>
<?php /*
					<th align="left"><?php echo JText::_('COM_KUNENA_GEN_EMAIL'); ?></th>
					<th align="left"><?php echo JText::_('COM_KUNENA_GEN_USERGROUP'); ?></th>
*/ ?>
					<th align="left"><?php echo JHTML::_('grid.sort', 'COM_KUNENA_VIEW_MODERATOR', 'moderator', $lists['order_Dir'], $lists['order'] ); ?></th>
					<th align="left"><?php echo JText::_('COM_KUNENA_GEN_SIGNATURE'); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="14">
						<div class="pagination">
							<div class="limit"><?php echo JText::_('COM_KUNENA_A_DISPLAY'). $pageNav->getLimitBox (); ?></div>
							<?php echo $pageNav->getPagesLinks (); ?>
							<div class="limit"><?php echo $pageNav->getResultsCounter (); ?></div>
						</div>
					</td>
				</tr>
			</tfoot>
			<?php
			if (!empty($users)) {
					$k = 1;
					//foreach ($profileList as $pl)
					$i = 0;
					foreach($users as $user) {
						$kunena_user = KunenaFactory::getUser($user->id);
						$k = 1 - $k;
						$userLogged = $kunena_user->isOnline() ? '<img src="components/com_kunena/images/tick.png" width="16" height="16" border="0" alt="" />': '';
						$userEnabled = $kunena_user->isBlocked() ? 'publish_x.png' : 'tick.png';
						$altUserEnabled = $kunena_user->isBlocked() ? JText::_( 'Blocked' ) : JText::_( 'Enabled' );
						$userBlockTask =  $kunena_user->isBlocked() ? 'userunblock' : 'userblock';
						$userbanned = $kunena_user->isBanned() ? 'tick.png' : 'publish_x.png';
						$userBannedTask = $kunena_user->isBanned() ? 'userunban' : 'userban';
						$altUserBanned = $kunena_user->isBanned() ? JText::_( 'Banned' ) : JText::_( 'Not banned' );
					?>
			<tr class="row<?php echo $k; ?>">
			<td class="right"><?php echo $i + $pageNav->limitstart + 1; ?></td>
				<td align="center">
					<?php echo JHTML::_('grid.id', $i, $user->id) ?>
				</td>
				<td align="center" width="1%"><?php echo $kunena_user->getAvatarLink('kavatar', 36, 36); ?></td>
				<td align="center" width="1%"><?php echo kescape($kunena_user->userid); ?></td>
				<td>
					<a href="#edit" onclick="return listItemTask('cb<?php echo $i; ?>','userprofile')"><?php echo kescape($kunena_user->username); ?></a>
				</td>
				<td>
					<a href="#edit" onclick="return listItemTask('cb<?php echo $i; ?>','userprofile')"><?php echo kescape($kunena_user->name); ?></a></td>
				<td align="center"><?php echo $userLogged; ?></td>
				<td align="center">
					<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $userBlockTask; ?>')">
						<img src="images/<?php echo $userEnabled;?>" width="16" height="16" border="0" alt="<?php echo $altUserEnabled; ?>" />
					</a></td>
				<td align="center">
					<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $userBannedTask; ?>')">
						<img src="images/<?php echo $userbanned;?>" width="16" height="16" border="0" alt="<?php echo $altUserBanned; ?>" />
					</a>
				</td>
<?php /*
				<td width="100"><?php echo kescape($kunena_user->email);
						?>&nbsp;</td>
				<td width="100"><?php echo kescape($kunena_user->usertype);
						?>&nbsp;</td>
*/ ?>
				<td align="center">
					<?php
					if ($kunena_user->moderator) {
						echo JText::_('COM_KUNENA_ANN_YES');
					} else {
						echo JText::_('COM_KUNENA_ANN_NO');
					}
					?>
				</td>
				<td width="*"><?php echo kescape ( $kunena_user->signature ); ?></td>
			</tr>
		<?php $i++; }
		} else { ?>
			<tr><td colspan="11"><?php echo JText::_('COM_KUNENA_NOUSERSFOUND') ?></td></tr>
		<?php } ?>
		</table>
		<input type="hidden" name="filter_order" value="<?php echo kescape($lists['order']); ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo kescape($lists['order_Dir']); ?>" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="showprofiles" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="limitstart" value="<?php echo $pageNav->limitstart ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
	<?php
} //end function showProfiles

function newModerator($option, $id, $moderators, &$modIDs, $forumName, &$userList, $countUL, $pageNav) {
	?>

	<form action="index.php" method="post" name="adminForm">
		<table cellpadding="4" class="adminheading" cellspacing="0" border="0" width="100%">
			<tr>
				<th width="100%" class="user"><?php echo JText::_('COM_KUNENA_ADDMOD'). kescape($forumName); ?>
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
					name="cid[]" value="<?php echo kescape($pl->id);
						?>"
					onclick="isChecked(this.checked);" /></td>
				<td width="20"><a
					href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=userprofile&amp;do=show&amp;user_id=<?php echo kescape($pl->id);
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
						<div class="limit"><?php echo JText::_('COM_KUNENA_A_DISPLAY'). $pageNav->getLimitBox (); ?></div>
							<?php echo $pageNav->getPagesLinks (); ?>
						<div class="limit"><?php echo $pageNav->getResultsCounter (); ?></div>
					</div>
				</td>
			</tr>

			<tr>
				<td colspan="7"><?php echo JText::_('COM_KUNENA_NOTEUS'); ?></td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="id" value="<?php echo kescape($id); ?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="task" value="newmoderator" />
		<input type="hidden" name="limitstart" value="0" /></form>

		<?php
}

function editUserProfile($option, $user, $subslist, $subscatslist, $selectRank, $selectPref, $selectMod, $selectOrder, $uid, $modCats, $useriplist) {
				$kunena_config = KunenaFactory::getConfig ();
				$kunena_db = &JFactory::getDBO ();
				//fill the variables needed later
				$signature = $user->signature;
				$username = $user->username;
				$avatarint = KunenaFactory::getAvatarIntegration();
				$editavatar = is_a($avatarint, 'KunenaAvatarKunena') ? true : false;
				$avatar = $avatarint->getLink($user->id, '', 'profile');
				$ordering = $user->ordering;
				//that's what we got now; later the 'future_use' columns can be used..

				$csubslist = count ( $subslist );
				//        include_once ('components/com_kunena/bb_adm.js'); ?>
		<div class="kadmin-functitle icon-profiles"> <?php echo JText::_('COM_KUNENA_PROFFOR'); ?>: <?php echo kescape($user->name) .' ('. kescape($user->username) .')'; ?></div>
		<form action="index.php?option=<?php echo $option; ?>" method="post" name="adminForm">
		<?php jimport('joomla.html.pane');
			$myTabs = &JPane::getInstance('tabs', array('startOffset'=>0));
			?>
		<dl class="tabs" id="pane">

		<dt title="<?php echo JText::_('COM_KUNENA_A_BASIC_SETTINGS') ?>"><?php echo JText::_('COM_KUNENA_A_BASIC_SETTINGS') ?></dt>
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
	<br /><br />
	<div><?php echo JText::sprintf('COM_KUNENA_SIGNATURE_LENGTH_COUNTER', intval($kunena_config->maxsig),
			'<input readonly="readonly" type="text" name="current_count" value="'.(intval($kunena_config->maxsig)-JString::strlen($signature)).'" size="3" />');?>
	</div>
	<br />
	<div> <input type="checkbox" value="1" name="deleteSig" /> <em><?php echo JText::_('COM_KUNENA_DELSIG'); ?></em></div>

	</td>
	</tr>
	</table>
</fieldset>
</dd>
<dt title="<?php echo JText::_('COM_KUNENA_A_AVATARS') ?>"><?php echo JText::_('COM_KUNENA_A_AVATARS') ?></dt>
			<dd>
			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_A_AVATARS') ?></legend>
				<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
				<tr>
				<th colspan="2" class="title"><?php echo JText::_('COM_KUNENA_UAVATAR'); ?></th>
			</tr>
			<tr>
				<td class="contentpane">
				<?php echo $avatar;
				if ($editavatar) { ?>
					<p><input type="checkbox" value="1"
					name="deleteAvatar" /> <em><?php echo JText::_('COM_KUNENA_DELAV'); ?></em></p></td>
				<?php } else {
					echo "<td>&nbsp;</td>";
					echo '<input type="hidden" value="" name="avatar" />';
				}
				?>

				<td><?php if ($editavatar) {
					 } else {
					echo "<td>&nbsp;</td>";
				}
				?></td>
			</tr>
		</table>

	</fieldset>
</dd>

	<dt title="<?php echo JText::_('COM_KUNENA_MOD_NEW') ?>"><?php echo JText::_('COM_KUNENA_MOD_NEW') ?></dt>
		<dd>
			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_MOD_NEW') ?></legend>
				<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
					<tr>
						<th colspan="2" class="title"><?php echo JText::_('COM_KUNENA_MODCHANGE'); ?></th>
					</tr>
					<tr>
						<td width="150" class="contentpane"><?php echo JText::_('COM_KUNENA_ISMOD'); ?></td>
						<td><?php echo JText::_('COM_KUNENA_MODCATS'); ?></td>
					</tr>
					<tr>
						<td width="150" class="contentpane"><?php
						echo $selectMod;
						?>
						</td>
						<td><?php echo $modCats; ?></td>
					</tr>
				</table>
				<input type="hidden" name="uid" value="<?php echo kescape($uid); ?>" />
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="option" value="com_kunena" />
				<input type="hidden" name="boxchecked" value="1" />
			</fieldset>
		</dd>
		<dt title="<?php echo JText::_('COM_KUNENA_CATEGORIES_SUBSCRIPTIONS') ?>"><?php echo JText::_('COM_KUNENA_CATEGORIES_SUBSCRIPTIONS') ?></dt>
			<dd>
			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_CATEGORIES_SUBSCRIPTIONS') ?></legend>
				<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
					<tr>
						<th colspan="2" class="title"><?php
						echo JText::_('COM_KUNENA_SUBFOR');
						echo kescape($username);
						?>
						</th>
					</tr>
					<?php
					$enum = 1; //reset value
					$k = 0; //value for alternating rows

					if (!empty($subscatslist)) {
						foreach($subscatslist as $subscats) { //get all category details for each subscription
							$kunena_db->setQuery ( "select cat.name AS catname, cat.id, msg.subject, msg.id, msg.catid, msg.name AS username from #__kunena_categories AS cat INNER JOIN #__kunena_messages AS msg ON cat.id=msg.catid where cat.id='$subscats->category_id' GROUP BY cat.id" );
							$catdetail = $kunena_db->loadObjectList ();
							if (KunenaError::checkDatabaseError()) break;

							foreach ( $catdetail as $cat ) {
								$k = 1 - $k;
								echo "<tr class=\"row$k\">";
								echo "  <td width=\"30\">$enum</td>";
								echo " <td><strong>" . kescape ( $cat->catname ) ."</strong>" ." &nbsp;". JText::_('COM_KUNENA_LAST_MESSAGE'). "<em>".kescape ( $cat->subject )."</em>" ." &nbsp;". JText::_('COM_KUNENA_BY') ." &nbsp;". "<em>".kescape ( $cat->username )."</em></td>";
								echo "</tr>";
								$enum ++;
							}
						}
					} else {
						echo "<tr><td class=\"message\">" . JText::_('COM_KUNENA_NOCATSUBS') . "</td></tr>";
					}
					?>
				</table>
			</fieldset>
			</dd>
			<dt title="<?php echo JText::_('COM_KUNENA_SUBSCRIPTIONS') ?>"><?php echo JText::_('COM_KUNENA_SUBSCRIPTIONS') ?></dt>
			<dd>
			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_SUBSCRIPTIONS') ?></legend>
				<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
					<tr>
						<th colspan="2" class="title"><?php
						echo JText::_('COM_KUNENA_SUBFOR');
						echo kescape($username);
						?>
						</th>
					</tr>
					<?php
						$enum = 1; //reset value
						$k = 0; //value for alternating rows


					if ($csubslist > 0) {
						foreach ( $subslist as $subs ) { //get all message details for each subscription
							$kunena_db->setQuery ( "select * from #__kunena_messages where id=$subs->thread" );
							$subdet = $kunena_db->loadObjectList ();
							if (KunenaError::checkDatabaseError()) break;

							foreach ( $subdet as $sub ) {
								$k = 1 - $k;
								echo "<tr class=\"row$k\">";
								echo "  <td width=\"30\">$enum</td>";
								echo " <td><strong>" . kescape ( $sub->subject ) ."</strong>" ." &nbsp;". JText::_('COM_KUNENA_BY' ) ." &nbsp;". "<em>".kescape ( $sub->name )."</em></td>";
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
		<dt title="<?php echo JText::_('COM_KUNENA_TRASH_IP') ?>"><?php echo JText::_('COM_KUNENA_TRASH_IP') ?></dt>
			<dd>
			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_TRASH_IP') ?></legend>
				<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
					<tr>
						<th colspan="3" class="title"><?php
						echo JText::sprintf('COM_KUNENA_IPFOR', kescape($username));
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
						echo "  <td width=\"30\">".$i."</td>";
						echo "  <td width=\"60\"><strong>".kescape($ip)."</strong></td>";
						echo "  <td>(".JText::sprintf('COM_KUNENA_IP_OCCURENCES', $mescnt).(!empty($userlist)?" ".JText::sprintf('COM_KUNENA_USERIDUSED', kescape($userlist)):'').")</td>";
						//echo "  <td>&nbsp;</td>";
						echo "</tr>";
					}
					?>
				</table>
			</fieldset>
		</dd>
	</dl>
<?php echo JHTML::_( 'form.token' ); ?>
</form>
	<?php
		}
		function moveUserMessages ( $option, $return, $uid, $lists, $userids ) {
	?>
			<div class="kadmin-functitle icon-profiles"><?php echo JText::_('COM_KUNENA_A_MOVE_USERMESSAGES'); ?></div>
			<table class="adminform">
				<tbody>
					<tr>
						<td>
						<strong><?php echo JText::_('COM_KUNENA_CATEGORY_TARGET'); ?></strong>
						<form action="index.php" method="post" name="adminForm">
						<?php
							echo $lists;
						?>
						<input type="hidden" name="boxchecked" value="1" />
						<input type="hidden" name="return" value="<?php echo kescape($return);?>" />
						<input type="hidden" name="task" value="" />
						<input type="hidden" name="option" value="<?php echo $option; ?>" />
						<input type="hidden" name="uid[]" value="<?php echo implode(',',$uid); ?>" />
						<?php echo JHTML::_( 'form.token' ); ?>
						</form>
						</td>
						<td><strong><?php echo JText::_('COM_KUNENA_MOVEUSERMESSAGES_USERS_CURRENT'); ?></strong>
						<ol>
						<?php
						foreach($userids as $id){
							echo '<li>'.kescape($id->username).' ('.JText::_('COM_KUNENA_TRASH_AUTHOR_USERID').' '.kescape($id->id).')</li> ';
						}

						?>
						</ol>
						</td>
					</tr>
				</tbody>
			</table>
	<?php
		}
	

			//***************************************
			// Uploaded Image Browser
			//***************************************
			function browseUploaded($option, $attachments, $type) {
				$kunena_db = &JFactory::getDBO ();
				$map = JPATH_ROOT;
				kimport ('kunena.forum.message.attachment.helper');
				?>
			<script language="javascript" type="text/javascript">
					<!--
					function decision(message, url)
					{
						if (confirm(message))
							location.href = url;
					}
					//-->
			</script>
			<div class="kadmin-functitle icon-<?php echo $type ? 'images' : 'files' ?>">
				<?php echo $type ? JText::_('COM_KUNENA_A_IMGB_IMG_BROWSE') : JText::_('COM_KUNENA_A_IMGB_FILE_BROWSE'); ?>
			</div>
			<table class="adminform">
				<tr>
					<td>
					<?php echo $type ? JText::_('COM_KUNENA_A_IMGB_TOTAL_IMG') : JText::_('COM_KUNENA_A_IMGB_TOTAL_FILES') . ': ' . count ( $attachments ) ?>
					</td>
				</tr>
				<tr>
					<td>
					<?php echo $type ? JText::_('COM_KUNENA_A_IMGB_ENLARGE') : JText::_('COM_KUNENA_A_IMGB_DOWNLOAD'); ?>
					</td>
				</tr>
			</table>
			<?php if (!empty($attachments)) : ?>
			<table class="adminform">
				<tr>
<?php
			$i=0;
			foreach($attachments as $attachment) :
				$i++;
				$filename = $attachment->filename;
				$attach_live_path = JURI::root().'/'.$attachment->folder.'/'.$attachment->filename;
				$attach_path = JPATH_ROOT.'/'.$attachment->folder.'/'.$attachment->filename;
?>
				<td>
				<table style="border: 1px solid #ccc;">
					<tr>
					<td height="90" width="130" style="text-align: center">
<?php
				echo $type ? '<a href="' . kescape($attach_live_path) . '" target="_blank" title="' . JText::_('COM_KUNENA_A_IMGB_ENLARGE') . '">
				<img src="' . kescape($attach_live_path) . '" width="80" height="80" border="0" alt="" /></a>'
				: '<a href="' . kescape($attach_live_path) . '" title="' . JText::_('COM_KUNENA_A_IMGB_DOWNLOAD') . '">
				<img src="../administrator/components/com_kunena/images/kfile.png" border="0" alt="" /></a>';
?>
					</td>
					</tr>
					<tr>
					<td style="text-align: center">
					<br />
					<strong><?php echo JText::_('COM_KUNENA_A_IMGB_NAME') ?>: </strong>
					<?php echo kescape(KunenaForumMessageAttachmentHelper::shortenFileName($filename, 10, 15)) ?>
					<br />
				<?php if (is_file($attach_path)) { ?>
					<strong><?php echo JText::_('COM_KUNENA_A_IMGB_SIZE') ?>: </strong> <?php echo @filesize ( $attach_path ) . ' ' . JText::_('COM_KUNENA_A_IMGB_WEIGHT') ?>
					<br />
					<?php
					$type ? list ( $width, $height ) = @getimagesize ( $attach_path ) : '';
					echo $type ? '<strong>' . JText::_('COM_KUNENA_A_IMGB_DIMS') . ': </strong> ' . $width . 'x' . $height . '<br />' : '';
				}
				echo $type ? '<a href="javascript:decision(\'' . JText::_('COM_KUNENA_A_IMGB_CONFIRM') . '\',\'index.php?option=' . $option . '&amp;task=deleteImage&amp;id=' . $attachment->id . '\')">' . JText::_('COM_KUNENA_A_IMGB_REMOVE') . '</a><br />' : '<a href="javascript:decision(\'' . JText::_('COM_KUNENA_A_IMGB_CONFIRM') . '\',\'index.php?option=' . $option . '&task=deleteFile&id=' . kescape($attachment->id) . '\')">' . JText::_('COM_KUNENA_A_IMGB_REMOVE') . '</a><br />';

				if ($attachment->catid > 0) {
					echo '<a href="../index.php?option=' . $option . '&amp;func=view&amp;catid=' . kescape($attachment->catid) . '&amp;id=' . kescape($attachment->mesid) . '#' . kescape($attachment->mesid) . '" target="_blank">' . JText::_('COM_KUNENA_A_IMGB_VIEW') . '</a>';
				} else {
					echo JText::_('COM_KUNENA_A_IMGB_NO_POST');
				}
?>
					</td>
					</tr>
				</table>
				</td>
<?php
				if (! fmod ( $i, 5 )) {
					echo '</tr><tr align="center" valign="middle">';
				}
			endforeach;
?>
			</tr>
		</table>
		<?php endif; ?>
<?php
		}
			//***************************************
			// Show Smilies
			//***************************************
		function showsmilies($option, &$smileytmp, $pageNav) {
			$template = KunenaFactory::getTemplate();
		?>
		<div class="kadmin-functitle icon-smilies"><?php echo JText::_('COM_KUNENA_EMOTICONS_EMOTICON_MANAGER'); ?></div>
		<?php jimport('joomla.html.pane');
			$myTabs = &JPane::getInstance('tabs', array('startOffset'=>0));
			?>
		<dl class="tabs" id="pane">
		<dt title="<?php echo JText::_('COM_KUNENA_A_EMOTICONS'); ?>"><?php echo JText::_('COM_KUNENA_A_EMOTICONS'); ?></dt>
		<dd>
		<form action="index.php" method="post" name="adminForm">
			<table class="adminlist" border="0" cellspacing="0" cellpadding="3" width="100%">
			<thead>
				<tr>
					<th width="5" align="center">#</th>
					<th align="center" width="5"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count ( $smileytmp ); ?>);" /></th>
					<th align="center" width="50"><?php echo JText::_('COM_KUNENA_EMOTICON'); ?></th>
					<th align="center" width="50"><?php echo JText::_('COM_KUNENA_EMOTICONS_CODE'); ?></th>
					<th align="left" width="80%"><?php echo JText::_('COM_KUNENA_EMOTICONS_URL'); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="14">
						<div class="pagination">
							<div class="limit"><?php echo JText::_('COM_KUNENA_A_DISPLAY'). $pageNav->getLimitBox (); ?></div>
							<?php echo $pageNav->getPagesLinks (); ?>
							<div class="limit"><?php echo $pageNav->getResultsCounter (); ?></div>
						</div>
					</td>
				</tr>
			</tfoot>
				<?php
					$k = 1;
					$i = 0;
					for($i = 0, $n = count ( $smileytmp ); $i < $n; $i ++) {
						$k = 1 - $k;
						$s = &$smileytmp [$i];
				?>
				<tr class="row<?php echo $k; ?>" align="center">
					<td align="center"><a href="#edit"
						onclick="return listItemTask('cb<?php
						echo $i;
						?>','editsmiley')"><?php
						echo kescape($s->id);
						?></a></td>
					<td align="center"><input type="checkbox"
						id="cb<?php
						echo $i;
						?>" name="cid[]"
						value="<?php
						echo kescape($s->id);
						?>"
						onclick="isChecked(this.checked);" /></td>
					<td width="50" align="center"><a href="#edit"
						onclick="return listItemTask('cb<?php
						echo $i;
						?>','editsmiley')"><img
						src="<?php
						echo kescape( KURL_SITE . $template->getSmileyPath($s->location) )
						?>"
						alt="<?php
						echo kescape($s->location);
						?>" border="0" /></a></td>
					<td width="50" align="center"><?php echo kescape($s->code); ?>&nbsp;</td>
					<td width="80%" align="left"><?php echo kescape($s->location); ?>&nbsp;</td>
				</tr>
				<?php
					}
					?>
			</table>
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="showsmilies" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php echo '<input type = "hidden" name = "limitstart" value = "0" />'; ?>
			<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		</dd>
		<dt title="<?php echo JText::_('COM_KUNENA_A_EMOTICONS_UPLOAD'); ?>"><?php echo JText::_('COM_KUNENA_A_EMOTICONS_UPLOAD'); ?></dt>
		<dd>
		<form action="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=uploadsmilies" id="uploadForm" method="post" enctype="multipart/form-data" >
		<div style="padding:10px;">
			<?php echo JHTML::_( 'form.token' ); ?>
			<input type="file" id="file-upload" name="Filedata" />
			<input type="submit" id="file-upload-submit" value="<?php echo JText::_('COM_KUNENA_A_START_UPLOAD'); ?>" />
			<span id="upload-clear"></span>
		</div>
		<ul class="upload-queue" id="upload-queue">
			<li style="display: none" />
		</ul>
		<input type="hidden" name="return-url" value="<?php echo base64_encode('index.php?option=com_kunena&task=newsmiley'); ?>" />
		</form>
		</dd>
		</dl>
		<?php
			} //end function showsmilies

			function editsmiley($option, $smiley_edit_img, $filename_list, $smileypath, $smileycfg) {
			?>
		<script language="javascript" type="text/javascript">
			<!--
			function update_smiley(newimage)
			{
				document.smiley_image.src = "<?php
				echo kescape(KURL_SITE . $smileypath);
				?>" + newimage;
			}
			//-->
		</script>
		<div class="kadmin-functitle icon-smilies"><?php echo JText::_('COM_KUNENA_EMOTICONS_EDIT_SMILEY'); ?></div>
		<form action="index.php" method="post" name="adminForm">
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
					echo kescape(KURL_SITE . $smiley_edit_img);
					?>" border="0" alt="" /> &nbsp;</td>
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
					?>" /> <input type="hidden" name="task" value="showsmilies"> <input
						type="hidden" name="boxchecked" value="0"><input type="hidden"
						name="id" value="<?php
					echo kescape($smileycfg ['id']);
					?>" /></td>
				</tr>
			</table>
			<?php echo JHTML::_( 'form.token' ); ?>
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
				echo kescape(KURL_SITE . $smileypath);
				?>" + newimage;
			}
		//-->
		</script>
		<div class="kadmin-functitle icon-smilies"><?php echo JText::_('COM_KUNENA_EMOTICONS_NEW_SMILEY'); ?></div>
		<form action="index.php" method="post" name="adminForm">
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
					?>" /> <input type="hidden" name="task" value="showsmilies"> <input
						type="hidden" name="boxchecked" value="0" /></td>
				</tr>
			</table>
			<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
			} //end function newsmilies
			/// Rank Administration
			function showRanks($option, &$ranks, $pageNav, $order) {
			$kunena_db = &JFactory::getDBO ();
			$template = KunenaFactory::getTemplate();
		?>
		<div class="kadmin-functitle icon-ranks"><?php echo JText::_('COM_KUNENA_RANK_MANAGER'); ?></div>
		<?php jimport('joomla.html.pane');
			$myTabs = &JPane::getInstance('tabs', array('startOffset'=>0));
			?>
		<dl class="tabs" id="pane">
		<dt><?php echo JText::_('COM_KUNENA_A_RANKS'); ?></dt>
		<dd>
		<form action="index.php" method="post" name="adminForm">
			<table class="adminlist" border="0" cellspacing="0" cellpadding="3" width="100%">
			<thead>
				<tr>
					<th width="5" align="center">#</th>
					<th width="5" align="left"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count ( $ranks ); ?>);" /></th>
					<th width="20%" align="left"><?php echo JText::_('COM_KUNENA_RANKSIMAGE'); ?></th>
					<th width="50%" align="left" ><?php echo JText::_('COM_KUNENA_RANKS'); ?></th>
					<th width="10%" align="center" ><?php echo JText::_('COM_KUNENA_RANKS_SPECIAL'); ?></th>
					<th width="10%" align="center" class="nowrap" ><?php echo JText::_('COM_KUNENA_RANKSMIN'); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="6">
						<div class="pagination">
							<div class="limit"><?php echo JText::_('COM_KUNENA_A_DISPLAY'). $pageNav->getLimitBox (); ?></div>
							<?php echo $pageNav->getPagesLinks (); ?>
							<div class="limit"><?php echo $pageNav->getResultsCounter (); ?></div>
						</div>
					</td>
				</tr>
			</tfoot>
				<?php
					$k = 1;
					$i = 0;
					foreach ( $ranks as $id => $row ) {
						$k = 1 - $k;
						?>
				<tr class="row<?php
						echo $k;
						?>">
					<td align="center"><?php
						echo ($id + $pageNav->limitstart + 1);
						?></td>
					<td align="center"><input type="checkbox"
						id="cb<?php
						echo $id;
						?>" name="cid[]"
						value="<?php
						echo kescape($row->rank_id);
						?>"
						onclick="isChecked(this.checked);" /></td>
					<td><a href="#edit"
						onclick="return listItemTask('cb<?php
						echo $id;
						?>','editRank')"><img
						src="<?php
						echo kescape(KURL_SITE . $template->getRankPath($row->rank_image))
						?>"
						alt="<?php
						echo kescape($row->rank_image);
						?>" border="0" /></a></td>
					<td class="nowrap"><a href="#edit"
						onclick="return listItemTask('cb<?php
						echo $id;
						?>','editRank')"><?php
						echo kescape($row->rank_title);
						?></a></td>
					<td align="center"><?php
						if ($row->rank_special == 1) {
							echo JText::_('COM_KUNENA_ANN_YES');
						} else {
							echo JText::_('COM_KUNENA_ANN_NO');
						}
						?></td>
					<td align="center"><?php
						echo kescape($row->rank_min);
						?></td>
				</tr>
				<?php
					}
					?>
			</table>
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="task" value="ranks" />
			<input type="hidden" name="limitstart" value="0" />
			<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		</dd>
		<dt title="<?php echo JText::_('COM_KUNENA_A_RANKS_UPLOAD'); ?>"><?php echo JText::_('COM_KUNENA_A_RANKS_UPLOAD'); ?></dt>
		<dd>
		<form action="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=uploadranks" id="uploadForm" method="post" enctype="multipart/form-data" >
		<div style="padding:10px;">
			<?php echo JHTML::_( 'form.token' ); ?>
			<input type="file" id="file-upload" name="Filedata" />
			<input type="submit" id="file-upload-submit" value="<?php echo JText::_('COM_KUNENA_A_START_UPLOAD'); ?>" />
			<span id="upload-clear"></span>
		</div>
		<ul class="upload-queue" id="upload-queue">
			<li style="display: none" />
		</ul>
		<input type="hidden" name="return-url" value="<?php echo base64_encode('index.php?option=com_kunena&task=newRank'); ?>" />
		</form>
		</dd>
		</dl>
		<?php
			} //end function showRanks

		function newRank($option, $filename_list, $rankpath) {
		?>
		<script language="javascript" type="text/javascript">
			<!--
			function update_rank(newimage)
			{
				document.rank_image.src = "<?php
				echo kescape(KURL_SITE . $rankpath);
				?>" + newimage;
			}
			//-->
		</script>
		<div class="kadmin-functitle icon-ranks"><?php echo JText::_('COM_KUNENA_NEW_RANK'); ?></div>
		<form action="index.php" method="post" name="adminForm">
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
					?>" /> <input type="hidden" name="task" value="showRanks"> <input
						type="hidden" name="boxchecked" value="0" /></td>
				</tr>
			</table>
			<?php echo JHTML::_( 'form.token' ); ?>
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
				echo kescape(KURL_SITE . $path);
				?>" + newimage;
			}
			//-->
		</script>
		<div class="kadmin-functitle icon-ranks"><?php echo JText::_('COM_KUNENA_RANKS_EDIT'); ?></div>
		<form action="index.php" method="post" name="adminForm">
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
						onchange="update_rank(this.options[selectedIndex].value);" onmousemove="update_rank(this.options[selectedIndex].value);">
						<?php
					echo $filename_list;
					?>
					</select> &nbsp; <img name="rank_image"
						src="<?php
					echo kescape(KURL_SITE . $edit_img);
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
					?>" /> <input type="hidden" name="task" value="showRanks"> <input
						type="hidden" name="boxchecked" value="0" /><input type="hidden"
						name="id" value="<?php
					echo kescape($row->rank_id);
					?>" /></td>
				</tr>
			</table>
			<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
			} //end function newrank
} //end class