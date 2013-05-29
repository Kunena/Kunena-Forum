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

// Disable caching
$this->cache = false;

if ($this->spacing) : ?>
	<tr>
		<td class="kcontenttablespacer" colspan="<?php echo empty($this->topicActions) ? 5 : 6 ?>">&nbsp;</td>
	</tr>
<?php
endif;
echo KunenaLayout::factory('Topic/Row')
	->set('topic', $this->topic)
	->set('checkbox', !empty($this->topicActions))
	->setLayout('table');
?>
<!-- Module position -->
<?php if ($this->module) : ?>
	<tr>
		<td class="ktopicmodule" colspan="<?php echo empty($this->topicActions) ? 5 : 6 ?>"><?php echo $this->module; ?></td>
	</tr>
<?php endif; ?>
