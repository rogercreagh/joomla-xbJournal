<?php
/*******
 * @package xbJournals Component
 * @filesource admin/controllers/calendars.php
 * @version 0.0.7.1 4th July 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class XbjournalsControllerCalendars extends JControllerAdmin {
	
    public function getModel($name = 'Calendar', $prefix = 'XbjournalsModel', $config = array('ignore_request' => true)) {
				$model = parent::getModel($name, $prefix, $config );
				return $model;
	}
	
// 	public function getServerItems() {
// 	    $jip =  Factory::getApplication()->input;
// 	    $cid =  $jip->get('cid');
// 	    $calid = $cid[0];
// 	    $newcnt = XbjournalsHelper::getCalendarJournalEntries($calid);
// 	    Factory::getApplication()->enqueueMessage($cnts['new'].' new entries added, '.$cnts['update'].' updated, '.$cnts['same'].' unchanged');
// 	    $this->setRedirect('index.php?option=com_xbjournals&view=calendars');
// 	}
	
	public function fetchAllItems() {
	    $jip =  Factory::getApplication()->input;
	    $cid =  $jip->get('cid');
	    $calid = $cid[0];
	    $newcnts = $this->getModel('Calendars')->importJournalItems($calid);
	    $this->setRedirect('index.php?option=com_xbjournals&view=calendars');
	    
	}
	
	public function fetchDateItems() {
	    $jip =  Factory::getApplication()->input;
	    $cid =  $jip->get('cid');
	    $calid = $cid[0];
	    $start = $jip->get('startdate');
	    $end = $jip->get('enddate');
	    $dateprop = $jip->get('dateprop');
	    //	    Factory::getApplication()->enqueueMessage('<pre>'.print_r($jip,true).'</pre>');
	    $newcnts = $this->getModel('Calendars')->importJournalItems($calid, $start, $end, $dateprop);
	    $this->setRedirect('index.php?option=com_xbjournals&view=calendars');
	    
	}
	
/* 	public function getItemsSelection() {
	    $jip =  Factory::getApplication()->input;
	    $cid =  $jip->get('cid');
	    $calid = $cid[0];
	    $start = '';
	    $end = '';
	    $dateprop ='created';
	    $type = '';
	    $newcnts = $this->getModel('Calendars')->importSelectedItems($calid);
	    $this->setRedirect('index.php?option=com_xbjournals&view=calendars');
	    
	}
	
	public function getChangedItems() {
	    $jip =  Factory::getApplication()->input;
	    $cid =  $jip->get('cid');
	    $calid = $cid[0];
	    $newcnts = $this->getModel('Calendars')->importNewItems($calid);
	    $this->setRedirect('index.php?option=com_xbjournals&view=calendars');
	    
	}
	
	public function syncItems() {
	    Factory::getApplication()->enqueueMessage('Sorry, synchronisation is not yet implemented','Warning');
// 	    $jip =  Factory::getApplication()->input;
// 	    $cid =  $jip->get('cid');
// 	    $calid = $cid[0];
// 	    $newcnts = $this->getModel('Calendars')->importJournalItems($calid);
	    $this->setRedirect('index.php?option=com_xbjournals&view=calendars');
	    
	}
	
 */}
