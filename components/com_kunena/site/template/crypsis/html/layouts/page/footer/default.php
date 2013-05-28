<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Layouts
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<?php if (isset($this->rss)) : ?>
<div class="pull-right"><?php echo $this->rss; ?></div>
<div class="clearfix"></div>
<?php endif; ?>

<?php if (($time = $this->getTime()) !== null) : ?>
<div class="center"> <span><?php echo JText::sprintf('COM_KUNENA_VIEW_COMMON_FOOTER_TIME', $time) ?></span> </div>
<?php endif; ?>
