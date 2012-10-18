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
use FOS\UserBundle\Model\UserInterface;
use Ulipse\WorkincloserBundle\Controller\BaseController;
/**
 * @Route("/message")
 */
class DefaultController extends BaseController
{
    /**
     * @Route("/sendto/{id}", name="send_to_user")
     * @ParamConverter("second", class="UlipseUserBundle:User")
     */
    public function sendToAction(User $second)
    {
        //Todo : Secure this section, is_autorised_to_send_message_to($recipient) ?!
        $addresses = $this->getRepository('UlipseUserBundle:User')->getAddresses($second);
        $user = $this->get('security.context')->getToken()->getUser();

        if (!\is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $thread = $this->getRepository('UlipseMessageBundle:ThreadMetadata')->getThreadMetadataByParticipants($user, $second);

        if (!\is_null($thread)) {
            $form = $this->get('fos_message.reply_form.factory')->create($thread);

            return $this->get('templating')->renderResponse('UlipseMessageBundle:Default:sendto.html.twig', array(
                'second' => $second,
                'addresses' => $addresses,
                'thread' => $thread,
                'form' => $form->createView())
            );

        } else {
            $form = $this->get('fos_message.new_thread_form.factory')->create();
            return $this->get('templating')->renderResponse('UlipseMessageBundle:Default:sendto_newThread.html.twig', array(
                    'second' => $second,
                    'addresses' => $addresses,
                    'form' => $form->createView())
            );
        }
    }
}
