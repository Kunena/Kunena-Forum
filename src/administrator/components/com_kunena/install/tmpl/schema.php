<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Installer
 * @subpackage      Template
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Uri\Uri;

$this->document->addStyleSheet(Uri::base(true) . '/components/com_kunena/install/media/install.css');
?>
<div id="right">
	<div id="rightpad">
		<div id="step">
			<div class="u">
				<div class="u">
					<div class="u"></div>
				</div>
			</div>
			<div class="n">
				<span class="step">Kunena Database Schema</span>
			</div>
			<div class="c">
				<div class="c">
					<div class="c"></div>
				</div>
			</div>
		</div>

		<div id="installer">
			<div class="u">
				<div class="u">
					<div class="u"></div>
				</div>
			</div>
			<div class="n">
				<h2>Schema Difference</h2>
				<p><?php $this->displaySchemaDiff(); ?></p>
				<h2>Database Schema</h2>
				<p><?php $this->displaySchema(); ?></p>
			</div>
			<div class="c">
				<div class="c">
					<div class="c"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="clr"></div>
