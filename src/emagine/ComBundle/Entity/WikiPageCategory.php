<?php

namespace emagine\ComBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * WikiPageCategory
 *
 * @UniqueEntity("titre")
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class WikiPageCategory
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
     * @var string
     * 
     * @Assert\Length(
     *      min = 2,
     *      max = 20,
     *      minMessage = "Le titre doit contenir au moin {{ limit }} caractère",
     *      maxMessage = "Le titre doit contenir au plus {{ limit }} caractère"
     * )
     *
     * @ORM\Column(name="titre", type="string", length=255, nullable=false, unique=true)
     */
    private $titre;

    /**
     * @ORM\OneToMany(targetEntity="emagine\ComBundle\Entity\WikiPage", mappedBy="category")
     **/
    private $wikiPage;


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
     * Set titre
     *
     * @param string $titre
     * @return WikiPageCategory
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;
    
        return $this;
    }

    /**
     * Get titre
     *
     * @return string 
     */
    public function getTitre()
    {
        return $this->titre;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->wikiPage = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add wikiPage
     *
     * @param \emagine\ComBundle\Entity\WikiPage $wikiPage
     * @return WikiPageCategory
     */
    public function addWikiPage(\emagine\ComBundle\Entity\WikiPage $wikiPage)
    {
        $this->wikiPage[] = $wikiPage;
    
        return $this;
    }

    /**
     * Remove wikiPage
     *
     * @param \emagine\ComBundle\Entity\WikiPage $wikiPage
     */
    public function removeWikiPage(\emagine\ComBundle\Entity\WikiPage $wikiPage)
    {
        $this->wikiPage->removeElement($wikiPage);
    }

    /**
     * Get wikiPage
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWikiPage()
    {
        return $this->wikiPage;
    }
}