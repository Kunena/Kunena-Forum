<?php
/**
 * Kunena Component
 * @package Kunena.Template.Strapless
 * @subpackage Category
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

// Show one topic row
?>
<?php if ($this->spacing) : ?>

<tr>
  <td class="kcontenttablespacer"
		colspan="<?php echo empty($this->topicActions) ? 5 : 6 ?>">&nbsp;</td>
</tr>
<?php endif; ?>
<tr>
  <td class="span1 hidden-phone">
    <div class="pull-left"><?php echo $this->getTopicLink ( $this->topic, 'unread', $this->topic->getIcon() ) ?></div>
    <div class="clearfix"></div>
  </td>
  <td class="span7">
    <div class="ItemContent Discussion">
      <div class="Title"><a href="#"><?php echo $this->getTopicLink ( $this->topic, null, null, KunenaHtmlParser::stripBBCode ( $this->topic->first_post_message, 500), 'hasTooltip' ) ;?></a></div>
      <div class="Meta"> <span class="CommentCount"><i class="icon-comments-2"></i> <?php echo $this->formatLargeNumber ( max(0,$this->topic->getTotal()-1) ).' '. JText::_('COM_KUNENA_GEN_REPLIES')?></span> <i class="icon-eye"></i><span class="LastCommentBy"> <?php echo $this->formatLargeNumber ( $this->topic->hits ).' '.  JText::_('COM_KUNENA_GEN_HITS');?></span> <i class="icon-user"></i> <span>Started by <a class="tip" title="admin" href="#">
        <?php
				echo $this->topic->getFirstPostAuthor()->getLink();
			?>
      </a></span> <i class="icon-calendar"></i> <span><?php echo KunenaDate::getInstance($this->topic->first_post_time);?></span> </div>
      <div id="one">
        <div id="tow">
          <div class="well"> <?php echo KunenaHtmlParser::stripBBCode ( $this->topic->first_post_message, 100)  ;?></div>
        </div>
      </div>
    </div>
  </td>
  <td width="2%">
    <div class="pull-right kfrontend"> <a class="btn btn-micro" id="test1"  href="javascript:void(0);" onclick="javascript:showMessage();" title="Show Message"><i class="icon-downarrow"></i></a> <a class="btn btn-micro" id="test2" href="javascript:void(0);" onclick="javascript:hideMessage();" title="Hide Message"><i class="icon-uparrow"></i></a> </div>
    <div class="clearfix"></div>
  </td>
  <td class="span1">
    <?php if (!empty($this->topic->avatar)) : ?>
    <span class="ktopic-latest-post-avatar hidden-phone"> <?php echo $this->topic->getLastPostAuthor()->getLink( $this->topic->avatar ) ?></span>
    <?php endif; ?>
  </td>
  <td width="10%"> <span class="ktopic-latest-post hasTooltip" title="<?php echo $this->topic->getLastPostAuthor() ;?>">
    <?php
				echo $this->topic->getLastPostAuthor()->getLink();
			?>
    </span> <br />
    <span class="ktopic-date hasTooltip" title="<?php echo KunenaDate::getInstance($this->topic->last_post_time)->toKunena('config_post_dateformat_hover'); ?>"><?php echo $this->getTopicLink ( $this->topic, 'last', JText::_('COM_KUNENA_GEN_LAST_POST') ); ?></span> </td>
  <?php if (!empty($this->topicActions)) : ?>
  <td  width="1%">
    <input class ="kcheck" type="checkbox" name="topics[<?php echo $this->topic->id?>]" value="1" />
  </td>
  <?php endif; ?>
</tr>
<!-- Module position -->
<?php if ($this->module) : ?>
<tr>
  <td class="ktopicmodule"
		colspan="<?php echo empty($this->topicActions) ? 5 : 6 ?>"><?php echo $this->module; ?></td>
</tr>
<?php endif; ?>
