<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
        return $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername('ikolemaster');
        $user->setEmail('a@a.com');
        $user->setFullName('Adekola Adeniyi');
        $user->setPhone('9999999999');
        $user->setRoles(['ROLE_USER']);
        $user->setCity('manchester');
        $user->setState('England');
        $user->setZipcode('MNC0093');
        $user->setCountry('UK');
        $user->setStatus('active');
        $user->setProfileImage('https://images.unsplash.com/photo-1570295999919-56ceb5ecca61?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=880&q=80');
        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            'password1'
        ));
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setIsVerified(true);
        $manager->persist($user);

        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}