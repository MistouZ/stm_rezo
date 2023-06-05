<?php
/**
 * Created by PhpStorm.
 * User: adewynter
 * Date: 20/02/2019
 * Time: 13:38
 */

include("../../_cfg/cfg.php");

if(isset($_POST['valider'])){

    $label = $_POST["label"];
    $folderNumber = $counter->getFolder();
    $description = $_POST["description"];
    $seller = $_POST["seller-select"];
    $date = date("Y-m-d");
    $companyId = $_POST["idcompany"];



    $arraycounter = array();
    $counter = new Counter($arraycounter);
    $countermanager = new $CounterManager($bdd);
    $counter = $countermanager->getCount($companyId);

    $isActive = 1;

    $array = array(
        'label' => $label,
        //'folderNumber' => $folderNumber,
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
    //création des logs de création de dossier.
    $date = date('Y-m-d H:i:s');
    $arraylogs = array(
        'username' => $_COOKIE["username"],
        'company' => $companyId,
        'type' => "folder",
        'action' => "creation",
        'id' => $test,
        'date' => $date
    );

    print_r($arraylogs);

    $log = new Logs($arraylogs);
    $logsmgmt = new LogsManager($bdd);
    $logsmgmt = $logsmgmt->add($log);

    //incrémentation du nombre de dossier créer pour la société
    $counterFolder = $folderNumber+1;
   // $counter->setFolder($counterFolder);
   // $countermanager->updateCounter($counter);

    header('Location: '.URLHOST.$_COOKIE['company']."/dossier/afficher/success");*/
}
    
}