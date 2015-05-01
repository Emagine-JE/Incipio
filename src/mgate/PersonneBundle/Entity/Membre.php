<?php
        
/*
This file is part of Incipio.

Incipio is an enterprise resource planning for Junior Enterprise
Copyright (C) 2012-2014 Florian Lefevre.

Incipio is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

Incipio is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with Incipio as the file LICENSE.  If not, see <http://www.gnu.org/licenses/>.
*/


namespace mgate\PersonneBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * mgate\PersonneBundle\Entity\Membre
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="mgate\PersonneBundle\Entity\MembreRepository")
 */
class Membre {

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
       
    /**
     * @ORM\OneToMany(targetEntity="mgate\SuiviBundle\Entity\Mission", mappedBy="intervenant", cascade={"persist","remove"})
     */
    private $missions;

    /**
     * @ORM\OneToOne(targetEntity="mgate\PersonneBundle\Entity\Personne", inversedBy="membre", cascade={"persist", "merge", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $personne;
    
    /**
     * @var \Date $dateSignature
     *
     * @ORM\Column(name="dateCE", type="date",nullable=true)
     */
    private $dateConventionEleve;

    /**
     * @var string $identifiant
     *
     * @ORM\Column(name="identifiant", type="string", length=10, nullable=true, unique=true)
     */
    private $identifiant;
    
    /**
     * @var string $emailEMSE
     *
     * @ORM\Column(name="emailEMSE", type="string", length=50, nullable=true)
     */
    private $emailEMSE;

    /**
     * @var int $promotion
     * @ORM\Column(name="promotion", type="smallint", nullable=true)
     */
    private $promotion;
    
    /**
     * @var int $appartement
     * @ORM\Column(name="appartement", type="string", nullable=true)
     */
    private $appartement;

    /**
     * @var date $datedDeNaissance
     * @ORM\Column(name="birthdate", type="date", nullable=true)
     */
    private $dateDeNaissance;

    /**
     * @var string $lieuDeNaissancce
     * @ORM\Column(name="placeofbirth", type="string", nullable=true)
     */
    private $lieuDeNaissance;

    /**
     * @ORM\OneToMany(targetEntity="mgate\PersonneBundle\Entity\Mandat", mappedBy="membre", cascade={"persist","remove"})
     */
    private $mandats;
	
	/**
     * @var string $nationalite
     * @ORM\Column(name="nationalite", type="string", nullable=true)
     */
    private $nationalite;
    
    /**
     * @ORM\OneToMany(targetEntity="mgate\PubliBundle\Entity\RelatedDocument", mappedBy="membre", cascade={"remove"})
     */
    private $relatedDocuments;
    
    /**
     * @var string $photoURI
     * @ORM\Column(name="photoURI", type="string", nullable=true)
     */
    private $photoURI;

    /**
     * @ORM\ManyToMany(targetEntity="emagine\SecGBundle\Entity\AdhesionCheckerCategory")
     **/
    private $adhesions;

