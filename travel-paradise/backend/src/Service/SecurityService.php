<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Visit;
use Symfony\Component\Security\Core\Security;

class SecurityService
{
    public function __construct(
        private Security $security
    ) {}

    public function canAccessVisit(Visit $visit): bool
    {
        $user = $this->security->getUser();
        
        if (!$user instanceof User) {
            return false;
        }

        // Admin can access everything
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        // Guide can access their own visits
        if (in_array('ROLE_GUIDE', $user->getRoles()) && $visit->getGuide() === $user) {
            return true;
        }

        // Tourist can access visits they are part of
        if (in_array('ROLE_TOURIST', $user->getRoles())) {
            foreach ($visit->getTourists() as $tourist) {
                if ($tourist->getUser() === $user) {
                    return true;
                }
            }
        }

        return false;
    }

    public function canModifyVisit(Visit $visit): bool
    {
        $user = $this->security->getUser();
        
        if (!$user instanceof User) {
            return false;
        }

        // Admin can modify everything
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        // Guide can modify their own visits
        if (in_array('ROLE_GUIDE', $user->getRoles()) && $visit->getGuide() === $user) {
            return true;
        }

        return false;
    }

    public function canDeleteVisit(Visit $visit): bool
    {
        $user = $this->security->getUser();
        
        if (!$user instanceof User) {
            return false;
        }

        // Only admin can delete visits
        return in_array('ROLE_ADMIN', $user->getRoles());
    }

    public function canAddTourist(Visit $visit): bool
    {
        $user = $this->security->getUser();
        
        if (!$user instanceof User) {
            return false;
        }

        // Admin and guide can add tourists
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        if (in_array('ROLE_GUIDE', $user->getRoles()) && $visit->getGuide() === $user) {
            return true;
        }

        return false;
    }
} 