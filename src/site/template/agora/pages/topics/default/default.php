<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Agora
 * @subpackage      Pages.Topics
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

// The lists of topics (recent, no replies, my topics, ...). Agora shows who is online and the statistics only on the index of the
// forum, not under every list.
$content = $this->execute('Topic/Listing/Recent')
    ->setLayout('recent');

$this->addBreadcrumb(
    $content->headerText,
    'index.php?option=com_kunena&view=topics&layout=default'
);

echo $content;
