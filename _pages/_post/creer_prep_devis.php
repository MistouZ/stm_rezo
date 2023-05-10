<?php

/**
 * @author Amaury
 * @copyright 2019
 */

include("../../_cfg/cfg.php");

echo "Résultats : ";

$array = array();
$folder = new Folder($array);
$foldermanager = new FoldersManager($bdd);
$folder = $foldermanager->get($_POST["folder"]);
$folderId = $folder->getIdFolder();
$companyId = $folder->getCompanyId();
$customerId = $_POST["customer-select"];
$contactId = $_POST["contact-select"];



?>