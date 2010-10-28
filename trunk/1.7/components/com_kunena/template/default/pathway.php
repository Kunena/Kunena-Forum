<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 * Based on FireBoard Component
 * @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.bestofjoomla.com
 *
 * Based on Joomlaboard Component
 * @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @author TSMF & Jan de Graaff
 **/

// Dont allow direct linking
defined( '_JEXEC' ) or die();

require_once(KUNENA_PATH_LIB .DS. 'kunena.pathway.class.php');
$pathway = new CKunenaPathway();

$func = JString::strtolower ( JRequest::getCmd ( 'func', 'listcat' ) );
$catid = JRequest::getInt ( 'catid', 0 );
$id = JRequest::getInt ( 'id', 0 );
?>
<!-- Pathway -->
<?php
	// FIXME: move most of the code out of the template
	kimport('kunena.forum.category.helper');
	$jr_path_menu = array ();
	$fr_title_name = JText::_('COM_KUNENA_CATEGORIES');

	$parents = KunenaForumCategoryHelper::getParents($catid);
	$parents[] = KunenaForumCategoryHelper::get($catid);
	$parents = array_reverse($parents);
	foreach ( $parents as $parent ) {
		$fr_name = $this->escape( JString::trim ( $parent->name ) );
		$sname = CKunenaLink::GetCategoryLink ( 'showcat', $parent->id, $fr_name );

		if ($catid == $parent->id && $func != "view") {
			$fr_title_name = $fr_name;
			$jr_path_menu [] = $fr_name;
		} else {
			$jr_path_menu [] = $sname;
		}
	}

	//reverse the array
	$jr_path_menu = array_reverse ( $jr_path_menu );

	//attach topic name
	$this->kunena_topic_title = '';
	if ($func == "view" and $id) {
		$this->kunena_topic_title = $pathway->getMessagesTitles($id);
		$jr_path_menu [] = $this->escape($this->kunena_topic_title);
	}

	// print the list
	if (count ( $jr_path_menu ) == 0)
		$jr_path_menu [] = '';
	$jr_forum_count = count ( $jr_path_menu );

	$fireinfo = '';
	$firepath = '<div class="path-element-first">' . CKunenaLink::GetKunenaLink ( $this->escape( $this->config->board_title ) ) . '</div>';
	$firelast = '';

	for($i = 0; $i < $jr_forum_count; $i ++) {
		if ($i == $jr_forum_count - 1) {
			if ( $this->config->onlineusers ) :
			$firelast .= '<br /><div class="path-element-last">' . $jr_path_menu [$i] . $fireinfo . '</div>';
			endif;
		} else {
			$firepath .= '<div class="path-element">' . $jr_path_menu [$i] . '</div>';
		}
	}

	//get viewing
	$fireonline = '';
	if ( $this->config->onlineusers ) {
		if ($func == "userprofile") {
			$fireonline .= JText::_('COM_KUNENA_USER_PROFILE');
			$fireonline .= $this->escape($this->kunena_username);
		} else {
			$fireonline .= "<div class=\"path-element-users\">(".$pathway->getTotalViewing($func). ' ' . JText::_('COM_KUNENA_PATHWAY_VIEWING') . ")&nbsp;";
			$fireonline .= $pathway->getUsersOnlineList($func);
		}
		$fireonline .= '</div>';
	}

	$pathway->document->setTitle ( $this->kunena_topic_title ? $this->kunena_topic_title : $fr_title_name . ' - ' . $this->config->board_title );

	$this->kunena_pathway1 = $firepath . $fireinfo;
	$this->kunena_pathway2 = $firelast . $fireonline;
?>
<div class="kblock kpathway">
	<div class="kcontainer" id="pathway_tbody">
		<div class="ksectionbody">
			<div class = "kforum-pathway">
			<?php echo $this->kunena_pathway1 . $this->kunena_pathway2; ?>
			</div>
		</div>
	</div>
</div>
