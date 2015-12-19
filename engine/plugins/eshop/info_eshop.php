<?php

if (!defined('NGCMS'))
    exit('HAL');

LoadPluginLang('eshop', 'main', '', '', '#');
add_act('core', 'eshop_infovars_show');
register_htmlvar('js', admin_url.'/plugins/eshop/tpl/js/breeze.min.js');

function eshop_infovars_show()
{
global $CurrentHandler, $SYSTEM_FLAGS, $template, $lang, $mysql, $twig, $userROW, $ngCookieDomain;

    //$template['vars']['plugin_eshop_description_delivery'] = pluginGetVariable('eshop', 'description_delivery');
    //$template['vars']['plugin_eshop_description_order'] = pluginGetVariable('eshop', 'description_order');
    //$template['vars']['plugin_eshop_description_phones'] = pluginGetVariable('eshop', 'description_phones');
    $SYSTEM_FLAGS["eshop_description_delivery"] = pluginGetVariable('eshop', 'description_delivery');
    $SYSTEM_FLAGS["eshop_description_order"] = pluginGetVariable('eshop', 'description_order');
    $SYSTEM_FLAGS["eshop_description_phones"] = pluginGetVariable('eshop', 'description_phones');
    
    
    $filter = array();
    if (is_array($userROW)) {
        $filter []= '(user_id = '.db_squote($userROW['id']).')';
    }

    if (isset($_COOKIE['ngTrackID']) && ($_COOKIE['ngTrackID'] != '')) {
        $filter []= '(cookie = '.db_squote($_COOKIE['ngTrackID']).')';
    }

    $tCount = 0;

    if (count($filter) && is_array($res = $mysql->record("select count(*) as count from ".prefix."_eshop_compare where ".join(" or ", $filter), 1))) {
        $tCount = $res['count'];
    }

    $tVars = array(
        'count' => $tCount,
    );

    $tpath = locatePluginTemplates(array('compare_block_eshop'), 'eshop', pluginGetVariable('eshop', 'localsource'));

    $xt = $twig->loadTemplate($tpath['compare_block_eshop'].'compare_block_eshop.tpl');
    $template['vars']['plugin_eshop_compare'] = $xt->render($tVars);

    if (!isset($_COOKIE['ngCurrencyID'])) {
        $ngCurrencyID = "1";
        @setcookie('ngCurrencyID', $ngCurrencyID, time()+86400*365, '/', $ngCookieDomain, 0, 1);
    } else {
        $ngCurrencyID = $_COOKIE['ngCurrencyID'];
    }
    
    $currency_link = checkLinkAvailable('eshop', 'currency')?
            generateLink('eshop', 'currency', array()):
            generateLink('core', 'plugin', array('plugin' => 'eshop', 'handler' => 'currency'), array());

    $tEntry = array();
    foreach ($mysql->select("SELECT * FROM ".prefix."_eshop_currencies ORDER BY position, id") as $row)
    {
        $row['currency_link'] = $currency_link."?id=".$row['id'];
        $currency_tEntry[] = $row;
        
        if($ngCurrencyID == $row['id']) {
            $current_currency = $row;
        }
        
    }
    
    $SYSTEM_FLAGS["eshop_currency"] = $currency_tEntry;
    //$template['vars']['plugin_eshop_currency'] = $currency_tEntry;
                
    if(!isset($current_currency)) {
        $current_currency['id'] = 1;
        $current_currency['rate_from'] = 1;
    }

    //$template['vars']['plugin_eshop_currency_rate'] = $current_currency;
    
    $SYSTEM_FLAGS["current_currency"] = $current_currency;
    
}
