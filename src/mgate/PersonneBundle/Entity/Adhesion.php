<?php

namespace mgate\PersonneBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adhesion
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="mgate\PersonneBundle\Entity\AdhesionRepository")
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
     * @var boolean
     *
     * @ORM\Column(name="ri", type="boolean")
     */
    private $ri;

    /**
     * @var boolean
     *
     * @ORM\Column(name="FicheAdhesion", type="boolean")
     */
    private $ficheAdhesion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="CarteEtudiante", type="boolean")
     */
    private $carteEtudiante;

    /**
     * @var boolean
     *
     * @ORM\Column(name="CarteVitale", type="boolean")
     */
    private $carteVitale;

    /**
     * @var boolean
     *
     * @ORM\Column(name="RIB", type="boolean")
     */
    private $rIB;

    /**
     * @var boolean
     *
     * @ORM\Column(name="Cheque", type="boolean")
     */
    private $cheque;

    /**
     * @var boolean
     *
     * @ORM\Column(name="signature", type="boolean")
     */
    private $signature;


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
     * Set ri
     *
     * @param boolean $ri
     * @return Adhesion
     */
    public function setRi($ri)
    {
        $this->ri = $ri;
    
        return $this;
    }

    /**
     * Get ri
     *
     * @return boolean 
     */
    public function getRi()
    {
        return $this->ri;
    }

    /**
     * Set ficheAdhesion
     *
     * @param boolean $ficheAdhesion
     * @return Adhesion
     */
    public function setFicheAdhesion($ficheAdhesion)
    {
        $this->ficheAdhesion = $ficheAdhesion;
    
        return $this;
    }

    /**
     * Get ficheAdhesion
     *
     * @return boolean 
     */
    public function getFicheAdhesion()
    {
        return $this->ficheAdhesion;
    }

    /**
     * Set carteEtudiante
     *
     * @param boolean $carteEtudiante
     * @return Adhesion
     */
    public function setCarteEtudiante($carteEtudiante)
    {
        $this->carteEtudiante = $carteEtudiante;
    
        return $this;
    }

    /**
     * Get carteEtudiante
     *
     * @return boolean 
     */
    public function getCarteEtudiante()
    {
        return $this->carteEtudiante;
    }

    /**
     * Set carteVitale
     *
     * @param boolean $carteVitale
     * @return Adhesion
     */
    public function setCarteVitale($carteVitale)
    {
        $this->carteVitale = $carteVitale;
    
        return $this;
    }

    /**
     * Get carteVitale
     *
     * @return boolean 
     */
    public function getCarteVitale()
    {
        return $this->carteVitale;
    }

    /**
     * Set rIB
     *
     * @param boolean $rIB
     * @return Adhesion
     */
    public function setRIB($rIB)
    {
        $this->rIB = $rIB;
    
        return $this;
    }

    /**
     * Get rIB
     *
     * @return boolean 
     */
    public function getRIB()
    {
        return $this->rIB;
    }

    /**
     * Set cheque
     *
     * @param boolean $cheque
     * @return Adhesion
     */
    public function setCheque($cheque)
    {
        $this->cheque = $cheque;
    
        return $this;
    }

    /**
     * Get cheque
     *
     * @return boolean 
     */
    public function getCheque()
    {
        return $this->cheque;
    }

    /**
     * Set signature
     *
     * @param boolean $signature
     * @return Adhesion
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;
    
        return $this;
    }

    /**
     * Get signature
     *
     * @return boolean 
     */
    public function getSignature()
    {
        return $this->signature;
    }
}
