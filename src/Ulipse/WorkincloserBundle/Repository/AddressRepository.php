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
     * @param \Ulipse\UserBundle\Entity\User $user
     * @return array or null
     */
    public function getMatchingUsersWith(User $user)
    {
        $addresses = $this->findByUser($user);

        $distances = array();
        foreach ($addresses as $address) {
            $distances[] = \array_merge($this->getMatchingUsersWithFirstAddress($address),
            $this->getMatchingUsersWithSecondAddress($address));
        }

        return $this->getUsersFromDistancesOfUser($user, $distances);
    }


    /**
     * @param \Ulipse\UserBundle\Entity\User $user
     * @param array $distances
     * @return array
     */
    public function getUsersFromDistancesOfUser(User $user, $distances = array())
    {
        $result = array();
        foreach ($distances as $ds) {
            foreach ($ds as $distance) {
                if ($distance->getFirst()->getUser() != $user) {
                    $result[] = $distance->getFirst()->getUser();
                }
                if ($distance->getSecond()->getUser() != $user) {
                    $result[] = $distance->getSecond()->getUser();
                }
            }
        }

        return \array_unique($result);
    }

    /**
     * @param array $users_ids
     * @return array
     */
    public function getUsersById(array $users_ids)
    {
        $users = array();
        foreach ($users_ids as $user_id) {
            $users[] = $this->_em->getRepository('UlipseUserBundle:User')->find($user_id);
        }

        return $users;
    }

    /**
     * @param $address
     * @return array
     */
    public function getMatchingUsersWithFirstAddress($address)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('d')
            ->from('UlipseWorkincloserBundle:Distance', 'd')
            ->innerJoin('d.second', 'a')
            ->where('d.first = :first')
            ->andWhere('d.distance < :limit')
            ->setParameter('first', $address->getId())
            ->setParameter('limit', AddressRepository::LIMIT)
        ;

        return $qb->getQuery()->getResult()? : array();
    }

    /**
     * @param $address
     * @return array
     */
    public function getMatchingUsersWithSecondAddress($address)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('d')
            ->from('UlipseWorkincloserBundle:Distance', 'd')
            ->innerJoin('d.first', 'a')
            ->where('d.second = :second')
            ->andWhere('d.distance < :limit')
            ->setParameter('second', $address->getId())
            ->setParameter('limit', AddressRepository::LIMIT)
        ;

        return ($qb->getQuery()->getResult())? : array();
    }
}