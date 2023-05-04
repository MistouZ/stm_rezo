<?php

/**
 * @author Nicolas
 * @copyright 2019
 */
include("../../_cfg/cfg.php");

$array = array();
$companyNameData = $_GET["section"];
$type = $_GET['cat'];
$type2 = $_GET['soussouscat'];
$idQuotation = $_GET['soussoussouscat'];

$company = new Company($array);
$companymanager = new CompaniesManager($bdd);
$folder = new Folder($array);
$foldermanager = new FoldersManager($bdd);
$user = new Users($array);
$usermanager = new UsersManager($bdd);
$customer = new Customers($array);
$customermanager = new CustomersManager($bdd);
$quotation = new Quotation($array);
$quotationmanager = new QuotationManager($bdd);
$contact = new Contact($array);
$contactmanager = new ContactManager($bdd);
$tax = new Tax($array);
$taxmanager = new TaxManager($bdd);
$shatteredQuotation = new ShatteredQuotation($array);
$shatteredManager = new ShatteredQuotationManager($bdd);

$dateToProforma = date('d/m/Y');

switch($type){
    case "devis":
        $quotation = $quotationmanager->getByQuotationNumber($idQuotation);
        $entete = "du devis";
        $enteteIcon = '<i class="fas fa-file-invoice"></i>';
        $buttons = '<div class="actions">
                        <a href="'.URLHOST.$_COOKIE['company'].'/'.$type.'/modifier/'.$type2.'/'.$quotation->getQuotationNumber().'" class="btn btn-default btn-sm">
                            <i class="fas fa-edit"></i> Modifier </a>
                        <a target="_blank" href="'.URLHOST.$_COOKIE['company'].'/'.$type.'/imprimer/'.$type2.'/'.$quotation->getQuotationNumber().'" class="btn btn-default btn-sm">
                            <i class="fas fa-print"></i> Imprimer </a>
                        <a data-toggle="modal" href="#to_proforma" class="btn btn-default btn-sm">
                            <i class="fas fa-file-alt"></i> => Proforma </a>
                        <a data-toggle="modal" href="#to_facture" class="btn btn-default btn-sm">
                            <i class="fas fa-file-invoice-dollar"></i> => Facture </a>
                        <!--<a href="'.URLHOST.$_COOKIE['company'].'/'.$type.'/dupliquer/'.$quotation->getQuotationNumber().'" class="btn btn-default btn-sm">
                            <i class="fas fa-edit"></i> Dupliquer </a>-->
                            <a href="'.URLHOST.'_pages/_post/dupliquer_devis.php?quotationNumber='.$quotation->getQuotationNumber().'" class="btn btn-default btn-sm">
                            <i class="fas fa-edit"></i> Dupliquer </a>
                    </div>';
        break;
    case "proforma":
        $quotation = $quotationmanager->getByQuotationNumber($idQuotation);
        $entete = "de la proforma";
        $enteteIcon = '<i class="fas fa-file-alt"></i>';
        $buttons = '<div class="actions">
                        <a target="_blank" href="'.URLHOST.$_COOKIE['company'].'/'.$type.'/imprimer/'.$type2.'/'.$quotation->getQuotationNumber().'" class="btn btn-default btn-sm">
                            <i class="fas fa-print"></i> Imprimer </a>
                        <a data-toggle="modal" href="#to_facture" class="btn btn-default btn-sm">
                            <i class="fas fa-file-invoice-dollar"></i> => Facture </a>
                        <a data-toggle="modal" href="#to_devis" class="btn btn-default btn-sm">
                            <i class="fas fa-file-invoice"></i> => Devis </a>
                    </div>';
        break;
    case "facture":
        $quotation = $quotationmanager->getByQuotationNumber($idQuotation);
        $entete = "de la facture";
        $enteteIcon = '<i class="fas fa-file-invoice-dollar"></i>';
        $buttons = '<div class="actions">
                        <a target="_blank" href="'.URLHOST.$_COOKIE['company'].'/'.$type.'/imprimer/'.$type2.'/'.$quotation->getQuotationNumber().'" class="btn btn-default btn-sm">
                            <i class="fas fa-print"></i> Imprimer </a>
                        <a data-toggle="modal" href="#to_avoir" class="btn btn-default btn-sm">
                            <i class="fas fa-file-prescription"></i> => Avoir </a>
                        <a data-toggle="modal" href="#to_devis" class="btn btn-default btn-sm">
                            <i class="fas fa-file-invoice"></i> => Devis </a>
                    </div>';
        break;
    case "avoir":
        $quotation = $quotationmanager->getByQuotationNumber($idQuotation);
        $entete = "de l'avoir";
        $enteteIcon = '<i class="fas fa-file-prescription"></i>';
        $buttons = '<div class="actions">
                        <a target="_blank" href="'.URLHOST.$_COOKIE['company'].'/'.$type.'/imprimer/'.$type2.'/'.$quotation->getQuotationNumber().'" class="btn btn-default btn-sm">
                            <i class="fas fa-print"></i> Imprimer </a>
                    </div>';
        break;
}
$folder = $foldermanager->get($quotation->getFolderId());
$company = $companymanager->getByNameData($companyNameData);
$descriptions = new Description($array);
$descriptionmanager = new DescriptionManager($bdd);
$descriptions = $descriptionmanager->getByQuotationNumber($quotation->getQuotationNumber());
$contact = $contactmanager->getById($folder->getContactId());
$user = $usermanager->get($folder->getSeller());
$customer = $customermanager->getById($quotation->getCustomerId());
if($quotation->getType() == "S")
{
    $shatteredQuotation = $shatteredManager->getByQuotationNumberChild($quotation->getQuotationNumber());
}

