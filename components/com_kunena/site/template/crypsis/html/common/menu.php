<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>

<div class="navbar">
	<div class="navbar-inner">
		<div class="visible-desktop visible-tablet visible-large"> <?php echo $this->getMenu() ?>
			<?php  if ($this->me->exists()) {
						include_once (KPATH_SITE.'/template/crypsis/html/common/logout_menu.php');
					} else {
						include_once (KPATH_SITE.'/template/crypsis/html/common/login_menu.php');
					}
			?>
		</div>
		<div class="hidden-desktop hidden-large">
		<?php  if ($this->me->exists()) {
					include_once (KPATH_SITE.'/template/crypsis/html/common/mobile_menu_logout.php');
				} else {
					include_once (KPATH_SITE.'/template/crypsis/html/common/mobile_menu_login.php');
				}
		?>
		</div>
	</div>
</div>