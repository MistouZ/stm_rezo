<?php
ini_set('display_errors',1); error_reporting(E_ALL | E_STRICT);
/**
 * @author Nicolas
 * @copyright 2019
 */
 
include("../../_cfg/cfg.php");
include "../../_cfg/fonctions.php";

$quotationNumber = $_POST['quotationNumber'];
$type2 = $_POST['type'];
$percent = $_POST["shattered_percent"];

$array = array();
$quotationGet = new Quotation($array);
$quotationmanager = new QuotationManager($bdd);
$quotationGet = $quotationmanager->getByQuotationNumber($quotationNumber);

$date = $_POST['date'];

$today = date("Y-m-d");

if($_POST["shattered"] == "full" || $percent == 100)
{
    $data = array(
        'idQuotation' => $quotationGet->getIdQuotation(),
        'status' => 'En cours',
        'date' => $date,
        'validatedDate' => $today,
        'type' => 'P'
    );

    $quotation = new Quotation($data);

    $test = $quotationmanager->changeType($quotation);
    if($type2 == "partiels")
    {
        $descriptionmanager = new DescriptionManager($bdd);
        $shatteredQuotationManager = new ShatteredQuotationManager($bdd);
        $shatteredQuotationInit = new ShatteredQuotation($array);
        $shatteredQuotationInit = $shatteredQuotationManager->getByQuotationNumberChild($quotationNumber);
        $quotationNumberInit = $shatteredQuotationInit->getQuotationNumberInit();
        $quotationInit = $quotationNumberInit."_init";
        $test2 = $descriptionmanager->delete($quotationInit);
        $test3 = $shatteredQuotationManager->delete($quotationInit);
    }
    else{
        $test2 = "ok";
        $test3 = "ok";
    }
    $test4a = "ok";
    $test4b ="ok";
    $test5 = "ok";
}
elseif ($_POST["shattered"] == "partial" && $percent < 100)
{
    $descriptionmanager = new DescriptionManager($bdd);
    $description = new Description($array);

    $shatteredQuotationManager = new ShatteredQuotationManager($bdd);

    $folderId = $quotationGet->getFolderId();
    $companyId = $quotationGet->getCompanyId();
    $customerId = $quotationGet->getCustomerId();
    $contactId = $quotationGet->getContactId();
    $comment = $quotationGet->getComment();
    $label = $quotationGet->getLabel();
    $type3 = $quotationGet->getType();

    $date = date("Y-m-d");
    $status = "En cours";
    $type = "S"; // shattered quotation

    $data = array(
        'status' => $status,
        'label' => $label,
        'date' => $date,
        'validatedDate' => $today,
        'type' => $type,
        'comment' => $comment,
        'folderId' => $folderId,
        'companyId' => $companyId,
        'customerId' => $customerId,
        'contactId' => $contactId
    );

    $duplicate = new Quotation($data);
    $newquotationNumber = $quotationmanager->add($duplicate);

    //si le devis est déjà partiel, je récupère les données initiales
    if($type3 == "S")
    {
        $shatteredQuotationInit = new ShatteredQuotation($array);
        $shatteredQuotationInit = $shatteredQuotationManager->getByQuotationNumberChild($quotationNumber);
        $quotationNumber = $shatteredQuotationInit->getQuotationNumberInit();
        $quotationNumberChild = $shatteredQuotationInit->getQuotationNumberChild();
        $quotationInit = $quotationNumber."_init";
        $getDescription = $descriptionmanager->getByQuotationNumber($quotationInit);
        $rest = $shatteredQuotationInit->getPercent();
        $rest = $rest - $percent;
        $idShatteredQuotation = $shatteredQuotationInit->getIdShatteredQuotation();
    }
    else
    {
        $getDescription = $descriptionmanager->getByQuotationNumber($quotationNumber);
        $quotationInit = $quotationGet->getQuotationNumber()."_init";
        $rest = 100 - $percent;
        $i = 0;
        $descriptions= array();
        foreach ($getDescription as $description)
        {
            $description->setQuotationNumber($quotationInit);
            $descriptions[$i] = $description;
            $i++;
        }
        // Duplication des descriptions pour garder l'original
        $test = $descriptionmanager->add($descriptions,$quotationInit);
    }

    $dataShattered = array(
        'quotationNumberInit' => $quotationNumber,
        'quotationNumberChild' => $newquotationNumber,
        'percent' => $rest
    );
    $shatteredQuotation = new ShatteredQuotation($dataShattered);
    $test2 = $shatteredQuotationManager->add($shatteredQuotation);

    //Copie effectuée sur la description, on a créé l'object devis partiel et on a stocké le pourcentage restant à facturer

    $j = 0;
    $descriptionsReduced= array();
    $descriptionReduced = new Description($array);

    foreach ($getDescription as $descriptionReduced)
    {
        $value = getPercentOfNumber($descriptionReduced->getPrice(),$percent);
        $descriptionReduced->setPrice(round($value));
        $descriptionsReduced[$j] = $descriptionReduced;
        $j++;
    }
    if($type3 == "S"){
        $test3 = $descriptionmanager->update($descriptionsReduced,$quotationNumberChild);
    }
    else{
        $test3 = $descriptionmanager->update($descriptionsReduced,$quotationNumber);
    }

    if($rest != 0)
    {   //il reste à facturer alors je stocke les données restantes
        $getDescriptionInit = $descriptionmanager->getByQuotationNumber($quotationInit);
        $descriptionRest = new Description($array);
        $k = 0;
        foreach ($getDescriptionInit as $descriptionRest)
        {
            $value = getPercentOfNumber($descriptionRest->getPrice(),$rest);
            $descriptionRest->setPrice(round($value));
            $descriptionRest->setQuotationNumber($newquotationNumber);
            $descriptions[$k] = $descriptionRest;
            $k++;
        }
        //insertion du reste à payer
        $test4a = $descriptionmanager->add($descriptions,$newquotationNumber);
        $test4b = "ok";
    }
    else
    {
        //il ne reste rien à facturer alors je supprime les données partielles
        $test4a = $descriptionmanager->delete($quotationInit);
        $test4b = $shatteredQuotationManager->delete($quotationInit);
    }


    $data = array(
        'idQuotation' => $quotationGet->getIdQuotation(),
        'status' => 'En cours',
        'date' => $date,
        'validatedDate' => $today,
        'type' => 'P'
    );
    $quotation = new Quotation($data);
    $test5 = $quotationmanager->changeType($quotation);
}

if(is_null($test) || is_null($test2) || is_null($test3) || is_null($test4a) || is_null($test4b) || is_null($test5)){
    header('Location: '.$_SERVER['HTTP_REFERER'].'/errorProforma');
}else{
    header('Location: '.URLHOST.$_COOKIE['company'].'/proforma/afficher/'.$type2.'/'.$quotationNumber.'/successProforma');
}

?>