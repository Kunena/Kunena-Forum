<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Category
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$cols = empty($this->topicActions) ? 5 : 6;
$position = 'kunena_topic_' . $this->position;

// Show one topic row
?>
<?php if ($this->spacing) : ?>
<tr>
  <td class="kcontenttablespacer" colspan="<?php echo $cols; ?>">&nbsp;</td>
</tr>
<?php
endif;

echo KunenaLayout::factory('Topic/Row')->set('topic', $this->topic)->set('checkbox', !empty($this->topicActions))
	->setLayout('table_icons');

echo KunenaLayout::factory('Page/Module')->set('position', $position)->set('cols', $cols)->setLayout('table_row');
?>