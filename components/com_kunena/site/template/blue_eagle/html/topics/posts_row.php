<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage Topics
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

// Disable caching
$this->cache = false;

// Show one topic row
?>
<tr class="<?php echo $this->getTopicClass('k', 'row') ?>">
	<td class="kcol-first kcol-ktopicicon">
		<?php echo $this->getTopicLink ( $this->topic, 'unread', $this->topic->getIcon() ) ?>
	</td>

	<td class="kcol-mid ktopictittle">
	<?php
// FIXME:
		/*if ($this->message->attachments) {
			echo $this->getIcon ( 'ktopicattach', JText::_('COM_KUNENA_ATTACH') );
		}*/
	?>
		<div class="ktopic-title-cover">
			<?php echo $this->getTopicLink ( $this->topic, $this->message, KunenaHtmlParser::parseText ($this->message->subject, 30), KunenaHtmlParser::stripBBCode ($this->message->message, 500), 'ktopic-title km' ) ?>
		</div>
	</td>

	<td class="kcol-mid ktopictittle">
		<div class="ktopic-title-cover">
			<?php
			echo $this->getTopicLink ( $this->topic, null, null, KunenaHtmlParser::stripBBCode ( $this->topic->first_post_message, 500), 'ktopic-title km' );
			if ($this->topic->getUserTopic()->favorite) {
				echo $this->getIcon ( 'kfavoritestar', JText::_('COM_KUNENA_FAVORITE') );
			}
			if ($this->topic->unread) {
				echo $this->getTopicLink ( $this->topic, 'unread', '<sup dir="ltr" class="knewchar">(' . $this->topic->unread . ' ' . JText::_('COM_KUNENA_A_GEN_NEWCHAR') . ')</sup>' );
			}
			if ($this->topic->locked != 0) {
				echo $this->getIcon ( 'ktopiclocked', JText::_('COM_KUNENA_LOCKED_TOPIC') );
			}
			?>
		</div>
		<div class="ks">
			<span class="ktopic-category">
				<?php echo JText::sprintf('COM_KUNENA_CATEGORY_X', $this->getCategoryLink ( $this->topic->getCategory() ) ) ?>
			</span>
		</div>
	</td>
	<td class="kcol-mid kcol-ktopiclastpost">
		<div class="klatest-post-info">
			<?php
			if ($this->config->avataroncat > 0) :
				$profile = KunenaFactory::getUser((int)$this->message->userid);
				$useravatar = $profile->getAvatarImage('klist-avatar', 'list');
				if ($useravatar) :
			?>
			<span class="ktopic-latest-post-avatar">
			<?php echo $this->message->getAuthor()->getLink( $useravatar ) ?>
			</span>
			<?php
				endif;
			endif;
			?>
			<span class="ktopic-posted-time" title="<?php echo KunenaDate::getInstance($this->message->time)->toKunena('config_post_dateformat_hover'); ?>">
				<?php echo JText::_('COM_KUNENA_POSTED_AT') . ' ' . KunenaDate::getInstance($this->message->time)->toKunena('config_post_dateformat'); ?>&nbsp;
			</span>

			<?php if ($this->message->userid) : ?>
			<br />
			<span class="ktopic-by"><?php echo JText::_('COM_KUNENA_BY') . ' ' . $this->message->getAuthor()->getLink(); ?></span>
			<?php endif; ?>
		</div>
	</td>

<?php if (!empty($this->postActions)) : ?>
	<td class="kcol-mid ktopicmoderation"><input class ="kcheck" type="checkbox" name="posts[<?php echo $this->message->id?>]" value="1" /></td>
<?php endif; ?>
</tr>
<?php if ($this->module) : ?>
<tr>
	<td class="ktopicmodule" colspan="<?php echo empty($this->postActions) ? 5 : 6 ?>"><?php echo $this->module; ?></td>
</tr>
<?php endif; ?>
