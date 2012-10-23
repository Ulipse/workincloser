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

namespace Ulipse\MessageBundle\Form\Handler;

use Symfony\Component\Form;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;

use Ulipse\MessageBundle\Entity\Message;

class ReplyMessageHandler
{
    protected $form;
    protected $request;
    protected $em;

    /**
     * @param \Symfony\Component\Form                   $form
     * @param \Doctrine\ORM\EntityManager               $em
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function __construct(Form $form, EntityManager $em, Request $request)
    {
        $this->form    = $form;
        $this->em      = $em;
        $this->request = $request;
    }

    /**
     * @return bool
     */
    public function process()
    {
        if ('POST' == $this->request->getMethod()) {
            $this->form->bind($this->request);

            if ($this->form->isValid()) {
                return $this->onSuccess($this->form->getData());
            }
        }
        
        return false;
    }

    /**
     * @param \Ulipse\MessageBundle\Entity\Message $message
     *
     * @return bool
     */
    public function onSuccess(Message $message) {
        $this->em->persist($message);
        $this->em->flush();
        
        return true;
    }
}