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

$do 			= JRequest::getVar("do", "");
$id 			= intval(JRequest::getVar("id", ""));
$catid 			= JRequest::getInt('catid', 0);
CKunenaPolls::call_javascript_vote();
if ($do == 'vote')
{
	CKunenaPolls::call_javascript_vote();
	$dataspollresult = CKunenaPolls::get_poll_data($id);
	?>
<div>
            <?php
            if (file_exists(KUNENA_ABSTMPLTPATH . '/pathway.php'))
            {
                require_once (KUNENA_ABSTMPLTPATH . '/pathway.php');
            }
            else
            {
                require_once (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'pathway.php');
            }
            ?>
  </div>
<div class="k_bt_cvr1">
<div class="k_bt_cvr2">
<div class="k_bt_cvr3">
<div class="k_bt_cvr4">
<div class="k_bt_cvr5">
    <table class = "kblocktable" id = "kpoll" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
        <thead>
            <tr>
                <th align="left">
                    <div class = "ktitle_cover km">
                        <span class = "ktitle kl"><?php echo JText::_('COM_KUNENA_POLL_NAME'); ?> <?php echo CKunenaTools::parseText ( $dataspollresult[0]->title ); ?></span>
                    </div>

                    <img id = "BoxSwitch_polls__polls_tbody" class = "hideshow" src = "<?php echo KUNENA_URLIMAGESPATH . 'shrink.gif' ; ?>" alt = ""/>
                </th>
            </tr>
        </thead>
        <tbody id = "polls_tbody">
                <tr class = "ksectiontableentry2">
                    <td class = "td-1 km" align="left">
                        <div class = "polldesc">
                        <div id="poll_text_help"></div>
                        <form id="kpoll_form_vote" method="post" action="<?php echo CKunenaLink::GetJsonURL('pollvote'); ?>">
                        <fieldset>
                        <legend style="font-size: 14px;"><?php echo JText::_('COM_KUNENA_POLL_OPTIONS'); ?></legend>
                        <ul>
	<?php
    for ($i=0; $i < sizeof($dataspollresult);$i++)
    {
       echo "<li><input class=\"kpoll_boxvote\" type=\"radio\" name=\"kpollradio\" id=\"radio_name".$i."\" value=\"".$dataspollresult[$i]->id."\" />".CKunenaTools::parseText ( $dataspollresult[$i]->text )."</li>";
    }
       ?>

    	</ul>
    	<input type="hidden" name="kpoll_id" value="<?php echo $id; ?>">
    	</fieldset>
		<div class="poll_center" id="poll_buttons">
       <input id="k_poll_button_vote" type="submit" value="<?php echo JText::_('COM_KUNENA_POLL_BUTTON_VOTE'); ?>" />
       <?php
       echo '	'.CKunenaLink::GetThreadLink('view',$catid,$id,kunena_htmlspecialchars ( stripslashes ( JText::_('COM_KUNENA_POLL_NAME_URL_RESULT') ) ), kunena_htmlspecialchars ( stripslashes ( JText::_('COM_KUNENA_POLL_NAME_URL_RESULT') ) ), 'follow');
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
    <?php
}
elseif ($do == 'changevote')
{

	CKunenaPolls::call_javascript_vote();
	$dataspollresult = CKunenaPolls::get_poll_data($id);
	//Remove one vote to the user concerned and remove one vote in option
	$id_last_vote = CKunenaPolls::get_last_vote_id($kunena_my->id,$id);
	CKunenaPolls::change_vote($kunena_my->id,$id,$id_last_vote);
	?>
 	<div>
            <?php
            if (file_exists(KUNENA_ABSTMPLTPATH . '/pathway.php'))
            {
                require_once (KUNENA_ABSTMPLTPATH . '/pathway.php');
            }
            else
            {
                require_once (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'pathway.php');
            }
            ?>
  </div>
<div class="k_bt_cvr1">
<div class="k_bt_cvr2">
<div class="k_bt_cvr3">
<div class="k_bt_cvr4">
<div class="k_bt_cvr5">
    <table class = "kblocktable" id = "kpoll" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
        <thead>
            <tr>
                <th align="left">
                    <div class = "ktitle_cover km">
                        <span class = "ktitle kl"><?php echo JText::_('COM_KUNENA_POLL_NAME'); ?> <?php echo CKunenaTools::parseText ( $dataspollresult[0]->title ); ?></span>
                    </div>

                    <img id = "BoxSwitch_polls__polls_tbody" class = "hideshow" src = "<?php echo KUNENA_URLIMAGESPATH . 'shrink.gif' ; ?>" alt = ""/>
                </th>
            </tr>
        </thead>
        <tbody id = "polls_tbody">
                <tr class = "ksectiontableentry2">
                    <td class = "td-1 km" align="left">
                        <div class = "polldesc">
                        <div id="poll_text_help"></div>
                        <form id="kpoll_form_vote" method="post" action="<?php echo CKunenaLink::GetJsonURL('pollchangevote'); ?>">
                        <fieldset><legend style="font-size: 14px;"><?php JText::_('COM_KUNENA_POLL_OPTIONS'); ?></legend>
                        <ul>
	<?php
    for ($i=0; $i < sizeof($dataspollresult);$i++)
    {
    	if($dataspollresult[$i]->id == $id_last_vote){
       		echo "<li><input class=\"kpoll_boxvote\" type=\"radio\" name=\"kpollradio\" id=\"radio_name".$i."\" value=\"".$dataspollresult[$i]->id."\" checked />".CKunenaTools::parseText ( $dataspollresult[$i]->text )."</li>";
    	}else {
			echo "<li><input class=\"kpoll_boxvote\" type=\"radio\" name=\"kpollradio\" id=\"radio_name".$i."\" value=\"".$dataspollresult[$i]->id."\" />".CKunenaTools::parseText ( $dataspollresult[$i]->text )."</li>";
    	}
    }
       ?>
       	</ul>
    	<input type="hidden" name="kpoll_id" value="<?php echo $id; ?>">
    	</fieldset>
		<div class="poll_center" id="poll_buttons">
       <input id="k_poll_button_vote" type="submit" value="<?php echo JText::_('COM_KUNENA_POLL_BUTTON_VOTE'); ?>" />

       <?php
		echo '	'.CKunenaLink::GetThreadLink('view',$catid,$id,kunena_htmlspecialchars ( stripslashes ( JText::_('COM_KUNENA_POLL_NAME_URL_RESULT') ) ), kunena_htmlspecialchars ( stripslashes ( JText::_('COM_KUNENA_POLL_NAME_URL_RESULT') ) ), 'follow');
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
<?php
}
?>