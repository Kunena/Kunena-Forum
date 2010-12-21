<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
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
	<div class="kadmin-left">
		<div id="kadmin-menu">
			<?php $stask = JRequest::getVar ( 'task', null ); ?>
				<a class="kadmin-mainmenu icon-cp-sm" href="<?php echo JURI::base(); ?>index.php?option=com_kunena"><?php echo JText::_('COM_KUNENA_CP'); ?></a>
				<a class="kadmin-mainmenu icon-config-sm" href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=showconfig"><?php echo JText::_('COM_KUNENA_C_FBCONFIG'); ?></a>
				<a class="kadmin-mainmenu icon-adminforum-sm" href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=showAdministration"><?php echo JText::_('COM_KUNENA_C_FORUM'); ?></a>
				<a class="kadmin-mainmenu icon-profiles-sm" href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=showprofiles"><?php echo JText::_('COM_KUNENA_C_USER'); ?></a>
				<a class="kadmin-mainmenu icon-template-sm" href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=showTemplates"><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER'); ?></a>
				<a class="kadmin-mainmenu icon-smilies-sm" href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=showsmilies"><?php echo JText::_('COM_KUNENA_EMOTICONS_EMOTICON_MANAGER'); ?></a>
				<a class="kadmin-mainmenu icon-ranks-sm" href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=ranks"><?php echo JText::_('COM_KUNENA_RANK_MANAGER'); ?></a>
				<a class="kadmin-mainmenu icon-files-sm" href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=browseFiles"><?php echo JText::_('COM_KUNENA_C_FILES'); ?></a>
				<a class="kadmin-mainmenu icon-images-sm" href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=browseImages"><?php echo JText::_('COM_KUNENA_C_IMAGES'); ?></a>
				<a class="kadmin-mainmenu icon-prune-sm" href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=pruneforum"><?php echo JText::_('COM_KUNENA_C_PRUNETAB'); ?></a>
				<a class="kadmin-mainmenu icon-syncusers-sm" href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=syncusers"><?php echo JText::_('COM_KUNENA_SYNC_USERS'); ?></a>
				<a class="kadmin-mainmenu icon-recount-sm" href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=recount"><?php echo JText::_('COM_KUNENA_RECOUNTFORUMS'); ?></a>
				<a class="kadmin-mainmenu icon-trash-sm" href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=showtrashview"><?php echo JText::_('COM_KUNENA_TRASH_VIEW'); ?></a>
                <a class="kadmin-mainmenu icon-stats-sm" href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=showstats"><?php echo JText::_('COM_KUNENA_STATS_GEN_STATS'); ?></a>
				<a class="kadmin-mainmenu icon-systemreport-sm" href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=showsystemreport"><?php echo JText::_('COM_KUNENA_REPORT_SYSTEM'); ?></a>
				<a class="kadmin-mainmenu icon-support-sm" href="http://www.kunena.org" target="_blank"><?php echo JText::_('COM_KUNENA_C_SUPPORT'); ?></a>
		</div>
	</div>
	<div class="kadmin-right">
			<?php
			} // Finish: HEADER FUNC

			// Begin: FOOTER FUNC
			function showFbFooter() {
?>
	</div>
	<div class="kadmin-footer">
		<?php echo CKunenaVersion::versionHTML (); ?>
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
		<form enctype="multipart/form-data" action="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=installTemplate" method="post" name="adminForm">
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
		<form action="index.php" method="post" name="adminForm">
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
			<td><textarea style="width:100%;height:500px" cols="110" rows="25" name="filecontent" class="inputbox"><?php echo kescape($content); ?></textarea></td>
		</tr>
		</table>
		<input type="hidden" name="id" value="<?php echo kescape($template); ?>" />
		<input type="hidden" name="cid[]" value="<?php echo kescape($template); ?>" />
		<input type="hidden" name="filename" value="<?php echo kescape($filename); ?>" />
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

function showAdministration($rows, $children, $pageNav, $option, $lists) {
?>
	<div class="kadmin-functitle icon-adminforum"><?php echo JText::_('COM_KUNENA_ADMIN'); ?></div>
	<form action="index.php" method="post" name="adminForm">
		<table class="kadmin-sort">
			<tr>
				<td class="left" width="90%">
					<?php echo JText::_( 'Filter' ); ?>:
					<input type="text" name="search" id="search" value="<?php echo kescape($lists['search']);?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
					<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
				</td>
			</tr>
		</table>
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
			<thead>
				<tr>
					<th align="center" width="5">#</th>
					<th width="5"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count ( $rows ); ?>);" /></th>
					<th class="title"><?php echo JHTML::_('grid.sort', JText::_('COM_KUNENA_CATEGORY'), 'name', $lists['order_Dir'], $lists['order'] ); ?></th>
					<th><small><?php echo JHTML::_('grid.sort', JText::_('COM_KUNENA_CATID'), 'id', $lists['order_Dir'], $lists['order'] ); ?></small></th>
					<th width="100" class="center nowrap"><small>
						<?php echo JHTML::_('grid.sort', JText::_('COM_KUNENA_REORDER'), 'ordering', $lists['order_Dir'], $lists['order'] ); ?>
						<?php echo JHTML::_('grid.order',  $rows ); ?></small>
					</th>
					<th class="center"><small><?php echo JText::_('COM_KUNENA_LOCKED'); ?></small></th>
					<th class="center"><small><?php echo JText::_('COM_KUNENA_MODERATED'); ?></small></th>
					<th class="center"><small><?php echo JText::_('COM_KUNENA_REVIEW'); ?></small></th>
					<th class="center"><small><?php echo JText::_('COM_KUNENA_CATEGORY_ANONYMOUS'); ?></small></th>
					<th class="center"><small><?php echo JText::_('COM_KUNENA_ADMIN_POLLS'); ?></small></th>
					<th class="center"><small><?php echo JText::_('COM_KUNENA_PUBLISHED'); ?></small></th>
					<th class="center"><small><?php echo JText::_('COM_KUNENA_PUBLICACCESS'); ?></small></th>
					<th class="center"><small><?php echo JText::_('COM_KUNENA_ADMINACCESS'); ?></small></th>
					<th class="center"><small><?php echo JText::_('COM_KUNENA_CHECKEDOUT'); ?></small></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="14">
						<div class="pagination">
							<div class="limit"><?php echo JText::_('COM_KUNENA_A_DISPLAY'); ?> <?php echo $pageNav->getLimitBox (); ?></div>
							<?php echo $pageNav->getPagesLinks (); ?>
							<div class="limit"><?php echo $pageNav->getResultsCounter (); ?></div>
						</div>
					</td>
				</tr>
			</tfoot>
	<?php
		$k = 0;
		$i = 0;
		$n = count($rows);
		$img_yes = '<img src="images/tick.png" alt="'.JText::_('COM_KUNENA_A_YES').'" />';
		$img_no = '<img src="images/publish_x.png" alt="'.JText::_('COM_KUNENA_A_NO').'" />';
		foreach($rows as $row) {
	?>
		<tr <?php echo 'class = "row' . $k . '"';?>>
			<td class="right"><?php echo $i + $pageNav->limitstart + 1; ?></td>
			<td><?php echo JHTML::_('grid.id', $i, $row->id) ?></td>
			<td class="left" width="70%"><a href="#edit" onclick="return listItemTask('cb<?php echo $i ?>','edit')"><?php echo $row->treename; ?></a></td>
			<td class="center"><?php echo kescape($row->id); ?></td>

			<?php if (! $row->category): ?>

			<td class="right nowrap">
				<span><?php echo $pageNav->orderUpIcon ( $i, isset ( $children [$row->parent] [$row->location - 1] ), 'orderup', 'Move Up', 1 ); ?></span>
				<span><?php echo $pageNav->orderDownIcon ( $i, $n, isset ( $children [$row->parent] [$row->location + 1] ), 'orderdown', 'Move Down', 1 ); ?></span>
				<input type="text" name="order[<?php echo kescape($row->id) ?>]" size="5" value="<?php echo kescape($row->ordering); ?>" class="text_area center" />
			</td>
			<td colspan="5" class="center"><?php echo JText::_('COM_KUNENA_SECTION') ?></td>

			<?php else: ?>

			<td class="right nowrap">
				<span><?php echo $pageNav->orderUpIcon ( $i, isset ( $children [$row->parent] [$row->location - 1] ), 'orderup', 'Move Up', 1 ); ?></span>
				<span><?php echo $pageNav->orderDownIcon ( $i, $n, isset ( $children [$row->parent] [$row->location + 1] ), 'orderdown', 'Move Down', 1 ); ?></span>
				<input type="text" name="order[<?php echo kescape($row->id) ?>]" size="5" value="<?php echo kescape($row->ordering); ?>" class="text_area" style="text-align: center" />
			</td>
			<td class="center">
				<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo 'cat_lock_' . kescape($row->locked); ?>')">
					<?php echo ($row->locked == 1 ? $img_yes : $img_no); ?>
				</a>
			</td>
			<td class="center">
				<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo 'cat_moderate_' . kescape($row->moderated); ?>')">
					<?php echo ($row->moderated == 1 ? $img_yes : $img_no); ?>
				</a>
			</td>
			<td class="center">
				<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo 'cat_review_' . kescape($row->review); ?>')">
					<?php echo ($row->review == 1 ? $img_yes : $img_no); ?>
				</a>
			</td>
			<td class="center">
				<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo 'cat_allow_anonymous_' . kescape($row->allow_anonymous); ?>')">
					<?php echo ($row->allow_anonymous == 1 ? $img_yes : $img_no); ?>
				</a>
			</td>
			<td class="center">
				<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo 'cat_allow_polls_' . kescape($row->allow_polls); ?>')">
					<?php echo ($row->allow_polls == 1 ? $img_yes : $img_no); ?>
				</a>
			</td>

			<?php endif; ?>

			<td class="center"><?php echo JHTML::_('grid.published', $row, $i) ?></td>
			<td width="" align="center"><?php echo kescape($row->groupname); ?></td>
			<td width="" align="center"><?php echo kescape($row->admingroup); ?></td>
			<td width="15%" align="center"><?php echo kescape($row->editor); ?></td>
		</tr>
			<?php
			$i++;
			$k = 1 - $k;
			}
			?>
	</table>

	<input type="hidden" name="return" value="showAdministration" />
	<input type="hidden" name="filter_order" value="<?php echo kescape($lists['order']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo kescape($lists['order_Dir']); ?>" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="showAdministration" />
	<input type="hidden" name="limitstart" value="<?php echo $pageNav->limitstart ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHTML::_( 'form.token' ); ?>
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
                    alert("<?php echo JText::_('COM_KUNENA_ERROR1'); ?>");
                }
                else
                {
                    submitform(pressbutton);
                }
            }
		</script>
		<div class="kadmin-functitle icon-adminforum"><?php echo JText::_('COM_KUNENA_ADMIN') ?></div>
		<form action="index.php?option=<?php echo $option; ?>" method="post" name="adminForm">

		<?php jimport('joomla.html.pane');
		$myTabs = &JPane::getInstance('tabs', array('startOffset'=>0)); ?>
	<dl class="tabs" id="pane">
	<dt title="<?php echo JText::_('COM_KUNENA_CATEGORY_INFO'); ?>"><?php echo JText::_('COM_KUNENA_CATEGORY_INFO'); ?></dt>
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
					<td><input class="inputbox" type="text" name="name" size="120" maxlength="<?php echo intval($kunena_config->maxsubject); ?>" value="<?php echo kescape ( $row->name ); ?>" /></td>
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
		<dt title="<?php echo JText::_('COM_KUNENA_ADVANCEDDESC'); ?>"><?php echo JText::_('COM_KUNENA_ADVANCEDDESC'); ?></dt>
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
					<?php if ($row->accesstype == 'none'): ?>
					<tr>
						<td class="nowrap" valign="top"><?php echo JText::_('COM_KUNENA_PUBACC'); ?></td>
						<td valign="top"><?php echo $accessLists ['pub_access']; ?></td>
						<td><?php echo JText::_('COM_KUNENA_PUBACCDESC'); ?></td>
					</tr>
					<tr>
						<td class="nowrap" valign="top"><?php echo JText::_('COM_KUNENA_CGROUPS'); ?></td>
						<td valign="top"><?php echo $lists ['pub_recurse']; ?></td>
						<td valign="top"><?php echo JText::_('COM_KUNENA_CGROUPSDESC'); ?></td>
					</tr>
					<tr>
						<td valign="top"><?php echo JText::_('COM_KUNENA_ADMINLEVEL'); ?></td>
						<td valign="top"><?php echo $accessLists ['admin_access']; ?></td>
						<td valign="top"><?php echo JText::_('COM_KUNENA_ADMINLEVELDESC'); ?></td>
					</tr>
					<tr>
						<td class="nowrap" valign="top"><?php echo JText::_('COM_KUNENA_CGROUPS1'); ?></td>
						<td valign="top"><?php echo $lists ['admin_recurse']; ?></td>
						<td valign="top"><?php echo JText::_('COM_KUNENA_CGROUPS1DESC'); ?></td>
					</tr>
					<?php endif; ?>
					<?php if (!$row->id || $row->parent): ?>
					<tr>
						<td class="nowrap" valign="top"><?php echo JText::_('COM_KUNENA_REV'); ?></td>
						<td valign="top"><?php echo $lists ['forumReview']; ?></td>
						<td valign="top"><?php echo JText::_('COM_KUNENA_REVDESC'); ?></td>
					</tr>
					<tr>
						<td class="nowrap" valign="top"><?php echo JText::_('COM_KUNENA_CATEGORY_ANONYMOUS_ALLOW'); ?>:</td>
						<td valign="top"><?php echo $lists ['allow_anonymous']; ?></td>
						<td valign="top"><?php echo JText::_('COM_KUNENA_CATEGORY_ANONYMOUS_ALLOW_DESC'); ?></td>
					</tr>
					<tr>
						<td class="nowrap" valign="top"><?php echo JText::_('COM_KUNENA_CATEGORY_ANONYMOUS_DEFAULT'); ?>:</td>
						<td valign="top"><?php echo $lists ['post_anonymous']; ?></td>
						<td valign="top"><?php echo JText::_('COM_KUNENA_CATEGORY_ANONYMOUS_DEFAULT_DESC'); ?></td>
					</tr>
					<tr>
						<td class="nowrap" valign="top"><?php echo JText::_('COM_KUNENA_A_POLL_CATEGORIES_ALLOWED'); ?>:</td>
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
						<td><input class="inputbox" type="text" name="class_sfx" size="20" maxlength="20" value="<?php echo kescape($row->class_sfx); ?>" /></td>
						<td><?php echo JText::_('COM_KUNENA_CLASS_SFXDESC'); ?></td>
					</tr>
				</table>
			</fieldset>
			</dd>
			<dt title="<?php echo JText::_('COM_KUNENA_MODNEWDESC'); ?>"><?php echo JText::_('COM_KUNENA_MODNEWDESC'); ?></dt>
			<dd>
			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_MODHEADER'); ?></legend>
				<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
					<tr>
						<td class="nowrap" valign="top"><?php echo JText::_('COM_KUNENA_MOD'); ?></td>
						<td valign="top"><?php echo $lists ['forumModerated']; ?></td>
						<td valign="top"><?php echo JText::_('COM_KUNENA_MODDESC'); ?></td>
					</tr>
				</table>

				<?php if ($row->moderated) : ?>

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

					<?php if (count ( $moderatorList ) == 0) : ?>
					<tr>
						<td colspan="5"><?php echo JText::_('COM_KUNENA_NOMODS') ?></td>
					</tr>
					<?php else :
						$k = 1;
						$i = 0;
						foreach ( $moderatorList as $ml ) : $k = 1 - $k; ?>
					<tr class="row<?php echo $k; ?>">
						<td width="20"><?php echo $i + 1; ?></td>
						<td width="20">
							<input type="checkbox" id="cb<?php echo $i; ?>" name="cid[]" value="<?php echo kescape($ml->id); ?>" onclick="isChecked(this.checked);" />
						</td>
						<td><?php echo kescape($ml->name); ?></td>
						<td><?php echo kescape($ml->username); ?></td>
						<td><?php echo kescape($ml->email); ?></td>
						<td align="center"><img src="images/tick.png" alt="" /></td>
					</tr>
						<?php 	$i ++;
						endforeach;
					endif;
					?>
				</table>
				<?php endif; ?>
			</fieldset>
			<?php endif; ?>
		</dd>
	</dl>
	<input type="hidden" name="id" value="<?php echo kescape($row->id); ?>" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="showAdministration" />
	<?php if ($row->ordering != 0) : ?>
	<input type="hidden" name="ordering" value="<?php echo kescape($row->ordering) ?>" />
	<?php endif; ?>
	<?php echo JHTML::_( 'form.token' ); ?>
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

		<dt title="<?php echo JText::_('COM_KUNENA_A_BASICS') ?>"><?php echo JText::_('COM_KUNENA_A_BASICS') ?></dt>
		<dd>
			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_A_BASIC_SETTINGS') ?></legend>

				<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
							<tr align="center" valign="middle">
						<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_A_BOARD_TITLE') ?>
						</td>
								<td align="left" valign="top" width="25%"><input type="text"
							name="cfg_board_title"
							value="<?php echo kescape ( $kunena_config->board_title );
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
							<textarea name="cfg_offline_message" rows="3" cols="50"><?php echo kescape ( $kunena_config->offline_message ); ?></textarea>
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
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_VERSION_CHECK') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['version_check'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_VERSION_CHECK_DESC') ?>
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
			<dt title="<?php echo JText::_('COM_KUNENA_A_FRONTEND') ?>"><?php echo JText::_('COM_KUNENA_A_FRONTEND') ?></dt>
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
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_PLUGINS_SUPPORT') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['jmambot'];
						?></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_PLUGINS_SUPPORT_DESC') ?>
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
								<td align="left" valign="top"><?php echo $lists ['avataroncat']; ?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_SHOW_AVATAR_ON_CAT_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_LISTCAT_SHOW_MODERATORS') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['listcat_show_moderators']; ?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_LISTCAT_SHOW_MODERATORS_DESC') ?>
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
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_RULESPAGE_CID') ?>
						</td>
								<td align="left" valign="top"><input type="text" name="cfg_rules_cid"
							value="<?php echo kescape($kunena_config->rules_cid);
						?>" /></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_RULESPAGE_CID_DESC') ?>
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
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_COM_A_ENABLELIGHTBOX') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['lightbox'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_COM_A_ENABLELIGHTBOX_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_COM_A_SHOW_TOPICS_FROM_LAST_TIME') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['show_list_time'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_COM_A_SHOW_TOPICS_FROM_LAST_TIME_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_COM_A_USER_SESSIONS_TYPE') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['show_session_type'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_COM_A_SESSIONS_TYPE_DESC') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_COM_A_USER_SESSIONS_START_TIME') ?>
						</td>
								<td align="left" valign="top"><input type="text"
							name="cfg_show_session_starttime"
							value="<?php echo kescape($kunena_config->show_session_starttime);
						?>" /></td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_COM_A_SESSIONS_START_TIME_DESC') ?>
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

			<dt title="<?php echo JText::_('COM_KUNENA_A_USERS') ?>"><?php echo JText::_('COM_KUNENA_A_USERS') ?></dt>
			<dd>
			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_A_USER_RELATED') ?></legend>
				<table cellpadding="4" cellspacing="0" border="0" width="100%" class="kadmin-adminform">
					<tr align="center" valign="middle">
						<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_A_DISPLAY_NAME') ?>
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
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_THANKYOU') ?></td>
								<td align="left" valign="top"><?php echo $lists ['showthankyou'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_THANKYOU_DESC') ?></td>
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
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_SHOWBANNEDREASON_PROFILE') ?></td>
								<td align="left" valign="top"><?php echo $lists ['showbannedreason'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_SHOWBANNEDREASON_PROFILE_DESC') ?>
						</td>
					</tr>
				</table>
			</fieldset>
			</dd>
			<dt title="<?php echo JText::_('COM_KUNENA_A_SECURITY') ?>"><?php echo JText::_('COM_KUNENA_A_SECURITY') ?></dt>
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
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_COM_A_MOD_SEE_DELETED') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['mod_see_deleted'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_COM_A_MOD_SEE_DELETED_DESC') ?>
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
			<dt title="<?php echo JText::_('COM_KUNENA_A_AVATARS') ?>"><?php echo JText::_('COM_KUNENA_A_AVATARS') ?></dt>
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
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_AVSIZE') ?></td>
								<td align="left" valign="top"><input type="text" name="cfg_avatarsize" class="ksm-field"
							value="<?php echo kescape($kunena_config->avatarsize);
						?>" /> kB</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_AVATAR_QUALITY') ?>
						</td>
						<td class="nowrap" align="left" valign="top"><input type="text"
							name="cfg_avatarquality" class="ksm-field"
							value="<?php echo kescape($kunena_config->avatarquality);
						?>" /> %</td>
					</tr>
				</table>
			</fieldset>
			</dd>
			<dt title="<?php echo JText::_('COM_KUNENA_A_UPLOADS') ?>"><?php echo JText::_('COM_KUNENA_A_UPLOADS') ?></dt>
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
								<td align="left" valign="top"><input type="text" name="cfg_imagesize" class="ksm-field"
							value="<?php echo kescape($kunena_config->imagesize);
						?>" /> kB</td>
						<td align="left" valign="top">
							<?php echo JText::sprintf('COM_KUNENA_A_IMGSIZE_DESC',
														ini_get('post_max_size'), ini_get('upload_max_filesize'),
														function_exists('php_ini_loaded_file') ? php_ini_loaded_file() : '') ?>
						</td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_IMGWIDTH') ?></td>
								<td align="left" valign="top"><input type="text" name="cfg_imagewidth" class="ksm-field"
							value="<?php echo kescape($kunena_config->imagewidth);
						?>" /> px</td>
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_IMGWIDTH_DESC') ?></td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_IMGHEIGHT') ?></td>
								<td align="left" valign="top"><input type="text"
							name="cfg_imageheight" class="ksm-field"
							value="<?php echo kescape($kunena_config->imageheight);
						?>" /> px</td>
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_IMGHEIGHT_DESC') ?></td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_IMGTHUMBWIDTH') ?></td>
								<td align="left" valign="top"><input type="text" name="cfg_thumbwidth" class="ksm-field"
							value="<?php echo kescape($kunena_config->thumbwidth);
						?>" /> px</td>
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_IMGTHUMBWIDTH_DESC') ?></td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_IMGTHUMBHEIGHT') ?></td>
								<td align="left" valign="top"><input type="text" class="ksm-field"
							name="cfg_thumbheight"
							value="<?php echo kescape($kunena_config->thumbheight);
						?>" /> px</td>
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_IMGTHUMBHEIGHT_DESC') ?></td>
					</tr>
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_IMGQUALITY') ?></td>
								<td align="left" valign="top"><input type="text" name="cfg_imagequality" class="ksm-field"
							value="<?php echo kescape($kunena_config->imagequality);
						?>" /> px</td>
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
						<td align="left" valign="top"><input type="text" name="cfg_filesize" class="ksm-field "
							value="<?php echo kescape($kunena_config->filesize);
						?>" /> <?php echo JText::_('COM_KUNENA_A_FILESIZE_KB') ?></td>
						<td align="left" valign="top">
							<?php echo JText::sprintf('COM_KUNENA_A_FILESIZE_DESC',
														ini_get('post_max_size'), ini_get('upload_max_filesize'),
														function_exists('php_ini_loaded_file') ? php_ini_loaded_file() : '') ?>
						</td>
					</tr>
				</table>
			</fieldset>
			</dd>
			<dt title="<?php echo JText::_('COM_KUNENA_A_RANKING') ?>"><?php echo JText::_('COM_KUNENA_A_RANKING') ?></dt>
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
			<dt title="<?php echo JText::_('COM_KUNENA_A_BBCODE') ?>"><?php echo JText::_('COM_KUNENA_A_BBCODE') ?></dt>
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
					<tr align="center" valign="middle">
						<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_COM_A_BBCODE_IMG_SECURE') ?>
						</td>
								<td align="left" valign="top"><?php echo $lists ['bbcode_img_secure'];
						?>
						</td>
								<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_COM_A_BBCODE_IMG_SECURE_DESC') ?>
						</td>
					</tr>
				</table>
			</fieldset>
			</dd>
			<dt title="<?php echo JText::_('COM_KUNENA_A_INTEGRATION') ?>"><?php echo JText::_('COM_KUNENA_A_INTEGRATION') ?></dt>
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
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_INTEGRATION_ACTIVITY_LIMIT') ?></td>
							<td align="left" valign="top"><input type="text" name="cfg_activity_limit" class="ksm-field"
								value="<?php echo kescape($kunena_config->activity_limit);?>" /></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_INTEGRATION_ACTIVITY_LIMIT_DESC') ?></td>
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
							<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_AUP_MINIMUM_POINTS_ON_REPLY'); ?></td>
							<td align="left" valign="top" width="25%"><input type="text"
								name="cfg_alphauserpointsnumchars"
								value="<?php echo kescape($kunena_config->alphauserpointsnumchars); ?>" /></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_AUP_MINIMUM_POINTS_ON_REPLY_DESC'); ?></td>
						</tr>
					</table>
				</fieldset>
				</dd>
				<dt title="<?php echo JText::_('COM_KUNENA_ADMIN_RSS') ?>"><?php echo JText::_('COM_KUNENA_ADMIN_RSS') ?></dt>
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
							<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_A_RSS_AUTHOR_IN_TITLE') ?></td>
							<td align="left" valign="top" width="25%"><?php echo $lists ['rss_author_in_title']; ?></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_RSS_AUTHOR_IN_TITLE_DESC') ?></td>
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
						<tr align="center" valign="middle">
							<td align="left" valign="top" width="25%"><?php echo JText::_('COM_KUNENA_A_RSS_CACHE') ?></td>
							<td align="left" valign="top" width="25%"><?php echo $lists ['rss_cache']; ?></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_A_RSS_CACHE_DESC') ?></td>
						</tr>
					</table>
				</fieldset>
				</dd>
				<dt title="<?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_PLUGINS') ?>"><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_PLUGINS') ?></dt>
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
						<tr align="center" valign="middle">
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_ALLOWED') ?></td>
							<td align="left" valign="top"><?php echo $lists ['userlist_allowed']; ?></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_ALLOWED_DESC') ?></td>
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
								name="cfg_poppollscount"
								value="<?php echo kescape($kunena_config->poppollscount);
							?>" /></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_POLLSPOP');
							?></td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_THANKSSTATS');
							?></td>
							<td align="left" valign="top"><?php echo $lists ['showpopthankyoustats'];
							?>
							</td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_THANKSSTATSDESC');
							?>
							</td>
						</tr>
						<tr align="center" valign="middle">
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_THANKSPOP');
							?></td>
							<td align="left" valign="top"><input type="text"
								name="cfg_popthankscount"
								value="<?php echo kescape($kunena_config->popthankscount);
							?>" /></td>
							<td align="left" valign="top"><?php echo JText::_('COM_KUNENA_THANKSPOP');
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
			function showCss($file, $option) {
				$f = fopen ( $file, "r" );
				$content = fread ( $f, filesize ( $file ) );
		?>
		<div class="kadmin-functitle icon-editcss"><?php echo JText::_('COM_KUNENA_CSSEDITOR'); ?></div>
		<form action="index.php?" method="post" name="adminForm" class="adminForm" id="adminForm">
			<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminform">
				<tr>
					<th colspan="4"><?php echo JText::_('COM_KUNENA_PATH'); ?> <?php echo kescape($file); ?></th>
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
					<?php echo JText::_( 'Filter' ); ?>:
					<input type="text" name="search" id="search" value="<?php echo kescape($lists['search']);?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
					<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
				</td>
			</tr>
		</table>
		<table class="adminlist" border="0" cellspacing="0" cellpadding="3" width="100%">
			<thead>
				<tr>
					<th align="center" width="5">#</th>
					<th align="center" width="5"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count ( $users ); ?>);" /></th>
					<th align="center"><?php echo JText::_('COM_KUNENA_USRL_AVATAR'); ?></th>
					<th class="title" align="center"><?php echo JHTML::_('grid.sort', JText::_('COM_KUNENA_ANN_ID'), 'id', $lists['order_Dir'], $lists['order'] ); ?></th>
					<th align="left"><?php echo JHTML::_('grid.sort', JText::_('COM_KUNENA_USRL_NAME'), 'username', $lists['order_Dir'], $lists['order'] ); ?></th>
					<th align="left"><?php echo JHTML::_('grid.sort', JText::_('COM_KUNENA_USRL_REALNAME'), 'name', $lists['order_Dir'], $lists['order'] ); ?></th>
					<th align="center"><?php echo JText::_('COM_KUNENA_USRL_LOGGEDIN'); ?></th>
					<th align="center"><?php echo JText::_('COM_KUNENA_USRL_ENABLED'); ?></th>
					<th align="center"><?php echo JText::_('COM_KUNENA_USRL_BANNED'); ?></th>
