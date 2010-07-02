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

global $kunena_icons;

$document=& JFactory::getDocument();

$document->addScriptDeclaration( 'function tableOrdering( order, dir, task ) {
var form=document.adminForm;
form.filter_order.value=order;
form.filter_order_Dir.value=dir;
document.adminForm.submit( task );}' );
$option=JRequest::getCmd ( 'option' );

$document->setTitle(JText::_('COM_KUNENA_USRL_USERLIST') . ' - ' . $this->config->board_title);

$document->addScriptDeclaration( "document.addEvent('domready', function() {
		// Attach auto completer to the following ids:
		new Autocompleter.Request.JSON('kusersearch', '" . CKunenaLink::GetJsonURL('autocomplete', 'getuser', false) . "', { 'postVar': 'value' });
});");

?>

<script type="text/javascript">
	<!--
	function validate()
	{
		if ((document.usrlform.search == "") || (document.usrlform.search.value == ""))
		{
			alert('<?php echo JText::_('COM_KUNENA_USRL_SEARCH_ALERT'); ?>');
			return false;
		}
		else
		{
			return true;
		}
	}
	//-->
</script>
<div class="kblock">
	<div class="kheader">
		<span class="ktoggler"><a class="ktoggler close"  rel="searchuser_tbody"></a></span>
		<h2><span><?php printf(JText::_('COM_KUNENA_USRL_REGISTERED_USERS'), $this->app->getCfg('sitename'), $this->total);?></span></h2>
	</div>
	<div class="kcontainer" id="searchuser_tbody">
		<div class="kbody">
			<div class="search-user">
				<form name="usrlform" method="post" action="<?php echo CKunenaLink::GetUserlistURL(); ?>" onsubmit="return validate()">
					<input id="kusersearch" type="text" name="search" class="inputbox"
						value="<?php echo $this->search; ?>" onblur="if(this.value=='') this.value='<?php echo $this->search; ?>';" onfocus="if(this.value=='<?php echo $this->search; ?>') this.value='';" />
					<input type="image" src="<?php echo KUNENA_TMPLTMAINIMGURL .'/images/usl_search_icon.png' ;?>" alt="<?php echo JText::_('COM_KUNENA_USRL_SEARCH'); ?>" align="top" style="border: 0px;"/>
				</form>
			</div>
			<div class="userlist-jump">
				<?php $this->displayForumJump(); ?>
			</div>
        </div>
	</div>
