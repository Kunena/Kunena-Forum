<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Templates
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
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
	<div><?php include KPATH_ADMIN.'/template/joomla30/common/menu.php'; ?></div>
		</div>
		<!-- Right side -->
			<div class="span10">	
             <div class="well well-small" style="min-height:120px;">
                       <div class="nav-header"><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_EDIT_TEMPLATE'); ?> - <?php echo JText::_($this->details->name);  ?></div>
                         <div class="row-striped">
                         <br />
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena') ?>" method="post" id="adminForm" name="adminForm">
		<input type="hidden" name="view" value="templates" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="templatename" value="<?php echo $this->escape($this->templatename); ?>">
		<?php echo JHtml::_( 'form.token' ); ?>

		<p class="pull-left">
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_DETAILS' ); ?></legend>
				<table class="admintable">
				<tr>
					<td colspan="2" class="key" style="text-align:left; padding: 10px 0 0 10px;"><h1><?php echo JText::_($this->details->name); ?></h1></td>
				</tr>
				<tr>
					<td valign="top" class="key"><?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_AUTHOR' ); ?>:</td>
					<td><strong><?php echo JText::_($this->details->author); ?></strong></td>
				</tr>
				<tr>
					<td valign="top" class="key"><?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_DESCRIPTION' ); ?>:</td>
					<td><?php $path = KPATH_SITE.'/template/'.$this->templatename. '/images/template_thumbnail.png';
						if (file_exists ( $path )) : ?>
						<div class="tpl-thumbnail"><img src ="<?php echo JUri::root(true); ?>/components/com_kunena/template/<?php echo $this->escape($this->templatename); ?>/images/template_thumbnail.png" alt="" /></div>
						<?php endif; ?>
						<div class="tpl-desc"><?php echo JText::_($this->details->description); ?></div>
					</td>
				</tr>
				</table>
			</fieldset>
		</p>
		<p class="pull-right">
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_PARAMETERS' ); ?></legend>
				<table class="admintable">
				<tr>
					<td colspan="2" class="key" style="text-align:left; padding: 10px">
						<?php $templatefile = KPATH_SITE.'/template/'.$this->templatename.'/params.ini';
							echo is_writable($templatefile) ? JText::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_PARAMSWRITABLE', $this->escape($templatefile)):JText::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_PARAMSUNWRITABLE', $this->escape($templatefile));
						?>
					</td>
				</tr>
				<tr>
					<td class="kparameters">
						<?php if ($this->form instanceof JForm) : ?>
						<table class="paramlist admintable">
							<?php foreach ($this->params->toArray() as $item): ?>
							<tr>
								<?php if ($item[0]) : ?>
								<td width="40%" class="paramlist_key">
									<?php echo $item[0] ?>
								</td>
								<td class="paramlist_value">
									<?php echo $item[1] ?>
								</td>
								<?php else : ?>
								<td class="paramlist_value" colspan="2">
									<?php echo $item[1] ?>
								</td>
								<?php endif ?>
							</tr>
							<?php endforeach ?>
						</table>
						<?php
							else :
								echo '<em>' . JText :: _('COM_KUNENA_A_TEMPLATE_MANAGER_NO_PARAMETERS') . '</em>';
							endif;
						?>
					</td>
				</tr>
				</table>
			</fieldset>
		</p>
		</form>
		</div>
        </div>
	<div class="kadmin-footer center">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
	</div>