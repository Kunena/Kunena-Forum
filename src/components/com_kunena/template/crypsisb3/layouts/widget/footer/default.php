<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Widget
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;
?>

<?php if (($rss = $this->getRSS()) !== null) : ?>
<div class="pull-right large-kicon"><?php echo $this->getRSS(); ?></div>
<div class="clearfix"></div>
<?php endif; ?>

<?php if (($time = $this->getTime()) !== null) : ?>
<div class="center">
	<?php echo JText::sprintf('COM_KUNENA_VIEW_COMMON_FOOTER_TIME', $time); ?>
</div>
<?php endif;
