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

namespace Ulipse\WorkincloserBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AddressRepositoryTest extends WebTestCase
{
    private $em;
    
    public function setUp() {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
    }
    
    public function testgetUsersMatchingWith()
    {
       $user = $this->em->getRepository('UlipseUserBundle:User')->find(1);
       
       $matching = $this->em->getRepository('UlipseWorkincloserBundle:Address')->getUsersMatchingWith($user);
       
       $user_matching = $this->em->getRepository('UlipseUserBundle:User')->find(3);
       
       $this->assertContains($user_matching, $matching);
    }
}
