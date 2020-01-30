<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
**/

namespace Kunena\Forum\Site;

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use function defined;

$this->ktemplate = \Kunena\Forum\Libraries\Factory\KunenaFactory::getTemplate();
$icon            = $this->ktemplate->getTopicLabel($this->topic);
$topicicontype   = $this->ktemplate->params->get('topicicontype');
$class           = ' class="' . ' badge badge-' . $icon->labeltype . '"';

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
	<?php
	if ($topicicontype !== 0)
		:
		?>
		<?php echo $icons ?>
	<?php endif; ?>
	<span class="sr-only"></span><?php echo Text::_($icon->name); ?>
</span>
