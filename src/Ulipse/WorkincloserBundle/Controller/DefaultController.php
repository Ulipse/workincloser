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

namespace Ulipse\WorkincloserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

use Symfony\Component\HttpFoundation\Response,
    Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\UserInterface;
use Ulipse\WorkincloserBundle\Entity\Address,
    Ulipse\WorkincloserBundle\Entity\Addresses,
    Ulipse\WorkincloserBundle\Event\DistanceEvent;

class DefaultController extends BaseController
{
    /**
     * @Route("/privacy", name="privacy")
     * @Cache(expires="tomorrow")
     */
    public function privacyAction()
    {
        return $this->render("UlipseWorkincloserBundle:Default:privacy.html.twig");
    }

    /**
     * @Route("/about", name="about")
     * @Cache(expires="tomorrow")
     */
    public function aboutAction()
    {
        return $this->render("UlipseWorkincloserBundle:Default:about.html.twig");
    }

    /**
     * @Route("/help", name="help")
     * @Cache(expires="tomorrow")
     */
    public function helpAction()
    {
        return $this->render("UlipseWorkincloserBundle:Default:help.html.twig");
    }

    /**
     * @Route("/conditions", name="conditions")
     * @Cache(expires="tomorrow")
     */
    public function conditionsAction()
    {
        return $this->render("UlipseWorkincloserBundle:Default:conditions.html.twig");
    }

    /**
     * @Route("/", name="index")
     * @Template()
     */
    public function indexAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        if (!\is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        $addresses = new ArrayCollection();
        $entities = $this->getRepository('UlipseWorkincloserBundle:Address')->findByUser($user);
        if (!$entities) {
            foreach ($this->getParameter('address.type') as $type) {
                $address = new Address();
                $addresses[$type] = $address->setUser($user)->setType($type);
            }
        } else {
            foreach ($entities as $entity) {
                $addresses[$entity->getType()] = $entity;
            }
        }
        $form = $this->createForm(new \Ulipse\WorkincloserBundle\Form\Type\AddressesType(), new Addresses($addresses));

        if ($this->get('request')->isXmlHttpRequest()) {
            $form->bind($this->get('request'));
            if ($form->isValid()) {
                $data = $form->get('addresses')->getViewData();
                foreach ($data as $address) {
                    $this->getEntityManager()->persist($address);
                }
                $this->getEntityManager()->flush();

                $this->get('event_dispatcher')->dispatch('ulipseworkincloser_bundle.address.setting', new DistanceEvent($data, $user));

                return new Response(json_encode(array('success' => true, 'message' => 'Okey ;)')));
            } else {
                return new Response(json_encode(array('success' => false, 'message' => 'Invalide :(')));
            }
        }

        return array('form' => $form->createView());
    }


    /**
     * @Route("/corresponding", name="corresponding")
     * @Template()
     */
    public function correspondingAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        if (!\is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $matching = $this->getRepository('UlipseWorkincloserBundle:Address')->getMatchingUsersWith($user);

        return $this->render("UlipseWorkincloserBundle:Default:matching.html.twig", array('matching' => $matching));
    }
}