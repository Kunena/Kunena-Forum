<?php
/**
 * Kunena Component
 *
 * @package        Kunena.Framework
 *
 * @copyright      Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Model;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Object\CMSObject;
use Joomla\Input\Input;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use function defined;

/**
 * Model for Kunena
 *
 * @since   Kunena 2.0
 */
class Model extends BaseDatabaseModel
{
	/**
	 * @var     string JSite|JAdministrator
	 * @since   Kunena 6.0
	 */
	public $app = null;

	/**
	 * @var     KunenaUser
	 * @since   Kunena 6.0
	 */
	public $me = null;

	/**
	 * @var     KunenaConfig
	 * @since   Kunena 6.0
	 */
	public $config = null;

	/**
	 * @var     string Registry
	 * @since   Kunena 6.0
	 */
	public $params = null;

	/**
	 * @var     Input
	 * @since   Kunena 6.0
	 */
	protected $input = null;

	/**
	 * @var     InputFilter
	 * @since   Kunena 6.0
	 */
	protected $filter = null;

	/**
	 * @var     CMSObject
	 * @since   Kunena 6.0
	 */
	protected $state = null;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	protected $embedded = false;

	/**
	 * @param   array                     $config   config
	 * @param   MVCFactoryInterface|null  $factory  factory
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	public function __construct($config = array(), MVCFactoryInterface $factory = null)
	{
		$this->option = 'com_kunena';
		parent::__construct($config);

		$this->app    = Factory::getApplication();
		$this->me     = KunenaUserHelper::getMyself();
		$this->config = KunenaFactory::getConfig();
		$this->input  = $this->app->input;
	}

	/**
	 * @param   array  $params    params
	 * @param   bool   $embedded  embedded
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function initialize($params = [], $embedded = true)
	{
		if ($embedded)
		{
			$this->embedded = true;
			$this->setState('embedded', true);
			$this->filter = InputFilter::getInstance();
		}

		if ($params instanceof Registry)
		{
			$this->params = $params;
		}
		else
		{
			$this->params = new Registry($params);
		}
	}

	/**
	 * @return  integer
	 *
	 * @since   Kunena 6.0
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
	 * @param   mixed  $var  The output to escape.
	 *
	 * @return  mixed The escaped value.
	 *
	 * @since   Kunena 6.0
	 */
	public function escape($var)
	{
		return htmlspecialchars($var, ENT_COMPAT, 'UTF-8');
	}

	/**
	 * @return  Registry
	 *
	 * @since   Kunena 6.0
	 */
	protected function getParameters()
	{
		if (!$this->params)
		{
			$this->params = ComponentHelper::getParams('com_kunena');
		}

		return $this->params;
	}

	/**
	 * @param   string  $key      key
	 * @param   string  $request  request
	 * @param   null    $default  default
	 * @param   string  $type     type
	 *
	 * @return  mixed|object
	 *
	 * @since   Kunena 6.0
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
	 * @param   string  $name     name
	 * @param   null    $default  default
	 * @param   string  $hash     hash
	 * @param   string  $type     type
	 *
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
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
	 * @param   string  $name     name
	 * @param   bool    $default  default
	 * @param   string  $hash     hash
	 *
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 */
	protected function getBool($name, $default = false, $hash = 'request')
	{
		return $this->getVar($name, $default, $hash, 'bool');
	}

	/**
	 * @param   string  $name     name
	 * @param   string  $default  default
	 * @param   string  $hash     hash
	 *
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 */
	protected function getCmd($name, $default = '', $hash = 'request')
	{
		return $this->getVar($name, $default, $hash, 'cmd');
	}

	/**
	 * @param   string  $name     name
	 * @param   float   $default  default
	 * @param   string  $hash     hash
	 *
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 */
	protected function getFloat($name, $default = 0.0, $hash = 'request')
	{
		return $this->getVar($name, $default, $hash, 'float');
	}

	/**
	 * @param   string  $name     name
	 * @param   int     $default  default
	 * @param   string  $hash     hash
	 *
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 */
	protected function getInt($name, $default = 0, $hash = 'request')
	{
		return $this->getVar($name, $default, $hash, 'int');
	}

	/**
	 * @param   string  $name     name
	 * @param   string  $default  default
	 * @param   string  $hash     hash
	 *
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 */
	protected function getString($name, $default = '', $hash = 'request')
	{
		return $this->getVar($name, $default, $hash, 'string');
	}

	/**
	 * @param   string  $name     name
	 * @param   string  $default  default
	 * @param   string  $hash     hash
	 *
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 */
	protected function getWord($name, $default = '', $hash = 'request')
	{
		return $this->getVar($name, $default, $hash, 'word');
	}
}
