<?php
/*******
 * @package xbJournals
 * @filesource 
 * @version 0.0.0.1 1st April 2023
 * @since 0.0.0.1 1st April 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\MVC\Controller\BaseController;

$app = Factory::getApplication();
if (!Factory::getUser()->authorise('core.manage', 'com_xbjournals')) {
    Factory::getApplication()->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'),'warning');
    return false;
}

//add the component, xbculture and fontawesome css
$params = ComponentHelper::getParams('com_xbjournals');
if ($params->get('savedata','notset')=='notset') {
    Factory::getApplication()->enqueueMessage(Text::_('XBCULTURE_OPTIONS_UNSAVED'),'Error');
}

$document = Factory::getDocument();

// $usexbcss = $params->get('use_xbcss',1);
// if ($usexbcss<2) {
//     $cssFile = Uri::root(true)."/media/com_xbjournals/css/xbjournals.css";
//     $altcss = $params->get('css_file','');
//     if ($usexbcss==0) {
//         if ($altcss && file_exists(JPATH_ROOT.$altcss)) {
//             $cssFile = $altcss;
//         }
//     }
//     $document->addStyleSheet($cssFile);
// }

//$cssFile = '<script src="https://kit.fontawesome.com/012857417f.js" crossorigin="anonymous"></script>';
//$cssFile = "https://use.fontawesome.com/releases/v5.8.1/css/all.css\" integrity=\"sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf\" crossorigin=\"anonymous";
//$document->addStyleSheet($cssFile);

$document->addScript('https://kit.fontawesome.com/012857417f.js', array(), array('crossorigin'=>'anonymous','async'=>'async'));

// Require helper files
JLoader::register('XbjournalsHelper', JPATH_ADMINISTRATOR . '/components/com_xbjournals/helpers/xbjournals.php');
//JLoader::register('XbfilmsGeneral', JPATH_ADMINISTRATOR . '/components/com_xbfilms/helpers/xbfilmsgeneral.php');
//JLoader::register('XbcultureHelper', JPATH_ADMINISTRATOR . '/components/com_xbpeople/helpers/xbculture.php');

// Get an instance of the controller prefixed
$controller = JControllerLegacy::getInstance('Xbjournals');

// Perform the Request task and Execute request task
$controller->execute(Factory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();

