<?php
/*******
 * @package xbJournals Component
 * @filesource admin/controllers/attachments.php
 * @version 0.0.5.0 13th May 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class XbjournalsControllerAttachments extends JControllerAdmin {
	
	public function getModel($name = 'Attachments', $prefix = 'XbjournalsModel', 
	       $config = array('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config );
		return $model;
	}
		
}
