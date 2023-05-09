<?php
/**
 * Created by PhpStorm.
 * User: adewynter
 * Date: 20/02/2019
 * Time: 13:38
 */

include("../../_cfg/cfg.php");


if(isset($_POST['valider'])){
    $name=$_POST['name'];
    $percent = $_POST['percent'];
    $value = ($percent /100);
    $is_active =1;
    if(isset($_POST["default"]))
    {
        $isdefault = 1;
    }
    else{
        $isdefault = 0;
    }

    $array = array(
        'name' => $name,
        'percent' => $percent,
        'value' => $value,
        'isActive' => $is_active,
        'isDefault' => $isdefault
    );

    $tax = new Tax($array);
    $taxmanager = new TaxManager($bdd);
    $existe = $taxmanager->getByPercent($percent);
    echo "on a passé getbypercent";

    echo "on a récupéré le %";

    if(is_null($existe))
    {
        echo "je n'existe pas";
        $test = $taxmanager->add($tax);
        if(is_null($test)){
            header('Location: '.URLHOST.$_COOKIE['company']."/taxe/afficher/error");
        }else{
            header('Location: '.URLHOST.$_COOKIE['company']."/taxe/afficher/success");
        }
    }
    else{
        echo "j'existe";
        header('Location: '.URLHOST.$_COOKIE['company']."/taxe/afficher/existe");
    }





}