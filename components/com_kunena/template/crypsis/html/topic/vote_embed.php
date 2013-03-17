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
?>

<div>
  <div> <span class="ktoggler"><a class="ktoggler close" title="<?php echo JText::_('COM_KUNENA_TOGGLER_COLLAPSE') ?>" rel="kpolls_tbody"></a></span>
    <h3 class="page-header"><span><?php echo JText::_('COM_KUNENA_POLL_NAME') .' '. KunenaHtmlParser::parseText ($this->poll->title); ?></span></h3>
  </div>
  <div class="kcontainer" id="kpolls_tbody">
    <div class="kbody">
      <table class="kblocktable" id="kpoll">
        <tr class="krow2">
          <td class="kcol-first km">
            <div class="kpolldesc">
<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" id="kpoll-form-vote" method="post">
                <input type="hidden" name="view" value="topic" />
                <input type="hidden" name="task" value="vote" />
                <input type="hidden" name="catid" value="<?php echo $this->topic->category_id ?>" />
                <input type="hidden" name="id" value="<?php echo $this->topic->id ?>" />
                <?php echo JHtml::_( 'form.token' ); ?>
                <fieldset>
                  <legend><?php echo JText::_('COM_KUNENA_POLL_OPTIONS'); ?></legend>
                  <ul>
                    <?php foreach ($this->poll->getOptions() as $key=>$poll_option) : ?>
                    <li>
                      <input class="kpoll-boxvote" type="radio" name="kpollradio" id="radio_name<?php echo intval($key) ?>" value="<?php echo intval($poll_option->id) ?>" <?php if ($this->voted && $this->voted->lastvote == $poll_option->id) echo 'checked="checked"' ?> />
                      <?php echo KunenaHtmlParser::parseText ($poll_option->text ) ?> </li>
                    <?php endforeach; ?>
                  </ul>
                </fieldset>
                <div id="kpoll-btns">
                  <input id="kpoll-button-vote" class="btn ks" type="submit" value="<?php echo $this->voted && $this->config->pollallowvoteone ? JText::_('COM_KUNENA_POLL_BUTTON_CHANGEVOTE') : JText::_('COM_KUNENA_POLL_BUTTON_VOTE'); ?>" />
                </div>
              </form>
            </div>
          </td>
        </tr>
      </table>
    </div>
  </div>
</div>
