<?php

if (!defined('NGCMS')) die ('HAL');

include_once(dirname(__FILE__).'/cache.php');

class YMLCategory extends ImportConfig {

    /**
     * �������� ������ ���� ���������
     * @return array
     */
    function GetFromSite() {
        global $tpl, $mysql, $twig;

        $catz_arr = array();
        foreach ($mysql->select("SELECT * FROM ".prefix."_eshop_categories ORDER BY position, id") as $category)
        {
            $this->Add2Session(
                $category['id'],
                $category['name'],
                $category['url'],
                $category['id'],
                $category['parent_id']
            );
        }

        if(!empty($_SESSION['cats'])) {
            $this->eco('������������ ���������: '.count($_SESSION['cats']).' ��.<br>');
        } else {
            $this->eco('�� ����� ��� ��� ���������<br>');
        }
    }

    /**
     * �������� ��������� �� XML � ��������� ��
     * @param $xml
     */
    function GetFromXML($xml) {
        global $tpl, $mysql, $twig, $parse;
        $update = 0;
        foreach($xml as $xml_cat) {
            $NAME = iconv('utf-8','windows-1251',(string)$xml_cat);
            $UF_ID = (int)$xml_cat->attributes()->id;
            $UF_PARENT_ID = (int)$xml_cat->attributes()->parentId;
            
            if(!in_array($UF_ID, $_SESSION['cats_uf_ids'])) {

                $URL = strtolower($parse->translit($NAME, 1, 1));
                
                if ($URL) {
                    if ( !is_array($mysql->record("select id from ".prefix."_eshop_categories where url = ".db_squote($URL)." limit 1")) ) {
                        
                        $mysql->query('INSERT INTO '.prefix.'_eshop_categories (id, name, url, meta_title, parent_id) 
                            VALUES 
                            (   '.db_squote($UF_ID).',
                                '.db_squote($NAME).',
                                '.db_squote($URL).',
                                '.db_squote($NAME).',
                                '.db_squote($UF_PARENT_ID).'
                            )
                        ');
                        
                        $this->Add2Session(
                            $UF_ID,
                            $NAME,
                            $URL,
                            $UF_ID,
                            $UF_PARENT_ID
                        );
                        
                        generate_catz_cache(true);
                        
                        $this->eco('��������� ���������: '.$NAME.'<br>');
                        $update++;
                    }
                }
                
                
            }

        }

        $this->eco('���������� XML ���������: '.count($xml).' ��.<br>');
        $this->eco('��������� '.$update.' ���������.<br>');
    }

    /**
     * ��������� ��� � ������ ��� �����������
     * @param $id
     * @param $name
     * @param $code
     * @param $uf_id
     * @param $uf_parent_id
     */
    function Add2Session($id, $name, $code, $uf_id, $uf_parent_id) {
        $_SESSION['cats'][$id]['ID'] = $id;
        $_SESSION['cats'][$id]['NAME'] = $name;
        $_SESSION['cats'][$id]['CODE'] = $code;
        $_SESSION['cats'][$id]['UF_ID'] = $uf_id;
        $_SESSION['cats'][$id]['UF_PARENT_ID'] = $uf_parent_id;
        $_SESSION['cats_uf_ids'][] = $uf_id;
    }


}

class YMLOffer extends YMLCategory {

