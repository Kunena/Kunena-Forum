<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Categories
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/** @var KunenaAdminViewCategories $this */

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
//JHtml::_('formbehavior.chosen', 'select');

if (version_compare(JVERSION, '3.2', '>'))
{
	JHtml::_('behavior.tabstate');
}
?>

<div id="kunena" class="admin override">
	<div id="j-sidebar-container" class="span2">
		<div id="sidebar">
			<div class="sidebar-nav"><?php include KPATH_ADMIN.'/template/joomla30/common/menu.php'; ?></div>
		</div>
	</div>
	<div id="j-main-container" class="span10">
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=categories') ?>" method="post" id="adminForm" name="adminForm">
			<input type="hidden" name="task" value="save" />
			<input type="hidden" name="catid" value="<?php echo intval($this->category->id); ?>" />
			<?php echo JHtml::_( 'form.token' ); ?>

			<article class="data-block">
				<div class="data-container">
					<ul class="nav nav-tabs">
						<li class="active"><a href="#tab1" data-toggle="tab"><?php echo JText::_('COM_KUNENA_BASICSFORUMINFO'); ?></a></li>
						<li><a href="#tab2" data-toggle="tab"><?php echo JText::_('COM_KUNENA_CATEGORY_PERMISSIONS'); ?></a></li>
						<?php if (!$this->category->id || !$this->category->isSection()) : ?>
							<li><a href="#tab3" data-toggle="tab"><?php echo JText::_('COM_KUNENA_ADVANCEDDESCINFO'); ?></a></li>
							<li><a href="#tab4" data-toggle="tab"><?php echo JText::_('COM_KUNENA_MODHEADER'); ?></a></li>
						<?php endif; ?>
					</ul>
					<div class="tab-content" style="padding-bottom: 9px; border-bottom: 1px solid #ddd;">
						<div class="tab-pane active" id="tab1">
							<fieldset>
								<table class="table table-striped">
									<tr>
										<td><?php echo JText::_('COM_KUNENA_PARENT'); ?></td>
										<td>
											<?php echo $this->options ['categories']; ?>
											<p><?php echo JText::_('COM_KUNENA_PARENTDESC'); ?></p>
										</td>
									</tr>
									<tr>
										<td><?php echo JText::_('COM_KUNENA_NAMEADD'); ?></td>
										<td>
											<input class="inputbox" type="text" name="name" size="80" value="<?php echo $this->escape ( $this->category->name ); ?>" />
										</td>
									</tr>
									<tr>
										<td><?php echo JText::_('COM_KUNENA_A_CATEGORY_ALIAS'); ?></td>
										<td>
											<input class="inputbox" type="text" name="alias" size="80" value="<?php echo $this->escape ( $this->category->alias ); ?>" />
										</td>
									</tr>
									<?php if ($this->options ['aliases']) : ?>
										<tr>
											<td></td>
											<td>
												<?php echo $this->options ['aliases']; ?>
											</td>
										</tr>
									<?php endif ?>
									<tr>
										<td><?php echo JText::_('COM_KUNENA_PUBLISHED'); ?>:</td>
										<td><?php echo $this->options ['published']; ?></td>
									</tr>
									<tr>
										<td><?php echo JText::_('COM_KUNENA_DESCRIPTIONADD'); ?></td>
										<td>
											<textarea class="inputbox" cols="50" rows="6" name="description" id="description" style="width: 500px"><?php echo $this->escape ( $this->category->description ); ?></textarea>
										</td>
									</tr>
									<tr>
										<td><?php echo JText::_('COM_KUNENA_HEADERADD'); ?></td>
										<td>
											<textarea class="inputbox" cols="50" rows="6" name="headerdesc" id="headerdesc" style="width: 500px"><?php echo $this->escape ( $this->category->headerdesc ); ?></textarea>
										</td>
									</tr>
									<tr>
										<td><?php echo JText::_('COM_KUNENA_CLASS_SFX'); ?></td>
										<td>
											<input class="inputbox" type="text" name="class_sfx" size="20" maxlength="20" value="<?php echo $this->escape ( $this->category->class_sfx ); ?>" />
											<p><?php echo JText::_('COM_KUNENA_CLASS_SFXDESC'); ?></p>
										</td>
									</tr>
								</table>
							</fieldset>
						</div>

						<div class="tab-pane" id="tab2">
							<fieldset>
								<table class="table table-striped">
									<thead>
									<tr>
										<td><?php echo JText::_('COM_KUNENA_A_ACCESSTYPE_TITLE'); ?></td>
										<td><?php echo $this->options ['accesstypes']; ?></td>
										<td><?php echo JText::_('COM_KUNENA_A_ACCESSTYPE_DESC'); ?></td>
									</tr>
									</thead>
									<?php foreach ($this->options ['accesslists'] as $accesstype=>$accesslist) foreach ($accesslist as $accessinput) : ?>
										<tr class="kaccess kaccess-<?php echo $accesstype ?>" style="<?php echo $this->category->accesstype != $accesstype ? 'display:none' : '' ?>">
											<td><?php echo $accessinput['title'] ?></td>
											<td><?php echo $accessinput['input'] ?></td>
											<td><?php echo $accessinput['desc'] ?></td>
										</tr>
									<?php endforeach; ?>
								</table>
							</fieldset>
						</div>

						<?php if (!$this->category->id || !$this->category->isSection()) : ?>
							<div class="tab-pane" id="tab3">
								<fieldset>
									<table class="table table-striped">
										<tr>
											<td><?php echo JText::_('COM_KUNENA_LOCKED1'); ?></td>
											<td><?php echo $this->options ['forumLocked']; ?></td>
											<td><?php echo JText::_('COM_KUNENA_LOCKEDDESC'); ?></td>
										</tr>
										<tr>
											<td><?php echo JText::_('COM_KUNENA_REV'); ?></td>
											<td><?php echo $this->options ['forumReview']; ?></td>
											<td><?php echo JText::_('COM_KUNENA_REVDESC'); ?></td>
										</tr>
										<tr>
											<td><?php echo JText::_('COM_KUNENA_CATEGORY_ANONYMOUS_ALLOW'); ?>:</td>
											<td><?php echo $this->options ['allow_anonymous']; ?></td>
											<td><?php echo JText::_('COM_KUNENA_CATEGORY_ANONYMOUS_ALLOW_DESC'); ?></td>
										</tr>
										<tr>
											<td><?php echo JText::_('COM_KUNENA_CATEGORY_ANONYMOUS_DEFAULT'); ?>:</td>
											<td><?php echo $this->options ['post_anonymous']; ?></td>
											<td><?php echo JText::_('COM_KUNENA_CATEGORY_ANONYMOUS_DEFAULT_DESC'); ?></td>
										</tr>
										<tr>
											<td><?php echo JText::_('COM_KUNENA_A_POLL_CATEGORIES_ALLOWED'); ?>:</td>
											<td><?php echo $this->options ['allow_polls']; ?></td>
											<td><?php echo JText::_('COM_KUNENA_A_POLL_CATEGORIES_ALLOWED_DESC'); ?></td>
										</tr>
										<?php /*
									<tr>
										<td><?php echo JText::_('COM_KUNENA_CATEGORY_CHANNELS'); ?>:</td>
										<td><?php echo $this->options ['channels']; ?></td>
										<td><?php echo JText::_('COM_KUNENA_CATEGORY_CHANNELS_DESC'); ?></td>
									</tr>
									<tr>
										<td><?php echo JText::_('COM_KUNENA_CATEGORY_TOPIC_ORDERING'); ?>:</td>
										<td><?php echo $this->options ['topic_ordering']; ?></td>
										<td><?php echo JText::_('COM_KUNENA_CATEGORY_TOPIC_ORDERING_DESC'); ?></td>
									</tr>
									<tr>
										<td><?php echo JText::_('COM_KUNENA_A_CATEGORY_TOPICICONSET'); ?>:</td>
										<td><?php echo $this->options ['category_iconset']; ?></td>
										<td><?php echo JText::_('COM_KUNENA_A_POLL_CATEGORY_TOPICICONSET_DESC'); ?></td>
									</tr>
	*/ ?>
									</table>
								</fieldset>
							</div>
							<div class="tab-pane" id="tab4">
								<fieldset>
									<legend><?php echo JText::_('COM_KUNENA_MODSASSIGNED'); ?></legend>

									<table class="table table-bordered table-striped">
										<thead>
										<tr>
											<th><?php echo JText::_('COM_KUNENA_USERNAME'); ?></th>
											<th><?php echo JText::_('COM_KUNENA_USRL_REALNAME'); ?></th>
											<th class="span1"><?php echo JText::_('JGRID_HEADING_ID'); ?></th>
										</tr>
										</thead>

										<tbody>
										<?php $i=0; if (empty($this->moderators)) : ?>
											<tr>
												<td colspan="5" align="center"><?php echo JText::_('COM_KUNENA_NOMODS') ?></td>
											</tr>
										<?php else : foreach ( $this->moderators as $ml ) : ?>
											<tr>
												<td><?php echo $this->escape($ml->username); ?></td>
												<td><?php echo $this->escape($ml->name); ?></td>
												<td><?php echo $this->escape($ml->userid); ?></td>
											</tr>
										<?php endforeach; endif; ?>
										</tbody>
									</table>
								</fieldset>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</article>
		</form>
	</div>
	<div class="pull-right small">
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
	</div>
</div>
