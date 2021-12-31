<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsisb4
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

?>

<?php if (($rss = $this->getRSS()) !== null)
:
	?>
	<footer class="float-right large-kicon"><?php echo $this->getRSS(); ?></footer>
<?php endif; ?>

<?php if (($time = $this->getTime()) !== null)
:
	?>
	<footer class="center">
		<?php echo Text::sprintf('COM_KUNENA_VIEW_COMMON_FOOTER_TIME', $time); ?>
	</footer>
<?php endif;
