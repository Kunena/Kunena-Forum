<?php
/**
 * Kunena Component
 * @package Kunena.Template.Strapless
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
$this->displayBreadcrumb ();
$this->document->addScriptDeclaration('// <![CDATA[
var kunena_anonymous_name = "'.JText::_('COM_KUNENA_USERNAME_ANONYMOUS').'";
// ]]>');
?>
<?php if ($this->category->headerdesc) : ?>

<div class="well"> <?php echo KunenaHtmlParser::parseBBCode ( $this->category->headerdesc ) ?> </div>
<?php endif ?>
<?php
	$this->displayPoll();
	$this->displayModulePosition( 'kunena_poll' );
	$this->displayTopicActions();
?>
<div>
  <?php if ($this->total >1) : ?>
  <div class="pagination pull-right" style="margin:-20px 0 0 0 ;"><?php echo $this->getPagination (5); ?></div>
  <?php endif ?>
  <div>
    <h3><?php echo JText::_('COM_KUNENA_TOPIC') ?> <?php echo $this->escape($this->topic->subject) ?></h3>
    <?php $this->displayModulePosition( 'kunena_topictitle' ); ?>
  </div>
</div>
<div class="clearfix"></div>
<?php if ($this->usertopic->favorite) : ?>
<div class="kfavorite"></div>
<?php endif ?>
<?php if (!empty($this->keywords)) : ?>
<h6><?php echo JText::sprintf('COM_KUNENA_TOPIC_TAGS', $this->escape($this->keywords)) ?></h6>
<?php endif ?>
<span>
<?php $this->displayMessages() ?>
</span>
<div>
  <?php $this->displayTopicActions(); ?>
  <div class="pull-right">
    <div>
      <?php if ($this->total >1) : ?>
      <div class="pagination pull-right" style="margin:-50px 0 0 0 ;"><?php echo $this->getPagination (5); ?></div>
      <?php endif ?>
      <div style="padding-top:10px">
        <?php $this->displayForumJump (); ?>
      </div>
    </div>
  </div>
</div>
<div class="clearfix"></div>
<div class="row-fluid column-row">
  <div class="span10 column-item">
    <?php if (!empty ( $this->moderators ) ) : ?>
    <div class="klist-moderators">
      <?php
				echo '' . JText::_('COM_KUNENA_MODERATORS') . ": ";
				$modlinks = array();
				foreach ( $this->moderators as $moderator) {
					$modlinks[] = $moderator->getLink ();
				}
				echo implode(', ', $modlinks);
				?>
    </div>
    <?php endif; ?>
  </div>
</div>
