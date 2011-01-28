<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
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

$func = JString::strtolower ( JRequest::getCmd ( 'view', 'listcat' ) );
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

		if ($catid == $parent->id && ($func != "view" && $func!="topic")) {
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
	if ($func == "view" || $func=="topic" && $id) {
		$this->kunena_topic_title = KunenaForumTopicHelper::get($id)->subject;
		$jr_path_menu [] = $this->escape($this->kunena_topic_title);
	}

	// print the list
	if (count ( $jr_path_menu ) == 0)
		$jr_path_menu [] = '';
	$jr_forum_count = count ( $jr_path_menu );

	$firepath = '<div class="path-element-first">' . CKunenaLink::GetKunenaLink ( $this->escape( $this->config->board_title ) ) . '</div>';
	for($i = 0; $i < $jr_forum_count; $i ++) {
		$firepath .= '<div class="path-element">' . $jr_path_menu [$i] . '</div>';
	}

	//$pathway->document->setTitle ( $this->kunena_topic_title ? $this->kunena_topic_title : $fr_title_name . ' - ' . $this->config->board_title );

	$this->kunena_pathway1 = $firepath;
?>
<div class="kblock kpathway">
	<div class="kcontainer" id="pathway_tbody">
		<div class="ksectionbody">
			<div class = "kforum-pathway">
			<?php echo $this->kunena_pathway1; ?>
			</div>
		</div>
	</div>
</div>
