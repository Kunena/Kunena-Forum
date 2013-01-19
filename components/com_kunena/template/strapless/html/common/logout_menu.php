<?php
/**
 * Kunena Component
 * @package Kunena.Template.Strapless
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
JHtml::_('behavior.keepalive');
JHtml::_('bootstrap.tooltip');

// Basic logic has been taken from Joomla! 2.5 (mod_menu)
// HTML output emulates default Joomla! 1.5 (mod_mainmenu), but only first level is supported

// Note. It is important to remove spaces between elements.
?>

<!-- user dropdown -->

<ul class="nav pull-right">
  <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-large icon-user"></i> <b class="caret"></b></a>
    <ul class="dropdown-menu">
      <li>
        <form action="<?php echo JRoute::_('index.php?option=com_kunena'); ?>" method="post" id="login-form" class="form-inline">
          <div class="center">
            <div style="width:70px; padding-left:40px" ><a href="index.php?option=com_kunena&amp;view=user" class="thumbnail"><?php echo $this->me->getAvatarImage('kavatar'); ?></a></div>
            <p><strong><?php echo $this->escape($this->me->get('name'));?></strong></p>
          </div>
          <div class="divider"></div>
          <?php if( $this->me->isModerator() ) : ?>
          <div><a href="index.php?option=com_kunena&amp;view=announcement&amp;layout=list"><i class="icon-pencil-2"></i> Annoucement</a></div>
          <?php endif; ?>
          <?php if (!empty($this->privateMessagesLink)) : ?>
          <div> <a href="index.php?option=com_uddeim&amp;task=inbox" rel="follow"><i class="icon-mail"></i> Inbox: </a> </div>
          <?php endif ?>
          <div><a href="index.php?option=com_kunena&amp;view=user&amp;layout=edit"><i class="icon-cog"></i> Preferences</a></div>
          <!-- <li><a href="/help/support"><i class="icon-envelope"></i> Inbox</a></div> -->
          <div><a href="http://www.kunena.org/docs/"><i class="icon-help"></i> Help</a></div>
          <div class="divider"></div>
          <div style="padding-left:8px;">
            <button class="btn btn-link" name="submit" type="submit" style="color:#5388B4 !important"><i class="icon-out"></i> <?php echo JText::_('COM_KUNENA_PROFILEBOX_LOGOUT'); ?></button>
          </div>
          <input type="hidden" name="view" value="user" />
          <input type="hidden" name="task" value="logout" />
          <?php echo JHtml::_('form.token'); ?>
        </form>
      </li>
    </ul>
  </li>
</ul>
<!-- ./ user dropdown --> 

