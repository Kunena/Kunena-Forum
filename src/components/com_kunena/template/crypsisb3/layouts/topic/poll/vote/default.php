<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsisb3
 * @subpackage      Layout.Topic
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$this->addScript('assets/js/poll.js');

$polllifespan = '';
if (!empty($this->poll->polltimetolive))
{
	if (!$this->polllifespan)
	{
		$polllifespan = '<span style="font-size: 18px;"> (' . Text::sprintf('COM_KUNENA_POLL_RUNS_UNTILL', $this->poll->polltimetolive) . ')</span>';
	}
	else
	{
		$polllifespan = '<span style="font-size: 18px;"> (' . Text::sprintf('COM_KUNENA_POLL_WAS_ENDED', $this->poll->polltimetolive) . ')</span>';
	}
}
?>
	<div class="pull-right btn btn-default btn-small" data-toggle="collapse" data-target="#poll-vote">&times;</div>
	<h2>
		<?php echo Text::_('COM_KUNENA_POLL_NAME') . ' ' . KunenaHtmlParser::parseText($this->poll->title) . $polllifespan; ?>
	</h2>

	<div class="collapse in" id="poll-vote">
		<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic'); ?>" method="post">
			<input type="hidden" name="task" value="vote"/>
			<input type="hidden" name="catid" value="<?php echo $this->topic->category_id; ?>"/>
			<input type="hidden" name="id" value="<?php echo $this->topic->id; ?>"/>
			<?php echo HTMLHelper::_('form.token'); ?>

			<div class="well">
				<ul class="unstyled">

					<?php foreach ($this->poll->getOptions() as $key => $poll_option)
						:
						?>
						<li>
							<label>
								<input class="kpoll-boxvote" type="radio" name="kpollradio"
								       id="radio_name<?php echo (int) $key; ?>"
								       value="<?php echo (int) $poll_option->id; ?>"
									<?php
									if ($this->userhasvoted && $this->userhasvoted->lastvote == $poll_option->id)
									{
										echo 'checked="checked"';
									} ?> />
								<?php echo KunenaHtmlParser::parseText($poll_option->text); ?>
							</label>
						</li>
					<?php endforeach; ?>

				</ul>

				<input id="kpoll-button-vote" class="btn btn-success" type="submit"
				       value="<?php echo $this->userhasvoted && !$this->config->pollallowvoteone
					       ? Text::_('COM_KUNENA_POLL_BUTTON_CHANGEVOTE')
					       : Text::_('COM_KUNENA_POLL_BUTTON_VOTE'); ?>"/>
				<input id="kpoll_go_results" type="button" class="btn btn-success"
				       value="<?php echo Text::_('COM_KUNENA_POLL_BUTTON_VIEW_RESULTS') ?>"/>
				<input id="kpoll_hide_results" type="button" class="btn btn-success" style="display:none;"
				       value="<?php echo Text::_('COM_KUNENA_POLL_BUTTON_HIDE_RESULTS') ?>"/>
			</div>
		</form>
	</div>
<?php echo $this->subLayout('Topic/Poll/Results')->set('poll', $this->poll)->set('usercount', $this->usercount)->set('me', $this->me)->set('topic', $this->topic)->set('category', $this->category)->set('show_title', false);
