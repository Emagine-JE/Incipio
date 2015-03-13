<?php
        
/* 
   Alexandre Couedelo @ 2015-02-17 20:15:24
 */

namespace emagine\SecGBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use emagine\SecGBundle\Entity\AdhesionCheckerCategory;

class LoadAhesionCheckerCategoryData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $items = array(
            "Fiche d'Adhésion",
            "Carte étudiante",
            "Carte Vitale",
            "RIB",
            "Cheque",
            "signature",
            "cv"
        );

        foreach($items as $item){
            $p = new AdhesionCheckerCategory();
            $p->setNom($item);

            $manager->persist($p);
        }
        $manager->flush();
    }
}

