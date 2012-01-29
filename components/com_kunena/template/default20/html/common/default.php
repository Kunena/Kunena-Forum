<?php
/**
 * Kunena Component
 * @package Kunena.Template.Default20
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
		<div class="ksection">
			<h2 class="kheader"><?php echo $this->escape($this->header); ?></h2>
			<div class="kdetailsbox">
				<div class="kcontent">
				<?php
				if (!empty($this->html)) :
					echo $this->body;
				else :
					echo KunenaHtmlParser::parseBBCode($this->body);
				endif; ?>
				</div>
				<div class="clr"></div>
			</div>
		</div>