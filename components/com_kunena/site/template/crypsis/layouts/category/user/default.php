<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Category
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>

<div>
  <h2> <span>
    <?php if (!empty($this->header)) echo $this->escape($this->header); ?>
    </span> </h2>
</div>
<div>
  <div>
    <table class="table">
      <?php if (!count ( $this->categories ) ) : ?>
      <tr>
        <td> <?php echo JText::_('COM_KUNENA_CATEGORY_SUBSCRIPTIONS_NONE') ?> </td>
      </tr>
      <?php
		else :
			foreach ($this->categories as $this->category) {
				echo $this->subLayout('Category/User/Row')->setProperties($this->getProperties());
			}
		endif;
		?>
    </table>
  </div>
</div>
