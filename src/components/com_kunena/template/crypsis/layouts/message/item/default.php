<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Message
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
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

$list = array();
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
	<span class="visible-phone"><?php echo JText::_('COM_KUNENA_BY') . ' ' . $message->getAuthor()->getLink();?></span>
</small>

<div class="badger-left badger-info">
	<div class="kmessage">
		<div class="mykmsg-header">
            <?php echo (!$isReply) ? $avatarname . ' ' . JText::_('COM_KUNENA_MESSAGE_CREATED') . ' ' . KunenaForumMessage::getInstance()->getsubstr($this->escape($message->subject), 0, $subjectlengthmessage) : $avatarname . ' ' . JText::_('COM_KUNENA_MESSAGE_REPLIED') . ' ' . KunenaForumMessage::getInstance()->getsubstr($this->escape($message->subject), 0, $subjectlengthmessage); ?>
        </div>

   	    <div class="kmsg">
		    <?php  if (!$this->me->userid && !$isReply) :
			    echo $message->displayField('message');
		    else:
			    echo (!$this->me->userid && $this->config->teaser) ? JText::_('COM_KUNENA_TEASER_TEXT') : $this->message->displayField('message');
		    endif;?>
	    </div>

	    <?php if ($signature) : ?>
		    <div class="ksig">
			    <hr>
			    <span class="ksignature"><?php echo $signature; ?></span>
		    </div>
	    <?php endif ?>
	</div>
</div>

<?php if ($this->config->reportmsg && $this->me->exists()) :
	echo KunenaLayout::factory('Widget/Button')
		->setProperties(array('url' => '#report'. $message->id .'', 'name' => 'report', 'scope' => 'message',
		                      'type' => 'user', 'id' => 'btn_report', 'normal' => '', 'icon' => KunenaIcons::reportname(),
		                      'modal' => 'modal', 'pullright' => 'pullright'));
	if ($this->me->isModerator($this->topic->getCategory()) || $this->config->user_report || !$this->config->user_report && $this->me->userid != $this->message->userid) : ?>
		<div id="report<?php echo $this->message->id; ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="false">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<?php echo $this->subRequest('Topic/Report')->set('id', $this->topic->id); ?>
			</div>
		</div>
	<?php endif; ?>
<?php endif; ?>

<?php if (!empty($attachments)) : ?>
	<div class="kattach">
		<h5> <?php echo JText::_('COM_KUNENA_ATTACHMENTS'); ?> </h5>
		<ul class="thumbnails">
			<?php foreach ($attachments as $attachment) : ?>
				<li class="span3 center">
					<div class="thumbnail"> <?php echo $attachment->getLayout()->render('thumbnail'); ?> <?php echo $attachment->getLayout()->render('textlink'); ?> </div>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
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
if ($message->modified_time) {
	$datehover = 'title="' . KunenaDate::getInstance($message->modified_time)->toKunena('config_post_dateformat_hover') . '"';
	$dateshown = KunenaDate::getInstance($message->modified_time)->toKunena('config_post_dateformat') . ' ';
} ?>
<div class="alert alert-info hidden-phone" <?php echo $datehover ?>>
	<?php echo JText::_('COM_KUNENA_EDITING_LASTEDIT') . ': ' . $dateshown . JText::_('COM_KUNENA_BY') . ' ' . $message->getModifier()->getLink(null, null, '', '', null, $this->category->id) . '.'; ?>
	<?php if ($message->modified_reason) { echo JText::_('COM_KUNENA_REASON') . ': ' . $this->escape($message->modified_reason); } ?>
</div>
<?php endif; ?>

<?php if (!empty($this->thankyou)) : ?>
<div class="kmessage-thankyou">
	<?php
	foreach($this->thankyou as $userid => $thank)
	{
		if (!empty($this->thankyou_delete[$userid]))
		{
			$list[] = $thank . ' <a title="' . JText::_('COM_KUNENA_BUTTON_THANKYOU_REMOVE_LONG') . '" href="'
						. $this->thankyou_delete[$userid] . '">' . KunenaIcons::delete() . '</a>';
		}
		else
		{
			$list[] = $thank;
		}
	}

	echo JText::_('COM_KUNENA_THANKYOU') . ': ' . implode(', ', $list) . ' ';
	if ($this->more_thankyou) { echo JText::sprintf('COM_KUNENA_THANKYOU_MORE_USERS', $this->more_thankyou); }
	?>
</div>
<?php endif;
