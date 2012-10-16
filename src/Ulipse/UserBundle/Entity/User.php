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

namespace Ulipse\UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use FOS\MessageBundle\Model\ParticipantInterface as ParticipantInterface;
use Doctrine\ORM\Mapping as ORM;
/**
 * Ulipse\UserBundle\Entity\User
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Ulipse\UserBundle\Repository\UserRepository")
 */
class User extends BaseUser implements ParticipantInterface
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $phone;
     *
     * @ORM\Column(name="phone", type="string", nullable=true)
     */
    private $phone;

    /**
     * @var string $firstname;
     *
     * @ORM\Column(name="firstname", type="string", nullable=true)
     */
    private $firstname;

    /**
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }


    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    public function setFirstName($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getFirstName()
    {
        return $this->firstname;
    }

    public function __construct()
    {
        parent::__construct();
    }

}