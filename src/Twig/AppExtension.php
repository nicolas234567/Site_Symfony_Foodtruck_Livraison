<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Symfony\Component\Security\Core\Security;
use Twig\Extension\GlobalsInterface;

class AppExtension extends AbstractExtension implements GlobalsInterface
{
    private Security $security;
    private bool $demoMode = true;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function getGlobals(): array
    {
        $user = $this->security->getUser();

        // ⚡ Mode démo : toujours exposer le rôle admin si demoMode activé
        $roles = $user ? $user->getRoles() : [];
        if ($this->demoMode && empty($roles)) {
            $roles[] = 'ROLE_ADMIN';
        }

        return [
            'demoMode' => $this->demoMode,
            'userRoles' => $roles,
        ];
    }
}
