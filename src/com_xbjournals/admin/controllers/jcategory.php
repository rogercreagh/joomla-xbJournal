<?php
/*******
 * @package xbJournals
 * @filesource admin/controlers/jcategory.php
 * @version 0.0.6.1 12th June 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class XbjournalsControllerJcategory extends JControllerAdmin {
    
    public function getModel($name = 'Category', $prefix = 'XbjournalsModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);        
        return $model;
    }
    
    function jcategories() {
    	$this->setRedirect('index.php?option=com_xbjournals&view=jcategories');
    }

}