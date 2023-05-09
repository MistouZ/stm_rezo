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

?>
<script>
    function changeSelect(selected){
      //on recupere le php
      var data = <?php echo json_encode($tableauClient); ?>;
      console.log("selected.value : "+selected.value+", data[selected.value] : "+data[selected.value]);
      var monSelectB = document.getElementById("contact-select");
      //on efface tous les children options
      while (monSelectB.firstChild) {
        monSelectB.removeChild(monSelectB.firstChild);
      }
      //on rajoute les nouveaux children options
      for(var i in data[selected.value]){
        var opt = document.createElement("option");
        opt.value = i;
        opt.innerHTML = data[selected.value][i]; 
        monSelectB.appendChild(opt);
      }
    }
</script>
<div class="row">
    <div class="col-md-12">
        <?php if($retour == "error") { ?>
            <div class="alert alert-danger">
                <button class="close" data-close="alert"></button> Une erreur est survenue, le devis n'a donc pas pu être créé !</div>
        <?php } ?>
        <div class="portlet box blue-chambray">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fas fa-file-medical"></i>Création d'un nouveau devis</div>
            </div>
            