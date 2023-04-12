<?php
/*******
 * @package xbJournals Component
 * @filesource admin/controllers/server.php
 * @version 0.0.0.7 11th April 2023
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
    
    /**
     * Before saving need to check connection and after need to get new calendars
     */
    protected function postSaveHook(JModelLegacy $model, $validData = array()) {
        $item = $model->getItem();
        $sid = $item->get('id');
        $cnts = XbjournalsHelper::getServerCalendars($sid);
        Factory::getApplication()->enqueueMessage($cnts['new'].' new calendars added, '.$cnts['update'].' updated, '.$cnts['same'].' unchanged for '.$validData['title']);
        
    }
}
