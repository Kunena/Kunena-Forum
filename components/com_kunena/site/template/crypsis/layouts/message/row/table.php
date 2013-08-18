<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Topics
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/** @var KunenaLayout $this */
/** @var KunenaForumMessage $this->message */
/** @var bool $this->checkbox */

/** @var KunenaForumMessage $message */
$message = $this->message;
$topic = $message->getTopic();
$config = KunenaFactory::getConfig();
$cols = empty($this->checkbox) ? 4 : 5;
?>
<tr>
	<td class="span3 hidden-phone"> <?php echo $this->getTopicLink ( $topic, 'unread', $topic->getIcon() ) ?> </td>
	<td>
		<?php
		// FIXME:
		/*if ($message->attachments) {
			echo $this->getIcon ( 'ktopicattach', JText::_('COM_KUNENA_ATTACH') );
		}*/
		?>
		<div>
			<?php echo $this->getTopicLink ( $topic, $message,
				KunenaHtmlParser::parseText($message->subject, 30),
				KunenaHtmlParser::stripBBCode ($message->message, 500)) ?>
		</div>
	</td>
	<td class="span3">
		<div>
			<?php
			echo $this->getTopicLink ( $topic, null, null, KunenaHtmlParser::stripBBCode ( $topic->first_post_message, 500), 'ktopic-title km' );
			if ($topic->getUserTopic()->favorite) {
				echo $this->getIcon ( 'kfavoritestar', JText::_('COM_KUNENA_FAVORITE') );
			}
			if ($topic->unread) {
				echo $this->getTopicLink ( $topic, 'unread', '<sup dir="ltr">(' . $topic->unread . ' ' . JText::_('COM_KUNENA_A_GEN_NEWCHAR') . ')</sup>' );
			}
			if ($topic->locked != 0) {
				echo $this->getIcon ( 'ktopiclocked', JText::_('COM_KUNENA_LOCKED_TOPIC') );
			}
			?>
		</div>
		<div>
			<?php echo JText::sprintf('COM_KUNENA_CATEGORY_X', $this->getCategoryLink ( $topic->getCategory() ) ) ?>
		</div>
	</td>
	<td class="span5">
		<div>
			<?php
			if ($config->avataroncat > 0) :
				$profile = KunenaFactory::getUser((int)$message->userid);
				$useravatar = $profile->getAvatarImage('klist-avatar', 'list');
				if ($useravatar) :
					?>
					<?php echo $message->getAuthor()->getLink( $useravatar ) ?>
				<?php
				endif;
			endif;
			?>
			<span title="<?php echo KunenaDate::getInstance($message->time)->toKunena('config_post_dateformat_hover'); ?>">
				<?php echo JText::_('COM_KUNENA_POSTED_AT') . ' ' . KunenaDate::getInstance($message->time)->toKunena('config_post_dateformat'); ?>
			</span>
			<?php if ($message->userid) : ?>
			<br />
			<span><?php echo JText::_('COM_KUNENA_BY') . ' ' . $message->getAuthor()->getLink(); ?></span>
			<?php endif; ?>
		</div>
	</td>
	<?php if (!empty($this->checkbox)) : ?>
	<td class="span1">
		<input class ="kcheck" type="checkbox" name="posts[<?php echo $message->id?>]" value="1" />
	</td>
	<?php endif; ?>
	<?php
	if (!empty($this->position))
		echo $this->subLayout('Page/Module')
			->set('position', $this->position)
			->set('cols', $cols)
			->setLayout('table_row');
	?>
</tr>

