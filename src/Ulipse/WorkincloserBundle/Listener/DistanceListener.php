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

namespace Ulipse\WorkincloserBundle\Listener;

use Ulipse\WorkincloserBundle\Event\DistanceEvent;
use Ulipse\WorkincloserBundle\Entity\Distance;

class DistanceListener
{
    protected $em;
    protected $address_type;

    /**
     * @param $em
     * @param $address_type
     */
    public function __construct($em, $address_type)
    {
        $this->em = $em;
        $this->address_type = $address_type;
    }

    /**
     * @param \Ulipse\WorkincloserBundle\Event\DistanceEvent $event
     */
    public function onSetAddress(DistanceEvent $event)
    {
        foreach ($this->address_type as $type) {
            $this->setDistances($event->getAddresses(), $event->getUser(), $type);
        }
    }

    /**
     * @param $addresses
     * @param $user
     * @param $type
     */
    public function setDistances($addresses, $user, $type)
    {
        $entity = $addresses[$type];

        $addresses = $this->em->getRepository('UlipseWorkincloserBundle:Address')->getAddressesWhereNotUserAndType($user, $type);

        foreach($addresses as $address) {
            if ($entity != $address) {
                $distance = $this->em->getRepository('UlipseWorkincloserBundle:Distance')->getDistance($entity, $address);
                if (!$distance) {
                    $distance = new Distance();
                    $distance->setFirst($entity);
                    $distance->setSecond($address);
                }
                $distance->setEditedAt(new \DateTime());
                $distance->setDistance();
                $this->em->persist($distance);
            }
        }
        $this->em->flush();
    }
}