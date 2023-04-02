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

$usexbcss = $params->get('use_xbcss',1);
if ($usexbcss<2) {
    $cssFile = Uri::root(true)."/media/com_xbjournals/css/xbjournals.css";
    $altcss = $params->get('css_file','');
    if ($usexbcss==0) {
        if ($altcss && file_exists(JPATH_ROOT.$altcss)) {
            $cssFile = $altcss;
        }
    }
    $document->addStyleSheet($cssFile);
}

$cssFile = '<script src="https://kit.fontawesome.com/012857417f.js" crossorigin="anonymous"></script>';
$document->addStyleSheet($cssFile);

// Require helper files
//JLoader::register('XbfilmsHelper', JPATH_ADMINISTRATOR . '/components/com_xbfilms/helpers/xbfilms.php');
//JLoader::register('XbfilmsGeneral', JPATH_ADMINISTRATOR . '/components/com_xbfilms/helpers/xbfilmsgeneral.php');
//JLoader::register('XbcultureHelper', JPATH_ADMINISTRATOR . '/components/com_xbpeople/helpers/xbculture.php');

