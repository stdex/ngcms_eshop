<?php
$rootpath = $_SERVER['DOCUMENT_ROOT'];
@include_once $rootpath.'/engine/core.php';

try {

    $arrayreempla=array("/","");
    $targetPath = $rootpath . '/uploads/eshop/products/temp' . '/';
    $targetThumbPath = $rootpath . '/uploads/eshop/products/temp' . '/thumb/';

    $archivo= str_replace($arrayreempla," ", $_FILES['Filedata']['name']);

    $tempFile = $_FILES['Filedata']['tmp_name'];
    $imagen= $archivo;
    //$id = $_REQUEST['des'];
    $id = intval($_REQUEST['id']);
    $targetFile = str_replace("//", "/", $targetPath) . $imagen;
    $targetThumb = str_replace("//", "/", $targetThumbPath) . $imagen;
    $fileParts = pathinfo ( $_FILES ['Filedata'] ['name'] );
    $extension = $fileParts ['extension'];
    
    $extensions = array_map('trim', explode(',', pluginGetVariable('eshop', 'ext_image')));

    if(!in_array($extension, $extensions)) {
        return "0";
    }

    //$resultadoi = $mysql->query("INSERT INTO ".prefix."_zboard_images (`filepath`, `zid`)VALUES('$imagen','$id')") or die (mysql_error());

    //if ($resultadoi) {
    echo "1";

    // CREATE THUMBNAIL
    if ($extension == "jpg" || $extension == "jpeg") {
        $src = imagecreatefromjpeg ( $tempFile );
    } else if ($extension == "png") {
        $src = imagecreatefrompng ( $tempFile );
    } else {
        $src = imagecreatefromgif ( $tempFile );
    }

    list ( $width, $height ) = getimagesize ( $tempFile );

    $newwidth = pluginGetVariable('eshop', 'width_thumb');
    $newheight = ($height / $width) * $newwidth;
    $tmp = imagecreatetruecolor ( $newwidth, $newheight );

    imagecopyresampled ( $tmp, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height );

    $thumbname = $targetThumb;

    if (file_exists ( $thumbname )) {
        unlink ( $thumbname );
    }

    imagejpeg ( $tmp, $thumbname, 100 );

    imagedestroy ( $src );
    imagedestroy ( $tmp );

    move_uploaded_file($tempFile, $targetFile);
            
    //} else {
    //echo "0";
    //}
} catch (Exception $ex) {
    return "0";
}

