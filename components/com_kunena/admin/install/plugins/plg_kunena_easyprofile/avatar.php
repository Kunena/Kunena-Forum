<?php
/**
 * @author		Onlinecommunityhub.nl
 * @copyright	
 * @license		GNU General Public License version 3 or later
 */
defined ( '_JEXEC' ) or die ();

class KunenaAvatarEasyprofile extends KunenaAvatar {
	protected $params = null;

	public function __construct($params) {
		$this->params = $params;
	}

	public function getEditURL()
	{
		return JRoute::_('index.php?option=com_jsn&view=profile');
	}

        public function _getURL($user, $sizex, $sizey){

		if (!$user->userid == 0)
		{
	 		$user = KunenaFactory::getUser($user->userid);
        	        $user = JsnHelper::getUser($user->userid);
        	        if ($sizex <= 50)
        	        {
        	                $avatar = JURI::root( true ).'/'.$user->getValue('avatar_mini');
        	        }
        	        else
        	        {
        	                $avatar = JURI::root( true ).'/'.$user->getValue('avatar');
        	        }
		}
		elseif ($this->params->get('guestavatar', "easyprofile")  == "easyprofile")
		{
			$avatar = JUri::root( true ).'/components/com_jsn/assets/img/default.jpg';
		}
		else
		{
			$db=JFactory::getDbo();
			$query=$db->getQuery(true);
			$query->select('params')->from('#__jsn_fields')->where('alias=\'avatar\'');
			$db->setQuery($query);
			$params=$db->loadResult();
			$registry = new JRegistry;
			$registry->loadString($params);
			$params = $registry->toArray();
			if($params['image_defaultvalue'] <> "")
			{
				$avatar = JUri::root( true ).'/'.$params['image_defaultvalue'];
			}
			else
			{
				$avatar = JUri::root( true ).'/components/com_jsn/assets/img/default.jpg';
			}
		}
                return $avatar;
        }
}
