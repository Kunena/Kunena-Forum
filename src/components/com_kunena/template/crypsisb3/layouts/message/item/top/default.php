<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Message
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

// @var KunenaForumMessage $message

$message = $this->message;
$isReply = $this->message->id != $this->topic->first_post_id;
$signature = $this->profile->getSignature();
$attachments = $message->getAttachments();
$attachs = $message->getNbAttachments();
$avatarname = $this->profile->getname();
$topicStarter = $this->topic->first_post_userid == $this->message->userid;
$config = KunenaConfig::getInstance();
$subjectlengthmessage = $this->ktemplate->params->get('SubjectLengthMessage', 20);

if ($config->ordering_system == 'mesid')
{
	$this->numLink = $this->location;
}
else
{
	$this->numLink = $message->replynum;
}
?>

<small class="text-muted pull-right">
	<?php if ($this->ipLink && !empty($this->message->ip)) : ?>
		<?php echo KunenaIcons::ip();?>
		<span class="ip"> <?php echo $this->ipLink; ?> </span>
	<?php endif;?>
	<?php echo KunenaIcons::clock();?>
	<?php echo $message->getTime()->toSpan('config_post_dateformat', 'config_post_dateformat_hover'); ?>
	<?php if ($message->modified_time) :?> - <?php echo KunenaIcons::edit() . ' ' . $message->getModifiedTime()->toSpan('config_post_dateformat', 'config_post_dateformat_hover'); endif;?>
	<a href="#<?php echo $this->message->id; ?>" id="<?php echo $this->message->id; ?>" rel="canonical">#<?php echo $this->numLink; ?></a>
	<span class="visible-xs"><?php echo JText::_('COM_KUNENA_BY') . ' ' . $message->getAuthor()->getLink();?></span>
</small>
<div class="clear-fix"></div>
<div class="horizontal-message">
	<div class="profile-horizontal-top">
		<?php echo $this->subLayout('User/Profile')->set('user', $this->profile)->setLayout('horizontal')->set('topic_starter', $topicStarter)->set('category_id', $this->category->id); ?>
	</div>
	<div class="horizontal-message-top badger-info <?php if ($message->getAuthor()->isModerator()) : ?> badger-moderator <?php endif;?>"
		data-badger="<?php echo (!$isReply) ? $this->escape($avatarname) . ' ' . JText::_('COM_KUNENA_MESSAGE_CREATED') . ' ' . KunenaForumMessage::getInstance()->getsubstr($this->escape($message->subject), 0, $subjectlengthmessage) : $this->escape($avatarname) . ' ' . JText::_('COM_KUNENA_MESSAGE_REPLIED') . ' ' . KunenaForumMessage::getInstance()->getsubstr($this->escape($message->subject), 0, $subjectlengthmessage); ?>">
		<div class="kmessage">
			<div class="horizontal-message-text">
				<div class="kmsg"> <?php echo $message->displayField('message'); ?> </div>
			</div>
			<?php if ($signature) : ?>
				<div class="ksig">
					<hr>
					<span class="ksignature"><?php echo $signature; ?></span>
				</div>
			<?php endif ?>
			<?php if ($this->config->reportmsg && $this->me->exists()) :
				if ($this->me->isModerator($this->topic->getCategory()) || $this->config->user_report || !$this->config->user_report && $this->me->userid != $this->message->userid) : ?>
					<div id="report<?php echo $this->message->id; ?>" class="modal fade" tabindex="-1" role="dialog">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
									<?php echo $this->subRequest('Topic/Report')->set('id', $this->topic->id); ?>
								</div>
							</div>
						</div>
					</div>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php if (!empty($attachments)) : ?>
	<div class="kattach">
		<h5> <?php echo JText::_('COM_KUNENA_ATTACHMENTS'); ?> </h5>
		<ul class="thumbnails">
			<?php foreach ($attachments as $attachment) : ?>
				<li class="col-md-3 center">
					<div class="thumbnail">
						<?php echo $attachment->getLayout()->render('thumbnail'); ?>
						<?php echo $attachment->getLayout()->render('textlink'); ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<div class="clearfix"></div>
<?php elseif ($attachs->total > 0  && !$this->me->exists()) :

	if ($attachs->image > 0 && !$this->config->showimgforguest)
	{
		if ($attachs->image > 1)
		{
			echo KunenaLayout::factory('BBCode/Image')->set('title', JText::_('COM_KUNENA_SHOWIMGFORGUEST_HIDEIMG_MULTIPLES'))->setLayout('unauthorised');
		}
		else
		{
			echo KunenaLayout::factory('BBCode/Image')->set('title', JText::_('COM_KUNENA_SHOWIMGFORGUEST_HIDEIMG_SIMPLE'))->setLayout('unauthorised');
		}
	}

	if ($attachs->file > 0 && !$this->config->showfileforguest)
	{
		if ($attachs->file > 1)
		{
			echo KunenaLayout::factory('BBCode/Image')->set('title', JText::_('COM_KUNENA_SHOWIMGFORGUEST_HIDEFILE_MULTIPLES'))->setLayout('unauthorised');
		}
		else
		{
			echo KunenaLayout::factory('BBCode/Image')->set('title', JText::_('COM_KUNENA_SHOWIMGFORGUEST_HIDEFILE_SIMPLE'))->setLayout('unauthorised');
		}
	}
endif; ?>
<?php if ($message->modified_by && $this->config->editmarkup) :
$dateshown = $datehover = '';

if ($message->modified_time)
{
	$datehover = 'title="' . KunenaDate::getInstance($message->modified_time)->toKunena('config_post_dateformat_hover') . '"';
	$dateshown = KunenaDate::getInstance($message->modified_time)->toKunena('config_post_dateformat') . ' ';
} ?>
<div class="alert alert-info hidden-xs" <?php echo $datehover ?>>
	<?php echo JText::_('COM_KUNENA_EDITING_LASTEDIT') . ': ' . $dateshown . JText::_('COM_KUNENA_BY') . ' ' . $message->getModifier()->getLink(null, null, '', '', null, $this->category->id) . '.'; ?>
	<?php
	if ($message->modified_reason)
	{
		echo JText::_('COM_KUNENA_REASON') . ': ' . $this->escape($message->modified_reason);
	} ?>
</div>
<?php endif; ?>

<?php if (!empty($this->thankyou)) : ?>
<div class="kmessage-thankyou">
	<?php
	echo JText::_('COM_KUNENA_THANKYOU') . ': ' . implode(', ', $this->thankyou) . ' ';

	if ($this->more_thankyou)
	{
		echo JText::sprintf('COM_KUNENA_THANKYOU_MORE_USERS', $this->more_thankyou);
	}
	?>
</div>
<?php endif;
