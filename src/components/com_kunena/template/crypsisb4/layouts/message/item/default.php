<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsisb4
 * @subpackage      Layout.Message
 *
 * @copyright       Copyright (C) 2008 - 2021 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$message              = $this->message;
$isReply              = $this->message->id != $this->topic->first_post_id;
$signature            = $this->profile->getSignature();
$attachments          = $message->getAttachments();
$attachs              = $message->getNbAttachments();
$config               = KunenaConfig::getInstance();
$subjectlengthmessage = $this->ktemplate->params->get('SubjectLengthMessage', 20);

if ($config->ordering_system == 'mesid')
{
	$this->numLink = $this->location;
}
else
{
	$this->numLink = $message->replynum;
}

$list = [];
?>

	<small class="text-muted float-right">
		<?php if ($this->ipLink && !empty($this->message->ip)) : ?>
			<?php echo KunenaIcons::ip(); ?>
			<span class="ip"> <?php echo $this->ipLink; ?> </span>
		<?php endif; ?>
		<?php echo KunenaIcons::clock(); ?>
		<?php echo $message->getTime()->toSpan('config_post_dateformat', 'config_post_dateformat_hover'); ?>
		<?php if ($message->modified_time) : ?> - <?php echo KunenaIcons::edit() . ' ' . $message->getModifiedTime()->toSpan('config_post_dateformat', 'config_post_dateformat_hover'); endif; ?>
		<a href="#<?php echo $this->message->id; ?>" id="<?php echo $this->message->id; ?>"
		   rel="canonical">#<?php echo $this->numLink; ?></a>
		<span class="visible-xs"><?php echo Text::_('COM_KUNENA_BY') . ' ' . $message->getAuthor()->getLink(); ?></span>
	</small>

	<div class="shadow-none p-4 mb-5 rounded">
		<div class="mykmsg-header">
			<?php
			$title   = KunenaForumMessage::getInstance()->getsubstr($this->escape($message->subject), 0, $subjectlengthmessage);
			$langstr = $isReply ? 'COM_KUNENA_MESSAGE_REPLIED_NEW' : 'COM_KUNENA_MESSAGE_CREATED_NEW';
			echo Text::sprintf($langstr, $message->getAuthor()->getLink(), $this->getTopicLink($this->message->getTopic(), $this->message, $this->message->displayField('subject'), null, KunenaTemplate::getInstance()->tooltips())); ?>
		</div>
		<div class="kmsg">
			<?php if (!$this->me->userid && !$isReply) :
				echo $message->displayField('message');
			else:
				echo (!$this->me->userid && $this->config->teaser) ? Text::_('COM_KUNENA_TEASER_TEXT') : $this->message->displayField('message');
			endif; ?>
		</div>
		<?php if ($signature) : ?>
			<div class="ksig">
				<hr>
				<span class="ksignature"><?php echo $signature; ?></span>
			</div>
		<?php endif ?>
	</div>
<?php if ($this->config->reportmsg && $this->me->exists()) : ?>
	<div class="report pb-5">
		<?php echo KunenaLayout::factory('Widget/Button')
			->setProperties(['url'   => '#report' . $message->id . '', 'name' => 'report', 'scope' => 'message',
			                 'type'  => 'user', 'id' => 'btn_report', 'normal' => '', 'icon' => KunenaIcons::reportname(),
			                 'modal' => 'modal', 'pullright' => 'pullright',]); ?>
	</div>
	<?php if ($this->me->isModerator($this->topic->getCategory()) || $this->config->user_report || !$this->config->user_report && $this->me->userid != $this->message->userid) : ?>
		<div id="report<?php echo $this->message->id; ?>" class="modal fade" tabindex="-1" role="dialog"
		     aria-hidden="true" data-backdrop="false" style="display: none;">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="reportModalLabel">
							<?php echo Text::_('COM_KUNENA_REPORT_TO_MODERATOR'); ?>
						</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<?php echo $this->subRequest('Topic/Report')->set('id', $this->topic->id); ?>
					</div>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
	<?php endif; ?>
