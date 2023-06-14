<?php
/*******
 * @package xbJournals
 * @filesource admin/views/jcategories/view.html.php
 * @version 0.0.6.1 12th June 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

class XbjournalsViewJcategories extends JViewLegacy {
    
    function display($tpl = null) {
        // Get data from the model
        $this->items		= $this->get('Items');
        $this->pagination	= $this->get('Pagination');
        $this->state = $this->get('State');
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        
        $this->searchTitle = $this->state->get('filter.search');
        
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
        	Factory::getApplication()->enqueueMessage(implode('<br />', $errors),'error');
        	
            return false;
        }
        
        
        XbjournalsHelper::addSubmenu('jcategories');
        $this->sidebar = JHtmlSidebar::render();
        
        // Set the toolbar
        $this->addToolBar();
        
        // Display the template
        parent::display($tpl);
    }
    
    protected function addToolBar() {
        $canDo = XbjournalsHelper::getActions();
        
        ToolbarHelper::title(Text::_( 'XBJOURNALS_ADMIN_CATEGORIES_TITLE' ), 'folder' );
        
        //index.php?option=com_categories&view=category&layout=edit&extension=com_xbfilms
        if ($canDo->get('core.create') > 0) {
        	ToolbarHelper::custom('jcategories.categorynew','new','','XBJOURNALS_NEW_CAT',false);
        }
        if ($canDo->get('core.admin')) {
        	ToolbarHelper::editList('jcategories.categoryedit', 'XBJOURNALS_EDIT_CAT');       	
         }
         
         ToolbarHelper::custom(); //spacer
         
         if ($canDo->get('core.admin')) {
        	ToolbarHelper::preferences('com_xbjournals');
        }
        ToolbarHelper::help( '', false,'https://crosborne.uk/xbjournals/doc?tmpl=component#admin-cats' );
    }

    protected function setDocument() {
    	$document = Factory::getDocument();
    	$document->setTitle(Text::_('XBJOURNALS_ADMIN_CATEGORIES_TITLE'));
    }
}
