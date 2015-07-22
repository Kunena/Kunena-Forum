<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$item = array_shift($this->pathway);

if(empty($this->breadcrumbs_count))
{
    $this->breadcrumbs_count = 1;
}
?>

<?php if ($item) : ?>
<div class="kblock kpathway breadcrumbs-<?php echo $this->breadcrumbs_count; ?>">
	<div class="kcontainer">
		<div class="ksectionbody">
			<div class = "kforum-pathway">
				<div class="path-element-first"><a href="<?php echo $item->link ?>" rel="nofollow"><?php echo $item->name ?></a></div>
				<?php foreach($this->pathway as $item) : ?>
				<div class="path-element"><a href="<?php echo $item->link ?>" rel="nofollow"><?php echo $item->name ?></a></div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>
<?php $this->breadcrumbs_count++; ?>
<?php endif; ?>
