<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<?php
$this->addScriptDeclaration('// <![CDATA[
var kunena_anonymous_name = "'.JText::_('COM_KUNENA_USERNAME_ANONYMOUS').'";
// ]]>');
?>
<?php if ($this->category->headerdesc) : ?>

<div id="kforum-head" class="<?php echo isset ( $this->category->class_sfx ) ? ' kforum-headerdesc' . $this->escape($this->category->class_sfx) : '' ?>"> <?php echo KunenaHtmlParser::parseBBCode ( $this->category->headerdesc ) ?> </div>
<?php endif ?>
<?php
	$this->displayPoll();
	echo $this->subLayout('Page/Module')->set('position', 'kunena_poll');
	$this->displayTopicActions();
?>
<div>
  <div>
    <h3><span><?php echo JText::_('COM_KUNENA_TOPIC') ?> <?php echo $this->escape($this->topic->subject) ?></span></h3>
    <?php if (!empty($this->keywords)) : ?>
    <div><?php echo JText::sprintf('COM_KUNENA_TOPIC_TAGS', $this->escape($this->keywords)) ?></div>
    <?php endif ?>
  </div>
  <div>
    <div>
      <?php $this->displayMessage($this->state->get('item.mesid'), $this->messages[$this->state->get('item.mesid')]) ?>
      <?php $this->displayTopicActions(); ?>
    </div>
  </div>
</div>
<div>
  <div>
    <h3><span><?php echo JText::sprintf('COM_KUNENA_TOPIC_REPLIES_TITLE', $this->escape($this->topic->subject)) ?></span></h3>
  </div>
  <div>
    <div>
      <table>
        <?php foreach ( $this->messages as $id=>$message ) $this->displayMessage($id, $message, 'row') ?>
      </table>
    </div>
  </div>
</div>
<div>
  <div>
    <div>
      <?php $this->displayForumJump (); ?>
    </div>
    <?php if (!empty ( $this->moderators ) ) : ?>
    <div>
      <?php
				echo '' . JText::_('COM_KUNENA_MODERATORS') . ": ";
				$modlinks = array();
				foreach ( $this->moderators as $moderator ) {
					$modlinks[] = $moderator->getLink();
				}
				echo implode(', ', $modlinks);
				?>
    </div>
    <?php endif; ?>
  </div>
</div>
