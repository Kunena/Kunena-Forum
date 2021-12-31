<?php
/**
 * Kunena Component
 * @package        Kunena.Framework
 *
 * @copyright      Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;

/**
 * Model for Kunena
 *
 * @since  2.0
 */
class KunenaModel extends \Joomla\CMS\MVC\Model\BaseDatabaseModel
{
	/**
	 * @since Kunena
	 * @var  string JSite|JAdministrator
	 */
	public $app = null;

	/**
	 * @since Kunena
	 * @var KunenaUser
	 */
	public $me = null;

	/**
	 * @since Kunena
	 * @var KunenaConfig
	 */
	public $config = null;

	/**
	 * @since Kunena
	 * @var string \Joomla\Registry\Registry
	 */
	public $params = null;

	/**
	 * @since Kunena
	 * @var \Joomla\Input\Input
	 */
	protected $input = null;

	/**
	 * @since Kunena
	 * @var \Joomla\CMS\Filter\InputFilter
	 */
	protected $filter = null;

	/**
	 * @since Kunena
	 * @var JObject
	 */
	protected $state = null;

	/**
	 * @since Kunena
	 * @var boolean
	 */
	protected $embedded = false;

	/**
	 * @param   array               $config config
	 * @param   \Joomla\Input\Input $input  input
	 *
	 * @since Kunena
	 * @throws Exception
	 */
	public function __construct($config = array(), Joomla\Input\Input $input = null)
	{
		$this->option = 'com_kunena';
		parent::__construct($config);

		$this->app    = Factory::getApplication();
		$this->me     = KunenaUserHelper::getMyself();
		$this->config = KunenaFactory::getConfig();
		$this->input  = $input ? $input : $this->app->input;
	}

	/**
	 * @param   array $params   params
	 * @param   bool  $embedded embedded
	 *
	 * @return void
	 * @since Kunena
	 */
	public function initialize($params = array(), $embedded = true)
	{
		if ($embedded)
		{
			$this->embedded = true;
			$this->setState('embedded', true);
			$this->filter = \Joomla\CMS\Filter\InputFilter::getInstance();
		}

		if ($params instanceof \Joomla\Registry\Registry)
		{
			$this->params = $params;
		}
		else
		{
			$this->params = new \Joomla\Registry\Registry($params);
		}
	}

	/**
	 * @return integer
	 * @since Kunena
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
	 *
	 * @return mixed The escaped value.
	 * @since Kunena
	 */
	public function escape($var)
	{
		return htmlspecialchars($var, ENT_COMPAT, 'UTF-8');
	}

	/**
	 * @return \Joomla\Registry\Registry
	 * @since Kunena
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
	 * @param   string $key     key
	 * @param   string $request request
	 * @param   null   $default default
	 * @param   string $type    type
	 *
	 * @return mixed|object
	 * @since Kunena
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
	 * @param   string $name    name
	 * @param   null   $default default
	 * @param   string $hash    hash
	 * @param   string $type    type
	 *
	 * @return mixed
	 * @since Kunena
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
	 * @param   string $name    name
	 * @param   bool   $default default
	 * @param   string $hash    hash
	 *
	 * @return mixed
	 * @since Kunena
	 */
	protected function getBool($name, $default = false, $hash = 'request')
	{
		return $this->getVar($name, $default, $hash, 'bool');
	}

	/**
	 * @param   string $name    name
	 * @param   string $default default
	 * @param   string $hash    hash
	 *
	 * @return mixed
	 * @since Kunena
	 */
	protected function getCmd($name, $default = '', $hash = 'request')
	{
		return $this->getVar($name, $default, $hash, 'cmd');
	}

	/**
	 * @param   string $name    name
	 * @param   float  $default default
	 * @param   string $hash    hash
	 *
	 * @return mixed
	 * @since Kunena
	 */
	protected function getFloat($name, $default = 0.0, $hash = 'request')
	{
		return $this->getVar($name, $default, $hash, 'float');
	}

	/**
	 * @param   string $name    name
	 * @param   int    $default default
	 * @param   string $hash    hash
	 *
	 * @return mixed
	 * @since Kunena
	 */
	protected function getInt($name, $default = 0, $hash = 'request')
	{
		return $this->getVar($name, $default, $hash, 'int');
	}

	/**
	 * @param   string $name    name
	 * @param   string $default default
	 * @param   string $hash    hash
	 *
	 * @return mixed
	 * @since Kunena
	 */
	protected function getString($name, $default = '', $hash = 'request')
	{
		return $this->getVar($name, $default, $hash, 'string');
	}

	/**
	 * @param   string $name    name
	 * @param   string $default default
	 * @param   string $hash    hash
	 *
	 * @return mixed
	 * @since Kunena
	 */
	protected function getWord($name, $default = '', $hash = 'request')
	{
		return $this->getVar($name, $default, $hash, 'word');
	}
}