</div>
<div class="kblock">
	<div class="kheader">
		<span class="ktoggler"><a class="ktoggler close"  rel="userlist_tbody"></a></span>
		<h2><span><?php echo JText::_('COM_KUNENA_USRL_USERLIST'); ?></span></h2>
	</div>
	<div class="kcontainer" id="userlist_tbody">
		<div class="kbody">
				<form action="<?php echo CKunenaLink::GetUserlistURL(); ?>" method="POST" name="adminForm">
					<table>
						<tr class="ksth ks userlist">
							<th class="kcol frst ksectiontableheader" align="center">
							</th>

							<?php if ($this->config->userlist_online) { ?>
							<th align="center"><?php echo JText::_('COM_KUNENA_USRL_ONLINE'); ?></th>
							<?php } ?>

							<?php if ($this->config->userlist_avatar) { ?>
							<th align="center"><?php echo JText::_('COM_KUNENA_USRL_AVATAR'); ?></th>
							<?php } ?>

							<?php if ($this->config->userlist_name) { ?>
							<th class="usersortable" align="center"><?php echo JHTML::_( 'grid.sort', JText::_('COM_KUNENA_USRL_NAME'), 'name', $this->order_dir, $this->order); ?></th>
							<?php } ?>

							<?php if ($this->config->userlist_username) { ?>
							<th class="usersortable" align="center"><?php echo JHTML::_( 'grid.sort', JText::_('COM_KUNENA_USRL_USERNAME'), 'username', $this->order_dir, $this->order); ?></th>
							<?php } ?>

							<?php if ($this->config->userlist_posts) { ?>
							<th class="usersortable" align="center"><?php echo JHTML::_( 'grid.sort', JText::_('COM_KUNENA_USRL_POSTS'), 'posts', $this->order_dir, $this->order); ?></th>
							<?php } ?>

							<?php if ($this->config->userlist_karma) { ?>
							<th class="usersortable" align="center"><?php echo JHTML::_( 'grid.sort', JText::_('COM_KUNENA_USRL_KARMA'), 'karma', $this->order_dir, $this->order); ?></th>
							<?php } ?>

							<?php if ($this->config->userlist_email) { ?>
							<th class="usersortable" align="center"><?php echo JHTML::_( 'grid.sort', JText::_('COM_KUNENA_USRL_EMAIL'), 'email', $this->order_dir, $this->order); ?></th>
							<?php } ?>

							<?php if ($this->config->userlist_usertype) { ?>
							<th class="usersortable" align="center"><?php echo JHTML::_( 'grid.sort', JText::_('COM_KUNENA_USRL_USERTYPE'), 'usertype', $this->order_dir, $this->order); ?></th>
							<?php } ?>

							<?php if ($this->config->userlist_joindate) { ?>
							<th class="usersortable" align="center"><?php echo JHTML::_( 'grid.sort', JText::_('COM_KUNENA_USRL_JOIN_DATE'), 'registerDate', $this->order_dir, $this->order); ?></th>
							<?php } ?>

							<?php if ($this->config->userlist_lastvisitdate) { ?>
							<th class="usersortable" align="center"><?php echo JHTML::_( 'grid.sort', JText::_('COM_KUNENA_USRL_LAST_LOGIN'), 'lastvisitDate', $this->order_dir, $this->order); ?></th>
							<?php } ?>

							<?php if ($this->config->userlist_userhits) { ?>
							<th class="usersortable" align="center"><?php echo JHTML::_( 'grid.sort', JText::_('COM_KUNENA_USRL_HITS'), 'uhits', $this->order_dir, $this->order); ?></th>
							<?php } ?>
						</tr>

						<?php
						$i=1;

						foreach ($this->users as $user)
						{
							$evenodd=$i % 2;

							if ($evenodd == 0) {
								$usrl_class="row1";
							}
							else {
								$usrl_class="row2";
							}

							$nr=$i + $this->limitstart;

							$profile=KunenaFactory::getUser($user->id);
							$uslavatar=$profile->getAvatarLink('usl_avatar', 'list');
?>

						<tr class="k<?php echo $usrl_class ;?> km">
							<td class="kfirst frst ks" align="center"><?php echo $nr; ?></td>

							<?php if ($this->config->userlist_online) { ?>

							<td class="kmiddle">
							<?php // online - ofline status
								$isonline=$profile->isOnline();

								if ($isonline && $user->showOnline ==1 ) {
									echo isset($kunena_icons['onlineicon']) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons['onlineicon'] . '" border="0" alt="' . JText::_('COM_KUNENA_MODLIST_ONLINE') . '" />' : ' <img src="' . KUNENA_URLEMOTIONSPATH . 'onlineicon.gif" border="0" alt="' . JText::_('COM_KUNENA_MODLIST_ONLINE') . '" />';
								} else {
									echo isset($kunena_icons['offlineicon']) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons['offlineicon'] . '" border="0" alt="' . JText::_('COM_KUNENA_MODLIST_OFFLINE') . '" />' : ' <img src="' . KUNENA_URLEMOTIONSPATH . 'offlineicon.gif" border="0" alt="' . JText::_('COM_KUNENA_MODLIST_OFFLINE') . '" />';
								}
							?>
							</td>

							<?php } ?>

							<?php if ($this->config->userlist_avatar) { ?>
							<td class="kmiddle" align="center"><?php if(JString::strlen($uslavatar)) { echo CKunenaLink::GetProfileLink($user->id, $uslavatar); } else { echo '&nbsp;'; } ?></td>
							<?php } ?>

							<?php if ($this->config->userlist_name) { ?>
							<td class="kmiddle km" align="center"><?php echo CKunenaLink::GetProfileLink($user->id, $user->name); ?></td>
							<?php } ?>

							<?php if ($this->config->userlist_username) { ?>
							<td class="kmiddle km" align="center"><?php echo CKunenaLink::GetProfileLink($user->id, $user->username); ?></td>
							<?php } ?>

							<?php if ($this->config->userlist_posts) { ?>
							<td class="kmiddle ks" align="center"><?php echo $user->posts; ?></td>
							<?php } ?>

							<?php if ($this->config->userlist_karma) { ?>
								<td class="kmiddle ks" align="center"><?php echo $user->karma; ?></td>
							<?php } ?>

							<?php
							if ($this->config->userlist_email) {
								echo "\t\t<td class=\"kmiddle ks\" align=\"center\"><a href=\"mailto:$user->email\">$user->email</a></td>\n";
							}
							if ($this->config->userlist_usertype) {
								echo "\t\t<td class=\"kmiddle ks\" align=\"center\">$user->usertype</td>\n";
							}
							if ($this->config->userlist_joindate) {
								echo "\t\t<td class=\"kmiddle ks\" align=\"center\" title=\"".CKunenaTimeformat::showDate($user->registerDate, 'ago', 'utc')."\">" . CKunenaTimeformat::showDate($user->registerDate, 'datetime_today', 'utc') . "</td>\n";
							}
							if ($this->config->userlist_lastvisitdate) {
								echo "\t\t<td class=\"kmiddle ks\" align=\"center\" title=\"".CKunenaTimeformat::showDate($user->lastvisitDate, 'ago', 'utc')."\">" . CKunenaTimeformat::showDate($user->lastvisitDate, 'datetime_today', 'utc') . "</td>\n";
							}
							?>

							<td class="kmiddle lst ks" align="center"><?php if ($this->config->userlist_userhits) { echo $user->uhits; } ?></td>

						</tr>
						<?php
							$i++;
						}
						?>
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>">
		<input type="hidden" name="filter_order" value="<?php echo $this->order; ?>" />
		<input type="hidden" name="filter_order_dir" value="<?php echo $this->order_dir; ?>" />
		</form>

		<form name="usrlform" method="post" action="<?php echo CKunenaLink::GetUserlistURL(); ?>" onsubmit="return false;">
		<table width="100%" class="kuserlist_pagenav" border="0" cellspacing="0" cellpadding="0">
			<tr class="ksth ks">
				<th class="th-1 km" align="center" style="text-align:center;">

						<?php
						// TODO: fxstein - Need to perform SEO cleanup
						echo $this->pageNav->getPagesLinks(CKunenaLink::GetUserlistURL($this->searchuri)); ?>
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
        </div>
	</div>
</div>
<?php $this->displayWhoIsOnline(); ?>
