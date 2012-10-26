<?php

/**
 * Workincloser
 * Copyright (C) 2012 Inal DJAFAR - Ulipse
 *
 * This file is part of Workincloser.

 * Workincloser is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * Workincloser is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with Workincloser.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Ulipse\WorkincloserBundle\Repository;

use Doctrine\ORM\EntityRepository;

use Ulipse\UserBundle\Entity\User;

class AddressRepository extends EntityRepository
{
    /**
     * The minimum distance.
     */
    const LIMIT = 0.600;

    /**
     * @param \Ulipse\UserBundle\Entity\User $user
     * @param string $type
     * @return array
     */
    public function getAddressesWhereNotUserAndType(User $user, $type = '')
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('a')
           ->from('UlipseWorkincloserBundle:Address', 'a')
           ->where('a.type != :type')
           ->andWhere('a.user != :user')
           ->setParameter('type', $type)
           ->setParameter('user', $user->getId())
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @param \Ulipse\UserBundle\Entity\User $first
     * @param \Ulipse\UserBundle\Entity\User $second
     *
     * @return bool
     */
    public function areCompatible(User $first, User $second)
    {
        $users = $this->getUsersMatchingWith($first);
        if ($users) {
            foreach ($users as $user) {
                if ($user == $second) {
                    return true;
                }
            }
        }

        return false;
    }
    
    /**
     * 
     * @param type $user
     * @param type $type
     * @return array
     */
    public function getMatchingDistanceBy($user, $type)
    {
        $qb = $this->_em->createQueryBuilder();
        
        $qb->select('d')
                ->from('UlipseWorkincloserBundle:Distance','d')
                ->innerJoin('d.first', 'f')
                ->innerJoin('d.second', 's')
                ->where('f.user =:first OR s.user =:second')
                ->andWhere('f.type =:type')
                ->andWhere('d.distance < :limit')
                ->setParameter('first', $user->getId())
                ->setParameter('second', $user->getId())
                ->setParameter('limit', AddressRepository::LIMIT)
                ->setParameter('type', $type)
        ;
        
        return ($qb->getQuery()->getResult())? : array();
    }
    
    /**
     * 
     * @param type $distances
     * @param type $user
     * @return type
     */
    public function getUsersByDistances($distances, $user)
    {
        $users = array();
        foreach ($distances as $distance) {
            if ($distance->getFirst()->getUser() != $user) {
                $users[] = $distance->getFirst()->getUser();
            }
            if ($distance->getSecond()->getUser() != $user) {
                $users[] = $distance->getSecond()->getUser();
            }     
        }
       
        return $users;
    }
    
    /**
     * 
     * @param type $user
     * @return array|null
     */
    public function getUsersMatchingWith($user)
    {
        $workat_compatible_distances = $this->getMatchingDistanceBy($user, 'workat');
        $liveat_compatible_distances = $this->getMatchingDistanceBy($user, 'liveat');
        
        if ($liveat_compatible_distances && $workat_compatible_distances) {
            return \array_intersect($this->getUsersByDistances($workat_compatible_distances, $user), 
                    $this->getUsersByDistances($liveat_compatible_distances, $user));
        }
        
        return null;
    }
}