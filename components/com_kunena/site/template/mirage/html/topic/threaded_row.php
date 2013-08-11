<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<li class="topic_threaded_tree-row kbox-hover kbox-hover_list-row kbox-full">
	<dl class="list-unstyled">
		<dd class="topic_threaded_tree-post">
			<?php foreach($this->message->indent as $indent) : ?>
				<span class="ktree ktree-<?php echo $indent ?>  kbox-full">
					<?php switch ($indent) :
						case 'root' :
						?>
							<span class="root-halfbottom_vertical_line"></span>
							<span class="root-circle"></span>
						<?php break;
						case 'edge' : ?>
							<span class="edge-vertical_line"></span>
						<?php break;
						case 'crossedge' : ?>
							<span class="edge-vertical_line"></span>
							<span class="edge-halfright_horizontal_line"></span>
						<?php break;
							case 'lastedge' : ?>
							<span class=""></span>
						<?php break;
						case 'leaf' : ?>
							<span class=""></span>
							<?php
							break;
					endswitch; ?>
				</span>
			<?php endforeach; ?>
			<?php if ($this->message->id == $this->state->get('item.mesid')) : ?>
				<?php echo $this->escape($this->message->subject) ?>
			<?php else : ?>
				<?php echo $this->getTopicLink($this->topic, $this->message) ?>
			<?php endif; ?>
		</dd>
		<dd class="topic_threaded_tree-author"><?php echo $this->message->getAuthor()->getLink() ?></dd>
		<dd class="topic_threaded_tree-time" title="<?php echo KunenaDate::getInstance($this->message->time)->toKunena('config_post_dateformat_hover') ?>">
			<?php echo KunenaDate::getInstance($this->message->time)->toKunena('config_post_dateformat') ?>
		</dd>
	</dl>
</li>
