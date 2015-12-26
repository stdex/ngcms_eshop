<?php
if (!defined('NGCMS')) die ('HAL');

function generate_catz_cache($load = false)
{global $mysql, $config, $twig;

    $eshop_dir = get_plugcfg_dir('eshop');
    
    if(!file_exists($eshop_dir.'/cache_catz.php') or $load){
        
        $levels = array();
        $tree = array();
        $cur = array();

        foreach ($mysql->select("SELECT * FROM ".prefix."_eshop_categories ORDER BY position, id") as $rows)
        {          
            
            $cur = &$levels[$rows['id']];
            $cur['parent_id'] = $rows['parent_id'];
            $cur['name'] = $rows['name'];
            $cur['url'] = $rows['url'];
            $cur['image'] = $rows['image'];
            $cur['description'] = $rows['description'];
            $cur['active'] = $rows['active'];
            
            $cat_link = checkLinkAvailable('eshop', '')?
                        generateLink('eshop', '', array('alt' => $rows['url'])):
                        generateLink('core', 'plugin', array('plugin' => 'eshop'), array('alt' => $rows['url']));
            
            $cur['cat_link'] = $cat_link;
                          
            if($rows['parent_id'] == 0){
                $tree[$rows['id']] = &$cur;
            }
            else{
                $levels[$rows['parent_id']]['children'][$rows['id']] = &$cur;
            }
            
        }

        $tVars = array(
            'tree' => $tree,
        );

        $tpath = locatePluginTemplates(array('cats_tree'), 'eshop', pluginGetVariable('eshop', 'localsource'));
        $xt = $twig->loadTemplate($tpath['cats_tree'].'cats_tree.tpl');

        file_put_contents($eshop_dir.'/cache_catz.php', serialize($xt->render($tVars)));
        
    }

}
