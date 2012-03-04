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
<div class="box-module">
	<div class="box-wrapper">
		<div class="block">
			<div class="headerbox-wrapper box-full">
				<div class="header fl">
					<h2 class="header link-header2"><?php echo $this->escape($this->header); ?></h2>
				</div>
			</div>
			<div  class="detailsbox-wrapper">
				<div class="detailsbox">
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