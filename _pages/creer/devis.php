<?php
include("../../_cfg/cfg.php");

/**
 * @author Nicolas
 * @copyright 2019
 */

$array = array();
$companyNameData = $_GET["section"];

if(isset($_GET['soussouscat'])){
    $retour = $_GET['soussouscat'];
}

$company = new Company($array);
$companymanager = new CompaniesManager($bdd);
$folder = new Folder($array);
$foldermanager = new FoldersManager($bdd);
$user = new Users($array);
$usermanager = new UsersManager($bdd);
$customer = new Customers($array);
$customermanager = new CustomersManager($bdd);
$contact = new Contact($array);
$contactmanager = new ContactManager($bdd);
$supplier = new Suppliers($array);
$suppliermanager = new SuppliersManager($bdd);

$company = $companymanager->getByNameData($companyNameData);
$idCompany = $company->getIdcompany();
$foldermanager = $foldermanager->getListActive($idCompany);

$tax = new Tax($array);
$taxmanager = new TaxManager($bdd);

echo "ok";
?>
