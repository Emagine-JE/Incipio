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


namespace emagine\FormationBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * FormationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FormationRepository extends EntityRepository
{
    /**
     * 
     * @return array
     */
    public function findAllByMandat() {        
        $entities = $this->findBy(array(), array('mandat' => 'desc', 'dateDebut' => 'asc'));
        $formationsParMandat = array();
        foreach($entities as $formation){
            $mandat = $formation->getMandat();
            if(array_key_exists($mandat, $formationsParMandat))
                $formationsParMandat[$mandat][] = $formation;
            else
                $formationsParMandat[$mandat] = array($formation);
        }        
        return $formationsParMandat;
    }
}
