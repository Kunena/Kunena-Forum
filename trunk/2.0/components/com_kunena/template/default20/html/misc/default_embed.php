<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
		<div class="ksection">
			<h2 class="kheader"><?php echo $this->escape($this->header); ?></h2>
			<div class=kdetailsbox>
				<div class="kcontent">
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