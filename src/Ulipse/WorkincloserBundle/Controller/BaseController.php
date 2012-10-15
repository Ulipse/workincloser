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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManager;

class BaseController extends Controller
{
    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->getDoctrine()->getEntityManager();
    }

    /**
     * @param $repository
     * @return mixed
     */
    public function getRepository($repository)
    {
        if (!\is_null($repository)) {
            return $this->container->get('doctrine.orm.entity_manager')->getRepository($repository);
        }
    }

    /**
     * @param $parameter
     * @return mixed
     */
    public function getParameter($parameter)
    {
        return $this->container->getParameter($parameter);
    }

    /**
     * @param string $id
     * @return object
     * @throws \LogicException
     */
    public function get($id)
    {
        if ('entity_manger' == $id) {
            if (!$this->container->has('doctrine.orm.entity_manager')) {
                throw new \LogicException('The DoctrineBundle is not registered in your application.');
            }
            if ($this->container->has('entity_manager')) {
                return parent::get('entity_manager');
            }

            return $this->container->get('doctrine.orm.entity_manager');
        }

        return $this->container->get($id);
    }
}