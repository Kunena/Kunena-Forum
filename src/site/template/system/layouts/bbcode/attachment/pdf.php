<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.System
 * @subpackage      BBCode
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;

$attachment = $this->attachment;
$location   = $attachment->getUrl();

if (!$attachment->isPdf()) {
    return;
}
?>
<div class="clearfix"></div>

<style>
  /* Default (Desktop) */
  .pdf-viewer {
    width: 100%;
    min-height: 400px;
    height: 80vh;
  }

  /* Tablet */
  @media (max-width: 991px) {
    .pdf-viewer {
      min-height: 300px;
      height: 70vh;
    }
  }

  /* Smartphone */
  @media (max-width: 575px) {
    .pdf-viewer {
      min-height: 200px;
      height: 60vh;
    }
  }
</style>

<object class="pdf pdf-viewer"
        data="<?php echo $location; ?>"
        type="application/pdf">
    <p>
        <?php echo Text::_('COM_KUNENA_BBCODE_PDF_BROWSER_NOT_SUPPORT_EMBEDDED_PDF') ?>  
        <a href="<?php echo $location; ?>" download><?php echo Text::_('COM_KUNENA_BBCODE_PDF_DOWNLOAD_PDF') ?></a>.
    </p>
</object>

<div class="d-flex justify-content-center justify-content-md-end mt-2">
  <a class="btn btn-secondary"
     href="<?php echo $location; ?>"
     download>
    <i class="fas fa-download"></i> <?php echo Text::_('COM_KUNENA_BBCODE_PDF_DOWNLOAD_PDF') ?>
  </a>
</div>