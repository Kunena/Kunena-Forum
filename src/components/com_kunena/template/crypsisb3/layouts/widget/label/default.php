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

$icon = KunenaFactory::getTemplate()->getTopicLabel($this->topic);
$this->ktemplate = KunenaFactory::getTemplate();
$topicicontype   = $this->ktemplate->params->get('topicicontype');
$class = ' class="' . ' label label-' . $icon->labeltype . '"';
if ($topicicontype == 'B3')
{

	$icons = '<span class="glyphicon glyphicon-' . $icon->b3 . '" aria-hidden="true"></span>';
}
elseif ($topicicontype == 'fa')
{
	$icons = '<i class="fa fa-' . $icon->fa . '" aria-hidden="true"></i>';
}
else
{
	$icons = '';
}
?>
<span <?php echo $class; ?> >
	<?php if ($topicicontype !== 0) :?>
		<?php echo $icons ?>
	<?php endif; ?>
	<span class="sr-only"></span><?php echo JText::_($icon->name);?>
</span>
