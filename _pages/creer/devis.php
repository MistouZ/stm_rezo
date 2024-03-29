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
$companyId = $company->getIdcompany();
$foldermanager = $foldermanager->getListActive($companyId);
$customermanager = $customermanager->getListByCompany($company->getIdcompany());
$usermanager = $usermanager->getListByCompany($company->getIdcompany());

$tax = new Tax($array);
$taxmanager = new TaxManager($bdd);
$tempTaxes = array();
$taxmanagerTemp = array();

foreach ($customermanager as $customer) {

    //On récupère la liste des contacts en fonction du client
    $tempContact = array();
    $tableauContacts = $contactmanager->getList($customer->getIdCustomer());
    //echo " test 1 : ".$customer->getName()."<br/>";
    if(!empty($tableauContacts)){
       //echo " test 1-2 : ".$customer->getName()."<br/>";
        foreach($tableauContacts as $tableauContact){
            //echo " test 1-3 : ".$tableauContact->getFirstname()."<br/>";
            $tempContact[$tableauContact->getIdContact()]=$tableauContact->getFirstname().' '.$tableauContact->getName();
        }
        //echo " test 1-4 : sortie foreach 1<br/>";
        $tableauClient[$customer->getIdCustomer()] = $tempContact;
        //echo " test 1-5 : sortie foreach 2<br/>";
    }
    //echo " test 1-6 : sortie IF ID Customer : ".$customer->getIdCustomer()."<br/>";
    
    unset($tempTaxes);
    unset($taxmanagerTemp);
    $taxmanagerTemp = $taxmanager->getListByCustomer($customer->getIdCustomer());
    //echo " test 2-1 : ".$customer->getIdCustomer()."<br/>";
    foreach ($taxmanagerTemp as $tableauTaxe) {
        //echo " test 2-2 : ".$tableauTaxe->getValue()."<br/>";
        if(!empty($tableauTaxe)){
            //echo " test 3 : ".$tableauTaxe->getIdTax()."<br/>";
            //$tempTaxes[$tableauTaxe->getName()]=$tableauTaxe->getValue();
            $tempTaxes[$tableauTaxe->getIdTax()]=['nom' =>$tableauTaxe->getName(), 'valeur' => $tableauTaxe->getValue()];
            //echo " test 4 : ".$tableauTaxe->getValue()."<br/>";
            $tableauTaxes[$customer->getIdCustomer()] = $tempTaxes;
            //echo " test 5 : ".$tableauTaxes[$tableauTaxe->getIdTax()]."<br/>";
        }
        //echo " Je sors du IF <br/>";
    }
    //echo " Je sors du foreach tax <br/>";
}
//echo " Je sors du foreach global <br/>";
?>
<script>
    function changeSelect(selected){
        //on recupere le php
        var data = <?php echo json_encode($tableauClient); ?>;
        var monSelectA = document.getElementById("contact-select");
        //on efface tous les children options
        while (monSelectA.firstChild) {
            monSelectA.removeChild(monSelectA.firstChild);
        }
        //on rajoute les nouveaux children options
        for(var i in data[selected.value]){
            var opt = document.createElement("option");
            opt.value = i;
            opt.innerHTML = data[selected.value][i]; 
            monSelectA.appendChild(opt);
        }
        
        var data2 = <?php echo json_encode($tableauTaxes); ?>;
        var monSelectB = document.getElementsByClassName("taxe");
        var monSelectC = document.getElementsByClassName("taxeOption");

        for(var k=0; k<monSelectB.length; k++){
            while (monSelectB[k].firstChild) {
            monSelectB[k].removeChild(monSelectB[k].firstChild);
            }
            //on rajoute les nouveaux children options
            var opt = document.createElement("option");
            opt.value = "";
            opt.innerHTML = "Sélectionnez ..."; 
            monSelectB[k].appendChild(opt);
            
            /*for(var i in data2[selected.value]){
            opt = document.createElement("option");
            opt.value = data2[selected.value][i];
            opt.innerHTML = data2[selected.value][i]; 
            monSelectB[k].appendChild(opt);
            }*/
            for(var i in data2[selected.value]){
            opt = document.createElement("option");
            opt.value = data2[selected.value][i].valeur;
            opt.innerHTML = data2[selected.value][i].nom; 
            monSelectB[k].appendChild(opt);
            }
        }

        for(var k=0; k<monSelectC.length; k++){
            while (monSelectC[k].firstChild) {
            monSelectC[k].removeChild(monSelectC[k].firstChild);
            }
            //on rajoute les nouveaux children options
            var opt = document.createElement("option");
            opt.value = "";
            opt.innerHTML = "Sélectionnez ..."; 
            monSelectC[k].appendChild(opt);
            
            for(var k in data2[selected.value]){
            opt = document.createElement("option");
            opt.value = data2[selected.value][k].valeur;
            opt.innerHTML = data2[selected.value][k].nom; 
            monSelectC[k].appendChild(opt);
            }
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
            <div class="portlet-body form">
                <!-- BEGIN FORM-->
                <form action="<?php echo URLHOST."_pages/_post/creer_devis.php"; ?>" method="post" id="devis" name="devis" class="form-horizontal">
                    <div class="form-actions top">
                        <div class="row">
                            <div class="col-md-12" style="text-align: center;">
                                <button type="submit" class="btn green"><i class="fas fa-save"></i> Enregistrer</button>
                                <button type="button" class="btn default"><i class="fas fa-ban"></i> Annuler</button>
                            </div>
                        </div>
                    </div>
                    <div class="form-body">
                        <div class="form-group">
                            <div class="col-md-12">
                                <div class="portlet box blue-soft">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="fas fa-folder"></i>
                                            <span class="caption-subject bold uppercase"> Sélection du dossier </span>
                                        </div>
                                        <div class="tools">
                                            <a href="" class="collapse" data-original-title="" title=""> </a>
                                        </div>
                                    </div>
                                    <div class="portlet-body form" style="display: block;">
                                        <div class="row form-section" style="padding: 12px 20px 15px 20px; margin: 10px 0px 10px 0px !important;">
                                            <label class="col-md-2 control-label">Dossier
                                            <span class="required" aria-required="true"> * </span>
                                            </label>
                                            <div class="col-md-10">
                                                <select class="bs-select form-control" id="folder" name="folder" data-live-search="true" data-size="8">
                                                    <option value="">Choisissez un dossier...</option>
                                                    <?php
                                                        foreach ($foldermanager as $folder){
                                                            //$customer = $customermanager->getByID($folder->getCustomerId());
                                                    ?>
                                                    <option value="<?php echo $folder->getIdFolder(); ?>">N° <?php echo $folder->getFolderNumber()." ".$folder->getLabel(); ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                            <!--<div id="infos" class="row form-section" style="margin: 10px 0px 0px 0px !important;">-->
                                <div class="col-md-12">
                                    <div class="portlet box purple-sharp" style="margin-bottom: 0px !important;">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="fas fa-user-tie"></i>
                                                <span class="caption-subject bold uppercase"> Sélection du client </span>
                                            </div>
                                            <div class="tools">
                                                <a href="" class="collapse" data-original-title="" title=""> </a>
                                            </div>
                                        </div>
                                        <div class="portlet-body form" style="display: block;">
                                            <div class="row form-section" style="padding: 12px 20px 15px 20px; margin: 10px 0px 10px 0px !important;">
                                                <label class="col-md-2 control-label">Client
                                                <span class="required" aria-required="true"> * </span>
                                                </label>
                                                <div class="col-md-10">
                                                    <select id="customer-select" name="customer-select" class="form-control" onchange="changeSelect(this);">
                                                        <option value="">--Choississez le client--</option>
                                                        <?php
                                                            foreach($customermanager as $customer)
                                                            {
                                                                echo "<option value=" . $customer->getIdCustomer() . ">".$customer->getName()."</option>";
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row form-section" style="padding: 12px 20px 15px 20px; margin: 10px 0px 10px 0px !important;">
                                                <label class="col-md-2 control-label">Contact
                                                </label>
                                                <div class="col-md-10">
                                                    <select id="contact-select" name="contact-select" class="form-control">
                                                        <option value="">--Choississez le contact--</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="detaildevis">
                                <div class="col-md-12">
                                    <div class="portlet box blue-dark">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="fas fa-cogs"></i>
                                                <span class="caption-subject bold uppercase"> Détails du devis </span>
                                            </div>
                                            <div class="tools">
                                                <a href="" class="collapse" data-original-title="" title=""> </a>
                                            </div>
                                        </div>                                        
                                        <div class="portlet-body form" style="display: block;">
                                            <div class="row form-section" style="padding: 12px 20px 15px 20px; margin: 10px 0px 10px 0px !important;">
                                                <label class="col-md-2 control-label">Commercial <span class="required"> * </span>
                                                </label>
                                                <div class="col-md-6">
                                                    <select id="seller-select" name="seller-select" class="form-control">
                                                        <option value="">--Choississez le commercial--</option>
                                                        <?php
                                                            foreach ($usermanager as $user)
                                                            {
                                                                if($user->getIsSeller() == 1)
                                                                {
                                                                    echo "<option value=".$user->getUsername().">".$user->getFirstName()." ".$user->getName()."</option>";
                                                                }
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row form-section" style="padding: 12px 20px 15px 20px; margin: 10px 0px 10px 0px !important;">
                                                <label class="col-md-2 control-label">Libellé du devis
                                                </label>
                                                <div class="col-md-6">
                                                    <input type="text" id="libelle" name="label" class="form-control" placeholder="Libellé spécifique du devis">
                                                    <span class="help-block">Si le libellé n'est pas rempli, le devis récupérera le libellé du dossier</span>
                                                </div>
                                            </div>
                                            <div class="row form-section" style="padding: 12px 20px 15px 20px; margin: 10px 0px 10px 0px !important;">
                                                <label class="col-md-2 control-label">Commentaire
                                                </label>
                                                <div class="col-md-6">
                                                    <textarea class="form-control" id="comment" name="comment" rows="3" placeholder="Commentaire ..."></textarea>
                                                    <span class="help-block">Le commentaire s'affichera à la fin du devis</span>
                                                </div>
                                            </div>
                                            <div id="ligneDevis1" class="ligneDevis row" style="margin-left: 0px !important; margin-right: 0px !important;">
                                                <div class="col-md-12" style="display: flex; align-items: center;">
                                                    <div class="col-md-6">
                                                        <div class="form-group" style="margin-left: 0px !important; margin-right: 0px !important;">
                                                            <label class="control-label">Description</label>
                                                            <textarea class="form-control" id="descriptionDevis1" name="descriptionDevis[1]" rows="4"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <div class="form-group" style="margin-left: 0px !important; margin-right: 0px !important;">
                                                            <label class="control-label">Quantité</label>
                                                            <input type="number" id="quantiteDevis" name="quantiteDevis[1]" class="form-control" placeholder="Qt.">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <div class="form-group" style="margin-left: 0px !important; margin-right: 0px !important;">
                                                            <label class="control-label">Rem. (%)</label>
                                                            <input type="number" id="remiseDevis" name="remiseDevis[1]" class="form-control" placeholder="xx" max="100">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <div class="form-group" style="margin-left: 0px !important; margin-right: 0px !important;">
                                                            <label class="control-label">Taxes</label>
                                                            <select id="taxeDevis1" class="taxe form-control" name="taxeDevis[1]">
                                                                <option value="">Sélectionnez ...</option>
                                                                <?php
                                                                /*if( !empty($customer->getCustomerId())){
                                                                    $taxmanager = $taxmanager->getListByCustomer($customer->getCustomerId());
                                                                    foreach ($taxmanager as $tax){
                                                                ?>
                                                                    <option value="<?php echo $tax->getValue(); ?>"><?php echo $tax->getPercent()." %"; ?></option>
                                                                    <?php
                                                                    }
                                                                }*/
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group" style="margin-left: 0px !important; margin-right: 0px !important;">
                                                            <label class="control-label">Prix HT</label>
                                                            <input type="number" id="prixDevis1" name="prixDevis[1]" class="form-control" placeholder="HT">
                                                        </div>
                                                    </div>
                                                    <div id="divsupprDevis1" style="text-align: right;" class="col-md-1">
                                                        <div class="form-group" style="margin-left: 0px !important; margin-right: 0px !important;">
                                                            <button type="button" title="Supprimer la ligne" id="supprDevis1" class="btn red" onclick="supprLigneDevis(1);"><i class="fas fa-minus-square"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-actions fluid">
                                                <div class="row">
                                                    <div class="col-md-12" style="text-align: center;">
                                                        <button type="button" id="ajout" class="btn default grey-mint"><i class="fas fa-plus-square"></i> Ajouter une ligne</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="optdevis">
                                <div class="col-md-12">
                                    <div class="portlet box grey-cascade">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="fas fa-sliders-h"></i>
                                                <span class="caption-subject bold uppercase"> Options du devis </span>
                                            </div>
                                            <div class="tools">
                                                <a href="" class="expand" data-original-title="" title=""> </a>
                                            </div>
                                        </div>
                                        <div class="portlet-body form" style="display: none;">
                                            <div id="ligneOption1" class="ligneOption row" style="margin-left: 0px !important; margin-right: 0px !important;">
                                                <div class="col-md-12" style="display: flex; align-items: center;">
                                                    <div class="col-md-6">
                                                        <div class="form-group" style="margin-left: 0px !important; margin-right: 0px !important;">
                                                            <label class="control-label">Description</label>
                                                            <textarea class="form-control" id="descriptionOption1" name="descriptionOption[1]" rows="4"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <div class="form-group" style="margin-left: 0px !important; margin-right: 0px !important;">
                                                            <label class="control-label">Quantité</label>
                                                            <input type="number" id="quantiteOption" name="quantiteOption[1]" class="form-control" placeholder="Qt.">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <div class="form-group" style="margin-left: 0px !important; margin-right: 0px !important;">
                                                            <label class="control-label">Rem. (%)</label>
                                                            <input type="number" id="remiseOption" name="remiseOption[1]" class="form-control" placeholder="xx" max="100">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <div class="form-group" style="margin-left: 0px !important; margin-right: 0px !important;">
                                                            <label class="control-label">Taxes</label>
                                                            <select id="taxeOption1" class="taxe form-control" name="taxeOption[1]">
                                                                <option value="">Sélectionnez ...</option>
                                                                <?php
                                                                /*if( !empty($customer->getCustomerId())){
                                                                    $taxmanager = $taxmanager->getListByCustomer($customer->getCustomerId());
                                                                    foreach ($taxmanager as $tax){
                                                                ?>
                                                                    <option value="<?php echo $tax->getValue(); ?>"><?php echo $tax->getPercent()." %"; ?></option>
                                                                    <?php
                                                                    }
                                                                }*/
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group" style="margin-left: 0px !important; margin-right: 0px !important;">
                                                            <label class="control-label">Prix HT</label>
                                                            <input type="number" id="prixOption1" name="prixOption[1]" class="form-control" placeholder="HT">
                                                        </div>
                                                    </div>
                                                    <div id="divsupprOption1" style="text-align: right;" class="col-md-1">
                                                        <div class="form-group" style="margin-left: 0px !important; margin-right: 0px !important;">
                                                            <button type="button" title="Supprimer la ligne" id="supprOption1" class="btn red" onclick="supprLigneOption(1);"><i class="fas fa-minus-square"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-actions fluid">
                                                <div class="row">
                                                    <div class="col-md-12" style="text-align: center;">
                                                        <button type="button" id="ajoutOption" class="btn default grey-mint"><i class="fas fa-plus-square"></i> Ajouter une ligne</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="coutdevis">
                                <div class="col-md-12">
                                    <div class="portlet box red-flamingo" style="margin-bottom: 0px !important;">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="fas fa-hand-holding-usd"></i>
                                                <span class="caption-subject bold uppercase"> Coûts liés au devis </span>
                                            </div>
                                            <div class="tools">
                                                <a href="" class="expand" data-original-title="" title=""> </a>
                                            </div>
                                        </div>
                                        <div class="portlet-body form" style="display: none;">
                                            <div id="ligneCout1" class="ligneCout row" style="margin-left: 0px !important; margin-right: 0px !important;">
                                                <div class="col-md-12" style="display: flex; align-items: center;">
                                                    <div class="col-md-4">
                                                        <div class="form-group" style="margin-left: 0px !important; margin-right: 0px !important;">
                                                            <label class="control-label">Fournisseur</label>
                                                            <select id="fournisseur1" class="form-control" name="fournisseur[1]">
                                                                <option value="">Sélectionnez ...</option>
                                                                <?php
                                                                $suppliermanager = $suppliermanager->getListAllByCompany($company->getIdcompany());
                                                                foreach ($suppliermanager as $supplier){
                                                                ?>
                                                                    <option value="<?php echo $supplier->getIdSupplier(); ?>"><?php echo $supplier->getName(); ?></option>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group" style="margin-left: 0px !important; margin-right: 0px !important;">
                                                            <label class="control-label">Description</label>
                                                            <textarea class="form-control" id="descriptionCout1" name="descriptionCout[1]" rows="4"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group" style="margin-left: 0px !important; margin-right: 0px !important;">
                                                            <label class="control-label">Prix HT</label>
                                                            <input type="number" id="prixCout1" name="prixCout[1]" class="form-control" placeholder="HT">
                                                        </div>
                                                    </div>
                                                    <div id="divsupprCout1" style="text-align: right;" class="col-md-1">
                                                        <div class="form-group" style="margin-left: 0px !important; margin-right: 0px !important;">
                                                            <button type="button" title="Supprimer la ligne" id="supprCout1" class="btn red" onclick="supprLigneCout(1);"><i class="fas fa-minus-square"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-actions fluid">
                                                <div class="row">
                                                    <div class="col-md-12" style="text-align: center;">
                                                        <button type="button" id="ajoutCout" class="btn default grey-mint"><i class="fas fa-plus-square"></i> Ajouter une ligne</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions fluid">
                        <div class="row">
                            <div class="col-md-12" style="text-align: center;">
                                <button type="submit" class="btn green"><i class="fas fa-save"></i> Enregistrer</button>
                                <button type="button" class="btn default"><i class="fas fa-ban"></i> Annuler</button>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- END FORM-->
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {

    $("#customer-select").on("change",function(){
        var i = $(this).val();
    	console.log("selected.value : "+i+", data[selected.value] : "+i);
        
    	$.ajax({
            url: "<?php echo URLHOST."_cfg/fonctions.php"; ?>",
    		type: "POST",
            dataType: "json",
            contentType: 'application/x-www-form-urlencoded',
    		data: {functionCalled:'getTaxesFormCustomer',idFolder:i },
    	    cache: false,
    		success: function(response)
    	  {
            alert("Début");
            var monSelectB = document.getElementsByClassName("taxe");
            //on efface tous les children options
            for(var k=0; k<monSelectB.length; k++){
                while (monSelectB[k].firstChild) {
                console.log("option : "+monSelectB[k]);
                monSelectB[k].removeChild(monSelectB[k].firstChild);
                }
                //on rajoute les nouveaux children options
                var opt = document.createElement("option");
                opt.value = "";
                opt.innerHTML = "Sélectionnez ..."; 
                monSelectB[k].appendChild(opt);
                
                for(var i in response['taxes']){
                opt = document.createElement("option");
                opt.value = response.taxes[i].valeur;
                opt.innerHTML = response.taxes[i].nom; 
                monSelectB[k].appendChild(opt);
                }
            }
            var monSelectC = document.getElementsByClassName("taxeOption");
            //on efface tous les children options
            for(var k=0; k<monSelectC.length; k++){
                while (monSelectC[k].firstChild) {
                console.log("option : "+monSelectC[k]);
                monSelectC[k].removeChild(monSelectC[k].firstChild);
                }
                //on rajoute les nouveaux children options
                var opt = document.createElement("option");
                opt.value = "";
                opt.innerHTML = "Sélectionnez ..."; 
                monSelectC[k].appendChild(opt);
                
                for(var i in response['taxes']){
                opt = document.createElement("option");
                opt.value = response.taxes[i].valeur;
                opt.innerHTML = response.taxes[i].nom; 
                monSelectC[k].appendChild(opt);
                }
            }
          },
          error: function (jqXHR, exception) {
            var msg = '';
            if (jqXHR.status === 0) {
                msg = 'Not connect.\n Verify Network.';
            } else if (jqXHR.status == 404) {
                msg = 'Requested page not found. [404]';
            } else if (jqXHR.status == 500) {
                msg = 'Internal Server Error [500].';
            } else if (exception === 'parsererror') {
                msg = 'Requested JSON parse failed.';
            } else if (exception === 'timeout') {
                msg = 'Time out error.';
            } else if (exception === 'abort') {
                msg = 'Ajax request aborted.';
            } else {
                msg = 'Uncaught Error.\n' + jqXHR.responseText;
            }
            $('#spanCompany').html(msg);
           },
        });
    });

    /*$("#folder").on("change",function(){
        var i = $(this).val();
    	console.log("selected.value : "+i+", data[selected.value] : "+i);
        
    	$.ajax({
            url: "<?php //echo URLHOST."_cfg/fonctions.php"; ?>",
    		type: "POST",
            dataType: "json",
            contentType: 'application/x-www-form-urlencoded',
    		data: {functionCalled:'getContactFormFolder',idFolder:i },
    	    cache: false,
    		success: function(response)
    	  {
                 console.log(response);
                 $("#spanCompany").text(response.company);
                 $("#spanSeller").text(response.seller);
                 $("#spanCustomer").text(response.customer);
                 $("#spanContact").text(response.contact);
                 $("#libelle").attr("placeholder",response.label);
                 $("#detaildevis").css('display','');
                 $("#detaildevis").css('display','visible');
                 $("#optdevis").css('display','');
                 $("#optdevis").css('display','visible');
                 $("#coutdevis").css('display','');
                 $("#coutdevis").css('display','visible');
                 
                 var monSelectB = document.getElementsByClassName("taxe");
                  //on efface tous les children options
                  for(var k=0; k<monSelectB.length; k++){
                      while (monSelectB[k].firstChild) {
                        console.log("option : "+monSelectB[k]);
                        monSelectB[k].removeChild(monSelectB[k].firstChild);
                      }
                      //on rajoute les nouveaux children options
                      var opt = document.createElement("option");
                        opt.value = "";
                        opt.innerHTML = "Sélectionnez ..."; 
                        monSelectB[k].appendChild(opt);
                        
                      for(var i in response['taxes']){
                        opt = document.createElement("option");
                        opt.value = response.taxes[i].valeur;
                        opt.innerHTML = response.taxes[i].nom; 
                        monSelectB[k].appendChild(opt);
                      }
                  }
                  var monSelectC = document.getElementsByClassName("taxeOption");
                  //on efface tous les children options
                  for(var k=0; k<monSelectC.length; k++){
                      while (monSelectC[k].firstChild) {
                        console.log("option : "+monSelectC[k]);
                        monSelectC[k].removeChild(monSelectC[k].firstChild);
                      }
                      //on rajoute les nouveaux children options
                      var opt = document.createElement("option");
                        opt.value = "";
                        opt.innerHTML = "Sélectionnez ..."; 
                        monSelectC[k].appendChild(opt);
                        
                      for(var i in response['taxes']){
                        opt = document.createElement("option");
                        opt.value = response.taxes[i].valeur;
                        opt.innerHTML = response.taxes[i].nom; 
                        monSelectC[k].appendChild(opt);
                      }
                  }
    	  },
          error: function (jqXHR, exception) {
            var msg = '';
            if (jqXHR.status === 0) {
                msg = 'Not connect.\n Verify Network.';
            } else if (jqXHR.status == 404) {
                msg = 'Requested page not found. [404]';
            } else if (jqXHR.status == 500) {
                msg = 'Internal Server Error [500].';
            } else if (exception === 'parsererror') {
                msg = 'Requested JSON parse failed.';
            } else if (exception === 'timeout') {
                msg = 'Time out error.';
            } else if (exception === 'abort') {
                msg = 'Ajax request aborted.';
            } else {
                msg = 'Uncaught Error.\n' + jqXHR.responseText;
            }
            $('#spanCompany').html(msg);
        },
    	});
    });*/
    
    $('#ajout').click(function(){
    
      // get the last DIV which ID starts with ^= "klon"
      var $div = $('div[id^="ligneDevis"]:last').data( "arr", [ 1 ] );
      var $textarea = $('textarea[id^="descriptionDevis"]:last').data( "txt", [ 1 ] );
      // Read the Number from that DIV's ID (i.e: 3 from "klon3")
      // And increment that number by 1
      var num = parseInt( $div.prop("id").match(/\d+/g), 10 ) +1;
      
      // Clone it and assign the new ID (i.e: from num 4 to ID "klon4")
      var $klon = $div.clone(true).find(".help-block-error").text("").end().find(".has-error").removeClass("has-error").end().find("input,textarea").val("").end().find('textarea[id^="descriptionDevis"]:last').prop('id', 'descriptionDevis'+num ).end().find('textarea[name^="descriptionDevis"]:last').prop('name', 'descriptionDevis['+num+']' ).end().find('input[name^="quantiteDevis"]:last').prop('name', 'quantiteDevis['+num+']' ).end().find('input[name^="remiseDevis"]:last').prop('name', 'remiseDevis['+num+']' ).end().find('select[name^="taxeDevis"]:last').prop('name', 'taxeDevis['+num+']' ).end().find('select[id^="taxeDevis"]:last').prop('id', 'taxeDevis'+num ).end().find('input[name^="prixDevis"]:last').prop('name', 'prixDevis['+num+']' ).end().find('input[id^="prixDevis"]:last').prop('id', 'prixDevis'+num ).end().find('button[id^="supprDevis"]:last').prop('id', 'supprDevis'+num ).end().find('button[id^="supprDevis"]:last').attr('onclick', 'supprLigneDevis('+num+')' ).end().find('div[id^="divsupprDevis"]:last').prop('id', 'divsupprDevis'+num ).end().find('div[id="divsupprDevis'+num+'"]').css('display','' ).end().find('div[id="divsupprDevis'+num+'"]').css('display','block' ).end().prop('id', 'ligneDevis'+num );
      
      // Finally insert $klon wherever you want
      $("div[id*='divsupprDevis']").css('display','' );
      $("div[id*='divsupprDevis']").css('display','block' );
      $div.after( $klon.data( "arr", $.extend( [], $div.data( "arr" ) ) ) );
      
      $("#descriptionDevis"+num).each(function(){
        $(this).rules("add", {
            required: true
        });
      });
      $("#taxeDevis"+num).each(function(){
        $(this).rules("add", {
            required: true
        });
      });
      $("#prixDevis"+num).each(function(){
        $(this).rules("add", {
            required: true
        });
      });
    
    });
    $('#ajoutOption').click(function(){
    
      // get the last DIV which ID starts with ^= "klon"
      var $div = $('div[id^="ligneOption"]:last').data( "arr", [ 1 ] );
      var $textarea = $('textarea[id^="descriptionOption"]:last').data( "txt", [ 1 ] );
      // Read the Number from that DIV's ID (i.e: 3 from "klon3")
      // And increment that number by 1
      var num = parseInt( $div.prop("id").match(/\d+/g), 10 ) +1;
      
      // Clone it and assign the new ID (i.e: from num 4 to ID "klon4")
      var $klon = $div.clone(true).find(".help-block-error").text("").end().find(".has-error").removeClass("has-error").end().find("input,textarea").val("").end().find('textarea[id^="descriptionOption"]:last').prop('id', 'descriptionOption'+num ).end().find('textarea[name^="descriptionOption"]:last').prop('name', 'descriptionOption['+num+']' ).end().find('input[name^="quantiteOption"]:last').prop('name', 'quantiteOption['+num+']' ).end().find('input[name^="remiseOption"]:last').prop('name', 'remiseOption['+num+']' ).end().find('select[name^="taxeOption"]:last').prop('name', 'taxeOption['+num+']' ).end().find('select[id^="taxeOption"]:last').prop('id', 'taxeOption'+num ).end().find('input[name^="prixOption"]:last').prop('name', 'prixOption['+num+']' ).end().find('input[id^="prixOption"]:last').prop('id', 'prixOption'+num ).end().find('button[id^="supprOption"]:last').prop('id', 'supprOption'+num ).end().find('button[id^="supprOption"]:last').attr('onclick', 'supprLigneOption('+num+')' ).end().find('div[id^="divsupprOption"]:last').prop('id', 'divsupprOption'+num ).end().find('div[id="divsupprOption'+num+'"]').css('display','' ).end().find('div[id="divsupprOption'+num+'"]').css('display','block' ).end().prop('id', 'ligneOption'+num );
      
      // Finally insert $klon wherever you want
      $("div[id*='divsupprOption']").css('display','' );
      $("div[id*='divsupprOption']").css('display','block' );
      $div.after( $klon.data( "arr", $.extend( [], $div.data( "arr" ) ) ) );
      
      $("#descriptionOption"+num).each(function(){
        $(this).rules("add", {
            required: true
        });
      });
      $("#taxeOption"+num).each(function(){
        $(this).rules("add", {
            required: true
        });
      });
      $("#prixOption"+num).each(function(){
        $(this).rules("add", {
            required: true
        });
      });
    
    });
    $('#ajoutCout').click(function(){
    
      // get the last DIV which ID starts with ^= "klon"
      var $div = $('div[id^="ligneCout"]:last').data( "arr", [ 1 ] );
      var $textarea = $('textarea[id^="descriptionCout"]:last').data( "txt", [ 1 ] );
      // Read the Number from that DIV's ID (i.e: 3 from "klon3")
      // And increment that number by 1
      var num = parseInt( $div.prop("id").match(/\d+/g), 10 ) +1;
      
      // Clone it and assign the new ID (i.e: from num 4 to ID "klon4")
        var $klon = $div.clone(true).find(".help-block-error").text("").end().find(".has-error").removeClass("has-error").end().find("input,textarea").val("").end().find('textarea[id^="descriptionCout"]:last').prop('id', 'descriptionCout'+num ).end().find('textarea[name^="descriptionCout"]:last').prop('name', 'descriptionCout['+num+']' ).end().find('select[name^="fournisseur"]:last').prop('name', 'fournisseur['+num+']' ).end().find('select[id^="fournisseur"]:last').prop('id', 'fournisseur'+num ).end().find('input[name^="prixCout"]:last').prop('name', 'prixCout['+num+']' ).end().find('input[id^="prixCout"]:last').prop('id', 'prixCout'+num ).end().find('button[id^="supprCout"]:last').prop('id', 'supprCout'+num ).end().find('button[id^="supprCout"]:last').attr('onclick', 'supprLigneCout('+num+')' ).end().find('div[id^="divsupprCout"]:last').prop('id', 'divsupprCout'+num ).end().find('div[id="divsupprCout'+num+'"]').css('display','' ).end().find('div[id="divsupprCout'+num+'"]').css('display','block' ).end().prop('id', 'ligneCout'+num );
      
      // Finally insert $klon wherever you want
      $("div[id*='divsupprCout']").css('display','' );
      $("div[id*='divsupprCout']").css('display','block' );
      $div.after( $klon.data( "arr", $.extend( [], $div.data( "arr" ) ) ) );
      
      $("#descriptionCout"+num).each(function(){
        $(this).rules("add", {
            required: true
        });
      });
      $("#taxeCout"+num).each(function(){
        $(this).rules("add", {
            required: true
        });
      });
      $("#prixCout"+num).each(function(){
        $(this).rules("add", {
            required: true
        });
      });
    
    });  

});
function supprLigneDevis(selected){
    var nbDiv = $("div[class*='ligneDevis']").length;
    var selectedDiv = $("div[id='ligneDevis"+selected+"']");
    if(nbDiv>1){
        selectedDiv.remove();
    }else{
        selectedDiv.find('div[id="divsupprDevis'+selected+'"]').css('display','' ).end();
        selectedDiv.find('div[id="divsupprDevis'+selected+'"]').css('display','none' ).end();
        alert("Il n'est pas possible de supprimer la dernière ligne du devis !");
    }
}
function supprLigneOption(selected){
    var nbDiv = $("div[class*='ligneOption']").length;
    var selectedDiv = $("div[id='ligneOption"+selected+"']");
    if(nbDiv>1){
        selectedDiv.remove();
    }else{
        selectedDiv.find('div[id="divsupprOption'+selected+'"]').css('display','' ).end();
        selectedDiv.find('div[id="divsupprOption'+selected+'"]').css('display','none' ).end();
        alert("Il n'est pas possible de supprimer la dernière ligne des options !");
    }
}
function supprLigneCout(selected){
    var nbDiv = $("div[class*='ligneCout']").length;
    var selectedDiv = $("div[id='ligneCout"+selected+"']");
    if(nbDiv>1){
        selectedDiv.remove();
    }else{
        selectedDiv.find('div[id="divsupprCout'+selected+'"]').css('display','' ).end();
        selectedDiv.find('div[id="divsupprCout'+selected+'"]').css('display','none' ).end();
        alert("Il n'est pas possible de supprimer la dernière ligne des coûts !");
    }
}
</script>
