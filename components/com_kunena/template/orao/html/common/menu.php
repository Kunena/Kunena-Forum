<?php
/**
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
// TODO : To finish
$template = KunenaFactory::getTemplate();
$this->params = $template->params;
$this->my = JFactory::getUser ();
$private = KunenaFactory::getPrivateMessaging();
$pm = $private->getUnreadCount($this->my->id);
?>
<?php if (JDocumentHTML::countModules ( 'kunena_menu' )) : ?>

<div class="forumlist">
	<div class="catinner">
		<span class="corners-top"><span></span></span>
			<div id="ktop">
			<ul class="topiclist">
				<li class="header" style="height:32px;padding: 2px 0 0 10px;">
					<dl class="icon" style="background: none;">
						<dt style="width:1px;padding: 0px;">

						</dt>
						<?php if ($this->params->get('topmenuShow') == '1') { ?>
						<dd class="topics" style="width:60%; text-align:left;padding-left: 5px;">
							<div id="ktopmenu">
								<div id="ktab"><?php echo $this->getMenu() ?></div>
							</div>
						</dd>
						<?php } else { ?>
						<dd class="topics" style="width:60%; text-align:left;margin:10px 0 0 10px;padding-left: 5px;">
							<?php if ($this->my->id == 0) : ?>
								<?php echo JText::_('COM_KUNENA_PROFILEBOX_WELCOME'); ?>, <b><?php echo JText::_('COM_KUNENA_PROFILEBOX_GUEST'); ?></b>
							<?php else : ?>
								<?php echo JText::_('COM_KUNENA_PROFILEBOX_WELCOME'); ?>, <b><?php echo $this->me->getLink() ?></b>
							<?php endif;?>
						</dd>
						<?php } ?>
						<?php if ($this->my->id == 0) : ?>
						<dd class="tk-topmenu-links" style="float: right;">
						<?php //if ($login) : ?>
						<?php if ($this->params->get('loginLogout') == '1') : ?>
							<span id="tk-login" class="" title="Members Login:: ">
								<a class="tk-loginlink" style="color:#fff;" href="#mb_login" rel="lightbox[inline 360 180]"><?php echo JText::_('COM_KUNENA_PROFILEBOX_LOGIN'); ?></a>
							</span>
						<?php endif ?>
						<?php //if ($this->register) : ?>
							<span id="tk-register-rules-title" class="tk-registerlink handcursor" title="Members Registration:: ">
								<a class="tk-registerlink" style="color:#fff; cursor:pointer;"><?php echo JText::_('COM_KUNENA_PROFILEBOX_REGISTER'); ?></a>
							</span>
						<?php //endif ?>
						<?php //endif;?>
						<?php if ($this->params->get('searchLink') == '1') : ?>
							<span id="tk-search" class="tk-searchlink" title="Search in Forum:: ">
								<a class="tk-searchlink" style="color:#fff;" href="#mb_search" rel="lightbox[contact 250 80]"><?php echo JText::_('COM_KUNENA_SEARCH_SEND'); ?></a>
							</span>
						<?php endif;?>
						</dd>
						<?php else:?>
						<dd class="tk-topmenu-links" style="float: right;">
							<?php if ($this->params->get('loginLogout') == '1') : ?>
							<span id="tk-logout" class="" title="Members Logout:: ">
								<a class="tk-logoutlink" style="color:#fff;" href="#mb_logout" rel="lightbox[inline 360 100]"><?php echo JText::_('COM_KUNENA_PROFILEBOX_LOGOUT'); ?></a>
							</span>
							<?php endif ?>
							<?php //if (!empty($this->announcementsLink)) : ?>
							<span id="tk-ann" class="" title="Announcment:: ">
								<a class="tk-logoutlink" style="color:#fff;" href="index.php?option=com_kunena&view=announcement&layout=list"><?php echo JText::_('Ann'); ?></a>
							</span>
							<?php //endif ?>
							<?php if ($this->params->get('searchLink') == '1') : ?>
							<span id="tk-search" class="tk-searchlink" title="Search in Forum:: ">
								<a class="tk-searchlink" style="color:#fff;" href="#mb_search" rel="lightbox[contact 250 80]"><?php echo JText::_('COM_KUNENA_SEARCH_SEND'); ?></a>
							</span>
							<?php endif; ?>
							<?php //if (!empty($this->privateMessagesLink)) : ?>
							<span class="tk-pmlink <?php if ($pm > 0): echo 'tk-pm-new'; else : echo 'tk-pm-nonew'; endif;?>">
								<a class="" style="color:#fff;" href="#mb_pmread" rel="lightbox[pm-read 350 90]"><?php echo JText::_('COM_KUNENA_TEMPLATE_PRIVATE_MESSAGE'); ?></a>
							</span>
							<?php //endif; ?>
						</dd>
						<?php endif;?>

					</dl>
				</li>
			</ul>
			<div id="ktab-flat" class="tk-menu-flat">
				<?php $this->getModulePosition( 'kunena_flatmenu' ) ?>
			</div>
			</div>
		<span class="corners-bottom"><span></span></span>
	</div>
</div>

<?php endif; ?>
<?php include dirname ( __FILE__ ) . '/pmread.php';?>
<?php include dirname ( __FILE__ ) . '/quicksearch.php';?>
