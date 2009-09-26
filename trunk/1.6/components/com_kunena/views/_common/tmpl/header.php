<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;
$login = KFactory::getLogin();
$profile = KFactory::getProfile();
$userParams = &JComponentHelper::getParams('com_users');
$allowregistration = $userParams->get('allowUserRegistration');
$user = JFactory::getUser();
?>
	<div id="kunena_top">
		<div class="topnav">
			<ul>
				<li class="active"><?php echo JHtml::_('klink.recent', 'atag', '<span>'.JText::_('K_RECENT_DISCUSSIONS').'</span>', JText::_('K_RECENT_DISCUSSIONS')); ?></li>
				<li><?php echo JHtml::_('klink.category', 'atag', 0, '<span>'.JText::_('K_CATEGORIES').'</span>', JText::_('K_CATEGORIES'));?></li>
				<?php // <li><a href="/forum/faq" title="<?php echo JText::_('K_HELP'); ?/>"><span><?php echo JText::_('K_HELP'); ?/></span></a></li> ?>
			</ul>
		</div>
		<div class="search">
			<form action="/forum/search" name="Search" method="post">
				<input class="input_search" type="text" name="q" size="10" value="<?php echo JText::_('K_SEARCH_FORUM'); ?>" onblur="if(this.value=='') this.value='<?php echo JText::_('K_SEARCH_FORUM'); ?>';" onfocus="if(this.value=='<?php echo JText::_('K_SEARCH_FORUM'); ?>') this.value='';" />
				<input type="submit" value="<?php echo JText::_('K_GO'); ?>" name="submit" class="go_btn"/>
			</form>
		</div>
	</div>

	<div class="topline"></div>

<table class="profile_box" >
	<tr>
<?php if (!$user->id): ?>
		<td class="fcol">
			<p class="welcome"><?php echo JText::_('K_WELCOME'); ?>, <span><?php echo JText::_('K_GUEST'); ?></span></p>
			<p class="register_login"><?php echo JText::_('K_PLEASE'); ?> <a href="<?php echo $login->getLoginURL(); ?>"><?php echo JText::_('K_LOG_IN'); ?></a>
<?php if ($allowregistration): ?>
			<?php echo JText::_('K_OR'); ?> <a href="<?php echo $login->getRegisterURL(); ?>"><?php echo JText::_('K_REGISTER'); ?></a>
<?php endif; ?>.
			<a href="<?php echo $login->getLostPasswordURL(); ?>"><?php echo JText::_('K_LOST_PASSWORD'); ?></a></p>
		</td>
<?php else: ?>
		<td class="lcol">
			<a href="" title="" rel="nofollow"><?php echo $profile->showAvatar($user->id, 'avatar', true); ?></a>
		</td>
		<td class="rcol">
			<p class="welcome">Welcome, <b><?php echo $this->escape($user->name); ?></b></p>
			<p class="register_login">
<!-- 				<a href="" title="" rel="nofollow">Show Latest Posts</a> 
				| <a href="" title="" rel="nofollow">My Profile</a>  
				| --> <a href="<?php echo $login->getLoginURL(); ?>">Logout</a>
<!-- 				| <a href="">Announcements </a>
				| <a href="" title="" rel="nofollow">Advanced Search</a> -->
			</p>
		</td>
<?php endif; ?>												
	</tr>
</table>
									
<?php if (isset($this->announcements)) echo $this->loadCommonTemplate('announce'); ?>
