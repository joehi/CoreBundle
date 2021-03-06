<?php

/*
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package    con4gis
 * @version    6
 * @author     con4gis contributors (see "authors.txt")
 * @license    LGPL-3.0-or-later
 * @copyright  Küstenschmiede GmbH Software & Design
 * @link       https://www.con4gis.org
 */

use \con4gis\CoreBundle\Classes\Contao\Hooks\con4gisInsertTags;

$GLOBALS['con4gis']['version'] = '6.0';
$GLOBALS['con4gis']['core']['installed'] = true;

// API-Registration
$GLOBALS['TL_API'] = array();
$GLOBALS['TL_API']['fileUpload']  = 'con4gis\CoreBundle\Classes\C4GFileUpload';
$GLOBALS['TL_API']['imageUpload'] = 'con4gis\CoreBundle\Resources\contao\classes\C4GImageUpload';
$GLOBALS['TL_API']['deliver']     = 'con4gis\CoreBundle\Resources\contao\classes\C4GDeliverFileApi';

array_insert($GLOBALS['BE_MOD'], array_search('content', array_keys($GLOBALS['BE_MOD'])) + 1, array('con4gis_core' => array()));
array_insert($GLOBALS['BE_MOD'], array_search('content', array_keys($GLOBALS['BE_MOD'])) + 2, array('con4gis_bricks' => array()));

if(TL_MODE == "BE") {
    $GLOBALS['TL_CSS'][] = '/bundles/con4giscore/css/con4gis.css';
}
$GLOBALS['con4gis']['stringClass'] = '\Contao\StringUtil';

/**
 * Backend Modules
 */
array_insert($GLOBALS['BE_MOD'], array_search('content', array_keys($GLOBALS['BE_MOD'])) + 1, array
(
    'con4gis_core' => array
    (
        'c4g_core' => array
        (
            'callback' => 'con4gis\CoreBundle\Resources\contao\classes\C4GInfo'
        ),
        'c4g_settings' => array(
            'tables'        => array('tl_c4g_settings')
        ),
        'c4g_log' => array(
            'tables'        => array('tl_c4g_log')
        )
    )
));

//$GLOBALS['BE_MOD']['con4gis'] =
//    \con4gis\CoreBundle\Resources\contao\classes\C4GUtils::sortBackendModules($GLOBALS['BE_MOD']['con4gis']);

if(TL_MODE == "FE") {
    // TODO replace with symfony csrf token
    $rq = \Contao\RequestToken::get();
    $GLOBALS['TL_HEAD'][] = "<script>var c4g_rq = '" . $rq . "';</script>";
}
$apiBaseUrl = 'con4gis/api';

$GLOBALS['TL_HEAD'][] = "<script>var apiBaseUrl = '" . $apiBaseUrl . "';</script>";

$GLOBALS['TL_HEAD'][] = "<script>window.FontAwesomeConfig = {searchPseudoElements: true}</script>";

/**
 * Content Elements
 */
array_insert($GLOBALS['TL_CTE']['con4gis'], 2, array
(
    'c4g_activationpage' => 'con4gis\CoreBundle\Resources\contao\modules\ContentC4gActivationpage'
));
$GLOBALS['TL_MODELS']['tl_c4g_activationkey'] = 'con4gis\CoreBundle\Resources\contao\models\C4gActivationkeyModel';

$GLOBALS['TL_MODELS']['tl_c4g_settings'] = 'con4gis\CoreBundle\Resources\contao\models\C4gSettingsModel';

/**
 * Purge jobs
 */
$GLOBALS['TL_PURGE']['folders']['con4gis'] = array
(
    'callback' => array('con4gis\CoreBundle\Resources\contao\classes\C4GAutomator', 'purgeApiCache'),
    'affected' => array('var/cache/prod/con4gis')
);

/**
 * Con4Gis Caching
 *
 * caching is not auto-enabled. To register a service to be cached, insert it in the GLOBALS-Array
 * eg: $GLOBALS['CON4GIS']['USE_CACHE']['SERVICES'][] = "layerService"; => layerService requests are cached
 *
 * it is also possible to enable caching while existence of defined get parameters and values
 * eg: $GLOBALS['CON4GIS']['USE_CACHE']['PARAMS']['method'] = array('getLive'); => request with method=getLive will be cached
 *
 */
if (!$GLOBALS['CON4GIS']['USE_CACHE'])
{
    $GLOBALS['CON4GIS']['USE_CACHE'] = array();
    $GLOBALS['CON4GIS']['USE_CACHE']['SERVICES'] = array();
    $GLOBALS['CON4GIS']['USE_CACHE']['PARAMS'] = array();
}

/**
 * replace con4gis insertTags
 */
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('\con4gis\CoreBundle\Classes\Contao\Hooks\con4gisInsertTags', 'replaceTag');