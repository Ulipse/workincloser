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

namespace Ulipse\MessageBundle\Repository;

use Doctrine\ORM\EntityRepository;

use Ulipse\UserBundle\Entity\User;

class ThreadMetadataRepository extends EntityRepository
{
    /**
     * @param \Ulipse\UserBundle\Entity\User $first
     * @param \Ulipse\UserBundle\Entity\User $second
     *
     * @return null|object
     */
    public function getThreadMetadataByParticipants(User $first, User $second)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('m')
           ->from('UlipseMessageBundle:Message', 'm')
           ->innerJoin('m.metadata', 'mt')
           ->where('m.sender = :first AND mt.participant = :second')
           ->orWhere('m.sender = :second AND mt.participant = :first')
           ->setParameter('first', $first->getId())
           ->setParameter('second', $second->getId())
           ->groupBy('m.thread');
        ;

        try {
            $message = $qb->getQuery()->getOneOrNullResult();
            return ($message)? $message->getThread(): null;
        } catch (Doctrine\ORM\NonUniqueResultException $e) {
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}