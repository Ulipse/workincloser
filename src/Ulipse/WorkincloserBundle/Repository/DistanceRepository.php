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

class DistanceRepository extends EntityRepository
{
    /**
     * @param $first
     * @param $second
     *
     * @return object|null
     */
    public function getDistance($first, $second)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('d')
           ->from('UlipseWorkincloserBundle:Distance', 'd')
           ->where('d.first = :first AND d.second = :second')
           ->orWhere('d.first = :second AND d.second = :first')
           ->setParameter('first', $first->getId())
           ->setParameter('second', $second->getId())
        ;

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (Doctrine\ORM\NonUniqueResultException $e) {
            return null;
        }
    }
}