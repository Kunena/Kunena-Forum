<?php
/**
 * Kunena Component
 * @package Kunena.Template.Default
 * @subpackage Category
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<?php if (!empty ( $this->newTopicHtml ) || !empty ( $this->markReadHtml ) || !empty ( $this->subscribeCatHtml )) : ?>
<td class="klist-actions-forum">
	<div class="kmessage-buttons-row"><?php echo $this->newTopicHtml .' ' . $this->markReadHtml . ' ' . $this->subscribeCatHtml; ?></div>
</td>
<?php endif; ?>
