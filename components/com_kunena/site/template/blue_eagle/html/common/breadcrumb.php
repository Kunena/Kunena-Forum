<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$item = array_shift($this->pathway);
?>

<?php if ($item) : ?>
<div class="kblock kpathway">
	<div class="kcontainer">
		<div class="ksectionbody">
			<div class = "kforum-pathway">
				<div class="path-element-first"><a href="<?php echo $item->link ?>"><?php echo $item->name ?></a></div>
				<?php foreach($this->pathway as $item) : ?>
				<div class="path-element"><a href="<?php echo $item->link ?>"><?php echo $item->name ?></a></div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>
<?php endif ?>
