<?php

namespace emagine\SecGBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adhesion = membre_adhesioncheckercategory
 *
 * @ORM\Table(name="adhesion")
 * @ORM\Entity(repositoryClass="emagine\SecGBundle\Entity\AdhesionRepository")
 */
class Adhesion
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="membre_id", type="integer")
     */
    private $membreId;

    /**
     * @var integer
     *
     * @ORM\Column(name="category_id", type="integer")
     */
    private $categoryId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="etat", type="integer")
     */
    private $etat;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set membreId
     *
     * @param integer $membreId
     * @return Adhesion
     */
    public function setMembreId($membreId)
    {
        $this->membreId = $membreId;
    
        return $this;
    }

    /**
     * Get membreId
     *
     * @return integer 
     */
    public function getMembreId()
    {
        return $this->membreId;
    }

    /**
     * Set categoryId
     *
     * @param integer $categoryId
     * @return Adhesion
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
    
        return $this;
    }

    /**
     * Get categoryId
     *
     * @return integer 
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * Set etat
     *
     * @param integer $etat
     * @return Adhesion
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;
    
        return $this;
    }

    /**
     * Get etat
     *
     * @return integer 
     */
    public function getEtat()
    {
        return $this->etat;
    }
}