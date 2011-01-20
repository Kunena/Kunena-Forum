<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined( '_JEXEC' ) or die();

// url of current page that user will be returned to after bulk operation
$kuri = JURI::getInstance ();
$Breturn = $kuri->toString ( array ('path', 'query', 'fragment' ) );
$this->app->setUserState( "com_kunena.ActionBulk", JRoute::_( $Breturn ) );
?>
<div class="kblock">
	<div class="kheader">
		<h2><span><?php if (!empty($this->header)) echo $this->escape($this->header); ?></span></h2>
	</div>

	<div class="kcontainer">
		<div class="kbody">
			<table class="kblocktable" id="kflattable">
			<?php if (!count ( $this->categories ) ) : ?>
			<tr class="krow2">
				<td class="kcol-first">
					<?php echo JText::_('COM_KUNENA_CATEGORY_SUBSCRIPTIONS_NONE') ?>
				</td>
			</tr>
			<?php
			else :
				foreach ($this->categories as $this->category) {
					echo $this->loadTemplate('row');
				}
			endif;
			?>
			</table>
		</div>
	</div>
</div>