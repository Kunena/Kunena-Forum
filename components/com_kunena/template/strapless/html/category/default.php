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
$this->displayBreadcrumb ();

?>
<?php $this->displayCategories () ?>
<?php if ($this->category->headerdesc) : ?>

<div class="well well-small">
  <div class="row-fluid column-row">
    <div class="span12 column-item"> <?php echo KunenaHtmlParser::parseBBCode ( $this->category->headerdesc ); ?> </div>
  </div>
</div>
<?php endif; ?>
<?php if (!$this->category->isSection()) : ?>
<div>
  <div class="pull-left"> <a id="forumtop"> </a> <a href="#forumbottom" rel="nofollow"><span class="divider"></span><i class="icon-arrow-down hasTooltip"></i></a>
    <?php $this->displayCategoryActions() ?>
  </div>
  <div class="clearfix"></div>
  <?php if ($this->total >1) : ?>
  <div class="pull-right" style="margin-top:-40px;"><?php echo $this->getPagination (5); // odd number here (# - 5) ?></div>
  <div class="clearfix"></div>
  <?php endif; ?>
</div>
<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" name="ktopicsform" id="ktopicsform">
  <input type="hidden" name="view" value="topics" />
  <?php echo JHtml::_( 'form.token' ); ?>
  <div class="well">
    <?php if (!empty($this->topicActions)) : ?>
    <span class="pull-right ">
    <input class="kcheckall" type="checkbox" name="toggle" value="" />
    </span>
    <?php endif; ?>
    <h2><span><?php echo $this->escape($this->headerText); ?></span></h2>
    <div class="clearfix"></div>
    <div class="row-fluid column-row">
      <div class="span12 column-item">
        <table class="table table-striped table-hover">
          <?php if (empty ( $this->topics ) && empty ( $this->subcategories )) : ?>
          <tr>
            <td class="kcol-first"><?php echo JText::_('COM_KUNENA_VIEW_NO_POSTS') ?></td>
          </tr>
          <?php else : ?>
          <?php $this->displayRows (); ?>
          <?php  if ( !empty($this->topicActions) || !empty($this->embedded) ) : ?>
          <!-- Bulk Actions -->
          <tr>
            <td colspan="<?php echo empty($this->topicActions) ? 5 : 6 ?>" >
              <?php if (!empty($this->moreUri)) echo JHtml::_('kunenaforum.link', $this->moreUri, JText::_('COM_KUNENA_MORE'), null, null, 'follow'); ?>
              <?php if (!empty($this->topicActions)) : ?>
              <?php echo JHtml::_('select.genericlist', $this->topicActions, 'task', 'class="inputbox kchecktask" size="1"', 'value', 'text', 0, 'kchecktask'); ?>
              <?php if ($this->actionMove) :
								$options = array (JHtml::_ ( 'select.option', '0', JText::_('COM_KUNENA_BULK_CHOOSE_DESTINATION') ));
								echo JHtml::_('kunenaforum.categorylist', 'target', 0, $options, array(), 'class="inputbox fbs" size="1" disabled="disabled"', 'value', 'text', 0, 'kchecktarget');
								?>
              <button class="btn" name="kcheckgo" type="submit"><?php echo JText::_('COM_KUNENA_GO') ?></button>
              <?php endif;?>
              <?php endif; ?>
            </td>
          </tr>
          <!-- /Bulk Actions -->
          <?php endif; ?>
          <?php endif; ?>
        </table>
      </div>
    </div>
  </div>
</form>
<div>
  <div class="pull-left"> <a id="forumbottom"> </a> <a href="#forumtop" rel="nofollow"><span class="divider"></span><i class="icon-arrow-up hasTooltip"></i></a>
    <?php $this->displayCategoryActions() ?>
  </div>
  <div class="clearfix"></div>
  <?php if ($this->total >1) : ?>
  <div class="pull-right" style="margin-top:-40px;"><?php echo $this->getPagination (5); // odd number here (# - 5) ?></div>
  <div class="clearfix"></div>
  <?php endif; ?>
</div>
<div class="kmoderatorslist-jump fltrt">
  <?php $this->displayForumJump (); ?>
</div>
<?php if (!empty ( $this->moderators ) ) : ?>
<?php
				$modslist = array();
				foreach ( $this->moderators as $moderator ) {
					$modslist[] = $moderator->getLink();
				}
				echo JText::_('COM_KUNENA_MODERATORS') . ': ' . implode(', ', $modslist);
			?>
<?php endif; ?>
<?php endif; ?>
