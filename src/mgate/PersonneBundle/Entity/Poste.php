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

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * mgate\PersonneBundle\Entity\Poste
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="mgate\PersonneBundle\Entity\PosteRepository")
 */
class Poste
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
     
    /**
     * @var string $intitule
     *
     * @ORM\Column(name="intitule", type="string", length=255)
     */
    private $intitule;

    /**
     * @var text $description
     * 
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string $fiche URL vers la fiche de poste
     *
     * @ORM\Column(name="fiche", type="string", length=255, nullable=true)
     */
    private $fiche;

        /**
     * @var string $book URL vers la fiche de poste
     *
     * @ORM\Column(name="book", type="string", length=255, nullable=true)
     */
    private $book;

    /**
     * @ORM\OneToMany(targetEntity="mgate\PersonneBundle\Entity\Mandat", mappedBy="poste")
     * @ORM\JoinColumn(nullable=true)
     */
    private $mandats;

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
     * Get intitule
     *
     * @return string 
     */
    public function getIntitule()
    {
        return $this->intitule;
    }

    /**
     * Set intitule
     *
     * @param string $intitule
     * @return Poste
     */
    public function setIntitule($intitule)
    {
        $this->intitule = $intitule;
    
        return $this;
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->mandat = new \Doctrine\Common\Collections\ArrayCollection();
    }
    

    /**
     * Add mandats
     *
     * @param \mgate\PersonneBundle\Entity\Mandat $mandats
     * @return Poste
     */
    public function addMandat(\mgate\PersonneBundle\Entity\Mandat $mandats)
    {
        $this->mandats[] = $mandats;
    
        return $this;
    }

    /**
     * Remove mandats
     *
     * @param \mgate\PersonneBundle\Entity\Mandat $mandats
     */
    public function removeMandat(\mgate\PersonneBundle\Entity\Mandat $mandats)
    {
        $this->mandats->removeElement($mandats);
    }

    /**
     * Get mandats
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMandats()
    {
        return $this->mandats;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Poste
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set fiche
     *
     * @param string $fiche
     * @return Poste
     */
    public function setFiche($fiche)
    {
        $this->fiche = $fiche;
    
        return $this;
    }

    /**
     * Get fiche
     *
     * @return string 
     */
    public function getFiche()
    {
        return $this->fiche;
    }

    /**
     * Set book
     *
     * @param string $book
     * @return Poste
     */
    public function setBook($book)
    {
        $this->book = $book;
    
        return $this;
    }

    /**
     * Get book
     *
     * @return string 
     */
    public function getBook()
    {
        return $this->book;
    }
}