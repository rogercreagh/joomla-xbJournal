<?php
/*******
 * @package xbJournals Component
 * @filesource admin/controllers/dashboard.php
 * @version 0.0.0.2 3rd April 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
// No direct access to this file
defined('_JEXEC') or die;

class XbjournalsControllerDashboard extends JControllerAdmin {
    
    public function getModel($name = 'Dashboard', $prefix = 'XbjournalsModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config );
        return $model;
    }
    
}
