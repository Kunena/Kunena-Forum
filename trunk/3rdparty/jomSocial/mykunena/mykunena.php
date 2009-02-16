<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_BASE .DS.'components' .DS.'com_community' .DS.'libraries' .DS.'core.php');

class plgCommunityKunena extends CApplications
{
	var $name 		= "My on Kunena";
	var $_name		= 'Kunena';
	var $_path		= '';
	var $_user		= '';
	var $_my		= '';


	function onProfileDisplay()
	{
		$config	=& CFactory::getConfig();

		if( !$config->get('enablegroups') )
		{
			return JText::_('Groups are disabled by site administrator');
		}


		$uri	= JURI::base();

		$document	=& JFactory::getDocument();
		// Attach CSS
		$css		= JURI::base() . 'plugins/community/kunena/style.css';
		$document->addStyleSheet($css);

		$groupsModel		=& CFactory::getModel( 'groups' );
		$avatarModel		=& CFactory::getModel( 'avatar' );

		$user		= CFactory::getActiveProfile();
		$userName	= $user->getDisplayName();

		$groups		= $groupsModel->getGroups( $user->id );

		$my	=& JFactory::getUser();

		$username = $this->params->get('username');
		$password = $this->params->get('password');

		$db =& JFactory::getDBO();
		// Get forum user info:
		$db->setQuery("SELECT  a.*, b.* FROM #__fb_users as a"
                        . "\n LEFT JOIN #__users as b on b.id=a.userid"
                        . "\n where a.userid=" . $user->id);

    	$userinfo = $db->loadObject();

		$db =& JFactory::getDBO();
		// Get forum user info:
		$db->setQuery("SELECT  a.*, b.* FROM #__fb_users as a"
                        . "\n LEFT JOIN #__users as b on b.id=a.userid"
                        . "\n where a.userid=" . $user->id);

    	$userinfo = $db->loadObject();

		ob_start();
 		if( $userinfo ){
 			$usr_info = 1;
	 		//print_r($userinfo);
			$maxPost = intval($userinfo->posts);

			// Get latest forum topics

			// Search only within allowed group
			$query = "select gid from #__users where id={$my->id}";
	        $db->setQuery($query);
	        $db->query();

	        $dse_groupid = $db->loadObjectList();

	        if (count($dse_groupid)) {
	            $group_id = $dse_groupid[0]->gid;
	        }
	        else {
	            $group_id = 0;
	        }

	        $maxCount = $this->params->get('count', 5);
			$query = "SELECT a.* , b.id as category, b.name as catname, c.hits AS 'threadhits'" . "\n FROM #__fb_messages AS a, " . "\n #__fb_categories AS b, #__fb_messages AS c, #__fb_messages_text AS d" . "\n WHERE a.catid = b.id"
	               . "\n AND a.thread = c.id" . "\n AND a.id = d.mesid" . "\n AND a.hold = 0 AND b.published = 1" . "\n AND a.userid={$user->id}" . "\n AND (b.pub_access<='$group_id') " . "\n ORDER BY time DESC" . "\n LIMIT 0, $maxCount";
	        $db->setQuery($query);

	        $items = $db->loadObjectList();
		}else{
			$usr_info = 0;
			$userId = "";
			$userName = "";
			$items = "";
		}

		$fbItemid = '&amp;Itemid='.$this->getItemid();

    	$cache =& JFactory::getCache('plgCommunityKunena');
    	$cache->setCaching($this->params->get('cache', 1));
		$callback = array('plgCommunityKunena', '_getKunenaHTML');
		$content = $cache->call($callback, $usr_info, $user->id, $userName, $items, $fbItemid);
		return $content;
	}

