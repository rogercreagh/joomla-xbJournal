<?php
/*******
 * @package xbJournals Component
 * @filesource admin/controllers/servers.php
 * @version 0.1.0.1 6th July 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class XbjournalsControllerServers extends JControllerAdmin {
	
	public function getModel($name = 'Server', $prefix = 'XbjournalsModel', $config = array('ignore_request' => true)) {
				$model = parent::getModel($name, $prefix, $config );
				return $model;
	}
	
	public function getcals() {
	    $jip =  Factory::getApplication()->input;
	    $cid =  $jip->get('cid');
	    $serverid = $cid[0];
	    $clist = XbjournalsHelper::getServerCalendars($serverid.'list');
	    Factory::getApplication()->enqueueMessage($clist);
	    $this->setRedirect('index.php?option=com_xbjournals&view=servers');
	}
	
	public function listcals() {
	    $jip =  Factory::getApplication()->input;
	    $cid =  $jip->get('cid');
	    $sid = $cid[0];
	    $clist = XbjournalsHelper::listServerCalendars($sid);
	    Factory::getApplication()->enqueueMessage($clist);
	    $this->setRedirect('index.php?option=com_xbjournals&view=servers');
	}
	
	
}
