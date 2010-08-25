<?php
/**
 * @version $Id: menu.php 3076 2010-07-19 01:39:17Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();
?>

<?php if ($this->params->get('topheaderShow') == '1') : ?>
<div class="headerbar">
	<div>
		<div class="tk-logo">
		<?php if (JDocumentHTML::countModules ( 'kunena_header' )) { ?>
			<?php CKunenaTools::showModulePosition( 'kunena_header' ); ?>
		<?php } else { ?>
			<img src="components/com_kunena/template/Evolution/images/kunena.logo.png" alt="logo" />
			<span class="tk-version">1.6</span>
			<span class="tk-template-name">EVOLUTION</span>
		<?php } ?>
		</div>
		<div id="tk-topmenu">
			<?php CKunenaTools::showModulePosition( 'kunena_top' ); ?>
		</div>
	</div>
</div>
<?php endif ?>
<?php if ($this->params->get('kunenamenuShow') == '1') : ?>
<div id="ktop">
	<?php if (JDocumentHTML::countModules ( 'kunena_menu' )) : ?>
		<!-- Kunena Module Position: kunena_menu -->
		<div id="ktopmenu">
			<div id="ktab"><?php CKunenaTools::displayMenu() ?></div>
		</div>
		<!-- /Kunena Module Position: kunena_menu -->
	<?php endif; ?>
</div>
<?php endif ?>