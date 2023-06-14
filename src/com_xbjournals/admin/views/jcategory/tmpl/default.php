<?php
/*******
 * @package xbJournals
 * @filesource admin/views/fcategory/tmpl/edit.php
 * @version 0.0.6.1 13th June 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$item = $this->item;
$celink = 'index.php?option=com_categories&task=category.edit&id=';
$xblink = 'index.php?option=com_xbjournals';
?>
<div class="row-fluid">
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container" class="span12">
<?php endif;?>
		<form action="index.php?option=com_xbfilms&view=jcategory" method="post" id="adminForm" name="adminForm">
		<div class="row-fluid xbmb8">
			<div class= "span3">
				  <h3><?php echo Text::_('XBJOURNALS_ADMIN_CATEGORY_TITLE'); ?></h3>
				  <p><?php echo Text::_('XBJOURNALS_CATEGORY_SUBTITLE'); ?></p>
			</div>
			<div class= "span5">
    			<div class="xb11 pull-left xbit xbpt17 xbgrey xbmr20">   				 
    				<?php  $path = substr($item->path, 0, strrpos($item->path, '/'));
    					$path = str_replace('/', ' - ', $path);
    					echo 'root - '.$path; ?>
            	</div>
				<a href="<?php echo $celink.$item->id; ?>" class="badge badge-cat">
					<h2><?php echo $item->title; ?></h2>
				</a></div>
            <div class="span2">
                <p><?php echo '<i>'.Text::_('XBJOURNALS_ALIAS').'</i>: '.$item->alias; ?></p>
                <?php if ($item->note != '') : ?>
                	<p><?php echo '<i>'.Text::_('XBJOURNALS_ADMIN_NOTE').'</i>: '.$item->note; ?></p>
                <?php endif; ?>
            </div>
			<div class= "span2">
				<p><?php echo '<i>'.Text::_('JGRID_HEADING_ID').'</i>: '.$item->id; ?></p>
 			</div>
		</div>
		<div class="row-fluid xbmb8">
			<div class= "span6">
				<p><i><?php echo Text::_('XBJOURNALS_CAT_DESCRIPTION'); ?>:</i></p>
    			<?php if ($item->description != '') : ?>
         			<div class="xbbox xbboxgrey">
        				<?php echo $item->description; ?>
        			</div>
        		<?php else: ?>
        			<p><i><?php echo Text::_('XBJOURNALS_NO_DESC'); ?></i></p>
    			<?php endif; ?>
			</div>
   			<div class="span6">
   				<p>&nbsp;</p>
				<div class="xbbox gradpink xbmh200 xbyscroll">
					<p><?php echo $item->ccnt.' '.Text::_('XBJOURNALS_CALS_IN_CAT'); ?>  <span class="label label-cat"><?php echo $item->title; ?></span></p>
					<?php if ($item->ccnt > 0 ) : ?>
                    	<div class="xbmh200 xbyscroll">
    						<ul>
    						<?php foreach ($item->cals as $cal) { 
    						    echo '<li><a href="'.$xblink.'&view=calendar&task=calendar.edit&id='.$cal->calid.'">'.$cal->title.'</a></li> ';
    						} ?>				
    						</ul>
    					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class= "span6">
				<div class="xbbox gradcyan xbmh200 xbyscroll">
					<p><?php echo $item->jcnt.' '.Text::_('XBJOURNALS_JOURNALS_IN_CAT'); ?>  <span class="label label-cat"><?php echo $item->title; ?></span></p>
					<?php if ($item->jcnt > 0 ) : ?>
                    	<div class="xbmh200 xbyscroll">
    						<ul>
    						<?php foreach ($item->journals as $j) { 
    						    echo '<li><a href="'.$xblink.'&view=journal&task=journal.edit&id='.$j->jid.'">'.$j->title.'</a></li> ';
    						} ?>				
    						</ul>
    					</div>
					<?php endif; ?>
				</div>
			</div>
			<div class= "span6">
				<div class="xbbox gradyellow">
					<p><?php echo $item->ncnt.' '.Text::_('XBJOURNALS_NOTES_IN_CAT'); ?>  <span class="label label-cat"><?php echo $item->title; ?></span></p>
					<?php if ($item->ncnt > 0 ) : ?>
                    	<div class="xbmh200 xbyscroll">
    						<ul>
    						<?php foreach ($item->notes as $i=>$j) { 
    						    echo '<li><a href="'.$xblink.'&view=journal&task=journal.edit&id='.$j->jid.'">';
    						    echo ($j->title !='') ? $j->title : '<i>'.Text::_('XBJOURNALS_NO_TITLE').'</i>';
    						    echo '</a></li> ';
    						} ?>				
    						</ul>
    						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="tid" value="<?php echo $item->id;?>" />
		<?php echo HTMLHelper::_('form.token'); ?>
		</form>
	</div>
</div>
<center>
		<a href="<?php echo $xblink; ?>&view=jcategories" class="btn btn-small">
			<?php echo Text::_('XBJOURNALS_CAT_LIST'); ?></a>
		</center>
<div class="clearfix"></div>
<p><?php echo XbjournalsHelper::credit('xbJournals');?></p>

