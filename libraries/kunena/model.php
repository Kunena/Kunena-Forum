<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Model for Kunena
 *
 * @since		2.0
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

	public function __construct($config = array(), JInput $input = null)
	{
		$this->option = 'com_kunena';
		parent::__construct($config);

		$this->app = JFactory::getApplication();
		$this->me = KunenaUserHelper::getMyself();
		$this->config = KunenaFactory::getConfig();
		$this->input = $input ? $input : $this->app->input;
	}

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
	 * @param  mixed $var The output to escape.
	 * @return mixed The escaped value.
	 */
	public function escape($var)
	{
		return htmlspecialchars($var, ENT_COMPAT, 'UTF-8');
	}

	protected function getParameters()
	{
		if (!$this->params)
		{
			$this->params = $this->app->getParams('com_kunena');
		}

		return $this->params;
	}

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

	protected function getBool($name, $default = false, $hash = 'request')
	{
		return $this->getVar($name, $default, $hash, 'bool');
	}

	protected function getCmd($name, $default = '', $hash = 'request')
	{
		return $this->getVar($name, $default, $hash, 'cmd');
	}

	protected function getFloat($name, $default = 0.0, $hash = 'request')
	{
		return $this->getVar($name, $default, $hash, 'float');
	}

	protected function getInt($name, $default = 0, $hash = 'request')
	{
		return $this->getVar($name, $default, $hash, 'int');
	}

	protected function getString($name, $default = '', $hash = 'request')
	{
		return $this->getVar($name, $default, $hash, 'string');
	}

	protected function getWord($name, $default = '', $hash = 'request')
	{
		return $this->getVar($name, $default, $hash, 'word');
	}
}
