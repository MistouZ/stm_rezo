﻿<?php
/**
 * Created by PhpStorm.
 * User: adewynter
 * Date: 20/02/2019
 * Time: 13:38
 */

include("../../_cfg/cfg.php");

if(isset($_POST['valider'])){

    $label = $_POST["label"];
    $description = $_POST["description"];
    $date = date("Y-m-d");
    $companyId = $_POST["idcompany"];

   
    $arraycounter = array();
    $counter = new Counter($arraycounter);
    $countermanager = new CounterManager($bdd);
    $counter = $countermanager->getCount($companyId);

    $folderNumber = $counter->getFolder();

    $isActive = 1;
    
    $array = array(
        'label' => $label,
        'folderNumber' => $folderNumber,
        'date' => $date,
        'isActive' => $isActive,
        'description' => $description,
        'companyId' => $companyId
    );


    $folder = new Folder($array);
    $foldermanager = new FoldersManager($bdd);
    $test = $foldermanager->add($folder);

    if(is_null($test)){
        header('Location: '.URLHOST.$_COOKIE['company']."/dossier/afficher/error");
    }
    else{
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

        $log = new Logs($arraylogs);
        $logsmgmt = new LogsManager($bdd);
        $logsmgmt = $logsmgmt->add($log);

        //incrémentation du nombre de dossier créer pour la société
        $counterFolder = $folderNumber + 1;
        echo $counterFolder;
        $counter->setFolder($counterFolder);
        print_r($counter);
        $countermanager->updateCounter($counter);

        header('Location: '.URLHOST.$_COOKIE['company']."/dossier/afficher/success");
    }
    
}