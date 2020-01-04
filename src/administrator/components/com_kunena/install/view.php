<?php
/**
 * Kunena Component
 *
 * @package        Kunena.Installer
 *
 * @copyright      Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * The HTML Kunena configuration view.
 *
 * @since   Kunena 6.0
 */
class KunenaViewInstall extends Joomla\CMS\MVC\View\HtmlView
{
	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected $model = null;

	/**
	 * Method to display the view.
	 *
	 * @param   string  $tpl  A template file to load.
	 *
	 * @return  Exception|void    Exception on failure, void on success.
	 *
	 * @since   Kunena 1.6
	 *
	 * @throws  Exception
	 */
	public function display($tpl = null)
	{
		$layout = $this->getLayout();

		if ($layout == 'schema')
		{
			parent::display($tpl);

			return;
		}

		// Load the view data.
		$this->model = $this->get('Model');

		$versions = $this->model->getDetectVersions();
		$version  = reset($versions);
		$this->model->setAction(strtolower($version->action));
		$this->model->setStep(0);

		Factory::getApplication()->input->post->get('hidemainmenu', 1);

		parent::display($tpl);
	}

	/**
	 * Private method to set the toolbar for this view
	 *
	 * @access private
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function setToolBar()
	{
		// Set the titlebar text
		JToolbarHelper::title('<span>Kunena ' . KunenaForum::version() . '</span> ' . Text::_('COM_KUNENA_INSTALLER'), 'kunena.png');
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  KunenaSchemaException
	 * @throws  Exception
	 */
	public function displaySchema()
	{
		require_once KPATH_ADMIN . '/install/schema.php';
		$schema = new KunenaModelSchema;
		$create = $schema->getCreateSQL();
		echo '<textarea cols="80" rows="50">';
		echo $this->escape($schema->getSchema()->saveXML());
		echo '</textarea>';

		if (KunenaForum::isDev())
		{
			echo '<textarea cols="80" rows="20">';

			foreach ($create as $item)
			{
				echo $this->escape($item ['sql']) . "\n\n";
			}

			echo '</textarea>';
		}
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  KunenaSchemaException
	 * @throws  Exception
	 */
	public function displaySchemaDiff()
	{
		require_once KPATH_ADMIN . '/install/schema.php';
		$schema = new KunenaModelSchema;
		$diff   = $schema->getDiffSchema();
		$sql    = $schema->getSchemaSQL($diff);
		echo '<textarea cols="80" rows="20">';
		echo $this->escape($diff->saveXML());
		echo '</textarea>';

		if (KunenaForum::isDev())
		{
			echo '<textarea cols="80" rows="20">';

			foreach ($sql as $item)
			{
				echo $this->escape($item ['sql']) . "\n\n";
			}

			echo '</textarea>';
		}
	}
}
