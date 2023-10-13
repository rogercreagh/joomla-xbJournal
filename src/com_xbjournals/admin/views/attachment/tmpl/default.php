<?php
/*******
 * @package xbJournals Component
 * @filesource admin/views/attachment/tmpl/edit.php
 * @version 0.1.4.0 12th October 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

?>

<p>id: <?php 
echo $this->item->id;
?></p>
<p>entry_id: <?php 
echo $this->item->entry_id;
?></p>
<p>atthash: <?php 
echo $this->item->atthash;
?></p>
<p>uri: <?php 
echo $this->item->uri;
?></p>
<p>encoding: <?php 
echo $this->item->encoding;
?></p>
<p>fmttype: <?php 
echo $this->item->fmttype;
?></p>
<p>value: <?php 
echo $this->item->value;
?></p>
<p>filename: <?php 
echo $this->item->filename;
?></p>
<p>label: <?php 
echo $this->item->label;
?></p>
<p>otherparams: <?php 
echo $this->item->otherparams;
?></p>
<p>info: <?php 
echo $this->item->info;
?></p>
<p>localpath: <?php 
echo $this->item->localpath;
?></p>
