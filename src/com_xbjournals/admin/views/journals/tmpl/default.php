<?php
/*******
 * @package xbJournals Compnent
 * @filesource admin/views/calendars/tmpl/default.php
 * @version 0.0.1.2 23rd April 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
// No direct access to this file
defined('_JEXEC') or die;


use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;

HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

$user = Factory::getUser();
$userId = $user->get('id');

$listOrder     = $this->escape($this->state->get('list.ordering'));
$listDirn      = $this->escape(strtolower($this->state->get('list.direction')));
if (!$listOrder) {
    $listOrder='title';
    $listDirn = 'ascending';
}

$saveOrder      = $listOrder == 'ordering';
$canOrder       = $user->authorise('core.edit.state', 'com_xbjournals.server');
if ($saveOrder) {
    $saveOrderingUrl = 'index.php?option=com_xbjournals&task=journals.saveOrderAjax&tmpl=component';
    HTMLHelper::_('sortablelist.sortable', 'xbjournalsJournalsList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$itemeditlink='index.php?option=com_xbjournals&view=journal&task=journal.edit&id=';
$caleditlink='index.php?option=com_xbjournals&view=calendar&task=calendar.edit&id=';

?>
<form action="<?php echo Route::_('index.php?option=com_xbjournals&view=journals'); ?>" method="post" name="adminForm" id="adminForm">
<div class="row-fluid">
	<div id="j-sidebar-container">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" >
		<h3><?php echo Text::_( 'XBJOURNALS_JOURNAL_ENTRIES' ); ?></h3>
	<div class="pull-right span2">
		<p style="text-align:right;">
			<?php $fnd = $this->pagination->total;
			echo $fnd .' '. Text::_(($fnd==1) ? 'XBJOURNALS_ENTRY':'XBJOURNALS_ENTRIES').' '.Text::_('XBJOURNALS_FOUND_IN');
			echo ' '.$this->jcnt. Text::_(($this->jcnt==1) ? 'XBJOURNALS_JOURNAL':'XBJOURNALS_JOURNALS');
            ?>
		</p>
	</div>
	<div class="clearfix"></div>
	
	<div class="pagination">
		<?php  echo $this->pagination->getPagesLinks(); ?>
		<br />
	    <?php //echo 'sorted by '.$orderNames[$listOrder].' '.$listDirn ; ?>
	</div>
	<?php // Search tools bar
//        echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
    ?>
	<div class="clearfix"></div>

	<?php if (empty($this->items)) : ?>
		<div class="alert alert-no-items">
			<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
	<?php else : ?>
		<table class="table table-striped table-hover" id="xbjournalsJournalsList">	
			<colgroup>
				<col class="hidden-phone" style="width:25px;"><!-- ordering -->
				<col class="hidden-phone" style="width:25px;"><!-- checkbox -->
				<col style="width:55px;"><!-- status -->
				<col ><!-- calendar -->
				<col ><!-- title, -->
				<col ><!-- dtstart -->
				<col class="hidden-phone" style="width:230px;" ><!-- attach -->
				<col class="hidden-tablet hidden-phone" style="width:230px;"><!-- cats & tags -->
				<col class="" style="width:100px;" ><!-- syncdate -->
				<col class="hidden-phone" style="width:45px;"><!-- id -->
			</colgroup>	
			<thead>
				<tr>
					<th class="nowrap center" >
						<?php echo HTMLHelper::_('searchtools.sort', '', 'a.ordering', 
						    $listDirn, $listOrder, null, 'asc', 'XBCULTURE_HEADING_ORDERING_DESC', 'icon-menu-2'); ?>
					</th>
					<th class="center">
						<?php echo HTMLHelper::_('grid.checkall'); ?>
					</th>
					<th class="nowrap center">
						<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?php echo HTMLHelper::_('searchtools.sort','XBJOURNALS_JOURNAL','cal_title',$listDirn,$listOrder); ?>
					</th>
					<th>						
						<?php echo HTMLHelper::_('searchtools.sort', 'XBJOURNALS_TITLE', 'title', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?php echo HTMLHelper::_('searchtools.sort','XBJOURNALS_DATE','dtstart',$listDirn,$listOrder); ?>
					</th>
					<th>
						<?php echo Text::_('XBJOURNALS_ATTACHMENTS'); ?>
					</th>
					<th>cats&tags
						<?php echo HTMLHelper::_('searchtools.sort','XBJOURNALS_JOOMLA_CATEGORY','category_title',$listDirn,$listOrder ).' &amp; ';						
						echo Text::_( 'XBJOURNALS_TAGS' ); ?>
					</th>
					<th>
						<?php echo HTMLHelper::_('searchtools.sort','XBJOURNALS_SYNC_DATE','dtstamp',$listDirn,$listOrder); ?>
					</th>
					<th class="nowrap">
						<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder );?>
					</th>
			</thead>
			<tbody>
			</tbody>

		</table>	
	<?php endif; ?>
	
	
	<?php echo $this->pagination->getListFooter(); ?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo HTMLHelper::_('form.token'); ?>
</div>

</form>
<div class="clearfix"></div>
<p><?php echo XbjournalsHelper::credit('xbJournals');?></p>

