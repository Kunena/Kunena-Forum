<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

if($this->thankyou):
?>
<div class="kmessage-thankyou">
	<?php echo $this->thankyou ?>
</div>
<?php
	endif;
?>