<?php /*
					<th align="left"><?php echo JText::_('COM_KUNENA_GEN_EMAIL'); ?></th>
					<th align="left"><?php echo JText::_('COM_KUNENA_GEN_USERGROUP'); ?></th>
*/ ?>
					<th align="left"><?php echo JHTML::_('grid.sort', JText::_('COM_KUNENA_VIEW_MODERATOR'), 'moderator', $lists['order_Dir'], $lists['order'] ); ?></th>
					<th align="left"><?php echo JText::_('COM_KUNENA_GEN_SIGNATURE'); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="14">
						<div class="pagination">
							<div class="limit"><?php echo JText::_('COM_KUNENA_A_DISPLAY'); ?> <?php echo $pageNav->getLimitBox (); ?></div>
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
						$userLogged = $kunena_user->isOnline() ? '<img src="images/tick.png" width="16" height="16" border="0" alt="" />': '';
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
				<th width="100%" class="user"><?php echo JText::_('COM_KUNENA_ADDMOD'); ?> <?php echo kescape($forumName); ?>
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
					?>
				<?php } else {
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
						?> <?php
						echo kescape($username);
						?>
						</th>
					</tr>
					<?php
					$enum = 1; //reset value
					$k = 0; //value for alternating rows

					if (!empty($subscatslist)) {
						foreach($subscatslist as $subscats) { //get all category details for each subscription
							$kunena_db->setQuery ( "select cat.name AS catname, cat.id, msg.subject, msg.id, msg.catid, msg.name AS username from #__kunena_categories AS cat INNER JOIN #__kunena_messages AS msg ON cat.id=msg.catid where cat.id='$subscats->catid' GROUP BY cat.id" );
							$catdetail = $kunena_db->loadObjectList ();
							if (KunenaError::checkDatabaseError()) break;

							foreach ( $catdetail as $cat ) {
								$k = 1 - $k;
								echo "<tr class=\"row$k\">";
								echo "  <td width=\"30\">$enum</td>";
								echo "  <td><strong>" . kescape ( $cat->catname ) ."</strong>" . JText::_('COM_KUNENA_LAST_MESSAGE') . "<em>".kescape ( $cat->subject )."</em>". JText::_('COM_KUNENA_BY') . "<em>".kescape ( $cat->username )."</em></td>";
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
						?> <?php
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
								echo "  <td><strong>" . kescape ( $sub->subject ) ."</strong>" . JText::_('COM_KUNENA_BY') . "<em>".kescape ( $sub->name )."</em></td>";
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
				<td><input type="text" name="prune_days" value="30" /><?php echo JText::_('COM_KUNENA_A_PRUNE_DAYS') ?></td>
			</tr>
		</table>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
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
				<?php echo JHTML::_( 'form.token' ); ?>
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
					<?php echo kescape(CKunenaTools::shortenFileName($filename, 10, 15)) ?>
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
		function showsmilies($option, &$smileytmp, $pageNav, $smileypath) {
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
							<div class="limit"><?php echo JText::_('COM_KUNENA_A_DISPLAY'); ?> <?php echo $pageNav->getLimitBox (); ?></div>
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
							<div class="limit"><?php echo JText::_('COM_KUNENA_A_DISPLAY'); ?> <?php echo $pageNav->getLimitBox (); ?></div>
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

//Start trash view
function showtrashview($option, $trashitems, $pageNav, $lists) {
			?>
		<div class="kadmin-functitle icon-trash"><?php echo JText::_('COM_KUNENA_TRASH_VIEW'); ?></div>
		<form action="index.php" method="post" name="adminForm" class="adminform">
			<table class="kadmin-sort">
				<tr>
					<td class="left" width="90%">
						<?php echo JText::_( 'Filter' ); ?>:
						<input type="text" name="search" id="search" value="<?php echo kescape($lists['search']);?>" class="text_area" onchange="document.adminForm.submit();" />
						<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
						<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
					</td>
				</tr>
			</table>
			<table class="adminlist" border="0" cellspacing="0" cellpadding="3" width="100%">
			<thead>
				<tr>
					<th width="5" align="center">#</th>
					<th width="5" align="left"><input type="checkbox" name="toggle" value=""
						onclick="checkAll(<?php
					echo count ( $trashitems );
					?>);" /></th>
					<th width="5" align="left"><?php
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
			</thead>
			<tfoot>
				<tr>
					<td colspan="9">
						<div class="pagination">
							<div class="limit"><?php echo JText::_('COM_KUNENA_A_DISPLAY'); ?> <?php echo $pageNav->getLimitBox (); ?></div>
							<?php echo $pageNav->getPagesLinks (); ?>
							<div class="limit"><?php echo $pageNav->getResultsCounter (); ?></div>
						</div>
					</td>
				</tr>
			</tfoot>
				<?php
					$k = 0;
					$i = 0;
					foreach ( $trashitems as $id => $row ) {
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
						echo kescape($row->id);
						?>"
						onclick="isChecked(this.checked);" /></td>
					<td >
						<?php
						echo kescape($row->id);
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
			</table>
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="task" value="showtrashview" />
			<input type="hidden" name="limitstart" value="0" />
			<input type="hidden" name="return" value="showtrashview" />
			<input type="hidden" name="filter_order" value="<?php echo kescape($lists['order']); ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo kescape($lists['order_Dir']); ?>" />
		</form>
		<?php
			}
			function trashpurge($option, $return, $cid, $items) {
		?>
		<div class="kadmin-functitle"><?php echo JText::_('COM_KUNENA_TRASH_PURGE'); ?></div>
		<form action="index.php" method="post" name="adminForm">
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
			<input type="hidden" name="return" value="<?php echo kescape($return);?>" />
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
		<form action="index.php" method="post" name="adminForm" class="adminform">
		<fieldset><?php echo JText::_('COM_KUNENA_REPORT_SYSTEM_DESC'); ?><br /></fieldset>
		<fieldset>
			<div><a href="#" id="link_sel_all" ><?php echo JText::_('COM_KUNENA_REPORT_SELECT_ALL'); ?></a></div>
			<textarea id="report_final" name="report_final" cols="80" rows="15"><?php echo kescape($report); ?></textarea>
		</fieldset>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="1" />
	</form>
<?php
	}//End report system
} //end class