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
    public function getThreadByParticipants(User $first, User $second)
    {
        //select * from message m inner join message_thread_metadata mt on (m.thread_id = mt.thread_id) where (user_id = 1 and participant_id = 2) or (user_id = 2 and participant_id = 1);
        //$qb = $this->_em->createQueryBuilder();
            return \array_intersect($this->findByParticipant($first), $this->findByParticipant($second));
    }
}