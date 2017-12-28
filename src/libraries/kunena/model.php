<?php
/**
 * Kunena Component
 * @package    Kunena.Framework
 *
 * @copyright  (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license    https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Model for Kunena
 *
 * @since  2.0
 */
class KunenaModel extends JModelLegacy
{
	/**
	 * @var JSite|JAdministrator
	 */
	public $app = null;

	/**
	 * @var KunenaUser
	 */
	public $me = null;

	/**
	 * @var KunenaConfig
	 */
	public $config = null;

	/**
	 * @var JRegistry
	 */
	public $params = null;

	/**
	 * @var JInput
	 */
	protected $input = null;

	/**
	 * @var JFilterInput
	 */
	protected $filter = null;

	/**
	 * @var JObject
	 */
	protected $state = null;

	/**
	 * @var bool
	 */
	protected $embedded = false;

	/**
	 * @param   array  $config
	 * @param   JInput $input
	 *
	 * @throws Exception
	 */
	public function __construct($config = array(), JInput $input = null)
	{
		$this->option = 'com_kunena';
		parent::__construct($config);

		$this->app = JFactory::getApplication();
		$this->me = KunenaUserHelper::getMyself();
		$this->config = KunenaFactory::getConfig();
		$this->input = $input ? $input : $this->app->input;
	}

	/**
	 * @param   array $params
	 * @param   bool  $embedded
	 */
	public function initialize($params = array(), $embedded = true)
	{
		if ($embedded)
		{
			$this->embedded = true;
			$this->setState('embedded', true);
			$this->filter = JFilterInput::getInstance();
		}

		if ($params instanceof JRegistry)
		{
			$this->params = $params;
		}
		else
		{
			$this->params = new JRegistry($params);
		}
	}

	/**
	 * @return integer
	 */
	public function getItemid()
	{
		$Itemid = 0;

		if (!$this->embedded)
		{
			$active = $this->app->getMenu()->getActive();
			$Itemid = $active ? (int) $active->id : 0;
		}

		return $Itemid;
	}

	/**
	 * Escapes a value for output in a view script.
	 *
	 * @param   mixed $var The output to escape.
	 * @return mixed The escaped value.
	 */
	public function escape($var)
	{
		return htmlspecialchars($var, ENT_COMPAT, 'UTF-8');
	}

	/**
	 * @return JRegistry
	 */
	protected function getParameters()
	{
		if (!$this->params)
		{
			$this->params = $this->app->getParams('com_kunena');
		}

		return $this->params;
	}

	/**
	 * @param        $key
	 * @param        $request
	 * @param   null   $default
	 * @param   string $type
	 *
	 * @return mixed|object
	 */
	protected function getUserStateFromRequest($key, $request, $default = null, $type = 'none')
	{
		// If we are not in embedded mode, get variable from application
		if (!$this->embedded)
		{
			return $this->app->getUserStateFromRequest($key, $request, $default, $type);
		}

		// Embedded models/views do not have user state -- all variables come from parameters
		return $this->getVar($request, $default, 'request', $type);
	}

	/**
	 * @param        $name
	 * @param   null   $default
	 * @param   string $hash
	 * @param   string $type
	 *
	 * @return mixed
	 */
	protected function getVar($name, $default = null, $hash = 'request', $type = 'none')
	{
		// If we are not in embedded mode, get variable from request
		if (!$this->embedded)
		{
			if ($hash == 'request')
			{
				return $this->input->get($name, $default, $type);
			}
			else
			{
				return $this->input->{$hash}->get($name, $default, $type);
			}
		}

		return $this->filter->clean($this->params->get($name, $default), $type);
	}

	/**
	 * @param        $name
	 * @param   bool   $default
	 * @param   string $hash
	 *
	 * @return mixed
	 */
	protected function getBool($name, $default = false, $hash = 'request')
	{
		return $this->getVar($name, $default, $hash, 'bool');
	}

	/**
	 * @param        $name
	 * @param   string $default
	 * @param   string $hash
	 *
	 * @return mixed
	 */
	protected function getCmd($name, $default = '', $hash = 'request')
	{
		return $this->getVar($name, $default, $hash, 'cmd');
	}

	/**
	 * @param        $name
	 * @param   float  $default
	 * @param   string $hash
	 *
	 * @return mixed
	 */
	protected function getFloat($name, $default = 0.0, $hash = 'request')
	{
		return $this->getVar($name, $default, $hash, 'float');
	}

	/**
	 * @param        $name
	 * @param   int    $default
	 * @param   string $hash
	 *
	 * @return mixed
	 */
	protected function getInt($name, $default = 0, $hash = 'request')
	{
		return $this->getVar($name, $default, $hash, 'int');
	}

	/**
	 * @param        $name
	 * @param   string $default
	 * @param   string $hash
	 *
	 * @return mixed
	 */
	protected function getString($name, $default = '', $hash = 'request')
	{
		return $this->getVar($name, $default, $hash, 'string');
	}

	/**
	 * @param        $name
	 * @param   string $default
	 * @param   string $hash
	 *
	 * @return mixed
	 */
	protected function getWord($name, $default = '', $hash = 'request')
	{
		return $this->getVar($name, $default, $hash, 'word');
	}
}
