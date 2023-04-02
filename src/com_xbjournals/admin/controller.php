<?php
/*******
 * @package xbJournals
 * @filesource 
 * @version 0.0.0.1 1st April 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
// No direct access to this file
defined('_JEXEC') or die;

class XbjournalsController extends JControllerLegacy {
    protected $default_view = 'dashboard';
    
    public function display ($cachable = false, $urlparms = false){
        
        require_once JPATH_ADMINISTRATOR . '/components/com_xbjournals/helpers/SimpleCalDAVClient.php';
        //require_once JPATH_COMPONENT.'/helpers/xbfilmsgeneral.php';
        
        return parent::display();
    }
}