$date = date('d/m/Y',strtotime($quotation->getDate()));

if(isset($_GET['cat5'])){
    $retour = $_GET['cat5'];
}
?>
<div class="row">
    <div class="col-md-12">
        <?php if($retour == "error") { ?>
            <div class="alert alert-danger">
                <button class="close" data-close="alert"></button> Une erreur est survenue, le devis n'a donc pas pu être être mis à jour !</div>
        <?php }elseif($retour == "success"){ ?>
            <div class="alert alert-success">
                <button class="close" data-close="alert"></button> Le devis a bien été mis à jour !</div>
        <?php }elseif($retour == "errorProforma") { ?>
            <div class="alert alert-danger">
                <button class="close" data-close="alert"></button> Erreur lors du passage en proforma !</div>
        <?php }elseif($retour == "successProforma"){ ?>
            <div class="alert alert-success">
                <button class="close" data-close="alert"></button> Passage en proforma effectué avec succès !</div>
        <?php }elseif($retour == "errorDate") { ?>
            <div class="alert alert-danger">
                <button class="close" data-close="alert"></button> Une erreur est survenue, la date n'a donc pas pu être mise à jour !</div>
        <?php }elseif($retour == "successDate"){ ?>
            <div class="alert alert-success">
                <button class="close" data-close="alert"></button> La date a bien été modifiée !</div>
        <?php }elseif($retour == "errorFacture") { ?>
            <div class="alert alert-danger">
                <button class="close" data-close="alert"></button> Erreur lors du passage en facture !</div>
        <?php }elseif($retour == "successFacture"){ ?>
            <div class="alert alert-success">
                <button class="close" data-close="alert"></button> Passage en facture effectué avec succès !</div>
        <?php }elseif($retour == "errorDevis") { ?>
            <div class="alert alert-danger">
                <button class="close" data-close="alert"></button> Erreur lors du passage en devis !</div>
        <?php }elseif($retour == "successDevis"){ ?>
            <div class="alert alert-success">
                <button class="close" data-close="alert"></button> Passage en devis effectué avec succès !</div>
        <?php } ?>
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="portlet yellow-crusta box">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fas fa-info"></i>Informations</div>
                    </div>
                    <div class="portlet-body">
                        <div class="row static-info">
                            <div class="col-md-5 name"> <?php echo ucwords($type); ?>: </div>
                            <div class="col-md-7 value"> <?php echo $quotation->getQuotationNumber(); ?></div>
                        </div>
                        <div class="row static-info">
                            <div class="col-md-5 name"> Date: </div>
                            <div class="col-md-7 value"> <?php echo $date; ?> <a data-toggle="modal" href="#modif_date" ><i class="fas fa-edit"></i></a></div>
                        </div>
                        <div class="row static-info">
                            <div class="col-md-5 name"> Dossier N°: </div>
                            <div class="col-md-7 value"><?php echo $folder->getFolderNumber(); ?></div>
                        </div>
                        <div class="row static-info">
                            <div class="col-md-5 name"> Libellé : </div>
                            <div class="col-md-7 value"> <?php echo $folder->getLabel(); ?> </div>
                        </div>
                        <div class="row static-info">
                            <div class="col-md-5 name"> Commercial : </div>
                            <div class="col-md-7 value"> <?php echo $user->getName().' '.$user->getFirstName(); ?> </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-12">
                <div class="portlet blue-hoki box">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fas fa-user-tie"></i>Informations client </div>
                    </div>
                    <div class="portlet-body">
                        <div class="row static-info">
                            <div class="col-md-5 name"> Client: </div>
                            <div class="col-md-7 value"> <?php echo $customer->getName(); ?> </div>
                        </div>
                        <div class="row static-info">
                            <div class="col-md-5 name"> Adresse: </div>
                            <div class="col-md-7 value"> <?php echo $customer->getInvoiceAddress(); ?> </div>
                        </div>
                        <div class="row static-info">
                            <div class="col-md-5 name"> Contact: </div>
                            <div class="col-md-7 value"> <?php echo $contact->getFirstname()." ".$contact->getName(); ?> </div>
                        </div>
                        <div class="row static-info">
                            <div class="col-md-5 name"> Téléphone: </div>
                            <div class="col-md-7 value"> <?php echo $contact->getPhoneNumber(); ?> </div>
                        </div>
                        <div class="row static-info">
                            <div class="col-md-5 name"> Mail: </div>
                            <div class="col-md-7 value"> <?php echo $contact->getEmailAddress(); ?> </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="portlet grey-cascade box">
                    <div class="portlet-title">
                        <div class="caption">
                            <?php echo $enteteIcon; ?> Détail <?php echo $entete; ?> </div>
                            <?php echo $buttons; ?>
                    </div>
                    <div class="portlet-body">
                        <div class="table-responsive">
                            <form id="multiSelection" method="post">
                                <table class="table table-hover table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <?php
                                            if($type == "devis"){
                                            ?>
                                                <th style="text-align: center !important;" class="desktop"></th>
                                            <?php
                                            }
                                            ?>
                                            <th> Description </th>
                                            <th> Prix à l'unité </th>
                                            <th> QT. </th>
                                            <th> Taxe </th>
                                            <th> Remise </th>
                                            <th> Prix total HT </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $montant = 0;
                                            $totalTaxe = 0;
                                            $montantHT = 0;
                                            $arrayTaxesKey =  array();
                                            foreach($descriptions as $description){
                                                $montantLigne = $description->getQuantity()*$description->getPrice();
                                                $remise = $montantLigne*($description->getDiscount()/100);
                                                $montantLigne = $montantLigne-$remise;
                                                $taxe = $montantLigne*$description->getTax();
                                                $tax = $taxmanager->getByPercent($description->getTax()*100);

                                                //Calcul du détail des taxes pour l'affichage par tranche détaillée
                                                if(isset($arrayTaxesKey[$description->getTax()])){
                                                    $arrayTaxesKey[$description->getTax()]["Montant"] = $arrayTaxesKey[$description->getTax()]["Montant"]+$taxe;
                                                }else{
                                                    $arrayTaxesKey[$description->getTax()]['Taxe']=$tax->getName();
                                                    $arrayTaxesKey[$description->getTax()]['Montant']=$taxe;
                                                }

                                                $totalTaxe = $totalTaxe+$taxe;
                                                $montantHT = $montantHT+$montantLigne;
                                                $montant = $montant+$montantLigne+$taxe;
                                            ?>
                                            <tr>
                                                <?php
                                                if($type == "devis") {
                                                    ?>
                                                    <td><input class="selection" type="checkbox" name="selection[]" value="<?php echo $description->getIdDescription(); ?>"/></td>
                                                    <?php
                                                }
                                                ?>
                                                <td class="col-md-7"><?php echo nl2br($description->getDescription()); ?></td>
                                                <td class="col-md-1"><?php echo number_format($description->getPrice(),0,","," "); ?> XPF</td>
                                                <td><?php echo $description->getQuantity(); ?></td>
                                                <td><?php echo $description->getTax()*100; ?> %</td>
                                                <td><?php echo $description->getDiscount(); ?> %</td>
                                                <td class="col-md-1"><?php echo number_format($montantLigne,0,","," "); ?> XPF</td>
                                            </tr>
                                            <?php
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6"> </div>
            <div class="col-md-6">
                <div class="well">
                    <div class="row static-info align-reverse">
                        <div class="col-md-8 name"> Sous-total: </div>
                        <div class="col-md-3 value"> <?php echo number_format($montantHT,0,","," "); ?> XPF</div>
                    </div>
                    <div class="row static-info align-reverse">
                        <div class="col-md-8 name"> Total taxes : </div>
                        <div class="col-md-3 value"> <?php echo number_format($totalTaxe,0,","," "); ?> XPF</div>
                    </div>
                    <?php 
                        foreach($arrayTaxesKey as $key => $value){ 
                            if($arrayTaxesKey[$key]["Montant"]>0){
                    ?>
                    <div class="row static-info align-reverse">
                        <div class="col-md-8 name" style="font-size: 11px; font-style: italic;"> <?php echo $arrayTaxesKey[$key]["Taxe"]; ?> : </div>
                        <div class="col-md-3 value" style="font-size: 11px; font-style: italic;"> <?php echo number_format($arrayTaxesKey[$key]["Montant"],0,","," "); ?> XPF</div>
                    </div>
                    <?php }} ?>
                    <div class="row static-info align-reverse">
                        <div class="col-md-8 name" style="font-weight: 800; font-size: 16px;"> Total TTC : </div>
                        <div class="col-md-3 value" style="font-weight: 800; font-size: 16px;"> <?php echo number_format($montant,0,","," "); ?> XPF</div>
                    </div>
                </div>
            </div>
            <?php if($type =="facture" && $type2 !="valides")
                {?>
                    <form action="<?php echo URLHOST."_pages/_post/to_validate.php"; ?>" method="post" id="to_validate" class="form-horizontal form-row-seperated">
                        <input type="hidden" id="quotationNumber" name="quotationNumber" value="<?php echo $quotation->getQuotationNumber(); ?>">
                        <div class="modal-footer">
                            <button type="button" class="btn grey-salsa btn-outline" data-dismiss="modal">Fermer</button>
                            <button type="submit" class="btn green" name="valider">
                                <i class="fa fa-check"></i> Valider</button>
                        </div>
                    </form>
            <?php
                }?>
        </div>
        <div id="to_proforma" data-keyboard="false" data-backdrop="static" class="modal fade" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Passage <?php echo $entete; ?> <span style="font-style: italic; font-weight: 800;"><?php echo $quotation->getQuotationNumber(); ?></span> en proforma</h4>
                    </div>
                    <div class="modal-body form">
                        <form action="<?php echo URLHOST."_pages/_post/to_proforma.php"; ?>" method="post" id="to_proforma" class="form-horizontal form-row-seperated">
                            <div class="form-group">
                                <label class="control-label col-md-4">Date
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-8">
                                    <div class="input-group input-medium date date-picker"  data-date-lang="FR-fr" type="text">
                                        <input type="text" name="date" class="form-control" value="<?php echo $dateToProforma; ?>" >
                                        <span class="input-group-btn">
                                            <button class="btn default" type="button">
                                                <i class="fas fa-calendar-alt"></i>
                                            </button>
                                        </span>
                                    </div>
                                    <span class="help-block">Si aucune date n'est sélectionnée, la date par défaut sera celle du jour</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Partiel
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-9">
                                    <div class="radio-list" data-error-container="#credential_error">
                                        <?php if($quotation->getType() == "S")
                                            {
                                            ?>
                                                <label class="radio-inline"><input name="shattered" id="shattered1" type="radio" value="full" class="form-control" /><?php echo $shatteredQuotation->getPercent(); ?>
                                                    ?></label>
                                            <?php
                                            }
                                            else{
                                            ?>
                                                <label class="radio-inline"><input name="shattered" id="shattered1" type="radio" value="full" class="form-control" checked/>Non</label>
                                            <?php
                                            }
                                            ?>

                                        <label class="radio-inline"><input name="shattered" id="shattered2" type="radio" value="partial" class="form-control" />Partiel</label>
                                    </div>
                                    <div id="credential_error"> </div>
                                </div>
                            </div>
                            <div class="form-group" id="partial" style="display: none">
                                <label class="control-label col-md-3">Pourcentage à facturer
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-4">
                                    <input name="shattered_percent" id="shattered_percent" type="text" class="form-control" /> </div>
                            </div>
                            <input type="hidden" id="quotationNumber" name="quotationNumber" value="<?php echo $quotation->getQuotationNumber(); ?>">
                            <input type="hidden" id="type" name="type" value="<?php echo $type2; ?>">
                            <div class="modal-footer">
                                <button type="button" class="btn grey-salsa btn-outline" data-dismiss="modal">Fermer</button>
                                <button type="submit" class="btn green" name="valider">
                                    <i class="fa fa-check"></i> Valider</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

        </div>
        <div id="to_facture" data-keyboard="false" data-backdrop="static" class="modal fade" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Passage <?php echo $entete; ?> <span style="font-style: italic; font-weight: 800;"><?php echo $quotation->getQuotationNumber(); ?></span> en facture</h4>
                    </div>
                    <div class="modal-body form">
                        <form action="<?php echo URLHOST."_pages/_post/to_facture.php"; ?>" method="post" id="to_facture" class="form-horizontal form-row-seperated">
                            <div class="form-group">
                                <label class="control-label col-md-4">Date
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-8">
                                    <div class="input-group input-medium date date-picker"  data-date-lang="FR-fr" type="text">
                                        <input type="text" name="date" class="form-control" value="<?php echo $dateToProforma; ?>" >
                                        <span class="input-group-btn">
                                            <button class="btn default" type="button">
                                                <i class="fas fa-calendar-alt"></i>
                                            </button>
                                        </span>
                                    </div>
                                    <span class="help-block">Si aucune date n'est sélectionnée, la date par défaut sera celle du jour</span>
                                </div>
                            </div>
                            <input type="hidden" id="quotationNumber" name="quotationNumber" value="<?php echo $quotation->getQuotationNumber(); ?>">
                            <input type="hidden" id="type" name="type" value="<?php echo $type2; ?>">
                            <div class="modal-footer">
                                <button type="button" class="btn grey-salsa btn-outline" data-dismiss="modal">Fermer</button>
                                <button type="submit" class="btn green" name="valider">
                                    <i class="fa fa-check"></i> Valider</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
        <div id="to_avoir" data-keyboard="false" data-backdrop="static" class="modal fade" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Passage <?php echo $entete; ?> <span style="font-style: italic; font-weight: 800;"><?php echo $quotation->getQuotationNumber(); ?></span> en avoir</h4>
                    </div>
                    <div class="modal-body form">
                        <form action="<?php echo URLHOST."_pages/_post/to_avoir.php"; ?>" method="post" id="to_avoir" class="form-horizontal form-row-seperated">
                            <div class="form-group">
                                <label class="control-label col-md-4">Date
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-8">
                                    <div class="input-group input-medium date date-picker"  data-date-lang="FR-fr" type="text">
                                        <input type="text" name="date" class="form-control" value="<?php echo $dateToProforma; ?>" >
                                        <span class="input-group-btn">
                                            <button class="btn default" type="button">
                                                <i class="fas fa-calendar-alt"></i>
                                            </button>
                                        </span>
                                    </div>
                                    <span class="help-block">Si aucune date n'est sélectionnée, la date par défaut sera celle du jour</span>
                                </div>
                            </div>
                            <input type="hidden" id="quotationNumber" name="quotationNumber" value="<?php echo $quotation->getQuotationNumber(); ?>">
                            <input type="hidden" id="type" name="type" value="<?php echo $type2; ?>">
                            <div class="modal-footer">
                                <button type="button" class="btn grey-salsa btn-outline" data-dismiss="modal">Fermer</button>
                                <button type="submit" class="btn green" name="valider">
                                    <i class="fa fa-check"></i> Valider</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
        <div id="to_devis" data-keyboard="false" data-backdrop="static" class="modal fade" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Passage <?php echo $entete; ?> <span style="font-style: italic; font-weight: 800;"><?php echo $quotation->getQuotationNumber(); ?></span> en devis</h4>
                    </div>
                    <div class="modal-body form">
                        <form action="<?php echo URLHOST."_pages/_post/to_devis.php"; ?>" method="post" id="to_avoir" class="form-horizontal form-row-seperated">
                            <div class="form-group">
                                <label class="control-label col-md-4">Date
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-8">
                                    <div class="input-group input-medium date date-picker"  data-date-lang="FR-fr" type="text">
                                        <input type="text" name="date" class="form-control" value="<?php echo $dateToProforma; ?>" >
                                        <span class="input-group-btn">
                                            <button class="btn default" type="button">
                                                <i class="fas fa-calendar-alt"></i>
                                            </button>
                                        </span>
                                    </div>
                                    <span class="help-block">Si aucune date n'est sélectionnée, la date par défaut sera celle du jour</span>
                                </div>
                            </div>
                            <input type="hidden" id="quotationNumber" name="quotationNumber" value="<?php echo $quotation->getQuotationNumber(); ?>">
                            <input type="hidden" id="type" name="type" value="<?php echo $type2; ?>">
                            <div class="modal-footer">
                                <button type="button" class="btn grey-salsa btn-outline" data-dismiss="modal">Fermer</button>
                                <button type="submit" class="btn green" name="valider">
                                    <i class="fa fa-check"></i> Valider</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
        <div id="modif_date" data-keyboard="false" data-backdrop="static" class="modal fade" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Modifier la date <?php echo $entete; ?> <span style="font-style: italic; font-weight: 800;"><?php echo $quotation->getQuotationNumber(); ?></span></h4>
                    </div>
                    <div class="modal-body form">
                        <form action="<?php echo URLHOST."_pages/_post/modifier_date.php"; ?>" method="post" id="to_proforma" class="form-horizontal form-row-seperated">
                            <div class="form-group">
                                <label class="control-label col-md-4">Date
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-8">
                                    <div class="input-group input-medium date date-picker"  data-date-lang="FR-fr" type="text">
                                        <input type="text" name="date" class="form-control" value="<?php echo $date; ?>" >
                                        <span class="input-group-btn">
                                            <button class="btn default" type="button">
                                                <i class="fas fa-calendar-alt"></i>
                                            </button>
                                        </span>
                                    </div>
                                    <span class="help-block">Cliquez sur la date pour la modifier</span>
                                </div>
                            </div>
                            <input type="hidden" id="quotationNumber" name="quotationNumber" value="<?php echo $quotation->getQuotationNumber(); ?>">
                            <input type="hidden" id="type" name="type" value="<?php echo $type2; ?>">
                            <div class="modal-footer">
                                <button type="button" class="btn grey-salsa btn-outline" data-dismiss="modal">Fermer</button>
                                <button type="submit" class="btn green" name="valider">
                                    <i class="fa fa-check"></i> Valider</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script language="JavaScript">
    $('#select-all').click(function(){
        if($('#select-all').attr("checked")){
            $('#select-all').removeAttr('checked');
            $('.selection').each(function() {
                $(this).removeAttr('checked').uniform('refresh');
            });
            $.uniform.update();
        }else{
            $('#select-all').attr('checked','checked');
            $('.selection').each(function() {
                $(this).prop('checked',true);
                $(this).parent('span').addClass('checked');
            });
        }
    });
       $('#multiSelection :checkbox').change(function() {
        //$.uniform.update();
        var nb = $('#multiSelection :checkbox:checked').length;
        var nbTotal = $('#multiSelection :checkbox').length;
        if (nb>0) {
            if(nb==1){
                if($('#select-all').attr("checked")){
                    $('#select-all').removeAttr('checked').uniform('refresh');
                }else{
                    $("#actions").css("display","");
                    $("#actions").css("display","inline");
                }
            }
        } else {
            $("#actions").css("display","");
            $("#actions").css("display","none");
            $('#select-all').removeAttr('checked');
        }
    });

    $(document).ready(function(){
        $("input[name$='shattered']").click(function() {
            var test = $(this).val();
            if(test == "full"){
                $("#partial").hide();
            }
            else{
                $("#partial").show();
            }
        });
    });

</script>
