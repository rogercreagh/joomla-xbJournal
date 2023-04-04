<?php
/*******
 * @package xbJournals Component
 * @filesource admin/views/servers/view.html.php
 * @version 0.0.0.3 3rd April 2023
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

class XbjournalsViewServers extends JViewLegacy {
    
    public function display($tpl = null) {    
 
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
//        $this->filterForm = $this->get('FilterForm');
//        $this->activeFilters = $this->get('ActiveFilters');
        
//        $this->searchTitle = $this->state->get('filter.search');
        
//         require_once JPATH_ADMINISTRATOR . '/components/com_xbjournals/helpers/xbCalDav/SimpleCalDAVClient.php';
        
//         $client = new SimpleCalDAVClient();
        
//         $client->connect('https://cloud.crosborne.uk/remote.php/dav/calendars/roger','roger','Tbcb&1g0');
        
//         $arrayOfCalendars = $client->findCalendars(); // Returns an array of all accessible calendars on the server.
        
//         $client->setCalendar($arrayOfCalendars["gweldulas"]); // Here: Use the calendar ID of your choice. If you don't know which calendar ID to use, try config/listCalendars.php
        
//         $this->journalitems = $client->getJournals(); // Returns array($firstNewEventOnServer, $secondNewEventOnServer);
//         $this->notes = $client->getNotes(); // Returns array($firstNewEventOnServer, $secondNewEventOnServer);
        
        // Check for errors.
//        if (count($errors = $this->get('Errors'))) {
//            throw new Exception(implode("\n", $errors), 500);
//        }
        
        $this->addToolbar();
        XbjournalsHelper::addSubmenu('servers');
        $this->sidebar = JHtmlSidebar::render();
        
        parent::display($tpl);
        
        $this->setDocument();
        
    }
    
    protected function addToolbar() {
        $canDo = XbjournalsHelper::getActions();
        
        ToolbarHelper::title(Text::_( 'XBJOURNALS_ADMIN_SERVERS' ), 'screen' );
        
        if ($canDo->get('core.create') > 0) {
            ToolbarHelper::addNew('server.add','New Server');
        }
        ToolbarHelper::custom('servers.getcals', 'file-plus', '', 'XBJOURNALS_GETCALS', true) ;
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
        ToolbarHelper::help( '', false,'https://crosborne.uk/xbjournals/doc?tmpl=component#admin-servers' );
    }
    
    protected function setDocument() {
        $document = Factory::getDocument();
        $document->setTitle(Text::_('XBJOURNALS_ADMIN_SERVERS'));
    }
    
    
}
