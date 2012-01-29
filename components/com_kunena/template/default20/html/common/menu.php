<?php
/**
 * Kunena Component
 * @package Kunena.Template.Default20
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="block-wrapper box-border box-border_radius">
   <div class="menubox block">
   	<div class="menubox-global">
   	   <?php echo $this->getMenu() ?>
   	</div>
   	<div class="menubox-user">
         <ul class="menu">
            <?php if (!$this->me->exists()) : ?>
               <li><a href=""><?php echo JText::_('Sign In / Sign Up'); ?></a></li>
            <?php endif; ?>
            <?php if ($this->me->exists()) : ?>
               <li>
                  <div class="login-member">
               		<div class="login-avatar"><span><?php echo $this->me->getLink($this->me->getAvatarImage('', 'welcome'), JText::_('COM_KUNENA_VIEW_COMMON_LOGOUT_OWN_LINK_TITLE')) ?></span></div>
               		<div class="loginbox">
                  		<div class="login-welcome"><?php echo JText::sprintf('COM_KUNENA_VIEW_COMMON_LOGOUT_WELCOME', $this->me->getLink(null, JText::_('COM_KUNENA_VIEW_COMMON_LOGOUT_OWN_LINK_TITLE'))) ?></div>
                  		<div class="login-lastvisit"><?php //echo JText::sprintf('COM_KUNENA_VIEW_COMMON_LOGOUT_LASTVISIT', $this->lastvisitDate->toSpan('date_today', 'ago')); ?></div>
               		</div>
                  </div>
				  </li>
            <?php endif; ?>
         </ul>
   	</div>
   </div>
</div>
<div class="spacer"></div>