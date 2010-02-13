<?php // @version $Id: default.php 11917 2009-05-29 19:37:05Z ian $
defined('_JEXEC') or die('Restricted access');
$cparams = JComponentHelper::getParams ('com_media');
?>

<?php if ($this->params->get('show_page_title',1)) : ?>
<h1 class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
	<?php echo $this->escape($this->params->get('page_title')); ?>
</h1>
<?php endif; ?>

<?php if ($this->params->def('show_description', 1) || $this->params->def('show_description_image', 1)) : ?>
<div class="contentdescription<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
	<?php if ($this->params->get('show_description_image') && $this->section->image) : ?>
	<img src="<?php echo $this->baseurl . '/' . $this->escape($cparams->get('image_path')).'/'.$this->escape($this->section->image); ?>" class="image_<?php echo $this->escape($this->section->image_position); ?>" />
	<?php endif; ?>

	<?php if ($this->params->get('show_description') && $this->section->description) :
		echo $this->section->description;
	endif; ?>

	<?php if ($this->params->get('show_description_image') && $this->section->image) : ?>
	<div class="wrap_image">&nbsp;</div>
	<?php endif; ?>
</div>
<?php endif; ?>

<?php if ($this->params->def('show_categories', 1) && count($this->categories)) : ?>
<ul>
	<?php foreach ($this->categories as $category) :
		if (!$this->params->get('show_empty_categories') && !$category->numitems) :
			continue;
		endif; ?>
		<li>
			<a href="<?php echo $category->link; ?>" class="category"><?php echo $this->escape($category->title); ?></a>

			<?php if ($this->params->get('show_cat_num_articles')) : ?>
			<span class="small">
				( <?php if ($category->numitems==1) {
				echo $category->numitems ." ". JText::_( 'item' );	}
				else {
				echo $category->numitems ." ". JText::_( 'items' );} ?> )
			</span>
			<?php endif; ?>

			<?php if ($this->params->def('show_category_description', 1) && $category->description) : ?>
			<br />
			<?php echo $category->description; ?>
			<?php endif; ?>
	</li>
	<?php endforeach; ?>
</ul>
<?php endif;
