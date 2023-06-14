<?php
/*******
 * @package xbJournals
 * @filesource admin/controlers/jcategories.php
 * @version 0.0.6.1 12th June 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class XbjournalsControllerJcategories extends JControllerAdmin {
    
    protected $edcatlink = 'index.php?option=com_categories&task=category.edit&extension=';
    
    public function getModel($name = 'Categories', $prefix = 'XbjournalsModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
    
    function categoryedit() {
    	$ids =  Factory::getApplication()->input->get('cid');
    	$id=$ids[0];
    	//check if this is a film or people category
    	$db = Factory::getDBO();
    	$db->setQuery('SELECT extension FROM #__categories WHERE id = '.$db->quote($id));
    	$ext = $db->loadResult();
    	if ($ext == 'com_xbjournals') {
    	    $this->setRedirect($this->edcatlink.'com_xbjournals&id='.$id);
    	} else {
    	    if (XbjournalsHelper::checkComponent($ext)==1){
    	        $this->setRedirect($this->edcatlink.$ext.'&id='.$id);
    	    } else {
    	        Factory::getApplication()->enqueueMessage($ext.' not available','error');
    	    }
    	}
    }
    
    function categorynew() {
        $this->setRedirect($this->edcatlink.'com_xbjournals&id=0');
    }
        
}