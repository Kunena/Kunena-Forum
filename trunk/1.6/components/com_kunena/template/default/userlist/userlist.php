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
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die();

$document=& JFactory::getDocument();
$option=JRequest::getCmd ( 'option' );

$document->setTitle(JText::_('COM_KUNENA_USRL_USERLIST') . ' - ' . $this->escape($this->config->board_title));

$document->addScriptDeclaration( 'function tableOrdering( order, dir, task ) {
	var form=document.adminForm;
	form.filter_order.value=order;
	form.filter_order_Dir.value=dir;
	document.adminForm.submit( task );
}' );
$document->addScriptDeclaration( "document.addEvent('domready', function() {
	// Attach auto completer to the following ids:
	new Autocompleter.Request.JSON('kusersearch', '" . CKunenaLink::GetJsonURL('autocomplete', 'getuser', false) . "', { 'postVar': 'value' });
});");
// FIXME: do not use alert:
$document->addScriptDeclaration( "function validate() {
	if ((document.usrlform.search == '') || (document.usrlform.search.value == '')) {
		alert('" . JText::_('COM_KUNENA_USRL_SEARCH_ALERT') . "');
		return false;
	} else {
		return true;
	}
}");
?>
<div class="k-bt-cvr1">
<div class="k-bt-cvr2">
<div class="k-bt-cvr3">
<div class="k-bt-cvr4">
<div class="k_bt_cvr5">
	<table class="kblocktable" id ="kuserlist" border="0" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th>
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td align="left">
								<div class="ktitle-cover km">
									<span class="ktitle kl"> <?php echo JText::_('COM_KUNENA_USRL_USERLIST'); ?></span>&nbsp;
									<?php printf(JText::_('COM_KUNENA_USRL_REGISTERED_USERS'), $this->app->getCfg('sitename'), intval($this->total));?>
								</div>
							</td>

							<td align="right">
								<form name="usrlform" method="post" action="<?php echo CKunenaLink::GetUserlistURL(); ?>" onsubmit="return validate()">
									<input id="kusersearch" type="text" name="search" class="inputbox"
										value="<?php echo $this->escape($this->search); ?>" onblur="if(this.value=='') this.value='<?php echo $this->escape($this->search); ?>';" onfocus="if(this.value=='<?php echo $this->escape($this->search); ?>') this.value='';" />
									<?php // FIXME: fixed image ?>
									<input type="image" src="<?php echo KUNENA_TMPLTMAINIMGURL .'/images/usl_search_icon.png' ;?>" alt="<?php echo JText::_('COM_KUNENA_USRL_SEARCH'); ?>" align="top" style="border: 0px;" />
								</form>
							</td>
						</tr>
					</table>
				</th>
			</tr>
		</thead>

		<tbody>
			<tr>
				<td class="k-userlistinfo">
					<!-- Begin: Listing -->
					<form action="<?php echo CKunenaLink::GetUserlistURL(); ?>" method="post" name="adminForm">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr class="ksth ks">
							<th class="th-1 frst ksectiontableheader" align="center"> </th>

							<?php if ($this->config->userlist_online) : ?>
							<th class="th-2 ksectiontableheader" align="center"><?php echo JText::_('COM_KUNENA_USRL_ONLINE'); ?></th>
							<?php endif; ?>

							<?php if ($this->config->userlist_avatar) : ?>
							<th class="th-3 ksectiontableheader" align="center"><?php echo JText::_('COM_KUNENA_USRL_AVATAR'); ?></th>
							<?php endif; ?>

							<?php if ($this->config->userlist_name) : ?>
							<th class="th-4 ksectiontableheader usersortable" align="center"><?php echo JHTML::_( 'grid.sort', JText::_('COM_KUNENA_USRL_NAME'), 'name', $this->order_dir, $this->order); ?></th>
							<?php endif; ?>

							<?php if ($this->config->userlist_username) : ?>
							<th class="th-5 ksectiontableheader usersortable" align="center"><?php echo JHTML::_( 'grid.sort', JText::_('COM_KUNENA_USRL_USERNAME'), 'username', $this->order_dir, $this->order); ?></th>
							<?php endif; ?>

							<?php if ($this->config->userlist_posts) : ?>
							<th class="th-7 ksectiontableheader usersortable" align="center"><?php echo JHTML::_( 'grid.sort', JText::_('COM_KUNENA_USRL_POSTS'), 'posts', $this->order_dir, $this->order); ?></th>
							<?php endif; ?>

							<?php if ($this->config->userlist_karma) : ?>
							<th class="th-7 ksectiontableheader usersortable" align="center"><?php echo JHTML::_( 'grid.sort', JText::_('COM_KUNENA_USRL_KARMA'), 'karma', $this->order_dir, $this->order); ?></th>
							<?php endif; ?>

							<?php if ($this->config->userlist_email) : ?>
							<th class="th-8 ksectiontableheader usersortable" align="center"><?php echo JHTML::_( 'grid.sort', JText::_('COM_KUNENA_USRL_EMAIL'), 'email', $this->order_dir, $this->order); ?></th>
							<?php endif; ?>

							<?php if ($this->config->userlist_usertype) : ?>
							<th class="th-9 ksectiontableheader usersortable" align="center"><?php echo JHTML::_( 'grid.sort', JText::_('COM_KUNENA_USRL_USERTYPE'), 'usertype', $this->order_dir, $this->order); ?></th>
							<?php endif; ?>

							<?php if ($this->config->userlist_joindate) : ?>
							<th class="th-10 ksectiontableheader usersortable" align="center"><?php echo JHTML::_( 'grid.sort', JText::_('COM_KUNENA_USRL_JOIN_DATE'), 'registerDate', $this->order_dir, $this->order); ?></th>
							<?php endif; ?>

							<?php if ($this->config->userlist_lastvisitdate) : ?>
							<th class="th-11 ksectiontableheader usersortable" align="center"><?php echo JHTML::_( 'grid.sort', JText::_('COM_KUNENA_USRL_LAST_LOGIN'), 'lastvisitDate', $this->order_dir, $this->order); ?></th>
							<?php endif; ?>

							<?php if ($this->config->userlist_userhits) : ?>
							<th class="th-12 lst ksectiontableheader usersortable" align="center"><?php echo JHTML::_( 'grid.sort', JText::_('COM_KUNENA_USRL_HITS'), 'uhits', $this->order_dir, $this->order); ?></th>
							<?php endif; ?>
						</tr>

						<?php
						$i=1;

						foreach ($this->users as $user) :
							$evenodd = $i % 2;
							$nr = $i + $this->limitstart;
							$i++;

							if ($evenodd == 0) {
								$usrl_class="sectiontableentry1";
							}
							else {
								$usrl_class="sectiontableentry2";
							}

							$profile=KunenaFactory::getUser(intval($user->id));
							$uslavatar=$profile->getAvatarLink('usl_avatar', 'list');
						?>

						<tr class="k<?php echo $usrl_class ;?> km">
							<td class="td-1 frst ks" align="center"><?php echo $nr; ?></td>

							<?php if ($this->config->userlist_online) : ?>
							<td class="td-2">
								<?php // online - ofline status
								$isonline=$profile->isOnline();
								if ($isonline && $user->showOnline ==1 ) {
									echo CKunenaTools::showIcon ( 'konlineicon', JText::_('COM_KUNENA_MODLIST_ONLINE') );
								} else {
									echo CKunenaTools::showIcon ( 'kofflineicon', JText::_('COM_KUNENA_MODLIST_OFFLINE') );
								}
								?>
							</td>
							<?php endif; ?>

							<?php if ($this->config->userlist_avatar) : ?>
							<td class="td-3" align="center"><?php echo !empty($uslavatar) ? CKunenaLink::GetProfileLink(intval($user->id), $uslavatar) : '&nbsp;' ?></td>
							<?php endif; ?>

							<?php if ($this->config->userlist_name) : ?>
							<td class="td-4 km" align="center"><?php echo CKunenaLink::GetProfileLink(intval($user->id), $this->escape($user->name)); ?></td>
							<?php endif; ?>

							<?php if ($this->config->userlist_username) : ?>
							<td class="td-5 km" align="center"><?php echo CKunenaLink::GetProfileLink(intval($user->id), $this->escape($user->username)); ?></td>
							<?php endif; ?>

							<?php if ($this->config->userlist_posts) : ?>
							<td class="td-7 ks" align="center"><?php echo intval($user->posts); ?></td>
							<?php endif; ?>

							<?php if ($this->config->userlist_karma) : ?>
							<td class="td-7 ks" align="center"><?php echo intval($user->karma); ?></td>
							<?php endif; ?>

							<?php if ($this->config->userlist_email) : ?>
							<td class="td-8 ks" align="center"><a href=mailto:"<?php echo $this->escape($user->email) ?>"><?php echo $this->escape($user->email) ?></a></td>
							<?php endif; ?>

							<?php if ($this->config->userlist_usertype) : ?>
							<td class="td-9 ks" align="center"><?php echo $this->escape($user->usertype) ?></td>
							<?php endif; ?>

							<?php if ($this->config->userlist_joindate) : ?>
							<td class="td-10 ks" align="center" title="<?php echo CKunenaTimeformat::showDate($user->registerDate, 'ago', 'utc') ?>"><?php echo CKunenaTimeformat::showDate($user->registerDate, 'datetime_today', 'utc') ?></td>
							<?php endif; ?>

							<?php if ($this->config->userlist_lastvisitdate) : ?>
							<td class="td-11 ks" align="center" title="<?php echo CKunenaTimeformat::showDate($user->lastvisitDate, 'ago', 'utc') ?>"><?php echo CKunenaTimeformat::showDate($user->lastvisitDate, 'datetime_today', 'utc') ?></td>
							<?php endif; ?>

							<?php if ($this->config->userlist_userhits) : ?>
							<td class="td-12 lst ks" align="center"><?php echo $this->escape($user->uhits) ?></td>
							<?php endif; ?>
						</tr>
						<?php endforeach; ?>
					</table>
					<input type="hidden" name="option" value="<?php echo $option; ?>">
					<input type="hidden" name="filter_order" value="<?php echo intval($this->order); ?>" />
					<input type="hidden" name="filter_order_dir" value="<?php echo intval($this->order_dir); ?>" />
				</form>

				<form name="usrlform" method="post" action="<?php echo CKunenaLink::GetUserlistURL(); ?>" onsubmit="return false;">
					<table width="100%" class="kuserlist_pagenav" border="0" cellspacing="0" cellpadding="0">
						<tr class="ksth ks">
							<th class="th-1 km" align="center" style="text-align:center;">
								<?php
								// TODO: fxstein - Need to perform SEO cleanup
								echo $this->pageNav->getPagesLinks(CKunenaLink::GetUserlistURL($this->searchuri));
								?>
							</th>
						</tr>
					</table>

					<table class="kblocktable" id="kuserlist_bottom" style="border-bottom:0px;margin:0;" border="0" cellspacing="0" cellpadding="0" width="100%">
						<tr>
							<th class="th-right ks" align="right" style="text-align:right">
							<?php echo $this->pageNav->getPagesCounter(); ?> | <?php echo JText::_('COM_KUNENA_USRL_DISPLAY_NR'); ?> <?php echo $this->pageNav->getLimitBox(CKunenaLink::GetUserlistURL($this->searchuri)); ?>
							</th>
						</tr>
					</table>
				</form>
			</td>
		</tr>
	</tbody>
</table>
<!-- Finish: Listing -->

</div>
</div>
</div>
</div>
</div>

<?php $this->displayWhoIsOnline(); ?>

<!-- Begin: Forum Jump -->
<div class="k-bt-cvr1">
<div class="k-bt-cvr2">
<div class="k-bt-cvr3">
<div class="k-bt-cvr4">
<div class="k_bt_cvr5">
	<table class="kblocktable" id="kbottomarea" border="0" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th class="th-right">
					<?php $this->displayForumJump(); ?>
				</th>
			</tr>
		</thead>
		<tbody><tr><td></td></tr></tbody>
	</table>
</div>
</div>
</div>
</div>
</div>
<!-- Finish: Forum Jump -->