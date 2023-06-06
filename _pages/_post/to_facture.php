<?php
ini_set('display_errors',1); error_reporting(E_ALL | E_STRICT);
/**
 * @author Nicolas
 * @copyright 2019
 */
 
include("../../_cfg/cfg.php");

$idQuotation = $_POST['quotationNumber'];
$type2 = $_POST['type'];

$array = array();
$quotationNumber = new Quotation($array);
$quotationmanagerNumber = new QuotationManager($bdd);
$quotationNumber = $quotationmanagerNumber->getByQuotationNumber($idQuotation);

$arraycounter = array();
$counter = new Counter($arraycounter);
$countermanager = new CounterManager($bdd);
$counter = $countermanager->getCount($quotationNumber->getCompanyId());

$counterInvoice = $counter->getInvoice();

$date = $_POST['date'];

$today = date("Y-m-d");

$data = array(
    'idQuotation' => $quotationNumber->getIdQuotation(),
    'quotationNumber' => $counterInvoice,
    'status' => 'En cours',
    'label' => $quotationNumber->getLabel(),
    'date' => $date,
    'validatedDate' => $today,
    'type' => 'F'
);

$quotation = new Quotation($data);
$quotationmanager = new QuotationManager($bdd);

$test = $quotationmanager->changeType($quotation);
if(is_null($test)){
    header('Location: '.$_SERVER['HTTP_REFERER'].'/errorFacture');
}else{
    
    //Ajout d'un objet logs pour tracer l'action de passage en facture de la proforma
    $date = date('Y-m-d H:i:s');
    $arraylogs = array(
        'username' => $_COOKIE["username"],
        'company' => $quotationNumber->getCompanyId(),
        'type' => "quotation",
        'action' => "to_facture",
        'id' => $idQuotation,
        'date' => $date
    );
    $log = new Logs($arraylogs);
    $logsmgmt = new LogsManager($bdd);
    $logsmgmt = $logsmgmt->add($log);

    //incrémentation du nombre de factures créées pour la société
    $counterInvoice = $counterInvoice + 1;
    echo $counterInvoice;
    $counter->setInvoice($counterInvoice);
    print_r($counter);
    $countermanager->updateCounter($counter);

    header('Location: '.URLHOST.$_COOKIE['company'].'/facture/afficher/'.$type2.'/'.$idQuotation.'/successFacture');
}

?>