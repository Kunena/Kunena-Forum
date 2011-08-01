<?php
/**
 * @version $Id$
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
		$q = JRequest::getVar ( 'q', '' ); // Search words
		$searchuser = JRequest::getVar ( 'searchuser', '' ); // Search user
		// Backwards compability for old templates
		if (empty ( $q ) && isset ( $_REQUEST ['searchword'] )) {
			$q = JRequest::getVar ( 'searchword', '' );
		}
		if (empty ( $searchuser ) && isset ( $_REQUEST ['searchword'] )) {
			$searchuser = JRequest::getVar ( 'searchword', '' );
		}
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
						<dd class="topics" style="width:65%; text-align:left;padding-left: 5px;">
							<div id="ktopmenu">
								<div id="ktab"><?php echo $this->getMenu() ?></div>
							</div>
						</dd>
						<?php } else { ?>
						<dd class="topics" style="width:65%; text-align:left;margin:10px 0 0 10px;padding-left: 5px;">
							<?php if ($this->my->id == 0) : ?>
								<?php echo JText::_('COM_KUNENA_PROFILEBOX_WELCOME'); ?>, <b><?php echo JText::_('COM_KUNENA_PROFILEBOX_GUEST'); ?></b>
							<?php else : ?>
								<?php echo JText::_('COM_KUNENA_PROFILEBOX_WELCOME'); ?>, <b><?php echo CKunenaLink::GetProfileLink ( intval($this->my->id), $this->escape($this->my->name) ); ;?></b>
							<?php endif;?>
						</dd>
						<?php } ?>
						<?php if ($this->my->id == 0) : ?>
						<dd style="float: right;">
						<?php //if ($login) : ?>
						<?php if ($this->params->get('loginLogout') == '1') : ?>
							<span id="tk-login" class="tk-tips" title="Members Login:: ">
								<a class="tk-loginlink" style="color:#fff;" href="#mb_login" rel="lightbox[inline 360 180]"><?php echo JText::_('COM_KUNENA_PROFILEBOX_LOGIN'); ?></a>
							</span>
						<?php endif ?>
						<?php //if ($this->register) : ?>
							<span id="tk-register-rules-title" class="tk-registerlink tk-tips handcursor" title="Members Registration:: ">
								<a class="tk-registerlink" style="color:#fff; cursor:pointer;"><?php echo JText::_('COM_KUNENA_PROFILEBOX_REGISTER'); ?></a>
							</span>
						<?php //endif ?>
						<?php //endif;?>
						<?php if ($this->params->get('searchLink') == '1') : ?>
							<span id="tk-search" class="tk-searchlink tk-tips" title="Search in Forum:: ">
								<a class="tk-searchlink" style="color:#fff;" href="#mb_search" rel="lightbox[contact 250 80]"><?php echo JText::_('COM_KUNENA_SEARCH_SEND'); ?></a>
							</span>
						<?php endif;?>
						</dd>
						<?php else:?>
						<dd style="float: right;">
							<?php if ($this->params->get('loginLogout') == '1') : ?>
							<span id="tk-logout" class="tk-tips" title="Members Logout:: ">
								<a class="tk-logoutlink" style="color:#fff;" href="#mb_logout" rel="lightbox[inline 360 100]"><?php echo JText::_('COM_KUNENA_PROFILEBOX_LOGOUT'); ?></a>
							</span>
							<?php endif ?>
							<span id="tk-ann" class="tk-tips" title="Announcment:: ">
								<?php //echo $this->announcementsLink ?>
								<?php ?><a class="tk-logoutlink" style="color:#fff;" href="index.php?option=com_kunena&view=announcement&layout=list"><?php //echo JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS'); ?></a><?php ?>
							</span>
							<?php if ($this->params->get('searchLink') == '1') : ?>
							<span id="tk-search" class="tk-searchlink tk-tips" title="Search in Forum:: ">
								<a class="tk-searchlink" style="color:#fff;" href="#mb_search" rel="lightbox[contact 250 80]"><?php echo JText::_('COM_KUNENA_SEARCH_SEND'); ?></a>
							</span>
							<?php endif; ?>
							<?php //if (!empty($this->privateMessagesLink)) : ?>
							<span class="tk-pmlink <?php if (!empty($this->privateMessagesLink)): echo 'tk-pm-new'; else : echo 'tk-pm-nonew'; endif;?>">
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

<div id="mb_pmread" style="display: none;color:#999;background:#fff;position:relative;">
	<div class="tk-mb-header-pmread">
		<span class="tk-mb-first"><?php echo JText::_('COM_KUNENA_PMS_INBOX'); ?></span>
	</div>
	<div class="tk-mb-pmread" style="text-align:center;color:#666;">
		<span><br /><?php echo JText::_('COM_KUNENA_TEMPLATE_YOU_HAVE'); ?> <?php if (empty($this->privateMessagesLink)) echo JText::_('COM_KUNENA_TEMPLATE_YOU_HAVE_NOT'); else echo '';//$PMCount;?>
		<?php echo JText::_('COM_KUNENA_TEMPLATE_UNREAD_PRIVATE'); ?> <?php if (empty($this->privateMessagesLink)) echo JText::_('COM_KUNENA_TEMPLATE_MESSAGES'); /*elseif ($PMCount == 1) echo JText::_('COM_KUNENA_TEMPLATE_MESSAGE'); elseif ($PMCount > 1) echo JText::_('COM_KUNENA_TEMPLATE_MESSAGES');*/ ?></span>
	</div>
	<?php if (!empty($this->privateMessagesLink)) :?>
	<div class="tk-mb-inboxlink">
		<a class="tk-mb-inboxlink" href="<?php JURI::base() ?>index.php?option=com_uddeim&task=inbox"><?php echo JText::_('COM_KUNENA_TEMPLATE_READ_NOW'); ?></a>
	</div>
	<?php else :?>
	<div class="tk-mb-inboxlink">
		<a class="tk-mb-inboxlink" href="<?php JURI::base() ?>index.php?option=com_uddeim&task=inbox"><?php echo JText::_('COM_KUNENA_TEMPLATE_GO_TO_INBOX'); ?></a>
	</div>
	<?php endif; ?>
</div>


<div id="mb_search" style="display:none;">
	<div class="tk-mb-header-search" style="display:none; margin-bottom:10px;">
		<span class="tk-mb-first"><?php echo JText::_('COM_KUNENA_TEMPLATE_SEARCH_IN_FORUM'); ?></span>
	</div>
	<form action="<?php echo CKunenaLink::GetSearchURL('advsearch'); ?>" method="post" id="searchform" name="adminForm">
		<input id="keywords" class="tk-searchbox" type="text" name="q" value="<?php echo $this->escape($q); ?>" />
		<?php /*?><input id="kusername" class="tk-searchbox" type="text" name="searchuser" value="<?php echo $this->escape($searchuser); ?>" /><?php */?>
		<input class="tk-search-button" type="submit" value="<?php echo JText::_('COM_KUNENA_SEARCH_SEND'); ?>" />
	</form>
	<div>
		<a class="tk-mb-advsearchlink" href="<?php JURI::base() ?>index.php?option=com_kunena&view=search"><?php echo JText::_('COM_KUNENA_SEARCH_ADVSEARCH'); ?></a>
	</div>
</div>
