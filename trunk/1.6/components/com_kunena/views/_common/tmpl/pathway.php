<?php
/**
 * @version		$Id: $
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;
?>
<!-- Pathway -->
<div class="fb_forum-pathway">
<div class="path-element-first"><a href="" title="" rel="follow">Kunena</a></div>
<?php foreach ($this->path as &$category): ?>
<div class="path-element"><?php echo JHtml::_('klink.categories', 'atag', $category->id, $this->escape($category->name), $this->escape($category->name)); ?></div>
<?php endforeach; ?>
<br />
<div class="path-element-last"><?php if (isset($this->messages[0])) echo $this->escape($this->messages[0]->subject); ?></div>
<div class="path-element-users">(X viewing)&nbsp;(X) Guest</div>
</div>
<!-- / Pathway -->