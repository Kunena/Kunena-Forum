<?php
/**
 * @version		$Id$
 * MyKunena Plugin
 * @package plg_jomsocial_mykunena
 * @copyright	Copyright (C) 2009 - 2010 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */
defined ( '_JEXEC' ) or die ();

$path = JPATH_ROOT . '/components/com_community/libraries/core.php';
if (! is_file ( $path ))
	return;
require_once $path;

class plgCommunityMyKunena extends CApplications {
	var $name = "My Kunena Posts";
	var $_name = 'mykunena';

	function plgCommunityKunenaMenu(& $subject, $config) {
		parent::__construct ( $subject, $config );
	}

	protected static function kunenaOnline() {
		// Kunena detection and version check
		$minKunenaVersion = '1.6.0-RC2';
		if (! class_exists ( 'Kunena' ) || Kunena::versionBuild () < 3251) {
			return false;
		}
		// Kunena online check
		if (! Kunena::enabled ()) {
			return false;
		}
		return true;
	}

	function onProfileDisplay() {
		if (! self::kunenaOnline ()) return;

		//Load Language file.
		JPlugin::loadLanguage ( 'plg_community_mykunena', JPATH_ADMINISTRATOR );

		$document = JFactory::getDocument ();
		$document->addStyleSheet ( JURI::base () . 'plugins/community/mykunena/style.css' );

		$items = array();
		$user = CFactory::getRequestUser ();
		if ($user->id) {
			KunenaFactory::getSession ( true );
			require_once KPATH_SITE . '/funcs/latestx.php';
			$obj = new CKunenaLatestX('userposts', 0);
			$obj->user = JFactory::getUser($user->id);
			$obj->threads_per_page = $this->params->get ( 'count', 5 );
			$obj->embedded = 1;
			$obj->getUserPosts();
			$items = $obj->customreply;
		}

		$caching = $this->params->get ( 'cache', 1 );
		if ($caching) {
			$app = JFactory::getApplication ();
			$caching = $app->getCfg ( 'caching' );
		}

		$cache = JFactory::getCache ( 'plgCommunityMyKunena' );
		$cache->setCaching ( $caching );
		$callback = array ('plgCommunityMyKunena', '_getMyKunenaHTML' );
		$content = $cache->call ( $callback, $user, $items );
		return $content;
	}

	function _getMyKunenaHTML($user, $items) {
		ob_start ();

		$template = KunenaFactory::getTemplate ();
		if ( !$items ) : ?>

		<div class="icon-nopost"><img src="<?php echo JURI::base (); ?>plugins/community/mykunena/no-post.gif" alt="" /></div>
		<div class="content-nopost"><?php echo JText::sprintf ( 'PLG_COMMUNITY_MYKUNENA_NO_POSTS', $user->getDisplayName() ); ?></div>

		<?php else : ?>

		<div id="community-mykunena-wrap">
			<ul class="cList clrfix">
			<?php
			foreach ( $items as $item ) :
				$postDate = new JDate ( $item->time );
			?>
				<li>
					<div class="content">
						<a href="<?php echo KunenaRoute::_ ( "index.php?option=com_kunena&func=view&catid={$item->catid}&id={$item->id}" ); ?>" class="kjsubject"><?php echo $item->subject; ?></a> <?php echo JText::_('PLG_MYKUNENA_POST_IN'); ?>
						<a href="<?php echo KunenaRoute::_ ( "index.php?option=com_kunena&func=showcat&catid={$item->catid}" ); ?>" class="kjcategory"><?php echo $item->catname; ?></a> <?php echo JText::_('PLG_MYKUNENA_POST_ON'); ?>
						<span class="kjdate"><?php echo $postDate->toFormat ( JText::_ ( 'DATE_FORMAT_LC2' ) ); ?></span>
					</div>
				</li>
			<?php endforeach; ?>
			</ul>
		</div>

		<?php endif;

		$contents = ob_get_contents ();
		ob_end_clean ();
		return $contents;
	}
}