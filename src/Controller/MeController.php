<?php

namespace App\Controller;

use Symfony\Bundle\SecurityBundle\Security;

class MeController
{
    public function __construct(private Security $security)
    {
    }

    public function __invoke(): ?\Symfony\Component\Security\Core\User\UserInterface
    {
        $user = $this->security->getUser();
        return $user;
    }

}