<?php
/*******
 * @package xbJournals Component
 * @filesource admin/ayouts/selectdates.php
 * @version 0.0.7.0 1st July 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2022
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;


HTMLHelper::_('behavior.core');

$title = $displayData['title'];
Text::script('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST');
$message = "alert(Joomla.JText._('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'));";
?>
<button type="button" data-toggle="modal" 
	onclick="if (document.adminForm.boxchecked.value==0){<?php echo $message; ?>}else{jQuery( '#collapseModal' ).modal('show'); return true;}" 
	class="btn btn-small">
	<span class="icon-file-check" aria-hidden="true"></span>
	<?php echo $title; ?>
</button>
