<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Topic
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\Route\KunenaRoute;

$this->addScript('poll.js');

$polllifespan = '';

if ($this->show_title && $this->poll->polltimetolive != '1000-01-01 00:00:00') {
    if ($this->intervalTimeToLive->format('%R%a') >= 1) {
        $polllifespan = '<span style="font-size: 18px;"> (' . Text::sprintf('COM_KUNENA_POLL_RUNS_UNTILL', $this->poll->polltimetolive) . ')</span>';
    } else {
        $polllifespan = '<span style="font-size: 18px;"> (' . Text::sprintf('COM_KUNENA_POLL_WAS_ENDED', $this->poll->polltimetolive) . ')</span>';
    }
}
?>

<?php if ($this->show_title) :
    ?>
    <button class="btn btn-outline-primary border float-end" type="button" data-bs-toggle="collapse"
            data-bs-target="#poll-results"
            aria-expanded="false"
            aria-controls="poll-results">
        &times;
    </button>
    <h2>
        <?php echo Text::_('COM_KUNENA_POLL_NAME') . ' ' . KunenaParser::parseText($this->poll->title) . $polllifespan; ?>
    </h2>
<?php endif; ?>

<div class="" id="poll-results" <?php echo $this->show_title ? '' : 'style="display:none;"'; ?>>
    <table class="table table-striped table-bordered table-condensed">

        <?php
        foreach ($this->poll->getOptions() as $option) :
            $percentage = round(($option->votes * 100) / max($this->poll->getTotal(), 1), 1);
            ?>
            <tr>
                <td>
                    <?php echo KunenaParser::parseText($option->text); ?>
                </td>
                <td class="col-md-8">
                    <div class="progress progress-striped">
                        <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $percentage; ?>"
                             aria-valuemin="0" aria-valuemax="100"
                             style="height:30px;width:<?php echo $percentage; ?>%;"></div>
                    </div>
                </td>
                <td>
                    <?php
                    if (isset($option->votes) && $option->votes > 0) {
                        echo $option->votes;
                    } else {
                        echo Text::_('COM_KUNENA_POLL_NO_VOTE');
                    }
                    ?>
                </td>
                <td>
                    <?php echo $percentage; ?>%
                </td>
            </tr>
        <?php endforeach; ?>

        <tfoot>
        <tr>
            <td colspan="4">
                <?php
                echo Text::_('COM_KUNENA_POLL_VOTERS_TOTAL') . " <b>" . $this->usercount . "</b> ";

                if (!empty($this->users_voted_list)) :
                echo ' ( ' . implode(', ', $this->users_voted_list) . ' <span style="display: none;" id="kpoll-moreusers-span">, ' . implode(', ', $this->users_voted_morelist) . ' </span>) '; ?>
                    <?php
                    if ($this->usercount > '5') :
                        ?>
                        <a href="#kpoll-moreusers-span" class="link-show-extra-users link-show-more" id="kpoll-moreusers"><?php echo Text::_('COM_KUNENA_POLLUSERS_MORE') ?></a>
                        <a href="#kpoll-lessusers-span" class="link-show-extra-users" id="kpoll-lessusers" style="display: none;"><?php echo Text::_('COM_KUNENA_POLLUSERS_LESS') ?></a>                                                
                    <?php endif;
                endif; ?>
            </td>
        </tr>
        <?php
        $guest     = !$this->me->exists();
        $voteLink  = !$guest && $this->show_title && $this->topic->isAuthorised('poll.vote') && $this->topic->isAuthorised('reply');
        $voteLater = !$voteLink && $this->show_title && $this->waitToChangeVote;
        $moderator = $this->me->isModerator($this->category);

        if ($guest || $voteLink || $voteLater || $moderator) :
            ?>
        <tr>
            <td colspan="4">
                <?php if ($guest) : ?>
                    <?php echo Text::_('COM_KUNENA_POLL_NOT_LOGGED'); ?>
                <?php elseif ($voteLink) : ?>
                    <a href="<?php echo KunenaRoute::_("index.php?option=com_kunena&view=topic&layout=vote&catid={$this->category->id}&id={$this->topic->id}"); ?>"
                       class="kpoll-vote-link">
                        <?php echo $this->canChangeVote ? Text::_('COM_KUNENA_POLL_BUTTON_CHANGEVOTE') : Text::_('COM_KUNENA_POLL_BUTTON_VOTE'); ?>
                    </a>
                <?php elseif ($voteLater) : ?>
                    <span class="kpoll-change-vote-later"><?php echo Text::_('COM_KUNENA_POLL_CHANGE_VOTE_LATER'); ?></span>
                <?php endif; ?>

                <?php if ($moderator) :
                    ?>
                <button type="button" class="btn btn-outline-primary border" command="show-modal" commandfor="resetVotes">
                    <?php echo Text::_('COM_KUNENA_TOPIC_VOTE_RESET'); ?>
                </button>
                <dialog id="resetVotes" class="kunena-dialog" aria-labelledby="resetVotesLabel" closedby="any">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title" id="resetVotesLabel">
                                <?php echo Text::_('COM_KUNENA_TOPIC_MODAL_LABEL_VOTE_RESET'); ?>
                            </h3>
                            <button type="button" class="btn-close" command="close" commandfor="resetVotes"
                                    aria-label="<?php echo Text::_('JLIB_HTML_BEHAVIOR_CLOSE'); ?>"></button>
                        </div>
                        <div class="modal-body">
                            <p><?php echo Text::_('COM_KUNENA_TOPIC_MODAL_DESC_VOTE_RESET'); ?></p>
                        </div>
                        <div class="modal-footer">
                            <?php // The safe choice has the focus: the votes stay. ?>
                            <button type="button" class="btn btn-outline-primary border" command="close" commandfor="resetVotes" autofocus>
                                <?php echo Text::_('COM_KUNENA_TOPIC_MODAL_LABEL_CLOSE_RESETVOTE'); ?>
                            </button>
                            <a href="<?php echo KunenaRoute::_("index.php?option=com_kunena&view=topic&catid={$this->category->id}&id={$this->topic->id}&pollid={$this->poll->id}&task=resetvotes&" . Session::getFormToken() . '=1') ?>"
                               class="btn btn-outline-primary">
                                <?php echo Text::_('COM_KUNENA_TOPIC_MODAL_LABEL_CONFIRM_RESETVOTE'); ?>
                            </a>
                        </div>
                    </div>
                </dialog>
                <?php endif; ?>
            </td>
        </tr>
        <?php endif; ?>
        </tfoot>
    </table>
</div>
<div class="clearfix"></div>
