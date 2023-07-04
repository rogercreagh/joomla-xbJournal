<?php
/*******
 * @package xbJournals Component
 * @filesource admin/views/calendars/tmpl/defaut_dates_body.php
 * @version 0.0.7.2 4th July 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2022
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Layout\LayoutHelper;


?>
<a class="btn" type="button" onclick="document.getElementById('startdate').value='';document.getElementById('enddate').value='';" data-dismiss="modal">
	<?php echo JText::_('JCANCEL'); ?>
</a>
<button class="btn btn-success" type="submit" onclick="pleaseWait('waiting'); Joomla.submitbutton('calendars.fetchDateItems');">
	<?php echo JText::_('XBJOURNALS_FETCH_FROM_SERVER'); ?>
</button>
