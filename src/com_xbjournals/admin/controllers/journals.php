<?php
/*******
 * @package xbJournals Component
 * @filesource admin/controllers/journals.php
 * @version 0.0.3.0 8th May 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class XbjournalsControllerJournals extends JControllerAdmin {
	
	public function getModel($name = 'Journal', $prefix = 'XbjournalsModel', 
	       $config = array('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config );
		return $model;
	}
		
}
