<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined( '_JEXEC' ) or die();
?>

<div class="kblock kpathway">
	<div class="kcontainer" id="pathway_tbody">
		<div class="ksectionbody">
			<div class = "kforum-pathway">
				<div class="path-element-first"><?php echo array_shift($this->path) ?></div>
				<?php foreach($this->path as $element) : ?>
				<div class="path-element"><?php echo $element ?></div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>
