<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="kblock kdefault">
	<div class="kheader">
		<h2><?php echo $this->escape($this->header); ?></h2>
	</div>
	<div class="kcontainer">
		<div class="kbody">
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