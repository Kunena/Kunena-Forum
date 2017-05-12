<?php
/**
 * @author		Onlinecommunityhub.nl
 * @copyright	
 * @license		GNU General Public License version 3 or later
 */
defined ( '_JEXEC' ) or die ();

class KunenaProfileEasyprofile extends KunenaProfile {
	protected $params = null;

	public function __construct($params) {
		$this->params = $params;
	}

        public function getUserListURL($action = '', $xhtml = true)
        {
                if ($config->userlist_allowed == 1 && $my->id == 0)
                {
                        return false;
                }
                elseif ($this->params->get('userlist', 0)  == 0)
		{
			$config = KunenaFactory::getConfig();
	                $my     = JFactory::getUser();

			return KunenaRoute::_('index.php?option=com_kunena&view=user&layout=list' . $action, $xhtml);
		}
		else
		{
                	return JRoute::_('index.php?option=com_jsn&view=list&Itemid='.$this->params->get('menuitem',''),false);
		}
        }

	public function getProfileURL($userid, $task='', $xhtml = true) {
		//Make sure that user profile exist.
		if (!$userid || JsnHelper::getUser($userid) === null) {
			return false;
		}
		$user=JsnHelper::getUser($userid);
		return $user->getLink();
	}

	public function showProfile($view, &$params) {}

	public function getEditProfileURL($userid, $xhtml = true) {
		return $this->getProfileURL($userid, 'edit', $xhtml);
	}
}

