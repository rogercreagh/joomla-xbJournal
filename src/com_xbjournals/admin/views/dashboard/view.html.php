<?php
/*******
 * @package xbJournals Component
 * @filesource admin/views/dashboard/view.html.php
 * @version 0.0.5.4 18th May 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

class XbjournalsViewDashboard extends JViewLegacy {
    
    public function display($tpl = null) {    
 
        $params = ComponentHelper::getParams('com_xbjournals');
        $this->savedata = $params->get('savedata',1);
        $this->copy_remote = $params->get('copy_remote',1);
        $this->attach_path = $params->get('attach_path','');        
        
        $this->cats = $this->get('CatCnts');        
        $this->tags = $this->get('TagCnts');
        
        $this->xmldata = Installer::parseXMLInstallFile(JPATH_COMPONENT_ADMINISTRATOR . '/xbjournals.xml');
        $this->client = $this->get('Client');
        
        $this->servers = $this->get('Servers');
        $this->calendars = $this->get('Calendars');
        $this->journalStates = $this->get('JournalStates');
        $this->notebookStates = $this->get('NotebookStates');
        $this->attachmentCounts = $this->get('AttachmentCounts');
        $this->catStates = $this->get('JournalStates');
        $this->tags = $this->get('TagCnts');
        
        $this->state = $this->get('State');
        
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors), 500);
        }
        
//         if ($this->cats){
//             $clink='index.php?option=com_xbjournals&view=catinfo&id=';
//             $this->catlist = '<ul class="inline">';
//             foreach ($this->cats as $key=>$value) {
//                 $this->catlist .= '<li>';
//                 if ($value['level']==1) {
//                     $this->catlist .= '&nbsp;&nbsp;&nbsp;';
//                 } else {
//                     $this->catlist .= ' └─'.substr($value['path'],0,strrpos($value['path'], '/')).'-'; //str_repeat('-&nbsp;', $value['level']-1);
//                 }
//                 $lbl = $value['published']==1 ? 'label-success' : '';
//                 $this->catlist .='<a class="label label-success" href="'.$clink.$value['id'].'">'.$value['title'].'</a>&nbsp;(<i>'.$value['mapcnt'].':'.$value['mrkcnt'].':'.$value['trkcnt'].'</i>) ';
//                 $this->catlist .= '</li>';
//             }
//             $this->catlist .= '</ul>';
//         } else {
               $this->catlist = '<p class="xbnit">'.Text::_('XBJOURNALS_NONE_ASSIGNED').'</p>';
//         }
        
//         if ($this->tags){
//             $tlink='index.php?option=com_xbmaps&view=taginfo&id=';
//             $this->taglist = '<ul class="inline">';
//             foreach ($this->tags['tags'] as $key=>$value) {
//                 $this->taglist .= '<li>';
//                 if ($value['level']==1) {
//                     $this->taglist .= '&nbsp;&nbsp;&nbsp;';
//                 } else {
//                     $this->taglist .= ' └─'.substr($value['path'],0,strrpos($value['path'], '/')).'-';
//                 }
//                 $this->taglist .= '<a class="label label-info" href="'.$tlink.$value['id'].'">'.$key.'</a>&nbsp;(<i>'.$value['mapcnt'].':'.$value['mrkcnt'].':'.$value['trkcnt'].')</i></li> ';
//             }
//             $this->taglist .= '</ul>';
//         } else {
               $this->taglist = '<p class="xbnit">'.Text::_('XBJOURNALS_NONE_ASSIGNED').'</p>';
//         }
        
        $this->addToolbar();
        XbjournalsHelper::addSubmenu('dashboard');
        $this->sidebar = JHtmlSidebar::render();
        
        parent::display($tpl);
        
        $this->setDocument();
        
    }
    
    protected function addToolbar() {
        $canDo = XbjournalsHelper::getActions();
        
        ToolbarHelper::title(Text::_( 'XBJOURNALS_ADMIN_DASHBOARD_TITLE' ), 'info-2' );
        
        if ($canDo->get('core.create') > 0) {
            ToolbarHelper::addNew('server.add','New Server');
        }
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
        ToolbarHelper::help( '', false,'https://crosborne.uk/xbjournals/doc?tmpl=component#admin-dashboard' );
    }
    
    protected function setDocument() {
        $document = Factory::getDocument();
        $document->setTitle(Text::_('XBJOURNALS_ADMIN_DASHBOARD_TITLE'));
    }
    
    
}
