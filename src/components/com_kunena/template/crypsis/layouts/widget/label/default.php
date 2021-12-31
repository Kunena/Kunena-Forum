<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsis
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;

$this->ktemplate = KunenaFactory::getTemplate();
$icon            = $this->ktemplate->getTopicLabel($this->topic);
$topicicontype   = $this->ktemplate->params->get('topicicontype');
$class           = ' class="' . ' label label-' . $icon->labeltype . '"';

if ($topicicontype == 'B2')
{
	$icons = '<span class="icon icon-' . $icon->b2 . '" aria-hidden="true"></span>';
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
	<?php
	if ($topicicontype !== 0)
		:
		?>
		<?php echo $icons ?>
	<?php endif; ?>
	<span class="sr-only"></span><?php echo Text::_($icon->name); ?>
</span>
