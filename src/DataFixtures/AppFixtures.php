<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    // ...
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setFirstname('tiph')
        ->setLastname('ADMIN')
        ->setEmail('po@po.com')
        ->setRoles(["ROLE_ADMIN"])
        ->setIsVerified('1')
        ->setSlug('tiph-admin');

        $password = $this->hasher->hashPassword($user, 'Pa$$w0rd!');
        $user->setPassword($password);

        $manager->persist($user);
        $manager->flush();

        $this->addReference('user', $user);

    }
}


