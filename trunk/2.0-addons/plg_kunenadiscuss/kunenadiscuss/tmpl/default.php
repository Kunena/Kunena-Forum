<?php
/**
 * @version $Id: view.php 4062 2010-12-23 07:15:10Z severdia $
 * Kunena Discuss Plug-in
 * @package Kunena Discuss
 *
 * @Copyright (C) 2010-2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined( '_JEXEC' ) or die ( '' );
?>
<div class="kdiscuss-title"><?php echo CKunenaLink::GetThreadLink ( 'view', $this->topic->category_id, $this->topic->id, JText::_('PLG_KUNENADISCUSS_POSTS'), 'follow') ?></div>
<?php $this->displayMessages() ?>
<div class="kdiscuss-more"><?php echo CKunenaLink::GetThreadLink ( 'view', $this->topic->category_id, $this->topic->id, JText::_('COM_KUNENA_ANN_READMORE'), 'follow') ?></div>