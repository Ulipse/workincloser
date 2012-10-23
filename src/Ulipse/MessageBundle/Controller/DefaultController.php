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

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


use FOS\UserBundle\Model\UserInterface;
use Ulipse\WorkincloserBundle\Controller\BaseController;
use Ulipse\UserBundle\Entity\User;

use FOS\MessageBundle\FormModel\NewThreadMessage;


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
        $first = $this->get('security.context')->getToken()->getUser();

        if (!\is_object($first) || !$first instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        
        if (!$this->getRepository('UlipseWorkincloserBundle:Address')->areCompatible($first, $second)) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $thread = $this->getRepository('UlipseMessageBundle:ThreadMetadata')->getThreadMetadataByParticipants($first, $second);

        if ($thread) {
            $form = $this->get('fos_message.reply_form.factory')->create($thread);

            return $this->get('templating')->renderResponse('UlipseMessageBundle:Default:sendto.html.twig', array(
                'second'    => $second,
                'addresses' => $this->getRepository('UlipseUserBundle:User')->getAddresses($second),
                'thread'    => $thread,
                'form'      => $form->createView())
            );
        } else {
            $form = $this->get('fos_message.new_thread_form.factory')->create();

            return $this->get('templating')->renderResponse('UlipseMessageBundle:Default:sendto_newThread.html.twig', array(
                    'second'    => $second,
                    'addresses' => $this->getRepository('UlipseUserBundle:User')->getAddresses($second),
                    'form'      => $form->createView())
            );
        }
    }

    /**
     * @Route("/sendto", name="ulipse_message_thread_new")
     */
    public function newThreadAction()
    {
        //TODO : refactor this action using handler && add responses
        $request = $this->get('request');
        $form = $this->get('fos_message.new_thread_form.factory')->create();
        $message = new \FOS\MessageBundle\FormModel\NewThreadMessage();
        $user = $this->get('security.context')->getToken()->getUser();

        if (!\is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        
        if ('POST' == $request->getMethod()) {
            $form->bind($request);

            
            if ($form->isValid()) {
                $message = $form->getData();
                if (!$message instanceof NewThreadMessage) {
                   throw new \InvalidArgumentException(sprintf('Message must be a NewThreadMessage instance, "%s" given', get_class($message)));
                }
                $first = $user;
                $second = $message->getRecipient();
                
                $thread = $this->getRepository('UlipseMessageBundle:ThreadMetadata')->getThreadMetadataByParticipants($first, $second);

                if (!$thread) {
                    $thread = $this->get('fos_message.composer')->newThread()
                          ->setSubject($message->getSubject())
                          ->addRecipient($message->getRecipient())
                          ->setSender($user)
                          ->setBody($message->getBody())
                          ->getMessage();
                   
                } else {
                    $thread = $this->get('fos_message.composer')->reply($thread)
                                   ->setSender($user)
                                   ->setBody($message->getBody())
                                   ->getMessage();
                }
                $this->get('fos_message.sender')->send($thread);
                
                return new RedirectResponse($this->get('router')->generate('fos_message_thread_view', array(
                'threadId' => $thread->getThread()->getId()
            )));
            }
        }
    }
}
