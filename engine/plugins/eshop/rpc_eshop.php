<?php
if(!defined('NGCMS'))
{
    exit('HAL');
}

rpcRegisterFunction('eshop_linked_products', 'linked_prd');

function linked_prd($params){
    global $userROW, $mysql;

    //$req = iconv( "windows-1251", "utf-8", urldecode($_REQUEST["q"]) );
    $req = urldecode(filter_var( $_REQUEST["q"], FILTER_SANITIZE_STRING ));
    $mode = filter_var( $_REQUEST["mode"], FILTER_SANITIZE_STRING );
    $id = filter_var( $_REQUEST["id"], FILTER_SANITIZE_STRING );

    $conditions = array();
    if ($req) {
        array_push($conditions, "p.name LIKE '%".$req."%' ");
    }
    
    if ($mode == "edit") {
        array_push($conditions, "p.id != '".$id."' ");
    }

    $fSort = " GROUP BY p.id ORDER BY p.id DESC";
    $sqlQPart = "FROM ".prefix."_eshop_products p LEFT JOIN ".prefix."_eshop_products_categories pc ON p.id = pc.product_id LEFT JOIN ".prefix."_eshop_categories c ON pc.category_id = c.id LEFT JOIN ".prefix."_eshop_images i ON i.product_id = p.id ".(count($conditions)?"WHERE ".implode(" AND ", $conditions):'').$fSort;
    $sqlQ = "SELECT p.id AS id, p.code AS code, p.name AS name, p.active AS active, p.featured AS featured, p.position AS position, c.name AS category, i.filepath AS image_filepath ".$sqlQPart;

    foreach ($mysql->select(iconv( "utf-8", "windows-1251", $sqlQ )) as $row)
    {

        $tEntry[] = array (
            'id'                   => $row['id'],
            'name'                 => iconv("windows-1251", "utf-8", $row['name'] ),
            'image_filepath'       => $row['image_filepath'],
            'code'                 => iconv("windows-1251", "utf-8", $row['code'] ),
            'category'             => iconv("windows-1251", "utf-8", $row['category'] ),
        );
    }

    return array('status' => 1, 'errorCode' => 0, 'data' => $tEntry);
}
