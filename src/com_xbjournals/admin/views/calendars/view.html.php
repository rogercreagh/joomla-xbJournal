<?php
/*******
 * @package xbJournals Component
 * @filesource admin/views/calendars/view.html.php
 * @version 0.0.6.1 13th June 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;

class XbjournalsViewCalendars extends JViewLegacy {
    
    public function display($tpl = null) {    
 
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
//        $this->filterForm = $this->get('FilterForm');
//        $this->activeFilters = $this->get('ActiveFilters');
        
//        $this->searchTitle = $this->state->get('filter.search');
        
        
        // Check for errors.
//        if (count($errors = $this->get('Errors'))) {
//            throw new Exception(implode("\n", $errors), 500);
//        }
        
        $this->addToolbar();
        XbjournalsHelper::addSubmenu('calendars');
        $this->sidebar = JHtmlSidebar::render();
        
        parent::display($tpl);
        
        $this->setDocument();
        
    }
    
    protected function addToolbar() {
        $canDo = XbjournalsHelper::getActions();
        
        ToolbarHelper::title(Text::_( 'XBJOURNALS_ADMIN_CALENDARS_TITLE' ), 'screen' );
        
//         if ($canDo->get('core.create') > 0) {
//             ToolbarHelper::addNew('server.add','New Server');
//         }
//        ToolbarHelper::custom('calendars.getServerItems', 'file-plus', '', 'get items', true) ;
//        ToolbarHelper::custom('calendars.getAllItems', 'file-plus', '', 'Get All Items', true) ;
//        ToolbarHelper::custom('calendars.getChangedItems', 'new', '', 'Get New Items', true) ;
//        ToolbarHelper::custom('calendars.syncItems', 'refresh', '', 'Sync Items', true) ;

        $bar = Toolbar::getInstance('toolbar');
        $btnHtml = '<button onclick="if (document.adminForm.boxchecked.value == 0) {
            alert(Joomla.JText._(\'JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST\'));
        } else {
            pleaseWait(); Joomla.submitbutton(\'calendars.getAllItems\');
        }" class="btn btn-small"><span class="icon-file-plus"></span>Get All Items</button>';
        $bar->appendButton('Custom', $btnHtml, 'getall');
        $btnHtml = '<button onclick="if (document.adminForm.boxchecked.value == 0) {
            alert(Joomla.JText._(\'JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST\'));
        } else {
            pleaseWait(); Joomla.submitbutton(\'calendars.getChangedItems\');
        }" class="btn btn-small"><span class="icon-new"></span>Get New Items</button>';
        $bar->appendButton('Custom', $btnHtml, 'getnew');
        $btnHtml = '<button onclick="if (document.adminForm.boxchecked.value == 0) {
            alert(Joomla.JText._(\'JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST\'));
        } else {
            pleaseWait(); Joomla.submitbutton(\'calendars.syncItems\');
        }" class="btn btn-small"><span class="icon-refresh"></span>Sync Items</button>';
        $bar->appendButton('Custom', $btnHtml, 'sync');
        
        //        $layout = new FileLayout('joomla.toolbar.batch');
//        $batchButtonHtml = $layout->render(array('title' => Text::_('XBJOURNALS_RANGE')));
//        $bar->appendButton('Custom', $batchButtonHtml, 'batch');
        
        //        if ($canDo->get('core.edit') || ($canDo->get('core.edit.own'))) {
//            ToolbarHelper::editList('film.edit');
//        }
//         if ($canDo->get('core.edit.state')) {
//             ToolbarHelper::publish('film.publish','JTOOLBAR_PUBLISH', true);
//             ToolbarHelper::unpublish('film.unpublish', 'JTOOLBAR_UNPUBLISH', true);
//             ToolbarHelper::archiveList('film.archive');
//         }
//         if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
//             ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'film.delete','JTOOLBAR_EMPTY_TRASH');
//         } else if ($canDo->get('core.edit.state')) {
//             ToolbarHelper::trash('film.trash');
//         }
        
//         // Add a batch button
//         if ($canDo->get('core.create') && $canDo->get('core.edit')
//             && $canDo->get('core.edit.state'))
//         {
//             // we use a standard Joomla layout to get the html for the batch button
//             $bar = Toolbar::getInstance('toolbar');
//             $layout = new FileLayout('joomla.toolbar.batch');
//             $batchButtonHtml = $layout->render(array('title' => Text::_('JTOOLBAR_BATCH')));
//             $bar->appendButton('Custom', $batchButtonHtml, 'batch');
//         }
        
        if ($canDo->get('core.admin')) {
            ToolbarHelper::preferences('com_xbjournals');
        }
        ToolbarHelper::help( '', false,'https://crosborne.uk/xbjournals/doc?tmpl=component#admin-calendars' );
    }
    
    protected function setDocument() {
        $document = Factory::getDocument();
        $document->setTitle(Text::_('XBJOURNALS_ADMIN_CALENDARS_TITLE'));
    }
    
    
}
