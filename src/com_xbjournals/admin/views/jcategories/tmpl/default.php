<?php
/*******
 * @package xbJournals
 * @filesource admin/views/jcategories/tmpl/default.php
 * @version 0.0.6.4 16th June 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('formbehavior.chosen', 'select');

$user = Factory::getUser();
$userId         = $user->get('id');
$listOrder     = $this->escape($this->state->get('list.ordering'));
$listDirn      = $this->escape($this->state->get('list.direction'));

$celink = 'index.php?option=com_categories&task=category.edit&id=';
$cvlink = 'index.php?option=com_xbjournals&view=jcategory&id=';
$jvlink = 'index.php?option=com_xbjournals&view=journals&catid=';
$calvlink = 'index.php?option=com_xbjournals&view=calendars&catid=';

$prevext ='';

?>
<form action="index.php?option=com_xbjournals&view=jcategories" method="post" id="adminForm" name="adminForm">
	<?php if (!empty( $this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
	<?php else : ?>
        <div id="j-main-container">
	<?php endif;?>
	
	<div>
		<h3><?php echo Text::_('XBJOURNALS_ADMIN_CATEGORIES_TITLE'); ?></h3>
      	<p class="xb095"><?php echo Text::_('XBJOURNALS_CATSPAGE_SUBTITLE'); ?></p>
     </div>
	
	<div class="pull-right span2">
		<p style="text-align:right;">
			<?php $fnd = $this->pagination->total;
			echo $fnd .' '. lcfirst(Text::_(($fnd==1)?'XBJOURNALS_CATEGORY':'XBJOURNALS_CATEGORIES')).' '.Text::_('XBJOURNALS_FOUND'); ?>
		</p>
	</div>
	<div class="clearfix"></div>

	<?php
        // Search tools bar
        echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
    ?>
	<div class="clearfix"></div>

	<?php if (empty($this->items)) : ?>
		<div class="alert alert-no-items">
			<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
	<?php else : ?>	

<table class="table table-striped table-hover">
<thead>
<tr>
					<th class="hidden-phone center" style="width:25px;">
						<?php echo HTMLHelper::_('grid.checkall'); ?>
					</th>
			<th width="5%">
				<?php echo Text::_('JSTATUS'); ?>
			</th>
			<th>
				<?php echo HTMLHelper::_('grid.sort', 'XBJOURNALS_CATEGORY', 'path', $listDirn, $listOrder );?>
			</th>
			<th>
				<?php echo Text::_('XBJOURNALS_DESCRIPTION') ;?>
			</th>
			<th>
				<?php echo HTMLHelper::_('grid.sort', 'XBJOURNALS_CALENDARS', 'ccnt', $listDirn, $listOrder );?>
			</th>
			<th>
				<?php echo HTMLHelper::_('grid.sort', 'XBJOURNALS_JOURNALS', 'jcnt', $listDirn, $listOrder );?>
			</th>
			<th>
				<?php echo HTMLHelper::_('grid.sort', 'XBJOURNALS_NOTES', 'ncnt', $listDirn, $listOrder );?>
			</th>
			<th class="nowrap hidden-tablet hidden-phone" style="width:45px;">
				<?php echo HTMLHelper::_('grid.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder );?>
			</th>
		</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach ($this->items as $i => $item) :
				$canCheckin = $user->authorise('core.manage', 'com_checkin');
			?>
			<tr class="row<?php echo $i % 2; ?>" >	
					<td class="center hidden-phone">
						<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
					</td>
				<td class="center">
					<div class="btn-group">
						<?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'category.', false, 'cb'); ?>
							<?php if ($item->note!=''){ ?>
								<span class="btn btn-micro active hasTooltip" title="" 
									data-original-title="<?php echo '<b>'.Text::_( 'XBJOURNALS_ADMIN_NOTE' ) .'</b>: '. htmlentities($item->note); ?>">
									<i class="icon-info xbinfo"></i>
								</span>
							<?php } else {?>
								<span class="btn btn-micro inactive" style="visibility:hidden;" title=""><i class="icon-info"></i></span>
							<?php } ?>
					</div>
				</td>
 				<td>
					<?php if ($item->checked_out) {
    					$couname = Factory::getUser($item->checked_out)->username;
    					echo HTMLHelper::_('jgrid.checkedout', $i, Text::_('XBJOURNALS_OPENED_BY').': '.$couname, $item->checked_out_time, 'categories.', false);
    				} ?>
					<span class="xbnote"> 
 					<?php 	$path = substr($item->path, 0, strrpos($item->path, '/'));
						$path = str_replace('/', ' - ', $path);
						echo ($path!='') ? $path.' - <br/>' : ''; ?>
						</span>    				
    					<a href="<?php echo Route::_($cvlink . $item->id); ?>" title="Details" 
    						class="label label-success" style="padding:2px 8px;">
    						<span class="xb11"><?php echo $item->title; ?></span>
    					</a>
    			</td>
    			<td>
    				<p class="xb09"><?php echo $item->description; ?></p>
    			</td>
    			<td align="center">
   					<?php if ($item->ccnt >0) : ?> 
   						<span class="badge badge-pink">
   							<a href="<?php echo $calvlink.$item->id;?>"><?php echo $item->ccnt; ?>
   						</a></span>
   					<?php endif; ?>
   				</td>
    			<td align="center">
   					<?php if ($item->jcnt >0) : ?> 
   						<span class="badge badge-cyan">
   							<a href="<?php echo $jvlink.$item->id;?>"><?php echo $item->jcnt; ?>
   						</a></span>
   					<?php endif; ?>
   				</td>
    			<td align="center">
   					<?php if ($item->ncnt >0) : ?> 
   						<span class="badge badge-yellow">
   							<a href="<?php echo $nvlink.$item->id;?>"><?php echo $item->ncnt; ?>
   						</a></span>
   					<?php endif; ?>
   				</td>
  				<td align="center">
					<?php echo $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php endif; ?>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
<div class="clearfix"></div>
<p><?php echo XbjournalsHelper::credit('xbJournals');?></p>

