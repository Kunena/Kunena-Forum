<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="kmodule">
	<div class="box-wrapper">
		<div class="kbox box-color box-border box-border_radius box-border_radius-child box-shadow">
			<div class="headerbox-wrapper box-full">
				<div class="header fl">
					<h2 class="header link-header2"><?php echo $this->escape($this->header); ?></h2>
				</div>
			</div>
			<div  class="detailsbox-wrapper">
				<div class="detailsbox box-full box-hover box-border box-border_radius box-shadow">
					<div class="kcontent">
					<?php
					if (!empty($this->html)) :
						echo $this->body;
					else :
						echo KunenaHtmlParser::parseBBCode($this->body);
					endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="spacer"></div>