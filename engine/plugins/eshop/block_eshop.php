<?php

if (!defined('NGCMS'))
    die ('HAL');

include_once(dirname(__FILE__).'/cache.php');

function plugin_block_eshop($number, $mode, $cat, $overrideTemplateName, $cacheExpire) {
    global $config, $mysql, $tpl, $template, $twig, $twigLoader, $langMonths, $lang, $TemplateCache;

    // Prepare keys for cacheing
    $cacheKeys = array();
    $cacheDisabled = false;
    
    $conditions = array();
    if(isset($cat) && !empty($cat))
    {
        array_push($conditions, "c.id IN (".$cat.") ");
    }
    
    array_push($conditions, "p.active = 1");

    if (($number < 1) || ($number > 100))
        $number = 5;
       
    switch ($mode) {
        case 'view':
            $orderby = " ORDER BY p.view DESC ";
            break;
        case 'last':
            $orderby = " ORDER BY p.editdate DESC ";
            break;
        case 'stocked':
            array_push($conditions, "p.stocked = 1");
            $orderby = " ORDER BY p.editdate DESC ";
            break;
        case 'featured':
            array_push($conditions, "p.featured = 1");
            $orderby = " ORDER BY p.editdate DESC ";
            break;
        case 'rnd':	
            $cacheDisabled = true;
            $orderby = " ORDER BY RAND() DESC ";
            break;
        default:
            $mode = 'last';
            $orderby = " ORDER BY p.editdate DESC ";
            break;
    }

    $fSort = " GROUP BY p.id ".$orderby." LIMIT ".$number;
    $sqlQPart = "FROM ".prefix."_eshop_products p LEFT JOIN ".prefix."_eshop_products_categories pc ON p.id = pc.product_id LEFT JOIN ".prefix."_eshop_categories c ON pc.category_id = c.id LEFT JOIN ".prefix."_eshop_images i ON i.product_id = p.id LEFT JOIN ".prefix."_eshop_variants v ON p.id = v.product_id ".(count($conditions)?"WHERE ".implode(" AND ", $conditions):'').$fSort;
    $sqlQ = "SELECT p.id AS id, p.code AS code, p.name AS name, p.annotation AS annotation, p.body AS body, p.active AS active, p.featured AS featured, p.stocked AS stocked, p.position AS position, p.meta_title AS meta_title, p.meta_keywords AS meta_keywords, p.meta_description AS meta_description, p.date AS date, p.editdate AS editdate, p.views AS views, c.id AS cid, c.name AS category, i.filepath AS image_filepath, v.price AS price, v.compare_price AS compare_price, v.stock AS stock ".$sqlQPart;

    $tEntries = array();

    foreach ($mysql->select($sqlQ) as $row)
    {
        $view_link = checkLinkAvailable('eshop', 'show')?
            generateLink('eshop', 'show', array('id' => $row['id'])):
            generateLink('core', 'plugin', array('plugin' => 'eshop', 'handler' => 'show'), array('id' => $row['id']));
        
        $tEntries[] = array (
            'id'                   => $row['id'],
            'code'                 => $row['code'],
            'name'                 => $row['name'],
            
            'category'             => $row['category'],
            'image_filepath'       => $row['image_filepath'],

            'price'                => $row['price'],
            'compare_price'        => $row['compare_price'],
            'stock'                => $row['stock'],
            
            'active'               => $row['active'],
            'featured'             => $row['featured'],
            'stocked'              => $row['stocked'],
            
            'position'             => $row['position'],
            
            'date'                 => $row['date'],
            'editdate'             => $row['editdate'],
            
            'edit_link'            => "?mod=extra-config&plugin=eshop&action=edit_product&id=".$row['id']."",
            'view_link'            => $view_link,
        );
    }

    if ($overrideTemplateName) {
        $templateName = 'block/'.$overrideTemplateName;
    } else {
         $templateName = 'block/block_eshop';
    }

    // Determine paths for all template files
    $tpath = locatePluginTemplates(array($templateName), 'eshop', pluginGetVariable('eshop', 'localsource'));


    // Preload template configuration variables
    @templateLoadVariables();

    $cacheKeys []= '|number='.$number;
    $cacheKeys []= '|mode='.$mode;
    $cacheKeys []= '|cat='.$cat;
    $cacheKeys []= '|templateName='.$templateName;

    // Generate cache file name [ we should take into account SWITCHER plugin ]
    $cacheFileName = md5('eshop'.$config['theme'].$templateName.$config['default_lang'].join('', $cacheKeys)).'.txt';

    if (!$cacheDisabled && ($cacheExpire > 0)) {
        $cacheData = cacheRetrieveFile($cacheFileName, $cacheExpire, 'eshop');
        if ($cacheData != false) {
            // We got data from cache. Return it and stop
            return $cacheData;
        }
    }

    $tVars['mode']       = $mode;
    $tVars['number']     = $number;
    $tVars['entries']    = $tEntries;
    $tVars['tpl_url']    = tpl_url;
    $tVars['home']       = home;

    $xt = $twig->loadTemplate($tpath[$templateName].$templateName.'.tpl');
    $output = $xt->render($tVars);

    if (!$cacheDisabled && ($cacheExpire > 0)) {
        cacheStoreFile($cacheFileName, $output, 'eshop');
    }

    return $output;
}

function plugin_m_eshop_catz_tree() {
    global $config;

    $eshop_dir = get_plugcfg_dir('eshop');
    generate_catz_cache();

    if(file_exists($eshop_dir.'/cache_catz.php')){
        $output = unserialize(file_get_contents($eshop_dir.'/cache_catz.php'));
    } else {
        $output = '';
    }

    return $output;
}

//
// Twig блоки для вывода продукции на главную
// Параметры:
// * number			- число записей для вывода
// * mode			- режим вывода
// * template		- шаблон
// * cacheExpire	- время кеша (в секундах)
function plugin_block_eshop_showTwig($params) {
    global $CurrentHandler, $config;

    return	plugin_block_eshop($params['number'], $params['mode'], $params['cat'], $params['template'], isset($params['cacheExpire'])?$params['cacheExpire']:0);
}

//
// Twig блок для вывода дерева категорий
function plugin_m_eshop_catz_tree_showTwig($params) {
    global $CurrentHandler, $config;

    return plugin_m_eshop_catz_tree();
}

twigRegisterFunction('eshop', 'show', plugin_block_eshop_showTwig);
twigRegisterFunction('eshop', 'show_catz_tree', plugin_m_eshop_catz_tree_showTwig);
