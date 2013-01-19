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
  <td class="span6">
    <div class="row-fluid column-row pull-left">
      <div class="span12 column-item">
        <?php if ($this->topic->attachments) echo $this->getIcon ( 'ktopicattach', JText::_('COM_KUNENA_ATTACH') ); ?>
        <?php if ($this->topic->poll_id) echo $this->getIcon ( 'ktopicpoll', JText::_('COM_KUNENA_ADMIN_POLLS') ); ?>
        <div class="ktopic-title-cover">
          <?php
			echo $this->getTopicLink ( $this->topic, null, null, KunenaHtmlParser::stripBBCode ( $this->topic->first_post_message, 500), 'ktopic-title km' );
			if ($this->topic->getUserTopic()->favorite) {
				echo $this->getIcon ( 'kfavoritestar', JText::_('COM_KUNENA_FAVORITE') );
			}
			if ($this->me->exists() && $this->topic->getUserTopic()->posts) {
				echo $this->getIcon ( 'ktopicmy', JText::_('COM_KUNENA_MYPOSTS') );
			}
			if ($this->topic->unread) {
				echo $this->getTopicLink ( $this->topic, 'unread', '<sup dir="ltr" class="knewchar">(' . $this->topic->unread . ' ' . JText::_('COM_KUNENA_A_GEN_NEWCHAR') . ')</sup>' );
			}
			?>
        </div>
        <div class="ktopic-details-kcategory pull-left">
          <?php if (!isset($this->category) || $this->category->id != $this->topic->getCategory()->id) : ?>
          <span class="ktopic-category"> <?php echo JText::sprintf('COM_KUNENA_CATEGORY_X', $this->getCategoryLink ( $this->topic->getCategory() ) ) ?></span>
          <?php endif; ?>
          <span class="ktopic-by ks"><?php echo JText::_('COM_KUNENA_BY') . ' ' . $this->topic->getFirstPostAuthor()->getLink() ?></span> </div>
        <div class="ktopic-details-kcategory" style="clear: both;">
          <?php if ($this->pages > 1) : ?>
          <ul class="pagination">
            <li><?php echo $this->GetTopicLink ( $this->topic, 0, 1 ) ?></li>
            <?php if ($this->pages > 4) : $startPage = $this->pages - 3; ?>
            <li class="more">...</li>
            <?php else: $startPage = 1; endif;
			for($hopPage = $startPage; $hopPage < $this->pages; $hopPage ++) : ?>
            <li><?php echo $this->getTopicLink ( $this->topic, $hopPage, $hopPage+1 ) ?></li>
            <?php endfor; ?>
          </ul>
          <?php endif; ?>
        </div>
        <?php if (!empty($this->keywords)) : ?>
        <div class="ktopic-keywords"> <?php echo JText::sprintf('COM_KUNENA_TOPIC_TAGS', $this->escape($this->keywords)) ?> </div>
        <?php endif; ?>
      </div>
    </div>
    <div class="clearfix"></div>
  </td>
  <td class="span2"><span class="kcat-topics-number"><?php echo $this->formatLargeNumber ( max(0,$this->topic->getTotal()-1) ); ?></span> <span class="kcat-topics"><?php echo JText::_('COM_KUNENA_GEN_REPLIES') ?></span> <br />
    <span class="kcat-replies-number"><?php echo $this->formatLargeNumber ( max(0,$this->topic->getTotal()-1) ); ?></span> <span class="kcat-replies"><?php echo JText::_('COM_KUNENA_GEN_HITS');?> </span> </td>
  <td></td>
  <td class="span2">
    <div class="klatest-post-info pull-right img-polaroid">
      <?php if (!empty($this->topic->avatar)) : ?>
      <span class="ktopic-latest-post-avatar hidden-phone"> <?php echo $this->topic->getLastPostAuthor()->getLink( $this->topic->avatar ) ?></span>
      <?php endif; ?>
    </div>
  </td>
  <td class="span2">
    <div class="klatest-post-info pull-right"> <span class="ktopic-latest-post">
      <?php
			echo $this->getTopicLink ( $this->topic, 'last', JText::_('COM_KUNENA_GEN_LAST_POST') ); ?>
      <br />
      <?php
			echo ' ' . JText::_('COM_KUNENA_BY') . ' ' . $this->topic->getLastPostAuthor()->getLink();
			?>
      </span> </div>
    <div class="clearfix"></div>
  </td>
  <?php if (!empty($this->topicActions)) : ?>
  <td class="kcol-mid ktopicmoderation" width="5%">
    <div
			class="pull-right">
      <input class="kcheck" type="checkbox"
				name="topics[<?php echo $this->topic->id?>]" value="1" />
    </div>
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
