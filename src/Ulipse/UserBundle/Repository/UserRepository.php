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

namespace Ulipse\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;

use Ulipse\UserBundle\Entity\User;

class UserRepository extends EntityRepository
{
    /**
     * @param \Ulipse\UserBundle\Entity\User $user
     *
     * @return array
     */
    public function getAddresses(User $user)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('a')
            ->from('UlipseWorkincloserBundle:Address', 'a')
            ->where('a.user != :user')
            ->setParameter('user', $user->getId())
        ;

        return $qb->getQuery()->getArrayResult();
    }
}