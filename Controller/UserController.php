<?php

namespace Cirici\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\UserBundle\Controller\ResettingController;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\GetResponseUserEvent;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * User controller.
 *
 */
class UserController extends ResettingController
{
    /**
     * Request reset user password: submit form and send email
     *
     * @ApiDoc(
     *   resource=true,
     *   description="Request reset user password: submit form and send email",
     * )
     */
    public function sendEmailAction()
    {
        $username = $this->container->get('request')->request->get('username');

        /** @var $user UserInterface */
        $user = $this->container->get('fos_user.user_manager')
            ->findUserByUsernameOrEmail($username);

        if (null === $user) {
            return new JsonResponse(array(
                'error' => 'invalid.username',
                'username' => $username
            ), 403);
        }

        if ($user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
            return new JsonResponse(array(
                'error' => 'password.already_requested',
            ), 403);
        }

        if (null === $user->getConfirmationToken()) {
            /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
            $tokenGenerator = $this->container->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
        }

        $this->container->get('fos_user.mailer')->sendResettingEmailMessage($user);
        $user->setPasswordRequestedAt(new \DateTime());
        $this->container->get('fos_user.user_manager')->updateUser($user);

        return new JsonResponse(array(
            'message' => 'email.sent',
        ), 200);
    }

    /**
     * Tell the user to check his email provider
     */
    public function checkEmailAction()
    {
        $email = $request->query->get('email');

        if (empty($email)) {
            // the user does not come from the sendEmail action
            return new RedirectResponse($this->generateUrl('fos_user_resetting_request'));
        }

        return new JsonResponse(array(
            'email' => $email,
        ));
    }
}
