<?php

if (!defined('NGCMS'))
    exit('HAL');

LoadPluginLang('eshop', 'main', '', '', '#');
add_act('index', 'eshop_infovars_show');
register_htmlvar('js', admin_url.'/plugins/eshop/tpl/js/breeze.min.js');

function eshop_infovars_show()
{
global $CurrentHandler, $SYSTEM_FLAGS, $template, $lang, $mysql, $twig, $userROW;

    $template['vars']['plugin_eshop_description_phones'] = pluginGetVariable('eshop', 'description_phones');
    
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

}
