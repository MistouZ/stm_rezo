<?php
ini_set('display_errors',1); error_reporting(E_ALL | E_STRICT);
/**
 * @author Nicolas
 * @copyright 2019
 */
 
include("../../_cfg/cfg.php");

$quotationNumber = $_POST['quotationNumber'];
$type2 = $_POST['type'];
$currentType = $_POST['currentType'];

$array = array();
$company = new Company($array);
$companymanager = new CompaniesManager($bdd);
$companyNameData = $_POST["company"];
$company = $companymanager->getByNameData($companyNameData);
$companyId = $company->getIdcompany();

$quotation = new Quotation($array);
$quotationmanagerNumber = new QuotationManager($bdd);
$quotation = $quotationmanagerNumber->getByQuotationNumber($quotationNumber,$currentType, $companyId);

$costGet = new Cost($array);
$costmanager = new CostManager($bdd);

$descriptions = new Description($array);
$descriptionmanager = new DescriptionManager($bdd);
$descriptions = $descriptionmanager->getByQuotationNumber($quotationNumber, $quotation->getType(), $companyId);

$arraycounter = array();
$counter = new Counter($arraycounter);
$countermanager = new CounterManager($bdd);
$counter = $countermanager->getCount($quotation->getCompanyId());

$counterInvoice = $counter->getInvoice();

$date = $_POST['date'];

$today = date("Y-m-d");

$data = array(
    'idQuotation' => $quotation->getIdQuotation(),
    'quotationNumber' => $counterInvoice,
    'status' => 'En cours',
    'label' => $quotation->getLabel(),
    'date' => $date,
    'validatedDate' => $today,
    'type' => 'F'
);

$quotation = new Quotation($data);
$quotationmanager = new QuotationManager($bdd);

if( count($data) == 0){
    header('Location: '.$_SERVER['HTTP_REFERER'].'/errorFacture');
}else{
    
    $test = $quotationmanager->changeType($quotation);
    $test2 = $descriptionmanager->update($descriptions,$test,$quotation->getType(),$companyId);
    $test3 = $costmanager->UpdateCostType($test,$quotationNumber,"F",$companyId);
    if(is_null($test) || is_null($test2) || is_null($test3)){
        header('Location: '.$_SERVER['HTTP_REFERER'].'/errorFacture');
    }
    else{

        //Ajout d'un objet logs pour tracer l'action de passage en facture de la proforma
        $date = date('Y-m-d H:i:s');
        $arraylogs = array(
            'username' => $_COOKIE["username"],
            'company' => $quotation->getCompanyId(),
            'type' => "quotation",
            'action' => "to_facture",
            'id' => $quotationNumber,
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

        header('Location: '.URLHOST.$_COOKIE['company'].'/facture/afficher/'.$type2.'/'.$test.'/successFacture');
    }
  
}

?>
