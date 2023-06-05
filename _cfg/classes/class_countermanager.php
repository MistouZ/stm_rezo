<?php
/**
 * Created by PhpStorm.
 * Folder: adewynter
 * Date: 27/11/2018
 * Time: 10:42
 */

class CounterManager
{
    /**
     * PDO Database instance PDO
     * @var
     */
    private $_db;

    /**
     * folderManager constructor.
     * @param $_db
     */
    public function __construct($_db)
    {
        $this->_db = $_db;
    }

    /**
     * @param mixed $db
     */
    public function setDb(PDO $db)
    {
        $this->_db = $db;
    }

    /**
     * get all counter value
     * @return Counter
     */

    public function getCount($companyId)
    {
        $companyId = (integer) $companyId;
        $q = $this->_db->query('SELECT * FROM company_counting WHERE company_idcompany ='.$companyId);
        $donnees = $q->fetch(PDO::FETCH_ASSOC);
        return new Counter($donnees);
    }

    /**
     * Update counter
     * @param Counter $counter
     * @return string|null
     */
    
    public function updateCounter(Counter $counter)
    {
        try{
            $q = $this->_db->prepare('UPDATE company_counting SET folder = :folder, quotation = :quotation, invoice = :invoice, assets = :assets WHERE company_idcompany  = :idcompany');
            $q->bindValue(':folder', $counter->getFolder(), PDO::PARAM_INT);
            $q->bindValue(':quotation', $counter->getQuotation(), PDO::PARAM_INT);
            $q->bindValue(':invoice', $counter->getInvoice(), PDO::PARAM_INT);
            $q->bindValue(':assets', $counter->getAssets(), PDO::PARAM_INT);
            $q->bindValue(':idcompany', $counter->getCompany(), PDO::PARAM_INT);
   
            $q->execute();

            
            return "Ok";
        }
        catch(Exception $e){
            return null;
        }

    }
    
}