<?php
/**
 * Created by PhpStorm.
 * User: adewynter
 * Date: 20/02/2019
 * Time: 13:38
 */

include("../../_cfg/cfg.php");

print_r($_POST);

if(isset($_POST['valider'])){
    $label = $_POST["label"];
    $description = $_POST["description"];
    $seller = $_POST["seller-select"];
    $date = date("Y-m-d");
    $companyId = $_POST["idcompany"];

    $isActive = 1;

    $array = array(
        'label' => $label,
        'date' => $date,
        'isActive' => $isActive,
        'description' => $description,
        'seller' => $seller,
        'companyId' => $companyId
    );


    $folder = new Folder($array);
    $foldermanager = new FoldersManager($bdd);
    $test = $foldermanager->add($folder);

    
    

if(is_null($test)){
    header('Location: '.URLHOST.$_COOKIE['company']."/dossier/afficher/error");
}else{

    $arraylogs = array(
        'user' => $_COOKIE["username"],
        'type' => "dossier",
        'action' => "création",
        'id' => $test->getIdFolder(),
        'date'> = $date('d-m-Y H:i:s');
    );

    print_r($arraylogs);

    $log = new Logs($arraylogs);
    $logsmgmt = new LogsManager($bdd);
    $logsmgmt = $logsmgmt->add($log);
    //header('Location: '.URLHOST.$_COOKIE['company']."/dossier/afficher/success");
}
    
}