<?php
/**
 * @version $Id: editavatar.php 2406 2010-05-04 06:16:56Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/
defined( '_JEXEC' ) or die();

$i=1;
?>

<h2><?php echo JText::_('Ban history for (this user)'); ?></h2>
<table border="0" cellpadding="5" class="<?php echo isset ( $this->objCatInfo->class_sfx ) ? ' kblocktable' . $this->objCatInfo->class_sfx : ''; ?>">
	<thead>
	<tr class="ksth">
		<th class="view-th ksectiontableheader" width="1%"> # </th>
		<th class="view-th ksectiontableheader" width="20%" style="text-align:center;white-space:nowrap;"><?php echo JText::_('Banned User'); ?></th>
		<th class="view-th ksectiontableheader" width="20%" style="text-align:center;white-space:nowrap;"><?php echo JText::_('Ban Level'); ?></th>
		<th class="view-th ksectiontableheader" width="200" style="text-align:center;white-space:nowrap;"><?php echo JText::_('Start Time'); ?></th>
		<th class="view-th ksectiontableheader" width="200" style="text-align:center;white-space:nowrap;"><?php echo JText::_('Expire Time'); ?></th>
		<th class="view-th ksectiontableheader" width="10%" style="text-align:center;white-space:nowrap;"><?php echo JText::_('IP'); ?></th>
		<!--<th class="view-th ksectiontableheader" width="5%">&nbsp;</th>
		<th class="view-th ksectiontableheader" width="5%">&nbsp;</th>
		<th class="view-th ksectiontableheader" width="5%">&nbsp;</th>-->
	</tr>
	</thead>
	<tbody>
	<?php
		//$kid = 0;
		//if (is_dir($this->path) && (count($this->userfiles) > 0)) {
		//foreach ($this->userfiles as $userfile) {
			//$this->size = '<b>'.filesize($this->usefilespath.$userfile).'</b> bytes';
	?>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td style="text-align:center;padding: 1 10px;" width="1%"><?php //echo $this->id; ?> 83 </td>
		<td style="white-space:nowrap;text-align:center;padding: 1 10px;"> dragan </td>
		<td style="white-space:nowrap;text-align:center;padding: 1 10px;"><span><?php //echo $this->size; ?> banned </span></td>
		<td style="white-space:nowrap;text-align:center;padding: 1 10px;"><span><?php //echo $this->size; ?> 25.03.2010 23:36 </span></td>
		<td style="white-space:nowrap;text-align:center;padding: 1 10px;"><span><?php //echo $this->size; ?> 01.05.2010 23:36 </span></td>
		<td style="white-space:nowrap;text-align:center;padding: 1 10px;"><span><?php //echo $this->size; ?> 192.168.0.1 </span></td>
		<!--<td style="text-align:center;padding: 1 10px;"><a href="<?php //echo $this->userfilesurl.$userfile;?>"><img class="downloadicon" src="<?php echo KUNENA_URLICONSPATH . 'banned_red.png';?>"  alt="<?php echo JText::_('COM_KUNENA_ATTACH'); ?>" title="<?php //echo JText::_('COM_KUNENA_ATTACH'); ?>" /></a></td>
		<td style="text-align:center;padding: 1 10px;"><a href="<?php //CKunenaAttachments::deleteFile($userfile);?>"><img class="deleteicon" src="<?php echo KUNENA_URLICONSPATH . 'edit.png';?>"  alt="<?php echo JText::_('COM_KUNENA_ATTACH'); ?>" title="<?php //echo JText::_('COM_KUNENA_ATTACH'); ?>" /></a></td>
		<td style="white-space:nowrap;text-align:center;padding: 1 10px;"><a href="<?php //CKunenaAttachments::deleteFile($userfile);?>"><img class="deleteicon" src="<?php echo KUNENA_URLICONSPATH . 'delete.png';?>"  alt="<?php echo JText::_('COM_KUNENA_ATTACH'); ?>" title="<?php //echo JText::_('COM_KUNENA_ATTACH'); ?>" /></a></td>-->
	</tr>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td style="text-align:left;" width="100%" colspan="9"><b><?php echo JText::_('Created by'); ?></b> : Ilinka  -  25.03.2010 23:36  | <b>Modified by</b> : admin  -  26.03.2010 19:15</td>
	</tr>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td style="text-align:left;" width="100%" colspan="9"><b><?php echo JText::_('Public Reason'); ?></b> : Violation of Forum Rules N° 5</td>
	</tr>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td style="text-align:left;" width="100%" colspan="9"><b><?php echo JText::_('Private Reason'); ?></b> : Violation of Forum Rules N° 25</td>
	</tr>
			<?php
				//$kid++;
			?>
<?php //} 
//} 
//else { ?>
	<!--<tr class="ksectiontableentry<?php //echo ($i^=1)+1;?>">
		<td style="text-align:center;" width="100%" colspan="9"><?php echo JText::_('Actually No Banned Users'); ?></td>
	</tr>-->
<?php //} ?>
</tbody>
</table>
