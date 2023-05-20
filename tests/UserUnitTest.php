<?php

namespace App\Tests;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserUnitTest extends TestCase
{
    public function testIsTrue()
    {
        $user = new User();

        $user
            ->setEmail('true@test.com')
            ->setFirstname('prenom')
            ->setLastname('nom');
        
        $this->assertTrue($user->getEmail() === 'true@test.com');
        $this->assertTrue($user->getFirstname() === 'prenom');
        $this->assertTrue($user->getLastname() === 'nom');
    }

    public function testIsFalse()
    {
        $user = new User();

        $user
            ->setEmail('true@test.com')
            ->setFirstname('prenom')
            ->setLastname('nom');
        
        $this->assertFalse($user->getEmail() === 'false@test.com');
        $this->assertFalse($user->getFirstname() === 'false');
        $this->assertFalse($user->getLastname() === 'false');
    }

    public function testIsEmpty()
    {
        $user = new User();

        $this->assertEmpty($user->getEmail());
        $this->assertEmpty($user->getFirstname());
        $this->assertEmpty($user->getLastname());
    }
}