    /**
     * ��������� �������
     * @param $offer
     * @return bool
     */
    function Add($offer) {
        global $tpl, $mysql, $twig, $parse;
        
        // ��������
        $name = iconv('utf-8','windows-1251',(string)$offer->name);
        $description = iconv('utf-8','windows-1251',(string)$offer->description);
        $PROP = array();
        $PROP['id'] = (int)$offer->attributes()->id;
        $PROP['name'] = $name;
        $PROP['url'] = strtolower($parse->translit($name,1, 1));
        $PROP['meta_title'] = $name;
        $PROP['annotation'] = $description;

        $vnames = array();
        foreach ($PROP as $k => $v) { $vnames[] = $k.' = '.db_squote($v); }
        $mysql->query('INSERT INTO '.prefix.'_eshop_products SET '.implode(', ',$vnames).' ');
        
        $qid = $mysql->lastid('eshop_products');
        
        $PROP = array();
        // ������ ��������
        
        if(count($offer->picture) > 0) {
            foreach($offer->picture as $inx_img => $picture) {
                $PROP['picture'][] = $picture;
                
                try {
                    $rootpath = $_SERVER['DOCUMENT_ROOT'];
                    $url = $picture;
                    $name = basename($url);
                    $file_path = $rootpath."/uploads/eshop/products/temp/$name";
                    file_put_contents($file_path, file_get_contents($url));
                
                    $fileParts = pathinfo ( $file_path );
                    $extension = $fileParts ['extension'];
                    
                    $extensions = array_map('trim', explode(',', pluginGetVariable('eshop', 'ext_image')));
                    
                    if(!in_array($extension, $extensions)) {
                        return "0";
                    }
                    
                    // CREATE THUMBNAIL
                    if ($extension == "jpg" || $extension == "jpeg") {
                        $src = imagecreatefromjpeg ( $file_path );
                    } else if ($extension == "png") {
                        $src = imagecreatefrompng ( $file_path );
                    } else {
                        $src = imagecreatefromgif ( $file_path );
                    }
                    
                    list ( $width, $height ) = getimagesize ( $file_path );
                    
                    $newwidth = pluginGetVariable('eshop', 'width_thumb');
                    $newheight = ($height / $width) * $newwidth;
                    $tmp = imagecreatetruecolor ( $newwidth, $newheight );
                    
                    imagecopyresampled ( $tmp, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height );
                    
                    $thumbname = $rootpath."/uploads/eshop/products/temp/thumb/$name";
                    
                    if (file_exists ( $thumbname )) {
                        unlink ( $thumbname );
                    }
                    
                    imagejpeg ( $tmp, $thumbname, 100 );
                    
                    imagedestroy ( $src );
                    imagedestroy ( $tmp );
                    
                    
                    $img = $name;

                    $timestamp = time();
                    $iname = $timestamp."-".$img;
                    
                    $temp_name = $_SERVER['DOCUMENT_ROOT'].'/uploads/eshop/products/temp/'.$img;
                    $current_name = $_SERVER['DOCUMENT_ROOT'].'/uploads/eshop/products/'.$iname;
                    rename($temp_name, $current_name);
                    
                    $temp_name = $_SERVER['DOCUMENT_ROOT'].'/uploads/eshop/products/temp/thumb/'.$img;
                    $current_name = $_SERVER['DOCUMENT_ROOT'].'/uploads/eshop/products/thumb/'.$iname;
                    rename($temp_name, $current_name);
                                        
                    $mysql->query("INSERT INTO ".prefix."_eshop_images (`filepath`, `product_id`, `position`) VALUES ('$iname','$qid','$inx_img')");
                    
                    
                } catch (Exception $ex) {
                    return "0";
                }

            }
        }
        
        $category_id = (int)$offer->categoryId;
            
        if($category_id != 0) {
            $mysql->query("INSERT INTO ".prefix."_eshop_products_categories (`product_id`, `category_id`) VALUES ('$qid','$category_id')");
        }
        
        $price = (string)$offer->price;
        $stock = "5";
        if(isset($stock)) {
            $mysql->query("DELETE FROM ".prefix."_eshop_variants WHERE product_id='$qid'");
            $mysql->query("INSERT INTO ".prefix."_eshop_variants (`product_id`, `price`, `stock`) VALUES ('$qid', '$price', '$stock')");
        }
        
    }

    /**
     * ��������� �������
     * @param $id
     * @param $offer
     * @return bool1
     */
    function Update($id, $offer) {

        $el = new CIBlockElement;

        // ��������
        $PROP = array();
        $PROP['id'] = (int)$offer->attributes()->id;
        $PROP['available'] = (string)$offer->attributes()->available;
        $PROP['url'] = (string)$offer->url;
        $PROP['price'] = (string)$offer->price;
        $PROP['picture'] = (string)$offer->picture;
        $PROP['prop_shoose_size'] = (string)$offer->prop_shoose_size;
        $PROP['prop_close_size'] = (string)$offer->prop_close_size;

        // ��������
        $arLoadProductArray = Array(
            "IBLOCK_SECTION_ID" => $this->GetParentID((int)$offer->categoryId),
            "PROPERTY_VALUES"=> $PROP,
            "NAME"           => (string)$offer->name,
            "ACTIVE"         => "Y",
            "PREVIEW_TEXT"   => substr((string)$offer->description, 0, 255),
            "DETAIL_TEXT"    => (string)$offer->description
        );

        // ���������
        $result = $el->Update($id, $arLoadProductArray);
        if(!empty($result)) {
            return true;
        } else {
            die($el->LAST_ERROR);
        }
    }


}

class ImportConfig {
    public $iblock_id = 6;
    public $debug = true;

