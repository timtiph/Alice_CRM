<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Contact;
use libphonenumber\PhoneNumberUtil;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ContactFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $phoneNumberUtil = PhoneNumberUtil::getInstance();
        $phoneNumber = $phoneNumberUtil->parse('+33658745896', 'FR');

        $user = $this->getReference('user');

        
        // $product = new Product();
        $contact = new Contact();
        $contact->setFirstname('Elena')
                ->setLastname('MORAVI')
                ->setEmail('elena.m@gmail.com')
                ->setPhone($phoneNumber)
                ->setPosition('secretaire')
                ->setSlug('elena-moravi')
                ->setIsMain('0')
                ->setUser($user);

        // $manager->persist($product);

        $manager->persist($contact);
        $manager->flush();
    }
}
