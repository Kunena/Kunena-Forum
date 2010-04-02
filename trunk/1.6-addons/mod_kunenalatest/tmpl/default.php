<?php
/**
* @version		$Id
* @package		klatestpost
* @copyright	(c) 2010 Kunena Team, All rights reserved
* @license		GNU/GPL
*/

 // no direct access
defined('_JEXEC') or die('Restricted access');

$document =& JFactory::getDocument();
$document->addStyleSheet(JURI::root().'modules/mod_klatestpost/tmpl/klatest.css');
 ?>
<div class="klatest<?php echo $params->get( 'moduleclass_sfx' ) ?>">

<?php
//print_r($klistpost);
$config =& JFactory::getConfig(); 
$tzoffset = $config->getValue('config.offset');

//vérifier que $klistpost n'est pas vide

foreach($klistpost as $item) {
//construct the date 
$date = JFactory::getDate($item->time, $tzoffset);
$date = $date->toFormat($params->get( 'dateformat' ));

	?>
  <div style="display:block;float:left;width: 45%;">	
  <?php 
  if ($params->get( 'sh_avatar' )) { 
    if (!empty($item->avatar)) { 
      echo CKunenaLink::GetHrefLink(JRoute::_("index.php?option=com_kunena&amp;func=profile&amp;Itemid=" . KUNENA_COMPONENT_ITEMID . "&amp;userid=".$item->userid), '<img src="'.JURI::root(). 'images/fbfiles/avatars/s_' .$item->avatar.'" alt="'.$item->name.'" title="'.$item->name.'">', '', $rel='nofollow', ''); 
    } else { 
      echo CKunenaLink::GetHrefLink(JRoute::_("index.php?option=com_kunena&amp;func=profile&amp;Itemid=" . KUNENA_COMPONENT_ITEMID . "&amp;userid=".$item->userid), '<img src="'.JURI::root(). 'images/fbfiles/avatars/s_nophoto.jpg" alt="'.$item->name.'" title="'.$item->name.'">', '', $rel='nofollow', ''); 
    } 
  }
  if ($params->get( 'sh_rank' )) { 
    if (!empty($item->posts)) {?>
    <div style="display:block;font-size: x-small;"><?php echo JText::_('USER_POSTS'); ?>&nbsp; <?php echo $item->posts; ?></div>
  <?php 
    } else { ?>
    <div style="display:block;font-size: x-small;"><?php echo JText::_('NO_POSTS'); ?></div>
   <?php }
  } ?>     
  </div>  
  <div id="klatestnav">  
  <ul>
  <li>
  <?php  
  if ($params->get( 'sh_sticky' )) {
    if ( $item->ordering ) {
      echo '<img src="' .JURI::root(). 'modules/mod_klatestpost/tmpl/sticky.png" alt="'.JText::_('KLATESTPOST_STICKY_TOPIC').'" title="'.JText::_('KLATESTPOST_STICKY_TOPIC').'" />';
    }
  }
  echo $klink->GetThreadLink ( 'view', $item->catid, $item->id, substr(htmlspecialchars ( stripslashes( $item->subject ) ),'0',$params->get( 'titlelength' )), substr(htmlspecialchars ( stripslashes ( $item->messagetext ) ),'0',$params->get( 'messagelength' )), 'follow'); 
  if ($item->unread) { 
    echo $params->get( 'unreadindicator' ); 
  } 
  if ($params->get( 'sh_favorite' )) {
    if ($item->favcount ) {
				if ($item->myfavorite) {
					echo '<img class="favoritestar" src="' . KUNENA_URLICONSPATH . 'favoritestar.png"  alt="' . JText::_('KLATESTPOST_FAVORITE') . '" title="' . JText::_('KLATESTPOST_FAVORITE') . '" />';
				} else {
					echo '<img class="favoritestar-grey" src="' . KUNENA_URLICONSPATH . 'favoritestar-grey.png"  alt="' . JText::_('KLATESTPOST_FAVORITE') . '" title="' . JText::_('KLATESTPOST_FAVORITE') . '" />';
				}
			}
  }
  if ($params->get( 'sh_locked' )) {
    if ($item->locked) {
      echo '<img src="' . KUNENA_URLICONSPATH . 'lock_sm.png"  alt="' . JText::_('KLATESTPOST_LOCKED_TOPIC') . '" title="' . JText::_('KLATESTPOST_GEN_LOCKED_TOPIC') . '" />'; 
    }
  }   
  ?></li><li>
	<?php echo JText::_('POSTED_AT'); ?>&nbsp; <?php echo $date; ?></li>
	<li><?php echo CKunenaLink::GetCategoryLink ( 'showcat', $item->catid, stripslashes ( $item->catname ) ) ; ?></li>
	<li><?php 
  if (!$params->get( 'sh_avatar' )) { 
    echo JText::_('LAST_POST_BY'); ?>&nbsp; <?php echo CKunenaLink::GetHrefLink(JRoute::_("index.php?option=com_kunena&amp;func=profile&amp;Itemid=" . KUNENA_COMPONENT_ITEMID . "&amp;userid=".$item->userid), $item->name, '', $rel='nofollow', '');
	} ?></li>  
	</ul>
	</div>	
<?php } //end foreach ?><br />
<?php echo CKunenaLink::GetShowLatestLink(JText::_('MOD_KLATESTPOST_MORE_LINK')); ?>
</div>