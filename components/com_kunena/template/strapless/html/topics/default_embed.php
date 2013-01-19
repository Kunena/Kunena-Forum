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
?>

<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" name="ktopicsform" id="ktopicsform">
  <input type="hidden" name="view" value="topics" />
  <?php echo JHtml::_( 'form.token' ); ?>
  <div class="well">
    <div class="pagination pull-right" style="margin:0 25px 0 0 ;"><?php echo $this->getPagination (5); ?></div>
    <div class="clearfix"></div>
    <?php if (!empty($this->topicActions)) : ?>
    <span class="kcheckbox pull-right">
    <input class="kcheckall" type="checkbox" name="toggle" value="" />
    </span>
    <?php endif; ?>
    <h3 class="page-header"><span><?php echo $this->escape($this->headerText); ?></span></h3>
    <div class="clearfix"></div>
    <div class="row-fluid column-row">
      <div class="span12 column-item">
        <table class="table table-striped table-condensed">
          <?php if (empty ( $this->topics ) && empty ( $this->subcategories )) : ?>
          <tr class="krow2">
            <td class="kcol-first"><?php echo JText::_('COM_KUNENA_VIEW_NO_POSTS') ?></td>
          </tr>
          <?php else : ?>
          <?php $this->displayRows (); ?>
          <?php  if ( !empty($this->topicActions) || !empty($this->embedded) ) : ?>
          <tr class="krow1">
            <td colspan="<?php echo empty($this->topicActions) ? 5 : 6 ?>" class="kcol krowmoderation">
              <?php if (!empty($this->moreUri)) echo JHtml::_('kunenaforum.link', $this->moreUri, JText::_('COM_KUNENA_MORE'), null, null, 'follow'); ?>
              <?php if (!empty($this->topicActions)) : ?>
              <?php echo JHtml::_('select.genericlist', $this->topicActions, 'task', 'class="inputbox kchecktask" size="1"', 'value', 'text', 0, 'kchecktask'); ?>
              <?php if ($this->actionMove) :
								$options = array (JHtml::_ ( 'select.option', '0', JText::_('COM_KUNENA_BULK_CHOOSE_DESTINATION') ));
								echo JHtml::_('kunenaforum.categorylist', 'target', 0, $options, array(), 'class="inputbox fbs" size="1" disabled="disabled"', 'value', 'text', 0, 'kchecktarget');
								endif;?>
              <input type="submit" name="kcheckgo" class="btn" value="<?php echo JText::_('COM_KUNENA_GO') ?>" />
              <?php endif; ?>
            </td>
          </tr>
          <?php endif; ?>
          <?php endif; ?>
        </table>
      </div>
      <div class="pagination pull-right" style="margin:10px 25px 10px 0 ;"><?php echo $this->getPagination (5); ?></div>
      <div class="clearfix"></div>
    </div>
  </div>
</form>
