<?php
/**
 * @version $Id: default.php 4211 2011-01-16 15:09:56Z xillibit $
 * KunenaLatest Module
 * @package Kunena latest
 *
 * @Copyright (C) 2010-2011 www.kunena.org. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 */
defined ( '_JEXEC' ) or die ( '' );
?>
<?php
if ( $this->params->get ( 'sh_topiciconoravatar' ) == 1 ) : ?>
<li class="klatest-avatar"><?php echo $this->lastPostAuthor->getLink($this->lastPostAuthor->getAvatarImage('', $this->params->get ( 'avatarwidth' ), $this->params->get ( 'avatarheight' ))) ?></li>
<?php elseif ( $this->params->get ( 'sh_topiciconoravatar' ) == 0 ) : ?>
<li class="klatest-topicicon">[K=TOPIC_ICON]</li>
<?php endif; ?>

<li class="klatest-subject">
	<?php echo $this->getTopicLink($this->topic, null, $this->escape ( JString::substr ( $this->topic->subject, '0', $this->params->get ( 'titlelength' ) ) )) ?>
	<?php
	if ($this->topic->unread) {
		echo $this->getTopicLink($this->topic, 'unread', '<sup class="knewchar">(' . JText::_($this->params->get ( 'unreadindicator' )) . ')</sup>');
	}
	if ($this->params->get ( 'sh_sticky' ) && $this->topic->ordering) {
		echo $this->getIcon ( 'ktopicsticky', JText::_('MOD_KUNENALATEST_STICKY_TOPIC') );
	}
	if ($this->params->get ( 'sh_locked' ) && $this->topic->locked) {
		echo $this->getIcon ( 'ktopiclocked', JText::_('COM_KUNENA_GEN_LOCKED_TOPIC') );
	}
	if ($this->params->get ( 'sh_favorite' ) && $this->topic->getUserTopic()->favorite) {
		echo $this->getIcon ( 'kfavoritestar', JText::_('COM_KUNENA_FAVORITE') );
	}
	?>
</li>
<?php if ($this->params->get ( 'sh_firstcontentcharacter' )) : ?>
<li class="klatest-preview-content"><?php echo KunenaHtmlParser::stripBBCode($this->topic->last_post_message, $this->params->get ( 'lengthcontentcharacters' )); ?></li>
<?php endif; ?>
<?php if ($this->params->get ( 'sh_category' )) : ?>
<li class="klatest-cat"><?php echo JText::_ ( 'MOD_KUNENALATEST_IN_CATEGORY' ).' '. $this->categoryLink ?></li>
<?php endif; ?>
<?php if ($this->params->get ( 'sh_author' )) : ?>
<li class="klatest-author"><?php echo JText::_ ( 'MOD_KUNENALATEST_LAST_POST_BY' ) .' '. $this->lastPostAuthor->getLink($this->escape ( $this->topic->last_post_guest_name ) ); ?></li>
<?php endif; ?>
<?php if ($this->params->get ( 'sh_time' )) : ?>
<li class="klatest-posttime"><?php $override = $this->params->get ( 'dateformat' ); echo KunenaDate::getInstance($this->topic->last_post_time)->toKunena($override ? $override : 'config_post_dateformat');?></li>
<?php endif; ?>