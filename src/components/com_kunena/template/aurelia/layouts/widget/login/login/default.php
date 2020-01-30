<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Template.Aurelia
 * @subpackage  Layout.Widget
 *
 * @copyright   Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
**/

namespace Kunena\Forum\Site;

defined('_JEXEC') or die();

use function defined;

?>

<?php if ($this->plglogin)
:
	?>
	<div class="d-none d-lg-block">
		<?php
		if (\Kunena\Forum\Libraries\Factory\KunenaFactory::getTemplate()->params->get('displayDropdownMenu'))
	:
			?>
			<?php echo $this->setLayout('desktop'); ?>
		<?php endif; ?>
	</div>
	<div class="d-md-none">
		<?php if (\Kunena\Forum\Libraries\Factory\KunenaFactory::getTemplate()->params->get('displayDropdownMenu'))
	:
			?>
			<?php echo $this->setLayout('mobile'); ?>
		<?php endif; ?>
	</div>
<?php endif;