    function eco($data) {
        if($this->debug === true) {
            echo $data;
        }
    }

    function translitIt($str) {
        $str = Translit::transliterate($str);
        $str = Translit::asURLSegment($str);
        return $str;
    }
    
        
    function addToFiles($key, $url)
    {

        //$tempName = tempnam(ini_get('upload_tmp_dir'),'upload_');
        $tempName = tempnam('/tmp', 'php_files');
        $originalName = basename(parse_url($url, PHP_URL_PATH));

        $imgRawData = file_get_contents($url);
        file_put_contents($tempName, $imgRawData);
        $info = getimagesize($tempName);

        $_FILES[$key] = array(
            'name' => $originalName,
            'type' => $info['mime'],
            'tmp_name' => $tempName,
            'error' => 0,
            'size' => strlen($imgRawData),
        );
        
        //return $_FILES[$key];
    }
}

final class Translit{
    /**
     * ���/��� �������
     *
     * @var array
     * @access private
     * @static
     */
    static private $cyr = array(
        '�',  '�', '�', '�','�', '�', '�', '�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�', '�','�','�','�','�','�','�',
        '�',  '�', '�', '�','�', '�', '�', '�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�', '�','�','�','�','�','�', '�');

    /**
     * ��������� ������������
     *
     * @var array
     * @access private
     * @static
     */
    static private $lat = array(
        'Shh','Sh','Ch','C','Ju','Ja','Zh','A','B','V','G','D','Je','Jo','Z','I','J','K','L','M','N','O','P','R','S','T','U','F','Kh','Y','Y','','E','Je','Ji','I',
        'shh','sh','ch','c','ju','ja','zh','a','b','v','g','d','je','jo','z','i','j','k','l','m','n','o','p','r','s','t','u','f','kh','y','y','','e','je','ji', 'i');

    /**
     * ��������� ����������� ������
     * �� ���� ��������� ������ ����� ������
     *
     * @access private
     */
    private function __construct() {}

    /**
     * ����������� ����� ��������������
     *
     * @param string
     * @return string
     * @access public
     * @static
     */

    static public function transliterate($string, $wordSeparator = '', $clean = false) {
        //$str = iconv($encIn, "utf-8", $str);

        for($i=0; $i<count(self::$cyr); $i++){
            $string = str_replace(self::$cyr[$i], self::$lat[$i], $string);
        }

        $string = preg_replace("/([qwrtpsdfghklzxcvbnmQWRTPSDFGHKLZXCVBNM]+)[jJ]e/", "\${1}e", $string);
        $string = preg_replace("/([qwrtpsdfghklzxcvbnmQWRTPSDFGHKLZXCVBNM]+)[jJ]/", "\${1}y", $string);
        $string = preg_replace("/([eyuioaEYUIOA]+)[Kk]h/", "\${1}h", $string);
        $string = preg_replace("/^kh/", "h", $string);
        $string = preg_replace("/^Kh/", "H", $string);

        $string = trim($string);

        if ($wordSeparator) {
            $string = str_replace(' ', $wordSeparator, $string);
            $string = preg_replace('/['.$wordSeparator.']{2,}/','', $string);
        }

        if ($clean) {
            $string = strtolower($string);
            $string = preg_replace('/[^-_a-z0-9]+/','', $string);
        }

        //return iconv("utf-8", $encOut, $str);

        return $string;
    }

    /**
     * ���������� � ���
     *
     * @return string
     * @access public
     * @static
     */
    static public function asURLSegment($string){
        return strtolower(self::transliterate($string, '_', true));
    }

}
