<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="kmodule">
	<div class="kbox-wrapper kbox-full">
		<div class="common-default-kbox kbox kbox-full kbox-color kbox-border kbox-border_radius kbox-border_radius-vchild kbox-shadow kbox-animate">
			<div class="headerbox-wrapper kbox-full">
				<div class="header fl">
					<h2 class="header link-header2"><?php echo $this->header ?></h2>
				</div>
			</div>
			<div  class="detailsbox-wrapper innerspacer kbox-full">
				<div class="detailsbox kbox-full kbox-hover kbox-border kbox-border_radius kbox-shadow">
					<div class="kcontent">
						<?php echo $this->body ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