<?php endif; ?>
<?php if (!empty($attachments)) : ?>
	<div class="cart pb-3 pd-3">
		<h5 class="card-header"> <?php echo Text::_('COM_KUNENA_ATTACHMENTS'); ?> </h5>
		<div class="card-body kattach">
			<ul class="thumbnails" style="list-style:none;">
				<?php foreach ($attachments as $attachment) :
					if (!$attachment->inline): ?>
						<?php if ($attachment->isAudio()) :
							echo $attachment->getLayout()->render('audio'); ?>
						<?php elseif ($attachment->isVideo()) :
							echo $attachment->getLayout()->render('video'); ?>
						<?php else : ?>
							<li class="col-md-3 text-center">
								<div class="thumbnail">
									<?php echo $attachment->getLayout()->render('thumbnail'); ?>
									<?php echo $attachment->getLayout()->render('textlink'); ?>
								</div>
							</li>
						<?php endif; ?>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
	<div class="clearfix"></div>
<?php elseif ($attachs->total > 0 && !$this->me->exists()) :

	if ($attachs->image > 0 && !$this->config->showimgforguest)
	{
		if ($attachs->image > 1)
		{
			echo KunenaLayout::factory('BBCode/Image')->set('title', Text::_('COM_KUNENA_SHOWIMGFORGUEST_HIDEIMG_MULTIPLES'))->setLayout('unauthorised');
		}
		else
		{
			echo KunenaLayout::factory('BBCode/Image')->set('title', Text::_('COM_KUNENA_SHOWIMGFORGUEST_HIDEIMG_SIMPLE'))->setLayout('unauthorised');
		}
	}

	if ($attachs->file > 0 && !$this->config->showfileforguest)
	{
		if ($attachs->file > 1)
		{
			echo KunenaLayout::factory('BBCode/Image')->set('title', Text::_('COM_KUNENA_SHOWIMGFORGUEST_HIDEFILE_MULTIPLES'))->setLayout('unauthorised');
		}
		else
		{
			echo KunenaLayout::factory('BBCode/Image')->set('title', Text::_('COM_KUNENA_SHOWIMGFORGUEST_HIDEFILE_SIMPLE'))->setLayout('unauthorised');
		}
	}
endif; ?>

<?php if ($message->modified_by && $this->config->editmarkup) :
	$dateshown = $datehover = '';

	if ($message->modified_time)
	{
		$datehover = 'title="' . KunenaDate::getInstance($message->modified_time)->toKunena('config_post_dateformat_hover') . '"';
		$dateshown = KunenaDate::getInstance($message->modified_time)->toKunena('config_post_dateformat') . ' ';
	}
	?>
	<div class="alert alert-info hidden-xs-down" <?php echo $datehover ?>>
		<?php echo Text::sprintf('COM_KUNENA_EDITING_LASTEDIT_ON_BY', $dateshown, $message->getModifier()->getLink(null, null, '', '', null, $this->category->id)); ?>
		<?php if ($message->modified_reason)
		{
			echo Text::_('COM_KUNENA_REASON') . ': ' . $this->escape($message->modified_reason);
		} ?>
	</div>
<?php endif; ?>

<?php if (!empty($this->thankyou)) : ?>
	<div class="kmessage-thankyou">
		<?php
		foreach ($this->thankyou as $userid => $thank)
		{
			if (!empty($this->thankyou_delete[$userid]))
			{
				$list[] = $thank . ' <a title="' . Text::_('COM_KUNENA_BUTTON_THANKYOU_REMOVE_LONG') . '" href="'
					. $this->thankyou_delete[$userid] . '">' . KunenaIcons::cancel() . '</a>';
			}
			else
			{
				$list[] = $thank;
			}
		}

		echo KunenaIcons::thumbsup() . Text::_('COM_KUNENA_THANKYOU') . ': ' . implode(', ', $list) . ' ';
		if ($this->more_thankyou)
		{
			echo Text::sprintf('COM_KUNENA_THANKYOU_MORE_USERS', $this->more_thankyou);
		}
		?>
	</div>
<?php endif;