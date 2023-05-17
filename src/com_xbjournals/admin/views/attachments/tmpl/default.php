<?php
/*******
 * @package xbJournals Compnent
 * @filesource admin/views/attachments/tmpl/default.php
 * @version 0.0.5.3 17th May 2023
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
    $listOrder=' label';
    $listDirn = 'ascending';
}

$itemeditlink='index.php?option=com_xbjournals&view=note&task=note.edit&id=';
$caleditlink='index.php?option=com_xbjournals&view=calendar&task=calendar.edit&id=';

?>
<form action="<?php echo Route::_('index.php?option=com_xbjournals&view=attachments'); ?>" method="post" name="adminForm" id="adminForm">
<div class="row-fluid">
	<div id="j-sidebar-container">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" >
		<h3><?php echo Text::_( 'XBJOURNALS_ATTACHMENTS' ); ?></h3>

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
		<table class="table table-striped table-hover" id="xbjournalsNotesList">	
			<colgroup>
				<col class="center hidden-phone" style="width:25px;"><!-- checkbox -->
				<col ><!-- calendar/item -->
				<col ><!-- label -->
				<col ><!-- path (rem/local -->
				<col ><!-- filename (rem/local) -->
				<col class="hidden-tablet hidden-phone" style="width:230px;"><!-- type -->
				<col class="hidden-phone"><!-- info -->
				<col class="hidden-phone" style="width:100px;" ><!-- dtstamp -->
				<col class="nowrap center hidden-phone" style="width:45px;"><!-- id -->
			</colgroup>	
			<thead>
				<tr>
					<th>
						<?php echo HTMLHelper::_('grid.checkall'); ?>
					</th>
					<th>
						<?php echo HTMLHelper::_('searchtools.sort','XBJOURNALS_LINKED_ENTRY','cal_title',$listDirn,$listOrder); ?>
					</th>
					<th>						
						<?php echo HTMLHelper::_('searchtools.sort', 'XBJOURNALS_LABEL', 'label', $listDirn, $listOrder); ?>
					</th>
					<th><?php echo Text::_('XBJOURNALS_PATH_LOCREM'); ?>
					</th>
					<th><?php echo Text::_('XBJOURNALS_FILENAME_LOCREM'); ?>
					</th>
					<th><?php echo Text::_('XBJOURNALS_FILE_MIME'); ?>
					</th>
					<th><?php echo Text::_('XBJOURNALS_FILE_INFO'); ?>
					</th>
					<th>
						<?php echo HTMLHelper::_('searchtools.sort','XBJOURNALS_DATE','dtstamp',$listDirn,$listOrder); ?>
					</th>
					<th>
						<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder );?>
					</th>
			</thead>
			<tbody>
			<?php foreach ($this->items as $i => $item) :
                $canEdit    = $user->authorise('core.edit', 'com_xbjournals.journal.'.$item->id);
            ?>
                <tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid; ?>">	
					<td>
						<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
					</td>
					<td>
						<p><a href="<?php echo $caleditlink.$item->calid;?>"><?php echo $item->cal_title; ?>
							</a> (<?php echo $item->entry_type; ?>)
							<br /><span class="xbpl10"><a href="<?php echo $itemeditlink.$item->itemid;?>">
								<?php echo $item->itemtitle; ?></a></span>
						</p> 
					</td>
					<td>
						<p><b><?php echo $item->label; ?></b></p>
					</td>
					<td>
						<?php $local = ($item->localpath !='') ? pathinfo($item->localpath) : ''; 
                            $remote = ($item->uri !='') ? pathinfo($item->uri) : '';?>
						<p>
    						<?php if ($local != '') : ?>
    							<span class="xbnit">Local</span>: <?php echo $local['dirname']; ?><br />
    						<?php endif; ?>
    						<?php if ($remote != '') : ?>
    							<span class="xbnit">Rem.</span>: <?php $remote['dirname']; ?>
    						<?php else : ?>
    							<span class="xbnit">(embedded)</span>
    						<?php endif; ?>
						</p>
					</td>
					<td>
						<p>
    						<?php if ($local != '') : ?>
    							<span class="xbnit">Local</span>: <?php echo $local['basename']; ?><br />
    						<?php endif; ?>
    						<?php if ($remote != '') : ?>
    							<span class="xbnit">Rem.</span>: <?php echo $remote['basename']; ?>
    						<?php else : ?>
    							<span class="xbnt">Embed.</span>: <?php echo $item->filename; ?>
    						<?php endif; ?>
						</p>
					</td>
					<td>
						<p><?php echo $item->fmttype; ?>
					</td>
					<td>
						<p><?php echo $item->info; ?>
					</td>
					<td>
						<?php if($item->entrydate) {
						  echo HtmlHelper::date($item->entrydate , 'd M Y H:i');                      
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

	