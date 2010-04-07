<?php
/**
 * @version $Id$
 * Kunena Discuss Plugin
 * @package Kunena Discuss
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/
defined( '_JEXEC' ) or die ( '' );
?>
<div class="kdiscuss-title"><?php echo JText::_('PLG_KUNENADISCUSS_POSTS') ?></div>
<?php
foreach ( $this->messages as $message ) {
	$this->displayMessage($message);
}
