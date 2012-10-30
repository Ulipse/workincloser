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

namespace Ulipse\MessageBundle\Helper;

use FOS\MessageBundle\Sender\Sender;
use FOS\MessageBundle\Composer\Composer;
use FOS\MessageBundle\FormModel\NewThreadMessage;
use FOS\UserBundle\Model\UserInterface;

class MessageHelper
{
    protected $sender;
    protected $composer;

    public function __construct(Sender $sender, Composer $composer)
    {
        $this->sender  = $sender;
        $this->composer = $composer;
    }

    public function send(NewThreadMessage $message, UserInterface $sender)
    {
        $thread = $this->composer->newThread()
            ->setSubject($message->getSubject())
            ->addRecipient($message->getRecipient())
            ->setSender($sender)
            ->setBody($message->getBody())
            ->getMessage();

        $this->sender->send($thread);

        return $thread;
    }

    public function reply($thread, NewThreadMessage $message, UserInterface $sender)
    {
        $reply = $this->composer->reply($thread)
            ->setSender($sender)
            ->setBody($message->getBody())
            ->getMessage();

        $this->sender->send($reply);

        return $reply;
    }

}