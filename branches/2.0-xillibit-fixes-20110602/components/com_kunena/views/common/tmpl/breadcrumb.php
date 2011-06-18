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

<?php if ($this->item) : ?>
<div class="kblock kpathway">
	<div class="kcontainer" id="pathway_tbody">
		<div class="ksectionbody">
			<div class = "kforum-pathway">
				<div class="path-element-first"><a href="<?php echo $this->item->link ?>"><?php echo $this->item->name ?></a></div>
				<?php foreach($this->pathway as $item) : ?>
				<div class="path-element"><a href="<?php echo $item->link ?>"><?php echo $item->name ?></a></div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>
<?php endif ?>