    /**
     * Constructor
     */
    public function __construct() {
        $this->mandats = new ArrayCollection();
        $this->missions = new ArrayCollection();
        $this->relatedDocuments = new ArrayCollection();
        $this->adhesions = new ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set identifiant
     *
     * @param string $identifiant
     * @return Membre
     */
    public function setIdentifiant($identifiant) {
        $this->identifiant = $identifiant;

        return $this;
    }

    /**
     * Get identifiant
     *
     * @return string 
     */
    public function getIdentifiant() {
        return $this->identifiant;
    }

    /**
     * Set personne
     *
     * @param \mgate\PersonneBundle\Entity\Personne $personne
     * @return Membre
     */
    public function setPersonne(\mgate\PersonneBundle\Entity\Personne $personne = null) {
        if ($personne != null)
            $personne->setMembre($this);
        $this->personne = $personne;

        return $this;
    }

    /**
     * Get personne
     *
     * @return \mgate\PersonneBundle\Entity\Personne 
     */
    public function getPersonne() {
        return $this->personne;
    }

    /**
     * Set poste
     *
     * @param \mgate\PersonneBundle\Entity\Membre $poste
     * @return Membre
     */
    public function setPoste(\mgate\PersonneBundle\Entity\Poste $poste = null) {
        $this->poste = $poste;
        return $this;
    }

    /**
     * Get poste
     *
     * @return \mgate\PersonneBundle\Entity\Membre
     */
    public function getPoste() {
        return $this->poste;
    }

    /**
     * Add mandats
     *
     * @param \mgate\PersonneBundle\Entity\Mandat $mandats
     * @return Membre
     */
    public function addMandat(\mgate\PersonneBundle\Entity\Mandat $mandats) {
        $this->mandats[] = $mandats;

        return $this;
    }

    /**
     * Remove mandats
     *
     * @param \mgate\PersonneBundle\Entity\Mandat $mandats
     */
    public function removeMandat(\mgate\PersonneBundle\Entity\Mandat $mandats) {
        $this->mandats->removeElement($mandats);
    }

    /**
     * Get mandats
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMandats() {
        return $this->mandats;
    }

    /**
     * Set promotion
     *
     * @param integer $promotion
     * @return Membre
     */
    public function setPromotion($promotion) {
        $this->promotion = $promotion;

        return $this;
    }

    /**
     * Get promotion
     *
     * @return integer 
     */
    public function getPromotion() {
        return $this->promotion;
    }

    /**
     * Set dateDeNaissance
     *
     * @param \DateTime $dateDeNaissance
     * @return Membre
     */
    public function setDateDeNaissance($dateDeNaissance) {
        $this->dateDeNaissance = $dateDeNaissance;

        return $this;
    }

    /**
     * Get dateDeNaissance
     *
     * @return \DateTime 
     */
    public function getDateDeNaissance() {
        return $this->dateDeNaissance;
    }

    /**
     * Set lieuDeNaissance
     *
     * @param string $lieuDeNaissance
     * @return Membre
     */
    public function setLieuDeNaissance($lieuDeNaissance) {
        $this->lieuDeNaissance = $lieuDeNaissance;

        return $this;
    }

    /**
     * Get lieuDeNaissance
     *
     * @return string 
     */
    public function getLieuDeNaissance() {
        return $this->lieuDeNaissance;
    }


    /**
     * Set appartement
     *
     * @param integer $appartement
     * @return Membre
     */
    public function setAppartement($appartement)
    {
        $this->appartement = $appartement;
    
        return $this;
    }

    /**
     * Get appartement
     *
     * @return integer 
     */
    public function getAppartement()
    {
        return $this->appartement;
    }
	
	 /**
     * Set nationalite
     *
     * @param string $nationalite
     * @return Membre
     */
    public function setNationalite($nationalite)
    {
        $this->nationalite = $nationalite;
    
        return $this;
    }

    /**
     * Get nationalite
     *
     * @return string 
     */
    public function getNationalite()
    {
        return $this->nationalite;
    }
    
    /**
     * Set emailEMSE
     *
     * @param string $emailEMSE
     * @return Membre
     */
    public function setEmailEMSE($emailEMSE) {
        $this->emailEMSE = $emailEMSE;

        return $this;
    }

    /**
     * Get emailEMSE
     *
     * @return string 
     */
    public function getEmailEMSE() {
        return $this->emailEMSE;
    }

    /**
     * Set dateConventionEleve
     *
     * @param \DateTime $dateConventionEleve
     * @return Membre
     */
    public function setDateConventionEleve($dateConventionEleve)
    {
        $this->dateConventionEleve = $dateConventionEleve;
    
        return $this;
    }

    /**
     * Get dateConventionEleve
     *
     * @return \DateTime 
     */
    public function getDateConventionEleve()
    {
        return $this->dateConventionEleve;
    }

    /**
     * Add missions
     *
     * @param \mgate\SuiviBundle\Entity\Mission $missions
     * @return Membre
     */
    public function addMission(\mgate\SuiviBundle\Entity\Mission $missions)
    {
        $this->missions[] = $missions;
    
        return $this;
    }

    /**
     * Remove missions
     *
     * @param \mgate\SuiviBundle\Entity\Mission $missions
     */
    public function removeMission(\mgate\SuiviBundle\Entity\Mission $missions)
    {
        $this->missions->removeElement($missions);
    }

    /**
     * Get missions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMissions()
    {
        return $this->missions;
    }

    /**
     * Add relatedDocuments
     *
     * @param \mgate\PubliBundle\Entity\RelatedDocument $relatedDocuments
     * @return Membre
     */
    public function addRelatedDocument(\mgate\PubliBundle\Entity\RelatedDocument $relatedDocuments)
    {
        $this->relatedDocuments[] = $relatedDocuments;
    
        return $this;
    }

    /**
     * Remove relatedDocuments
     *
     * @param \mgate\PubliBundle\Entity\RelatedDocument $relatedDocuments
     */
    public function removeRelatedDocument(\mgate\PubliBundle\Entity\RelatedDocument $relatedDocuments)
    {
        $this->relatedDocuments->removeElement($relatedDocuments);
    }

    /**
     * Get relatedDocuments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRelatedDocuments()
    {
        return $this->relatedDocuments;
    }

    /**
     * Set photoURI
     *
     * @param string $photoURI
     * @return Membre
     */
    public function setPhotoURI($photoURI)
    {
        $this->photoURI = $photoURI;
    
        return $this;
    }

    /**
     * Get photoURI
     *
     * @return string 
     */
    public function getPhotoURI()
    {
        return $this->photoURI;
    }


    /**
     * Add adhesions
     *
     * @param \emagine\SecGBundle\Entity\AdhesionCheckerCategory $adhesions
     * @return Membre
     */
    public function addAdhesion(\emagine\SecGBundle\Entity\AdhesionCheckerCategory $adhesions)
    {
        $this->adhesions[] = $adhesions;
    
        return $this;
    }

    /**
     * Remove adhesions
     *
     * @param \emagine\SecGBundle\Entity\AdhesionCheckerCategory $adhesions
     */
    public function removeAdhesion(\emagine\SecGBundle\Entity\AdhesionCheckerCategory $adhesions)
    {
        $this->adhesions->removeElement($adhesions);
    }

    /**
     * Get adhesions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAdhesions()
    {
        return $this->adhesions;
    }
}