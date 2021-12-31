<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;

if (($this->getRSS()) !== null)
:
	?>
	<div class="pull-right large-kicon"><?php echo $this->getRSS(); ?></div>
	<div class="clearfix"></div>
<?php endif; ?>

	<?php if (($time = $this->getTime()) !== null)
	:
		?>
	<div class="d-flex justify-content-center">
		<?php echo Text::sprintf('COM_KUNENA_VIEW_COMMON_FOOTER_TIME', $time); ?>
	</div>
	<?php endif;
