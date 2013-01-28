<?php
/**
 * Kunena Component
 * @package Kunena.Template.Strapless
 * @subpackage Topics
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
JHtml::_('bootstrap.tooltip');
// Disable caching
$this->cache = false;

// Show one topic row
?>
<?php if ($this->spacing) : ?>

<tr>
  <td class="kcontenttablespacer" colspan="<?php echo empty($this->topicActions) ? 5 : 6 ?>">&nbsp;</td>
</tr>
<?php endif; ?>
<tr >
  <td class="kcol-mid kcol-ktopicicon hidden-phone span1"> <?php echo $this->getTopicLink ( $this->topic, 'unread', $this->topic->getIcon() ) ?> </td>
  <td class="kcol-mid kcol-ktopictitle span6">
      <div class="hasTooltip"><span class="Title">
        <?php
			 echo $this->getTopicLink ( $this->topic, null, null, KunenaHtmlParser::stripBBCode ( $this->topic->first_post_message, 500), 'hasTooltip' ) ;
			if ($this->topic->getUserTopic()->favorite) {
				?> <i class="icon-star hasTooltip"><?php JText::_('COM_KUNENA_FAVORITE') ?></i>
                <?php
			}
			if ($this->me->exists() && $this->topic->getUserTopic()->posts) {
				?> <i class="icon-flag hasTooltip" ><?php JText::_('COM_KUNENA_MYPOSTS') ?></i>
                <?php
			}
			if ($this->topic->unread) {
				echo $this->getTopicLink ( $this->topic, 'unread', '<sup dir="ltr" class="knewchar">(' . $this->topic->unread . ' ' . JText::_('COM_KUNENA_A_GEN_NEWCHAR') . ')</sup>' );
			}
			?>
        </span> </div>
      <div class="hasTooltip"> <span class="label label-info">Question</span> <?php if ($this->topic->locked != 0) {
				?> <span class="label label-important"><i class="icon-locked"><?php JText::_('COM_KUNENA_LOCKED') ?></i></span>
                <?php
			}
			?>
            in <?php echo $this->getCategoryLink ( $this->topic->getCategory() ,null, null, 'hasTooltip' ) ?></div>
            
    <?php if (!empty($this->keywords)) : ?>
    <div class="ktopic-keywords"> <?php echo JText::sprintf('COM_KUNENA_TOPIC_TAGS', $this->escape($this->keywords)) ?> </div>
    <?php endif; ?>
  </td>
  <td class="span1 hidden-phone"> <span><?php echo  JText::_('COM_KUNENA_GEN_HITS').':'.$this->formatLargeNumber ( $this->topic->hits ) ;?></span> <span><?php echo  JText::_('COM_KUNENA_GEN_REPLIES').':'.$this->formatLargeNumber ( max(0,$this->topic->getTotal()-1) );?></span> </td>
  <td class="span1">
    <?php if (!empty($this->topic->avatar)) : ?>
    <span class="ktopic-latest-post-avatar hidden-phone"> <?php echo $this->topic->getLastPostAuthor()->getLink( $this->topic->avatar ) ?></span>
    <?php endif; ?>
  </td>
  <td class="span2"> <span class="ktopic-latest-post hasTooltip" title="<?php echo $this->topic->getLastPostAuthor() ;?>">
    <?php
				echo $this->topic->getLastPostAuthor()->getLink();
			?>
    </span> <br />
    <span class="ktopic-date hasTooltip" title="<?php echo KunenaDate::getInstance($this->topic->last_post_time)->toKunena('config_post_dateformat_hover'); ?>"><?php echo $this->getTopicLink ( $this->topic, 'last', JText::_('COM_KUNENA_GEN_LAST_POST') ); ?></span> </td>
  <?php if (!empty($this->topicActions)) : ?>
  <td class="span1">
    <input class ="kcheck" type="checkbox" name="topics[<?php echo $this->topic->id?>]" value="1" />
  </td>
  <?php endif; ?>
</tr>
<!-- Module position -->
<?php if ($this->module) : ?>
<tr>
  <td class="ktopicmodule" colspan="<?php echo empty($this->topicActions) ? 5 : 6 ?>"><?php echo $this->module; ?></td>
</tr>
<?php endif; ?>
