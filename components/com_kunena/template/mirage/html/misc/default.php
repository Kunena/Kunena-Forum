<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Misc
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="box-module">
	<div class="block-wrapper box-color box-border box-border_radius box-shadow">
		<div class="section block">
			<div class="headerbox-wrapper box-full">
				<div class="header">
					<h2 class="header"><?php echo $this->escape($this->header); ?></h2>
				</div>
			</div>
			<div class="detailsbox-wrapper">
				<div class="detailsbox>">
					<div class="content">
						<?php
						if ($this->format == 'html') :
							echo $this->body;
						elseif ($this->format == 'text') :
							echo $this->escape($this->body);
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