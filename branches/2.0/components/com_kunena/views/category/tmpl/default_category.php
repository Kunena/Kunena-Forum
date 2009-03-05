<?php
/**
 * @version		$Id: default.php 5 2008-11-22 07:05:46Z louis $
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	(C) 2008 Kunena. All rights reserved, see COPYRIGHT.php
 * @license		GNU General Public License, see LICENSE.php
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die('Invalid Request.');
?>
<h3><a href="<?php echo JRoute::_('index.php?option=com_kunena&view=category&cat_id='.$this->child->id.':'.$this->child->path); ?>"><?php echo $this->child->title; ?></a></h3>
<p><?php echo $this->child->summary; ?></p>
