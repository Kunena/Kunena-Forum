<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Search
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>

<table class="table">
  <thead>
    <tr>
      <th colspan="2"> <span> <?php echo KunenaDate::getInstance($this->message->time)->toSpan() ?> </span> </th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td rowspan="2" valign="top">
        <ul>
          <li> <?php echo $this->message->getAuthor()->getLink() ?> </li>
          <li>
            <?php
						if ($this->useravatar) :
						?>
            <span> <?php echo $this->message->getAuthor()->getLink( $this->useravatar ) ?> </span>
            <?php endif; ?>
          </li>
        </ul>
      </td>
      <td>
        <div>
          <div> <span> <?php echo $this->getTopicLink($this->topic, $this->message, $this->subjectHtml); ?> </span> </div>
          <div> <?php echo $this->messageHtml ?> </div>
          <div> <?php echo JText::sprintf('COM_KUNENA_CATEGORY_X', $this->getCategoryLink ( $this->category, $this->escape($this->category->name))) ?> </div>
        </div>
      </td>
    </tr>
  </tbody>
</table>
