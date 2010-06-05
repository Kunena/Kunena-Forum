<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2010 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/
// Dont allow direct linking
defined( '_JEXEC' ) or die();

$this->call_javascript_vote();
$dataspollresult = $this->get_poll_data($this->id);
$json_action = 'pollvote';
if ( $this->changevote ) {
	// Get the $id of the last vote
	$id_last_vote = $this->get_last_vote_id($this->my->id,$this->id);
	// Reset last vote
	$this->change_vote($this->my->id,$this->id,$id_last_vote);
	$json_action = 'pollchangevote';
}
?>
<div>
<?php CKunenaTools::loadTemplate ( '/pathway.php' ); ?>
</div>
<div class="k-bt-cvr1">
<div class="k_bt_cvr2">
<div class="k_bt_cvr3">
<div class="k_bt_cvr4">
<div class="k_bt_cvr5">
    <table class = "kblocktable" id = "kpoll" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
        <thead>
            <tr>
                <th align="left">
                    <div class = "ktitle-cover km">
                        <span class = "ktitle kl"><?php echo JText::_('COM_KUNENA_POLL_NAME'); ?> <?php echo KunenaParser::parseText ( $dataspollresult[0]->title ); ?></span>
                    </div>

                    <img id = "BoxSwitch_polls__polls_tbody" class = "hideshow" src = "<?php echo KUNENA_URLIMAGESPATH . 'shrink.gif' ; ?>" alt = ""/>
                </th>
            </tr>
        </thead>
        <tbody id = "polls_tbody">
                <tr class = "ksectiontableentry2">
                    <td class = "td-1 km" align="left">
                        <div class = "kpolldesc">
                        <div id="kpoll-text-help"></div>
                        <form id="kpoll-form-vote" method="post" action="<?php echo CKunenaLink::GetJsonURL($json_action); ?>">
                        <fieldset><legend style="font-size: 14px;"><?php echo JText::_('COM_KUNENA_POLL_OPTIONS'); ?></legend>
                        <ul>
	<?php
    for ($i=0; $i < sizeof($dataspollresult);$i++)
    {
    	if ( $this->changevote ) {
    		if($dataspollresult[$i]->id == $id_last_vote){
       			echo "<li><input class=\"kpoll-boxvote\" type=\"radio\" name=\"kpollradio\" id=\"radio_name".$i."\" value=\"".$dataspollresult[$i]->id."\" checked />".KunenaParser::parseText ( $dataspollresult[$i]->text )."</li>";
    		}else {
				echo "<li><input class=\"kpoll-boxvote\" type=\"radio\" name=\"kpollradio\" id=\"radio_name".$i."\" value=\"".$dataspollresult[$i]->id."\" />".KunenaParser::parseText ( $dataspollresult[$i]->text )."</li>";
    		}
    	} else {
			echo "<li><input class=\"kpoll-boxvote\" type=\"radio\" name=\"kpollradio\" id=\"radio_name".$i."\" value=\"".$dataspollresult[$i]->id."\" />".KunenaParser::parseText ( $dataspollresult[$i]->text )."</li>";
    	}
    }
       ?>
       	</ul>
    	<input type="hidden" name="kpoll-id" value="<?php echo $this->id; ?>">
    	</fieldset>
		<div class="kpoll-center" id="kpoll-buttons">
       <input id="kpoll-button-vote" type="submit" value="<?php echo JText::_('COM_KUNENA_POLL_BUTTON_VOTE'); ?>" />

       <?php
		echo '	'.CKunenaLink::GetThreadLink('view',$this->catid,$this->id,JText::_('COM_KUNENA_POLL_NAME_URL_RESULT'), JText::_('COM_KUNENA_POLL_NAME_URL_RESULT'), 'follow');
		?>
		</div>
		</form>
       </div>
                	  </td>
                 </tr>
        </tbody>
    </table>
    </div>
</div>
</div>
</div>
</div>

