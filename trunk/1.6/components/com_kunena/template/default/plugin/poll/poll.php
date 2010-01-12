<?php
/**
* @version $Id: poll.php 1395 2009-12-30 14:40:22Z xillibit $
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2009 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/
// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

$do 			= JRequest::getVar("do", "");
$id 			= intval(JRequest::getVar("id", ""));
$value_choosed	= JRequest::getInt('radio', '');
CKunenaPolls::call_javascript_vote();
if ($do == 'results')
{
    //Prevent spam from users
    CKunenaPolls::save_results($id,$kunena_my->id,$value_choosed);
}

elseif( $do == 'dbchangevote')
{
	CKunenaPolls::save_changevote($id,$kunena_my->id,$value_choosed);
}
elseif ($do == 'vote')
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
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr1">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr2">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr3">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr4">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr5">
    <table class = "fb_blocktable" id = "fb_poll" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
        <thead>
            <tr>
                <th align="left">
                    <div class = "fb_title_cover fbm">
                        <span class = "fb_title fbl"><?php echo _KUNENA_POLL_NAME; ?> <?php echo $dataspollresult[0]->title; ?></span>
                    </div>

                    <img id = "BoxSwitch_polls__polls_tbody" class = "hideshow" src = "<?php echo KUNENA_URLIMAGESPATH . 'shrink.gif' ; ?>" alt = ""/>
                </th>
            </tr>
        </thead>
        <tbody id = "polls_tbody">
                <tr class = "<?php echo KUNENA_BOARD_CLASS;?>sectiontableentry2">
                    <td class = "td-1 fbm" align="left">
                        <div class = "polldesc">
                        <div style="font-weight:bold;" id="poll_text_help"></div>
	<?php
	echo "<fieldset><legend style=\"font-size: 14px;\">"._KUNENA_POLL_OPTIONS."</legend><ul>";
    for ($i=0; $i < sizeof($dataspollresult);$i++)
    {
       echo "<li><input type=\"radio\" name=\"radio\" id=\"radio_name".$i."\" value=\"".$dataspollresult[$i]->id."\" />".$dataspollresult[$i]->text."</li>";
    }
       echo "</ul></fieldset>";
       $button_vote = "<input type=\"button\" value=\""._KUNENA_POLL_BUTTON_VOTE."\" onClick=\"javascript:ajax(".sizeof($dataspollresult).",".$id.",'results');\" />";
       echo "<div class=\"poll_center\" id=\"poll_buttons\">".$button_vote;
       echo '	'.CKunenaLink::GetThreadLink('view',$catid,$id,kunena_htmlspecialchars ( stripslashes ( _KUNENA_POLL_NAME_URL_RESULT ) ), kunena_htmlspecialchars ( stripslashes ( _KUNENA_POLL_NAME_URL_RESULT ) ), 'follow');
    ?>
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
	CKunenaPolls::change_vote($kunena_my->id,$id,$dataspollresult[0]->lastvote);
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
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr1">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr2">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr3">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr4">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr5">
    <table class = "fb_blocktable" id = "fb_poll" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
        <thead>
            <tr>
                <th align="left">
                    <div class = "fb_title_cover fbm">
                        <span class = "fb_title fbl"><?php echo _KUNENA_POLL_NAME; ?> <?php echo $dataspollresult[0]->title; ?></span>
                    </div>

                    <img id = "BoxSwitch_polls__polls_tbody" class = "hideshow" src = "<?php echo KUNENA_URLIMAGESPATH . 'shrink.gif' ; ?>" alt = ""/>
                </th>
            </tr>
        </thead>
        <tbody id = "polls_tbody">
                <tr class = "<?php echo KUNENA_BOARD_CLASS;?>sectiontableentry2">
                    <td class = "td-1 fbm" align="left">
                        <div class = "polldesc">
                        <div style="font-weight:bold;" id="poll_text_help"></div>
	<?php
	echo "<fieldset><legend style=\"font-size: 14px;\">"._KUNENA_POLL_OPTIONS."</legend><ul>";
    for ($i=0; $i < sizeof($dataspollresult);$i++)
    {
    	if($dataspollresult[$i]->id == $dataspollresult[$i]->lastvote){
       		echo "<li><input type=\"radio\" name=\"radio\" id=\"radio_name".$i."\" value=\"".$dataspollresult[$i]->id."\" checked />".$dataspollresult[$i]->text."</li>";
    	}else {
			echo "<li><input type=\"radio\" name=\"radio\" id=\"radio_name".$i."\" value=\"".$dataspollresult[$i]->id."\" />".$dataspollresult[$i]->text."</li>";
    	}
    }
       echo "</ul></fieldset>";
       $button_vote = "<input type=\"button\" value=\""._KUNENA_POLL_BUTTON_VOTE."\" onClick=\"javascript:ajax(".sizeof($dataspollresult).",".$id.",'dbchangevote');\" />";
       echo "<div class=\"poll_center\" id=\"poll_buttons\">".$button_vote;
		echo '	'.CKunenaLink::GetThreadLink('view',$catid,$id,kunena_htmlspecialchars ( stripslashes ( _KUNENA_POLL_NAME_URL_RESULT ) ), kunena_htmlspecialchars ( stripslashes ( _KUNENA_POLL_NAME_URL_RESULT ) ), 'follow');
		?>
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