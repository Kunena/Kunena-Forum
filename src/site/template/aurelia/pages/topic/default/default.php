<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Pages.Topic
 *
 * @copyright       Copyright (C) 2008 - 2021 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

defined('_JEXEC') or die();

use Kunena\Forum\Libraries\Forum\Category\CategoryHelper;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use function defined;

$content = $this->execute('Topic/Item')
	->setLayout(KunenaUserHelper::getMyself()->getTopicLayout());

// Display breadcrumb path to the current category / topic / message / moderate.
$parents   = CategoryHelper::getParents($content->category->id);
$parents[] = $content->category;

foreach ($parents as $parent)
{
	$this->addBreadcrumb(
		$parent->displayField('name'),
		$parent->getUri()
	);
}

$this->addBreadcrumb(
	$content->topic->subject,
	$content->topic->getUri()
);

echo $content;
