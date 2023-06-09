<?php
/**
 * Created by PhpStorm.
 * User: adewynter
 * Date: 22/11/2018
 * Time: 11:30
 */

class Folder extends Features
{
    private $idFolder;
    private $folderNumber;
    private $label;
    private $isActive;
    private $date;
    private $description;
    private $companyId;

    /**
     * Folder constructor.
     */
    public function __construct(array $data)
    {
        $this->generate($data);
    }

    /**
     * @return mixed
     */
    public function getIdFolder()
    {
        return $this->idFolder;
    }

    /**
     * @param mixed $idFolder
     */
    public function setIdFolder($idFolder)
    {
        $this->idFolder = $idFolder;
    }

    /**
     * @return mixed
     */
    public function getFolderNumber()
    {
        return $this->folderNumber;
    }

    /**
     * @param mixed $folderNumber
     */
    public function setFolderNumber($folderNumber)
    {
        $this->folderNumber = $folderNumber;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param mixed $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return mixed
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @param mixed $isActive
     */
    public function setIsActive($isActive): void
    {
        $this->isActive = $isActive;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date): void
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * @param mixed $companyId
     */
    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;
    }
    
     

}