<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsis
 * @subpackage      Topic
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();
use Joomla\CMS\Language\Text;

$k = 0;
?>
<div class="pull-right">
	<div class="btn btn-small" data-toggle="collapse" data-target="#history">X</div>
</div>
<h2>
	<?php echo Text::_('COM_KUNENA_POST_TOPIC_HISTORY') ?>:
	<?php echo $this->escape($this->topic->subject) ?>
</h2>

<div id="history" class="collapse in">
	<p>
		<?php echo Text::_('COM_KUNENA_POST_TOPIC_HISTORY_MAX') . ' ' . $this->escape($this->config->historylimit) . ' ' . Text::_('COM_KUNENA_POST_TOPIC_HISTORY_LAST') ?>
	</p>
	<?php foreach ($this->history as $this->message)
		:
		?>

		<div class="row-fluid">
			<div class="span2 center">
				<ul class="unstyled center profilebox">
					<li>
						<strong><?php echo $this->message->getAuthor()->getLink(null, null, '', '', null, $this->topic->getcategory()->id) ?></strong>
					</li>
					<li>
						<?php
						$profile    = KunenaFactory::getUser(intval($this->message->userid));
						$useravatar = $profile->getAvatarImage(KunenaFactory::getTemplate()->params->get('avatarType'), 'profile');

						if ($useravatar)
							:
							echo $this->message->getAuthor()->getLink($useravatar, null, '', '', null, $this->topic->getcategory()->id);
						endif;
						?>
					</li>
				</ul>
			</div>
			<div class="span10">
				<small class="text-muted pull-right hidden-phone" style="margin-top:-5px;">
					<?php echo KunenaIcons::clock(); ?><?php echo $this->message->getTime()->toSpan('config_post_dateformat', 'config_post_dateformat_hover'); ?>
				</small>
				<div class="badger-left badger-info khistory">
					<div class="mykmsg-header">
						<?php echo $this->message->displayField('subject'); ?>
					</div>
					<div class="kmessage">
						<p class="kmsg"><?php echo KunenaHtmlParser::parseBBCode($this->message->message, $this) ?></p>
					</div>
					<?php
					$attachments = $this->message->getAttachments();

					if (!empty($attachments))
						:
						?>
						<div class="kattach">
							<h4><?php echo Text::_('COM_KUNENA_ATTACHMENTS'); ?></h4>
							<ul class="thumbnails">
								<?php foreach ($attachments as $attachment)
									:
									?>
									<li class="span4">
										<div class="thumbnail">
											<?php echo $attachment->getLayout()->render('thumbnail'); ?>
											<?php echo $attachment->getLayout()->render('textlink'); ?>
										</div>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
</div>