	function _getKunenaHTML($usr_info, $userId, $userName, $items, $fbItemid){
		ob_start();

 		if($usr_info){
 			if( !empty($items) ) {
				?>
				<div id="community-kunena-wrap">
				    <ul class="list">
				<?php
					foreach ($items as $item ){
						$fbURL 		= JRoute::_("index.php?option=com_kunena&amp;func=view".$fbItemid."&amp;catid=" . $item->catid . "&amp;id=" . $item->id . "#" . $item->id);
						$fbCatURL 	= JRoute::_("index.php?option=com_kunena".$fbItemid."&amp;func=showcat&amp;catid=" . $item->catid);
			            $postDate	= new JDate($item->time);
			            ?>


						<?php
						/*
						echo '<div style="border-bottom: 1px solid rgb(204, 204, 204); margin: 0pt 0pt 5px; padding: 4px;">';
						echo '<img src="' . $this->getTopicImoticon($item) . '" alt="emo" style="vertical-align: middle; margin: 0 5px 0 0;" />';
						echo '<a href="'. $fbURL .'">' . stripslashes ($item->subject) . '</a> ';
						echo 'in ' . '<a href="'. $fbCatURL .'">'. $item->catname .'</a> on '.  $postDate->toFormat(JText::_('DATE_FORMAT_LC2')) ;
						echo '</div>';
						*/
						?>
					    <li>
					        <div class="icon">
					            <img src="<?php echo plgCommunityKunena::getTopicImoticon($item); ?>" alt="" />
					        </div>
					        <div class="content">
					            <a href="<?php echo $fbURL;?>">
									<?php echo stripslashes ($item->subject); ?>
								</a> in
								<a href="<?php echo $fbCatURL; ?>"><?php echo $item->catname; ?></a>
								on <?php echo $postDate->toFormat(JText::_('DATE_FORMAT_LC2')); ?>
					        </div>
					        <div style="clear: both;"></div>
					    </li>

					<?php
					}
				?>
				    </ul>
				    <div style="clear: both;"></div>
				</div>
				<?php
			} else {
				?>
				<div class="icon-nopost">
		            <img src="<?php echo JURI::base(); ?>plugins/community/kunena/no-post.gif" alt="" />
		        </div>
		        <div class="content-nopost">
		            <?php echo $userName . ' ' . JText::_('hasn\'t join any discussion yet'); ?>
		        </div>
				<?php
			}
 		}else{
		 	?>
	        <div class="icon-nopost">
	            <img src="<?php echo JURI::base(); ?>plugins/community/kunena/no-post.gif" alt="" />
	        </div>
	        <div class="content-nopost">
	            <?php echo JText::_('No forum posts by the user yet'); ?>
	        </div>
			<?php
		 }

		$contents	= ob_get_contents();
		ob_end_clean();
		return $contents;
	}

	/**
	 * Return itemid for Kunena
	 */
	function getItemid(){
		$db =& JFactory::getDBO();
		$Itemid = 0;
		if (!defined("KUNENA_KUNENA_ITEMID")) {
	    	if ($Itemid < 1) {
	        	$db->setQuery("SELECT id FROM #__menu WHERE link = 'index.php?option=com_kunena' AND published = 1");
	        	$Itemid = $db->loadResult();

	        	if ($Itemid < 1) {
	         	   $Itemid = 0;
	        	}
	    	}
	    } else {
	    	$Itemid = KUNENA_KUNENA_ITEMID;
		}

	    return $Itemid;
	}

	/**
	 * Return path to topic emoticons
	 * Sadly, for now, we will only return default, emoticons
	 */
	function getTopicImoticon(&$item) {

		$emoticonPath = '';
		if( !defined('JB_URLEMOTIONSPATH' )) {
			$emoticonPath = JURI::base() . 'components/com_kunena/template/default_ex/images/english/emoticons/';
		} else {
			$emoticonPath = JB_URLEMOTIONSPATH;
		}

		// Emotions
        $topic_emoticons = array ();
        $topic_emoticons[0] = $emoticonPath . 'default.gif';
        $topic_emoticons[1] = $emoticonPath . 'exclam.gif';
        $topic_emoticons[2] = $emoticonPath . 'question.gif';
        $topic_emoticons[3] = $emoticonPath . 'arrow.gif';
        $topic_emoticons[4] = $emoticonPath . 'love.gif';
        $topic_emoticons[5] = $emoticonPath . 'grin.gif';
        $topic_emoticons[6] = $emoticonPath . 'shock.gif';
        $topic_emoticons[7] = $emoticonPath . 'smile.gif';

        return $topic_emoticons[$item->topic_emoticon];
	}
}
