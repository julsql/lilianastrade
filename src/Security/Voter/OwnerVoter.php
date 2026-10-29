<?php

namespace App\Security\Voter;

use App\Entity\Card;
use App\Entity\Deck;
use App\Entity\Merchant;
use App\Entity\MyCollection;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Un admin gère tout ; un marchand ne gère que ses propres decks, collections et cartes.
 */
class OwnerVoter extends Voter
{
    public const MANAGE = 'MANAGE';

    public function __construct(private AccessDecisionManagerInterface $accessDecisionManager)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return self::MANAGE === $attribute
            && ($subject instanceof Deck || $subject instanceof MyCollection || $subject instanceof Card || $subject instanceof Merchant);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        if ($this->accessDecisionManager->decide($token, ['ROLE_ADMIN'])) {
            return true;
        }

        $user = $token->getUser();
        if (!$user instanceof User || null === $user->getMerchant()) {
            return false;
        }

        $owner = match (true) {
            $subject instanceof Merchant => $subject,
            $subject instanceof Card => $subject->getMyCollection()?->getOwner(),
            default => $subject->getOwner(),
        };

        return null !== $owner?->getId() && $owner->getId() === $user->getMerchant()->getId();
    }
}
