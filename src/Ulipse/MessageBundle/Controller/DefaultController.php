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

namespace Ulipse\MessageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Ulipse\UserBundle\Entity\User;
use Ulipse\WorkincloserBundle\Controller\BaseController;
/**
 * @Route("/message")
 */
class DefaultController extends BaseController
{
    /**
     * @Route("/sendto/{id}", name="send_to_user")
     * @ParamConverter("user", class="UlipseUserBundle:User")
     * @Template()
     */
    public function sendToAction(User $user)
    {
        $addresses = $this->getRepository('UlipseUserBundle:User')->getAddresses($user);

        $user2 = $this->getRepository('UlipseUserBundle:User')->find(2);
        $thread = $this->getRepository('UlipseMessageBundle:ThreadMetadata')->getThreadByParticipants($user, $user2);

        return array('user' => $user, 'addresses' => $addresses, 'thread' => $thread);
    }
}
