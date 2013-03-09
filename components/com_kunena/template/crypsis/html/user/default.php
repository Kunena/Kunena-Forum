<?php
/**
 * Kunena Component
 * @package Kunena.Template.Strapless
 * @subpackage User
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$this->document->addScriptDeclaration ( "// <![CDATA[
window.addEvent('domready', function(){ $$('dl.tabs').each(function(tabs){ new KunenaTabs(tabs); }); });
// ]]>" );
?>

<h3><?php echo JText::_('COM_KUNENA_USER_PROFILE'); ?> <?php echo $this->escape($this->name); ?> </h3>
<hr />
<div class="clearfix"></div>
<div id="user">
  <div class="span12">
    <div>
      <div class="span3" style="width:13.404255317%"><a href="#" class="thumbnail"><?php echo $this->avatarlink; ?></a></div>
      <div class="span4">
        <p>admin</p>
        <p><strong><?php echo $this->escape($this->user->get('name'));?></strong></p>
        <span class=" badge badge-warning"><?php echo intval($this->thankyou); ?> Thanks</span> <span class=" badge badge-info"><?php echo intval($this->posts); ?> Messages</span> </div>
      <div class="span5"> <span>
        <?php $this->displayTemplateFile('user', 'default', 'social');?>
        </span> </div>
    </div>
  </div>
  <div class="clearfix"></div>
  <br />
  <div class="tabs-left">
    <ul id="myTab" class="nav nav-tabs">
      <li class="active"><a href="#home" data-toggle="tab"><?php echo JText::_('COM_KUNENA_USERPOSTS'); ?></a></li>
      <li><a href="#subscriptions" data-toggle="tab"><?php echo JText::_('COM_KUNENA_SUBSCRIPTIONS'); ?></a></li>
      <li><a href="#favorites" data-toggle="tab"><?php echo JText::_('COM_KUNENA_FAVORITES'); ?></a></li>
      <li><a href="#thankyou" data-toggle="tab"><?php echo JText::_('COM_KUNENA_THANK_YOU'); ?></a></li>
      <li><a href="#unapproved" data-toggle="tab"><?php echo JText::_('COM_KUNENA_MESSAGE_ADMINISTRATION'); ?></a></li>
      <li><a href="#attachments" data-toggle="tab"><?php echo JText::_('COM_KUNENA_MANAGE_ATTACHMENTS'); ?></a></li>
      <li><a href="#banmanager" data-toggle="tab"><?php echo JText::_('COM_KUNENA_BAN_BANMANAGER'); ?></a></li>
      <li><a href="#banhistory" data-toggle="tab"><?php echo JText::_('COM_KUNENA_BAN_BANHISTORY'); ?></a></li>
      <li><a href="#banuser" data-toggle="tab"><?php echo $this->banInfo->id ? JText::_('COM_KUNENA_BAN_EDIT') : JText::_('COM_KUNENA_BAN_NEW' ); ?></a></li>
    </ul>
    <div id="myTabContent" class="tab-content">
      <div class="tab-pane fade in active" id="home">
        <?php if($this->showUserPosts) : ?>
        <div>
          <?php $this->displayUserPosts(); ?>
        </div>
        <?php endif; ?>
      </div>
      <div class="tab-pane fade" id="subscriptions">
        <?php if ($this->showSubscriptions) :?>
        <div>
          <?php $this->displayCategoriesSubscriptions(); ?>
          <?php $this->displaySubscriptions(); ?>
        </div>
        <?php endif; ?>
      </div>
      <div class="tab-pane fade" id="favorites">
        <?php if ($this->showFavorites) : ?>
        <div>
          <?php $this->displayFavorites(); ?>
        </div>
        <?php endif; ?>
      </div>
      <div class="tab-pane fade" id="thankyou">
        <?php if ($this->showFavorites) : ?>
        <div>
          <?php $this->displayGotThankyou(); ?>
          <?php $this->displaySaidThankyou(); ?>
        </div>
        <?php endif; ?>
      </div>
      <div class="tab-pane fade" id="unapproved">
        <?php if ($this->showUnapprovedPosts): ?>
        <div>
          <?php $this->displayUnapprovedPosts(); ?>
        </div>
        <?php endif; ?>
      </div>
      <div class="tab-pane fade" id="attachments">
        <?php if ($this->showAttachments): ?>
        <div>
          <?php $this->displayAttachments(); ?>
        </div>
        <?php endif; ?>
      </div>
      <div class="tab-pane fade" id="banmanager">
        <?php if ($this->showBanManager): ?>
        <div>
          <?php $this->displayBanManager(); ?>
        </div>
        <?php endif; ?>
      </div>
      <div class="tab-pane fade" id="banhistory">
        <?php if ($this->showBanHistory):?>
        <div>
          <?php $this->displayBanHistory(); ?>
        </div>
        <?php endif; ?>
      </div>
      <div class="tab-pane fade" id="banuser">
        <?php if ($this->showBanUser) : ?>
        <div>
          <?php $this->displayBanUser(); ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
