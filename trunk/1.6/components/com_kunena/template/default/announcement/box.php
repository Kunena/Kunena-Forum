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

// Dont allow direct linking
defined( '_JEXEC' ) or die();
?>
<!-- ANNOUNCEMENTS BOX -->
<div class="k_bt_cvr1">
<div class="k_bt_cvr2">
<div class="k_bt_cvr3">
<div class="k_bt_cvr4">
<div class="k_bt_cvr5">
	<table class = "kblocktable" id = "kannouncement" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
		<thead>
			<tr>
				<th align="left">
					<div class = "ktitle_cover km">
						<span class = "ktitle kl"><?php echo $this->title; ?></span>
					</div>
					<div class="fltrt">
						<span id="ann_status"><a class="ktoggler close" rel="announcement"></a></span>
					</div>
				</th>
			</tr>
		</thead>

		<tbody id = "announcement">
			<?php if ($this->canEdit) : ?>
			<tr class = "ksth">
				<th class = "th-1 ksectiontableheader km" align="left">
					<?php echo CKunenaLink::GetAnnouncementLink( 'edit', $this->id, JText::_('COM_KUNENA_ANN_EDIT'), JText::_('COM_KUNENA_ANN_EDIT')); ?> |
					<?php echo CKunenaLink::GetAnnouncementLink( 'delete', $this->id, JText::_('COM_KUNENA_ANN_DELETE'), JText::_('COM_KUNENA_ANN_DELETE')); ?> |
					<?php echo CKunenaLink::GetAnnouncementLink( 'add',NULL, JText::_('COM_KUNENA_ANN_ADD'), JText::_('COM_KUNENA_ANN_ADD')); ?> |
					<?php echo CKunenaLink::GetAnnouncementLink( 'show', NULL, JText::_('COM_KUNENA_ANN_CPANEL'), JText::_('COM_KUNENA_ANN_CPANEL')); ?>
				</th>
			</tr>
			<?php endif; ?>

			<tr class = "ksectiontableentry2">
				<td class = "td-1 km" align="left">
					<?php if ($this->showdate > 0) : ?>
					<div class = "anncreated"><?php echo CKunenaTimeformat::showDate($this->created, 'date_today'); ?></div>
					<?php endif; ?>

					<div class = "anndesc"><?php echo $this->sdescription; ?>
					<?php if (!empty($this->description)) : ?>
					&nbsp; <?php echo CKunenaLink::GetAnnouncementLink( 'read', $this->id, JText::_('COM_KUNENA_ANN_READMORE'), JText::_('COM_KUNENA_ANN_READMORE'),'follow'); ?>
					<?php endif; ?>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
</div>
</div>
</div>
</div>
</div>
<!-- / ANNOUNCEMENTS BOX -->