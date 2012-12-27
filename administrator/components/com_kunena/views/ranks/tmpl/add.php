<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Ranks
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$document = JFactory::getDocument();
$document->addScriptDeclaration('function update_rank(newimage)
			{
				document.rank_image.src = "'.
				$this->escape(JUri::root() . $this->rankpath).'" + newimage;
			}');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

$changeOrder 	= ($this->state->get('list.ordering') == 'ordering' && $this->state->get('list.direction') == 'asc');

?>
<!-- Main page container -->
<div class="container-fluid">
<div class="row-fluid">
 <div class="span2">
	<div><?php include KPATH_ADMIN.'/views/common/tmpl/menu.php'; ?></div>
		</div>
		<!-- Right side -->
			<div class="span10">	
             <div class="well well-small" style="min-height:120px;">
                       <div class="nav-header"><?php if ( !$this->state->get('item.id') ): ?><?php echo JText::_('COM_KUNENA_NEW_RANK'); ?><?php else: ?><?php echo JText::_('COM_KUNENA_RANKS_EDIT'); ?><?php endif; ?></div>
                         <div class="row-striped">
                         <br />
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena') ?>" method="post" id="adminForm" name="adminForm">
			<input type="hidden" name="view" value="ranks" />
			<input type="hidden" name="task" value="save" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php if ( $this->state->get('item.id') ): ?><input type="hidden" name="rankid" value="<?php echo $this->state->get('item.id') ?>" /><?php endif; ?>
			<?php echo JHtml::_( 'form.token' ); ?>

			<table class="adminform">

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
					<?php else: ?><img name="rank_image" src="<?php echo $this->escape(JUri::root(true).'/'.$this->ktemplate->getRankPath( $this->rank_selected->rank_image)); ?>" border="0" alt="" /><?php endif; ?>
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
		</form>
	</div>
    </div>
	<div class="kadmin-footer">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
</div>