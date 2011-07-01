<?php
/**
 * @version $Id: default.php 4416 2011-02-16 08:43:29Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$document = JFactory::getDocument();
$document->addStyleSheet ( JURI::base(true).'/components/com_kunena/media/css/admin.css' );
$document->addScriptDeclaration('function update_rank(newimage)
			{
				document.rank_image.src = "'.
				$this->escape(KURL_SITE . $this->rankpath).'" + newimage;
			}');
?>
<div id="kadmin">
	<div class="kadmin-left"><?php include KPATH_ADMIN.'/views/common/tmpl/menu.php'; ?></div>
	<div class="kadmin-right">
	<div class="kadmin-functitle icon-ranks"><?php if ( !$this->state->get('item.id') ): ?><?php echo JText::_('COM_KUNENA_NEW_RANK'); ?><?php else: ?><?php echo JText::_('COM_KUNENA_RANKS_EDIT'); ?><?php endif; ?></div>
		<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" name="adminForm">
			<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminform">

				<tr align="center">
					<td width="100"><?php
					echo JText::_('COM_KUNENA_RANKS');
					?></td>
					<td width="200"><input class="post" type="text" name="rank_title"
						value="<?php echo isset($this->rank_selected) ? $this->rank_selected->rank_title : '' ?>" /></td>
				</tr>
				<tr>
					<td width="100"><?php
					echo JText::_('COM_KUNENA_RANKSIMAGE');
					?></td>
					<td><?php echo $this->listranks?> &nbsp;
					<?php if ( !$this->state->get('item.id') ): ?><img name="rank_image" src="" border="0" alt="" />
					<?php else: ?><img name="rank_image" src="<?php echo $this->escape(KURL_SITE .$this->template->getRankPath( $this->rank_selected->rank_image)); ?>" border="0" alt="" /><?php endif; ?>
					</td>
				</tr>
				<tr>
					<td width="100"><?php
					echo JText::_('COM_KUNENA_RANKSMIN');
					?></td>
					<td><input class="post" type="text" name="rank_min" value="<?php echo isset($this->rank_selected) ? $this->rank_selected->rank_min : '1' ?>" /></td>
				</tr>
				<tr>
					<td width="100"><?php
					echo JText::_('COM_KUNENA_RANKS_SPECIAL');
					?></td>
					<td><input type="checkbox" <?php echo isset($this->rank_selected) && $this->rank_selected->rank_special ? 'checked="checked"' : '' ?> name="rank_special" value="1" /></td>
				</tr>
				<tr>
					<td colspan="2" align="center"> </td>
				</tr>
			</table>
			<input type="hidden" name="option" value="com_kunena" />
			<input type="hidden" name="view" value="ranks" />
			<input type="hidden" name="task" value="save" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php if ( $this->state->get('item.id') ): ?><input type="hidden" name="rankid" value="<?php echo $this->state->get('item.id') ?>" /><?php endif; ?>
			<?php echo JHTML::_( 'form.token' ); ?>
		</form>
	</div>
	<div class="kadmin-footer">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
</div>