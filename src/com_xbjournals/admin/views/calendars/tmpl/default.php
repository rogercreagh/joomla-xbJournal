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
    $saveOrderingUrl = 'index.php?option=com_xbjournals&task=calendars.saveOrderAjax&tmpl=component';
    HTMLHelper::_('sortablelist.sortable', 'xbjournalsCalendarsList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$calviewlink='index.php?option=com_xbjournals&view=calendar&layout=default&id=';

?>
<form action="<?php echo Route::_('index.php?option=com_xbjournals&view=calendars'); ?>" method="post" name="adminForm" id="adminForm">
<div class="row-fluid">
	<div id="j-sidebar-container">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" >
		<h3><?php echo Text::_( 'XBJOURNALS_CALENDARS' ); ?></h3>
	<div class="pull-right span2">
		<p style="text-align:right;">
			<?php $fnd = $this->pagination->total;
			echo $fnd .' '. Text::_(($fnd==1)?'XBJOURNALS_CALENDAR':'XBJOURNALS_CALENDARS').' '.Text::_('XBJOURNALS_FOUND');
            ?>
		</p>
	</div>
	<div class="clearfix"></div>
	
	<?php
        // Search tools bar
//        echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
    ?>
	<div class="clearfix"></div>
	
	<div class="pagination">
		<?php  echo $this->pagination->getPagesLinks(); ?>
		<br />
	    <?php //echo 'sorted by '.$orderNames[$listOrder].' '.$listDirn ; ?>
	</div>

	<?php if (empty($this->items)) : ?>
		<div class="alert alert-no-items">
			<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
	<?php else : ?>
		<table class="table table-striped table-hover" id="xbjournalsCalendarsList">	
			<thead>
				<tr>
					<th class="nowrap center hidden-phone" style="width:25px;">
						<?php echo HTMLHelper::_('searchtools.sort', '', 'ordering', 
						    $listDirn, $listOrder, null, 'asc', 'XBJOURNALS_ORDERING_DESC', 'icon-menu-2'); ?>
					</th>
					<th class="hidden-phone center" style="width:25px;">
						<?php echo HTMLHelper::_('grid.checkall'); ?>
					</th>
					<th class="nowrap center" style="width:55px">
						<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?php echo HTMLHelper::_('searchtools.sort','XBJOURNALS_TITLE','title',$listDirn,$listOrder); ?>
					</th>					
					<th>
						<?php echo Text::_('XBJOURNALS_SERVER');?>
					</th>
					<th class="hidden-tablet hidden-phone" >
						<?php echo (Text::_('XBJOURNALS_DESCRIPTION'));?>
					</th>
					<th>
						<?php echo (Text::_('XBJOURNALS_ENTRIES')); ?>
					</th>
					<th class="nowrap hidden-tablet hidden-phone" style="width:100px;">
						<?php echo HTMLHelper::_('searchtools.sort', 'XBJOURNALS_CHECKED', 'last_checked', $listDirn, $listOrder );?>
					</th>
					<th class="nowrap hidden-tablet hidden-phone" style="width:100px;">					
						<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder );?>
					<th>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php
			foreach ($this->items as $i => $item) :
                $canEdit    = $user->authorise('core.edit', 'com_xbjournals.calendar.'.$item->id);
                $canCheckin = $user->authorise('core.manage', 'com_checkin') 
                                        || $item->checked_out==$userId || $item->checked_out==0;
				$canEditOwn = $user->authorise('core.edit.own', 'com_xbmaps.map.'.$item->id) && $item->created_by == $userId;
                $canChange  = $user->authorise('core.edit.state', 'com_xbmaps.map.'.$item->id) && $canCheckin;
			?>
			<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid; ?>">	
				<td class="order nowrap center hidden-phone">
					<?php
						$iconClass = '';
						if (!$canChange) {
							$iconClass = ' inactive';
						} elseif (!$saveOrder) {
							$iconClass = ' inactive tip-top hasTooltip" title="' . HTMLHelper::tooltipText('JORDERINGDISABLED');
						}
					?>
					<span class="sortable-handler<?php echo $iconClass; ?>">
						<span class="icon-menu" aria-hidden="true"></span>
					</span>
					<?php if ($canChange && $saveOrder) : ?>
						<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
					<?php endif; ?>
				</td>
				<td class="center hidden-phone">
					<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
				</td>
				<td class="center">
					<div class="btn-group">
						<?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'calendar.', $canChange, 'cb'); ?>
						<?php if ($item->note!=""){ ?>
							<span class="btn btn-micro active hasTooltip" title="" data-original-title="<?php echo '<b>'.Text::_( 'XBJOURNALS_ADMIN_NOTE' ) .'</b>: '. htmlentities($item->note); ?>">
								<i class="icon- xbinfo"></i>
							</span>
						<?php } else {?>
							<span class="btn btn-micro inactive" style="visibility:hidden;" title=""><i class="icon-info"></i></span>
						<?php } ?>
					</div>
				</td>
				<td>
					<p class="xb12 xbbold xbmb8">
					<?php if ($item->checked_out) {
					    $couname = Factory::getUser($item->checked_out)->username;
					    echo HTMLHelper::_('jgrid.checkedout', $i, Text::_('XBJOURNALS_OPENED_BY').': '.$couname, $item->checked_out_time, 'map.', $canCheckin);
					} ?>
					<?php if ($canEdit || $canEditOwn) : ?>
						<a href="<?php echo Route::_($calviewlink.$item->id);?>"
							title="<?php echo Text::_('edit calendar'); ?>" >
							<b><?php echo $this->escape($item->title); ?></b></a> 
					<?php else : ?>
						<?php echo $this->escape($item->title); ?>
					<?php endif; ?>
                    <br />                        
					<?php $alias = Text::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
                    	<span class="xbnit xb08"><?php echo $alias;?></span>
					</p>
				</td>
				<td>
					<?php echo $item->server_title;?>
				</td>
				<td>
					<?php echo $item->description; ?>
				</td>
				<td>
					<?php if ($item->ecnt > 0) : ?>
						<details>
							<summary>
								<?php echo $item->ecnt.' '.Text::_('XBJOURNALS_TOTAL_JOURNAL_ENTRIES'); ?>
								<br /><?php echo Text::_('XBJOURNALS_MOST_RECENT_FIVE'); ?>
							</summary>
							<ul>
								<?php foreach ($item->entries as $i=>$ent) : ?>
								    <li>
								    	<?php echo $ent['title'].'<br ><span class="xb09">'.$ent['dtstart'].'</span>'; ?>
								    </li>
								<?php  endforeach; ?>
							</ul>
						</details>
					<?php else : ?>
						<?php echo Text::_('XBJOURNALS_NO_ENTRIES_FOUND'); ?>
						<br />
					<?php endif; ?>
				</td>
				<td class="hidden-phone">
					<?php echo HtmlHelper::date($item->last_checked, 'd M Y H:m');?>					
				</td>
				<td class="hidden-phone">
					<?php echo $item->id; ?>					
				</td>
			</tr>			
			<?php endforeach; ?>
			
			</tbody>
		</table>
	
	<?php endif; ?>
	</div>
	<?php echo $this->pagination->getListFooter(); ?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo HTMLHelper::_('form.token'); ?>
</div>
</form>
