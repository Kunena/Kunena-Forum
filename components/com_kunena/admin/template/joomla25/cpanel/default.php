<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage CPanel
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/** @var KunenaAdminViewCpanel $this */
?>
<div id="kunena" class="admin override">
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span12">
				<div id="j-sidebar-container" class="span2">
					<div id="sidebar">
						<div class="sidebar-nav"><?php include KPATH_ADMIN.'/template/joomla25/common/menu.php'; ?></div>
					</div>
				</div>
				<div id="j-main-container" class="span10">
					<div class="row-fluid">
						<div class="span7">
							<div class="well well-small">
								<div class="module-title nav-header"><?php echo JText::_('COM_KUNENA_CPANEL_DESC_WELCOME') ?></div>
								<hr class="hr-condensed">
								<div id="dashboard-icons" class="btn-group">
									<a class="btn" href="index.php?option=com_kunena&view=categories">
										<img src="components/com_kunena/media/icons/large/categories.png" alt="<?php echo JText::_('COM_KUNENA_CPANEL_LABEL_CATEGORIES') ?>" /><br />
										<span><?php echo JText::_('COM_KUNENA_CPANEL_LABEL_CATEGORIES') ?></span>
									</a>
									<a class="btn" href="index.php?option=com_kunena&view=users">
										<img src="components/com_kunena/media/icons/large/users.png" alt="<?php echo JText::_('COM_KUNENA_CPANEL_LABEL_USERS') ?>" /><br />
										<span><?php echo JText::_('COM_KUNENA_CPANEL_LABEL_USERS') ?></span>
									</a>
									<a class="btn" href="index.php?option=com_kunena&view=attachments">
										<img src="components/com_kunena/media/icons/large/files.png" alt="<?php echo JText::_('COM_KUNENA_CPANEL_LABEL_FILES') ?>" /><br />
										<span><?php echo JText::_('COM_KUNENA_CPANEL_LABEL_FILES') ?></span>
									</a>
									<a class="btn" href="index.php?option=com_kunena&view=smilies">
										<img src="components/com_kunena/media/icons/large/smileys.png" alt="<?php echo JText::_('COM_KUNENA_CPANEL_LABEL_EMOTICONS') ?>" /><br />
										<span><?php echo JText::_('COM_KUNENA_CPANEL_LABEL_EMOTICONS') ?></span>
									</a>
									<a class="btn" href="index.php?option=com_kunena&view=ranks">
										<img src="components/com_kunena/media/icons/large/ranks.png" alt="<?php echo JText::_('COM_KUNENA_CPANEL_LABEL_RANKS') ?>" /><br />
										<span><?php echo JText::_('COM_KUNENA_CPANEL_LABEL_RANKS') ?></span>
									</a>
									<a class="btn" href="index.php?option=com_kunena&view=templates">
										<img src="components/com_kunena/media/icons/large/templates.png" alt="<?php echo JText::_('COM_KUNENA_CPANEL_LABEL_TEMPLATES') ?>" /><br />
										<span><?php echo JText::_('COM_KUNENA_CPANEL_LABEL_TEMPLATES') ?></span>
									</a>
									<a class="btn" href="index.php?option=com_kunena&view=config">
										<img src="components/com_kunena/media/icons/large/prune.png" alt="<?php echo JText::_('COM_KUNENA_CPANEL_LABEL_CONFIG') ?>" /><br />
										<span><?php echo JText::_('COM_KUNENA_CPANEL_LABEL_CONFIG') ?></span>
									</a>
									<a class="btn" href="index.php?option=com_kunena&view=plugins">
										<img src="components/com_kunena/media/icons/large/pluginsmanager.png" alt="<?php echo JText::_('COM_KUNENA_CPANEL_LABEL_PLUGINS') ?>" /><br/>
										<span><?php echo JText::_('COM_KUNENA_CPANEL_LABEL_PLUGINS') ?></span>
									</a>
									<a class="btn" href="index.php?option=com_kunena&view=tools">
										<img src="components/com_kunena/media/icons/large/config.png" alt="<?php echo JText::_('COM_KUNENA_CPANEL_LABEL_TOOLS') ?>" /><br/>
										<span><?php echo JText::_('COM_KUNENA_CPANEL_LABEL_TOOLS') ?></span>
									</a>
									<a class="btn" href="index.php?option=com_kunena&view=trash">
										<img src="components/com_kunena/media/icons/large/trash.png" alt="<?php echo JText::_('COM_KUNENA_CPANEL_LABEL_TRASH') ?>" /><br/>
										<span><?php echo JText::_('COM_KUNENA_CPANEL_LABEL_TRASH') ?></span>
									</a>
								</div>
							</div>
						</div>
						<div class="span5">
							<div class="well well-small">
								<div class="center">
									<img src="components/com_kunena/media/icons/kunena_logo.png" />
								</div>
								<hr class="hr-condensed">
								<dl class="dl-horizontal">
									<dt><?php echo JText::_('COM_KUNENA_CPANEL_LABEL_VERSION') ?>:</dt>
									<dd><?php echo KunenaForum::version(); ?></dd>
									<dt><?php echo JText::_('COM_KUNENA_CPANEL_LABEL_CODENAME') ?>:</dt>
									<dd><?php echo KunenaForum::versionName(); ?></dd>
									<dt><?php echo JText::_('COM_KUNENA_CPANEL_LABEL_DATE') ?>:</dt>
									<dd><?php echo KunenaForum::versionDate(); ?></dd>
									<dt><?php echo JText::_('COM_KUNENA_CPANEL_LABEL_AUTHOR') ?>:</dt>
									<dd><a href="http://www.kunena.org/team" target="_blank">Kunena Team</a></dd>
									<dt><?php echo JText::_('COM_KUNENA_CPANEL_LABEL_COPYRIGHT') ?>:</dt>
									<dd>&copy; 2008 - 2014 Kunena, All rights reserved.</dd>
									<dt><?php echo JText::_('COM_KUNENA_CPANEL_LABEL_LABEL_LICENSE') ?>:</dt>
									<dd>GNU General Public License</dd>
								</dl>
							</div>
						</div>
					</div>
				</div>
				<div class="pull-right small">
					<?php echo KunenaVersion::getLongVersionHTML (); ?>
				</div>
			</div>
		</div>
	</div>
</div>
