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

if(!empty($this->thankyou)):
?>
<div class="kmessage-thankyou">
	<?php echo JText::_('COM_KUNENA_THANKYOU').': ';
	foreach($this->thankyou as $k=>$w){
		if($k === 0){
			echo $w;
		}elseif ( $k === 9){ //9 because deafult $limit is on 10 in sql query TODO make it in backend adjustable? then put in here variable
			echo ', '.$w.' ...';
		}else{
			echo ', '.$w;
		}
	}?>
</div>
<?php
	endif;
?>