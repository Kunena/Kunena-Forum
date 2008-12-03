<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	(C) 2008 Kunena. All rights reserved, see COPYRIGHT.php
 * @license		GNU General Public License, see LICENSE.php
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die('Invalid Request.');

// Render the template header.
//echo $this->loadCommonTemplate('header');
?>
<h1><?php echo $this->category->title; ?></h1>
<p><?php echo $this->category->summary; ?></p>
<div><?php echo $this->category->description; ?></div>

<?php
if (count($this->category->children)) {
	echo $this->loadTemplate('children');
}
