<?php
if (!defined('NGCMS')) die ('HAL');

function generate_entries_cnt_cache($load = false)
{global $mysql, $config;

    $eshop_dir = get_plugcfg_dir('eshop');

    if(!file_exists($eshop_dir.'/cache_entries_cnt.php') or $load){
        $result = $mysql->result('SELECT COUNT(id) FROM '.prefix.'_eshop_products WHERE active = \'1\' ');
        file_put_contents($eshop_dir.'/cache_entries_cnt.php', serialize($result));
    }

}

function generate_catz_cache($load = false)
{global $mysql, $config;

    $eshop_dir = get_plugcfg_dir('eshop');
    
    if(!file_exists($eshop_dir.'/cache_catz.php') or $load){
        
        $catt = array(); 
        foreach ($mysql->select("SELECT c.id AS cat_id, COUNT(p.id) AS num FROM ".prefix."_eshop_products p LEFT JOIN ".prefix."_eshop_products_categories pc ON p.id = pc.product_id LEFT JOIN ".prefix."_eshop_categories c ON pc.category_id = c.id WHERE p.active = 1 GROUP BY c.id ") as $rows)
        {
            $catt[$rows['cat_id']] .= $rows['num'];
        }
        
        foreach ($mysql->select('SELECT * FROM '.prefix.'_eshop_categories ORDER BY position ASC') as $cat_row)
        {
            
            $catlink = checkLinkAvailable('eshop', '')?
                    generateLink('eshop', '', array('cat' => $cat_row['id'])):
                    generateLink('core', 'plugin', array('plugin' => 'eshop'), array('cat' => $cat_row['id']));
            
            
            $cats_ID[$cat_row['id']][] = $cat_row;
            $cats[$cat_row['parent_id']][$cat_row['id']] =  $cat_row;
            $cats[$cat_row['parent_id']][$cat_row['id']]['url'] =  $catlink;
            $cats[$cat_row['parent_id']][$cat_row['id']]['num'] =  $catt[$cat_row['id']]?$catt[$cat_row['id']]:'0';
        }
        
        $catz_tree = build_tree_catz($cats,0);
        file_put_contents($eshop_dir.'/cache_catz.php', serialize($catz_tree));
        
    }

}

function build_tree_catz($cats,$parent_id,$only_parent = false){
    if(is_array($cats) and isset($cats[$parent_id])){
        $tree = '<ul class="items">';
        if($only_parent==false){
            foreach($cats[$parent_id] as $cat){
                
                $tree .= '<li class="column_0"><a href="'.$cat['url'].'" class="title-category-l1  is-sub"><span class="helper"></span><span class="text-el">'.$cat['name'].'</span></a> ('.$cat['num'].')';
                $tree .=  build_tree_catz($cats,$cat['id']);
                $tree .= '</li>';
            }
        }elseif(is_numeric($only_parent)){
            $cat = $cats[$parent_id][$only_parent];
            $tree .= '<li>'.$cat['cat_name'].' #'.$cat['id'];
            $tree .=  build_tree_catz($cats,$cat['id']);
            $tree .= '</li>';
        }
        $tree .= '</ul>';
    }
    else return null;
    return $tree;
}
