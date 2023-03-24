<?php

namespace App\DataFixtures;

use App\Entity\Contact;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ContactFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        $contact = new Contact();
        $contact->setFirstname('Elena')
                ->setLastname('MORAVI')
                ->setEmail('elena.m@gmail.com')
                ->setTel('0606060606')
                ->setPosition('secretaire')
                ->setIsMain('0')
                ->setUser(1);

        // $manager->persist($product);

        $manager->persist($contact);
        $manager->flush();
    }
}
