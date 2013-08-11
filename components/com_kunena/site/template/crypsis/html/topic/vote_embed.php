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

<div>
  <div> <span><a class="ktoggler close" title="<?php echo JText::_('COM_KUNENA_TOGGLER_COLLAPSE') ?>" rel="kpolls_tbody"></a></span>
    <h3><span><?php echo JText::_('COM_KUNENA_POLL_NAME') .' '. KunenaHtmlParser::parseText ($this->poll->title); ?></span></h3>
  </div>
  <div id="kpolls_tbody">
    <div>
      <table id="kpoll">
        <tr class="krow2">
          <td>
            <div>
              <form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" id="kpoll-form-vote" method="post">
                <input type="hidden" name="view" value="topic" />
                <input type="hidden" name="task" value="vote" />
                <input type="hidden" name="catid" value="<?php echo $this->topic->category_id ?>" />
                <input type="hidden" name="id" value="<?php echo $this->topic->id ?>" />
                <?php echo JHtml::_( 'form.token' ); ?>
                <fieldset>
                  <ul>
                    <?php foreach ($this->poll->getOptions() as $key=>$poll_option) : ?>
                    <li>
                      <input class="kpoll-boxvote" type="radio" name="kpollradio" id="radio_name<?php echo intval($key) ?>" value="<?php echo intval($poll_option->id) ?>" <?php if ($this->voted && $this->voted->lastvote == $poll_option->id) echo 'checked="checked"' ?> />
                      <?php echo KunenaHtmlParser::parseText ($poll_option->text ) ?> </li>
                    <?php endforeach; ?>
                  </ul>
                </fieldset>
                <div id="kpoll-btns">
                  <input id="kpoll-button-vote" class="btn btn-success" type="submit" value="<?php echo $this->voted && $this->config->pollallowvoteone ? JText::_('COM_KUNENA_POLL_BUTTON_CHANGEVOTE') : JText::_('COM_KUNENA_POLL_BUTTON_VOTE'); ?>" />
                </div>
                <br/>
              </form>
            </div>
          </td>
        </tr>
      </table>
    </div>
  </div>
</div>
