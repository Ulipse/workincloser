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

namespace Ulipse\WorkincloserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ulipse\WorkincloserBundle\Entity\Distance
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Ulipse\WorkincloserBundle\Repository\DistanceRepository")
 */
class Distance
{
    const EARTH_RADIUS = 6378;

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var float $distance
     *
     * @ORM\Column(name="distance", type="float")
     */
    private $distance;

    /**
     * @var \DateTime $editedAt
     *
     * @ORM\Column(name="editedAt", type="datetime")
     */
    private $editedAt;

    /**
     * @var \Ulipse\WorkincloserBundle\Entity\Address
     *
     * @ORM\ManyToOne(targetEntity="Ulipse\WorkincloserBundle\Entity\Address")
     * @ORM\JoinColumn(nullable=false, name="first_address")
     */
    private $first;

    /**
     * @var \Ulipse\WorkincloserBundle\Entity\Address
     *
     * @ORM\ManyToOne(targetEntity="Ulipse\WorkincloserBundle\Entity\Address")
     * @ORM\JoinColumn(nullable=false, name="second_address")
     */
    private $second;


    public function __construct(\Ulipse\WorkincloserBundle\Entity\Address $first = null, \Ulipse\WorkincloserBundle\Entity\Address $second = null)
    {
        $this->first = $first;
        $this->second = $second;
    }

    /**
     * Get delta longitude
     *
     * @return float
     */
    public function getDeltaLng()
    {
        return $this->second->getLongitude() - $this->first->getLongitude();
    }

    /**
     * Get delta latitude
     *
     * @return float
     */
    public function getDeltaLat()
    {
        return $this->second->getLatitude() - $this->first->getLatitude();
    }

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
     * Set distance
     *
     * @return Distance
     */
    public function setDistance()
    {
        $a = sin(deg2rad($this->getDeltaLat()/2)) * sin(deg2rad($this->getDeltaLat()/2)) + cos(deg2rad($this->first->getLatitude())) * cos(deg2rad($this->second->getLatitude())) * sin(deg2rad($this->getDeltaLng()/2)) * sin(deg2rad($this->getDeltaLng()/2)) ;
        $c = asin(min(1, sqrt($a)));
        $d = $c * Distance::EARTH_RADIUS * 2;

        $this->distance = $d + sqrt($d);
    
        return $this;
    }

    /**
     * Get distance
     *
     * @return float 
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * Set editedAt
     *
     * @param \DateTime $editedAt
     * @return Distance
     */
    public function setEditedAt($editedAt)
    {
        $this->editedAt = $editedAt;
    
        return $this;
    }

    /**
     * Get editedAt
     *
     * @return \DateTime 
     */
    public function getEditedAt()
    {
        return $this->editedAt;
    }

    /**
     * Set First
     *
     * @param \Ulipse\WorkincloserBundle\Entity\Address $first
     * @return Distance
     */
    public function setFirst(\Ulipse\WorkincloserBundle\Entity\Address $first)
    {
        $this->first = $first;

        return $this;
    }

    /**
     * Get First
     *
     * @return \Ulipse\WorkincloserBundle\Entity\Address
     */
    public function getFirst()
    {
        return $this->first;
    }

    /**
     * Set Second
     *
     * @param \Ulipse\WorkincloserBundle\Entity\Address $second
     * @return Distance
     */
    public function setSecond(\Ulipse\WorkincloserBundle\Entity\Address $second)
    {
        $this->second = $second;

        return $this;
    }

    /**
     * Get Second
     *
     * @return \Ulipse\WorkincloserBundle\Entity\Address
     */
    public function getSecond()
    {
        return $this->second;
    }

}
