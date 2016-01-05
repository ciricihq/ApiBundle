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

    public function resetAction($token)
    {
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->container->get('fos_user.resetting.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->container->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_SUCCESS, $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_profile_show');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            return $response;
        }

        return $this->render('FOSUserBundle:Resetting:reset.html.twig', array(
            'token' => $token,
            'form' => $form->createView(),
        ));
    }

}
