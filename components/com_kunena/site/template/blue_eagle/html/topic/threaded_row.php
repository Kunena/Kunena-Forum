<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<tr class="krow<?php echo $this->mmm % 2 ?> <?php echo $this->class ?>">
	<td class="kcol-first kmsgsubject kmsgsubject<?php echo $this->escape($this->msgsuffix) ?>">
		<span class="<?php echo implode('"></span><span class="ktree ktree-', $this->message->indent)?>"></span>
		<?php if ($this->message->id == $this->state->get('item.mesid')) : ?>
		<?php echo $this->escape($this->message->subject) ?>
		<?php else : ?>
		<?php echo $this->getTopicLink($this->topic, $this->message, $this->escape($this->message->subject)) ?>
		<?php endif; ?>
	</td>
	<td class="kcol-mid kprofile kprofile-list"><?php echo $this->message->getAuthor()->getLink() ?></td>
	<td class="kcol-last kmsgdate kmsgdate-list" title="<?php echo KunenaDate::getInstance($this->message->time)->toKunena('config_post_dateformat_hover') ?>">
		<?php echo KunenaDate::getInstance($this->message->time)->toKunena('config_post_dateformat') ?>
	</td>
</tr>
