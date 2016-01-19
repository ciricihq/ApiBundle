<?php

namespace Cirici\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiUserController extends FOSRestController
{
    public function userAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if ($user) {
            return new JsonResponse(array(
                'id' => $user->getId(),
                'username' => $user->getUsername()
            ));
        }

        return new JsonResponse(array(
            'message' => 'User is not identified'
        ));
    }
}
