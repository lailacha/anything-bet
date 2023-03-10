<?php

namespace App\Security\Voter;

use App\Entity\BettingGroup;
use App\Entity\User;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class GroupAdminVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const SHOW = 'SHOW';
    public const DELETE = 'DELETE';

    protected function supports(string $attribute, $subject): bool
    {

        if(!in_array($attribute, [self::EDIT, self::DELETE, self::SHOW])) {
            return false;
        }


        return $subject instanceof BettingGroup;
    }

    /**
     * @param string $attribute
     * @param BettingGroup $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }
        switch ($attribute) {
            case self::DELETE:
            case self::EDIT:
            return in_array('ROLE_ADMIN', $user->getRoles()) || $subject->getAdministrators()->contains($user);
                break;
            case self::SHOW:
                return in_array('ROLE_ADMIN', $user->getRoles()) || $subject->getAdministrators()->contains($user) || $subject->getMembers()->contains($user);
                break;
        }

        return true;
    }
}
