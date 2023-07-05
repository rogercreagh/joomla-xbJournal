<?php
/*******
 * @package xbJournals Component
 * @filesource admin/controllers/server.php
 * @version 0.0.7.4 5th July 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;

class XbjournalsControllerServer extends  FormController {
    
    public function __construct($config = array(), MVCFactoryInterface $factory = null)
    {
        parent::__construct($config, $factory);
        //$this->registerTask('savepreview', 'save');
    }
    
    public function listcals() {
        $jip =  Factory::getApplication()->input;
        $sid = $jip->get('id');
        $clist = XbjournalsHelper::listServerCalendars($sid);
        Factory::getApplication()->enqueueMessage($clist);
        $this->setRedirect('index.php?option=com_xbjournals&task=server.edit&id='.$sid);        
    }
    
    public function getcals() {
        $jip =  Factory::getApplication()->input;
        $sid = $jip->get('id');
        $cnts = XbjournalsHelper::getServerCalendars($sid);
        Factory::getApplication()->enqueueMessage($cnts['new'].' new calendars added, '.$cnts['update'].' updated, '.$cnts['same'].' unchanged');
        $this->setRedirect('index.php?option=com_xbjournals&task=server.edit&id='.$sid);
        
    }
 
//     protected function xgetcals() {
//         $item = $model->getItem();
//         $serverid = $item->get('id');
//         $newcnt = XbjournalsHelper::getServerCalendars($serverid);
//         Factory::getApplication()->enqueueMessage($cnts['new'].' new calendars added, '.$cnts['update'].' updated, '.$cnts['same'].' unchanged');
//         $this->setRedirect('index.php?option=com_xbjournals&view=server&id='.$serverid);        
//     }

    protected function postSaveHook(JModelLegacy $model, $validData = array()) {
        $item = $model->getItem();
        $sid = $item->get('id');
        $cnts = XbjournalsHelper::listServerCalendars($sid);
        Factory::getApplication()->enqueueMessage($clist); 
    }

}
