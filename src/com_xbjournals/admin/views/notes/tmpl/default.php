<?php
/*******
 * @package xbJournals Compnent
 * @filesource admin/views/notes/tmpl/default.php
 * @version 0.0.4.4 12th May 2023
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
use Joomla\CMS\Uri\Uri;


HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', '.multipleTags', null, array('placeholder_text_multiple' => Text::_('XBJOURNALS_SELECT_TAGS')));
HTMLHelper::_('formbehavior.chosen', '.multipleCats', null, array('placeholder_text_multiple' => Text::_('XBJOURNALS_SELECT_CATS')));
HTMLHelper::_('formbehavior.chosen', 'select');

$user = Factory::getUser();
$userId = $user->get('id');

$listOrder     = $this->escape($this->state->get('list.ordering'));
$listDirn      = $this->escape(strtolower($this->state->get('list.direction')));
if (!$listOrder) {
    $listOrder=' title';
    $listDirn = 'ascending';
}

$saveOrder      = $listOrder == 'ordering';
$canOrder       = $user->authorise('core.edit.state', 'com_xbjournals.server');
if ($saveOrder) {
    $saveOrderingUrl = 'index.php?option=com_xbjournals&task=notes.saveOrderAjax&tmpl=component';
    HTMLHelper::_('sortablelist.sortable', 'xbjournalsNotesList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$itemeditlink='index.php?option=com_xbjournals&view=note&task=note.edit&id=';
$caleditlink='index.php?option=com_xbjournals&view=calendar&task=calendar.edit&id=';
$catviewlink='';
$tagviewlink='';

?>
<form action="<?php echo Route::_('index.php?option=com_xbjournals&view=notes'); ?>" method="post" name="adminForm" id="adminForm">
<div class="row-fluid">
	<div id="j-sidebar-container">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" >
		<h3><?php echo Text::_( 'XBJOURNALS_NOTEBOOK_ENTRIES' ); ?></h3>

		<?php // Search tools bar
            echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
        ?>	
        <div class="pull-right pagination xbm0">
    		<?php  echo $this->pagination->getPagesLinks(); ?>
    	</div>
    
    	<div class="pull-left">
    		<?php  echo $this->pagination->getResultsCounter(); ?> 
          <?php if($this->pagination->pagesTotal > 1) echo ' on '.$this->pagination->getPagesCounter(); ?>
    		<p>
              
                <?php echo 'Sorted by '.$listOrder.' '.$listDirn ; ?>
    		</p>
    	</div>
        <div class="clearfix"></div>      
              
	<?php if (empty($this->items)) : ?>
		<div class="alert alert-no-items">
			<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
	<?php else : ?>
		<?php $subitembreakdone = false; ?>
		<table class="table table-striped table-hover" id="xbjournalsNotesList">	
			<colgroup>
				<col class="nowrap center hidden-phone" style="width:25px;"><!-- ordering -->
				<col class="center hidden-phone" style="width:25px;"><!-- checkbox -->
				<col class="nowrap center" style="width:55px;"><!-- status -->
				<col ><!-- calendar -->
				<col ><!-- title, -->
				<col style="width:140px;" ><!-- dtstart -->
				<col class="hidden-phone" ><!-- attach -->
				<col class="hidden-tablet hidden-phone" style="width:230px;"><!-- cats & tags -->
				<col class="" style="width:100px;" ><!-- syncdate -->
				<col class="center nowrap hidden-phone" style="width:45px;"><!-- id -->
			</colgroup>	
			<thead>
				<tr>
					<th>
						<?php echo HTMLHelper::_('searchtools.sort', '', 'a.ordering', 
						    $listDirn, $listOrder, null, 'asc', 'XBCULTURE_HEADING_ORDERING_DESC', 'icon-menu-2'); ?>
					</th>
					<th>
						<?php echo HTMLHelper::_('grid.checkall'); ?>
					</th>
					<th>
						<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?php echo HTMLHelper::_('searchtools.sort','XBJOURNALS_JOURNAL','cal_title',$listDirn,$listOrder); ?>
					</th>
					<th>						
						<?php echo HTMLHelper::_('searchtools.sort', 'XBJOURNALS_TITLE', 'title', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?php echo HTMLHelper::_('searchtools.sort','XBJOURNALS_CREATED','created',$listDirn,$listOrder); ?>
					</th>
					<th>
						<?php echo Text::_('XBJOURNALS_PARENT'). ', '.Text::_('XBJOURNALS_SUB_ITEMS').' &amp; '.Text::_('XBJOURNALS_ATTACHMENTS'); ?>
					</th>
					<th>
						<?php echo HTMLHelper::_('searchtools.sort','XBJOURNALS_JOOMLA_CATEGORY','category_title',$listDirn,$listOrder ).' &amp; ';						
						echo Text::_( 'XBJOURNALS_TAGS' ); ?>
					</th>
					<th>
						<?php echo HTMLHelper::_('searchtools.sort','XBJOURNALS_SYNC_DATE','dtstamp',$listDirn,$listOrder); ?>
					</th>
					<th>
						<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder );?>
					</th>
			</thead>
			<tbody>
			<?php
			foreach ($this->items as $i => $item) :
                $canEdit    = $user->authorise('core.edit', 'com_xbjournals.journal.'.$item->id);
                $canCheckin = $user->authorise('core.manage', 'com_checkin') 
                                        || $item->checked_out==$userId || $item->checked_out==0;
				$canEditOwn = $user->authorise('core.edit.own', 'com_xbjournals.journal.'.$item->id) && $item->created_by == $userId;
                $canChange  = $user->authorise('core.edit.state', 'com_xbjournals.journal.'.$item->id) && $canCheckin;
                if ((!$subitembreakdone) && ($item->parentuid != '') ) :?>
     				<tr><td colspan="3" style="background-color:#ffe;"></td><td class="xbnit" colspan="7" style="background-color:#ffe;"><?php echo Text::_('XBJOURNALS_SUB_ITEMS_HEADER')?></td><tr>
     				<?php $subitembreakdone = true; ?>
				<?php endif;  ?>
				<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid; ?>">	
					<td class="order">
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
					<td>
						<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
					</td>
					<td>
						<div class="btn-group">
							<?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'journal.', $canChange, 'cb'); ?>
							<?php if ($item->note!=""){ ?>
								<span class="btn btn-micro active hasTooltip" title="" data-original-title="<?php echo '<b>'.Text::_( 'XBJOURNALS_ADMIN_NOTE' ) .'</b>: '. htmlentities($item->note); ?>">
									<i class="icon-info xbinfo"></i>
								</span>
							<?php } else {?>
								<span class="btn btn-micro inactive" style="visibility:hidden;" title=""><i class="icon-info"></i></span>
							<?php } ?>
						</div>
					</td>
					<td>
						<p><a href="<?php echo $caleditlink.$item->calendar_id;?>"><?php echo $item->cal_title.' - '.Text::_('XBJOURNALS_NOTEBOOK'); ?></a>
						</p> 
					</td>
					<td>
						<p>
						<?php if ($item->checked_out) {
						    $couname = Factory::getUser($item->checked_out)->username;
						    echo HTMLHelper::_('jgrid.checkedout', $i, Text::_('XBJOURNALS_OPENED_BY').': '.$couname, $item->checked_out_time, 'journal.', $canCheckin);
						} ?>
						<?php if ($canEdit || $canEditOwn) : ?>
							<a href="<?php echo Route::_($itemeditlink.$item->id);?>"
								title="<?php echo Text::_('XBJOURNALS_EDIT_ITEM'); ?>" >
								<b><?php echo $this->escape($item->title); ?></b>
							</a>
						<?php else : ?>
							<?php echo $this->escape($item->title); ?>
						<?php endif; ?>
						<br /><span class="xb09">alias:<?php echo $item->alias; ?></span>
						</p>
					</td>
					<td>
						<?php if($item->created) {
						  echo HtmlHelper::date($item->created,'D jS M Y');                      
                        } ?>
						
					</td>
					<td>
						<?php if ($item->parent) : ?>
							<p><span class="xbnit"><?php echo Text::_('XBJOURNALS_PARENT'); ?></span>: 
								<?php echo $item->parent; ?>
							</p>
						<?php endif; ?>
						<?php if ($item->subs) : ?>
							<details><summary>
									<span class="xbm0 xbnit"><?php echo Text::_('XBJOURNALS_SUB_ITEMS'); ?>:</span>
								</summary>
    							<?php echo $item->subs; ?>
							</details>
						<?php endif; ?>
						<?php if ($item->atts) : ?>
							<details><summary>
									<span class="xbm0 xbnbit"><?php echo Text::_('XBJOURNALS_ATTACHMENTs'); ?>:</span> 
								</summary>
    							<?php echo $item->atts; ?>
							</details>
						<?php endif; ?>
					</td>
					<td>
						<?php if($item->catid) : ?>
    						<p><a class="label label-cat" href="<?php echo $catviewlink.$item->catid; ?>" 
        							title="<?php echo $item->category_title; ?>">
        								<?php echo $item->category_title; ?>
        							</a>
    						</p>
    					<?php endif; ?>
						<?php if($item->tags) : ?>						
    						<ul class="inline">
    						<?php foreach ($item->tags as $t) : ?>
    							<li><a href="<?php echo $tagviewlink.$t->id; ?>" class="label label-tags">
    								<?php echo $t->title; ?></a>
    							</li>												
    						<?php endforeach; ?>
    						</ul>	
    					<?php endif; ?>					    											
					</td>
					<td>
						<?php if($item->dtstamp) {
						  echo HtmlHelper::date($item->dtstamp , 'd M Y H:i');                      
                        } ?>
					</td>
					<td>
						<?php echo $item->id; ?>
					</td>					
				</tr>
			<?php endforeach; ?>				
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

