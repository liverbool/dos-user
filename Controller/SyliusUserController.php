<?php

namespace Dos\UserBundle\Controller;

use Sylius\Bundle\UserBundle\Controller\UserController;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Security\TokenProviderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SyliusUserController extends UserController
{
    /**
     * @param $response
     *
     * @return RedirectResponse
     */
    private function checkRedirection($response)
    {
        if ($response instanceof RedirectResponse) {
            return $response->setTargetUrl($this->generateUrl($this->config->getRedirectReferer()));
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    protected function generateResetPasswordRequestUrl($token)
    {
        if (is_numeric($token)) {
            return $this->generateUrl($this->config->getRedirectReferer());
        }

        return $this->generateUrl($this->config->getRedirectReferer());
    }

    /**
     * {@inheritdoc}
     */
    protected function handleResetPasswordRequest(TokenProviderInterface $generator, UserInterface $user, $senderEvent)
    {
        return $this->checkRedirection(parent::handleResetPasswordRequest($generator, $user, $senderEvent));
    }

    /**
     * {@inheritdoc}
     */
    protected function handleResetPassword(UserInterface $user, $newPassword)
    {
        return $this->checkRedirection(parent::handleResetPassword($user, $newPassword));
    }

    /**
     * {@inheritdoc}
     */
    protected function handleChangePassword(UserInterface $user, $newPassword)
    {
        return $this->checkRedirection(parent::handleChangePassword($user, $newPassword));
    }
}
