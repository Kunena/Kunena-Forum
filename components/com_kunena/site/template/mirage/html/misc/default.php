<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Misc
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="kmodule misc-default">
	<div class="kbox-wrapper kbox-full">
		<div class="misc-default-kbox kbox kbox-full kbox-color kbox-border kbox-border_radius kbox-border_radius-vchild kbox-shadow kbox-animate">
			<div class="headerbox-wrapper kbox-full">
				<div class="header">
					<h2 class="header"><?php echo $this->header ?></h2>
				</div>
			</div>
			<div class="detailsbox-wrapper innerspacer kbox-full">
				<div class="detailsbox>">
					<div class="content">
						<?php echo $this->body ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

