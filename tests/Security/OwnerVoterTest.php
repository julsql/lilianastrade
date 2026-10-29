<?php

namespace App\Tests\Security;

use App\Entity\Card;
use App\Entity\Deck;
use App\Entity\Merchant;
use App\Entity\MyCollection;
use App\Entity\User;
use App\Security\Voter\OwnerVoter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\NullToken;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class OwnerVoterTest extends TestCase
{
    private static int $nextId = 1;

    public function testAdminCanManageAnything(): void
    {
        $voter = $this->voter(isAdmin: true);
        $token = $this->tokenFor($this->user($this->merchant()));

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $voter->vote($token, $this->deckOf($this->merchant()), ['MANAGE']));
    }

    #[DataProvider('subjects')]
    public function testOwnerCanManageOwnSubject(callable $subjectFor): void
    {
        $merchant = $this->merchant();
        $token = $this->tokenFor($this->user($merchant));

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $this->voter()->vote($token, $subjectFor($this, $merchant), ['MANAGE']));
    }

    #[DataProvider('subjects')]
    public function testOtherMerchantCannotManageSubject(callable $subjectFor): void
    {
        $token = $this->tokenFor($this->user($this->merchant()));

        $this->assertSame(VoterInterface::ACCESS_DENIED, $this->voter()->vote($token, $subjectFor($this, $this->merchant()), ['MANAGE']));
    }

    public static function subjects(): iterable
    {
        yield 'merchant' => [fn (self $t, Merchant $m) => $m];
        yield 'deck' => [fn (self $t, Merchant $m) => $t->deckOf($m)];
        yield 'collection' => [fn (self $t, Merchant $m) => $t->collectionOf($m)];
        yield 'card' => [fn (self $t, Merchant $m) => (new Card())->setMyCollection($t->collectionOf($m))];
    }

    public function testAnonymousAndUserWithoutMerchantAreDenied(): void
    {
        $deck = $this->deckOf($this->merchant());

        $this->assertSame(VoterInterface::ACCESS_DENIED, $this->voter()->vote(new NullToken(), $deck, ['MANAGE']));
        $this->assertSame(VoterInterface::ACCESS_DENIED, $this->voter()->vote($this->tokenFor(new User()), $deck, ['MANAGE']));
    }

    public function testUnpersistedOwnerIsDenied(): void
    {
        $token = $this->tokenFor((new User())->setEmail('new@example.com')->setMerchant(new Merchant()));

        $this->assertSame(VoterInterface::ACCESS_DENIED, $this->voter()->vote($token, $this->deckOf(new Merchant()), ['MANAGE']));
    }

    public function testCardWithoutCollectionIsDenied(): void
    {
        $token = $this->tokenFor($this->user($this->merchant()));

        $this->assertSame(VoterInterface::ACCESS_DENIED, $this->voter()->vote($token, new Card(), ['MANAGE']));
    }

    public function testAbstainsOnOtherAttributesAndSubjects(): void
    {
        $token = $this->tokenFor($this->user($this->merchant()));

        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $this->voter()->vote($token, $this->deckOf($this->merchant()), ['EDIT']));
        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $this->voter()->vote($token, new \stdClass(), ['MANAGE']));
    }

    private function voter(bool $isAdmin = false): OwnerVoter
    {
        $decisionManager = $this->createStub(AccessDecisionManagerInterface::class);
        $decisionManager->method('decide')->willReturn($isAdmin);

        return new OwnerVoter($decisionManager);
    }

    private function tokenFor(User $user): UsernamePasswordToken
    {
        return new UsernamePasswordToken($user, 'main', $user->getRoles());
    }

    private function user(Merchant $merchant): User
    {
        return (new User())->setEmail('m'.$merchant->id.'@example.com')->setMerchant($merchant);
    }

    private function merchant(): Merchant
    {
        $merchant = new Merchant();
        $merchant->id = self::$nextId++;

        return $merchant;
    }

    public function deckOf(Merchant $merchant): Deck
    {
        return (new Deck())->setOwner($merchant);
    }

    public function collectionOf(Merchant $merchant): MyCollection
    {
        return (new MyCollection())->setOwner($merchant);
    }
}
