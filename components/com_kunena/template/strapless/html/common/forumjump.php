<?php
/**
 * Kunena Component
 * @package Kunena.Template.Strapless
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');
?>

<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" id="jumpto" name="jumpto" method="post" target="_self">
  <input type="hidden" name="view" value="category" />
  <input type="hidden" name="task" value="jump" />
  <span class="kright"> <?php echo $this->categorylist; ?> </span>
</form>
