<?php

namespace App\DataFixtures;

use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

use Faker;

class UsersFixtures extends Fixture
{
    private $passwordEncoder;
    private $slugger;

    public function __construct(UserPasswordHasherInterface $passwordEncoder,SluggerInterface $slugger) {
        $this->passwordEncoder = $passwordEncoder;
        $this->slugger = $slugger;
    }
    public function load(ObjectManager $manager): void
    {
        $admin = new Users();
        $admin->setEmail('ricki.manao@gmail.com');
        $admin->setLastname('RAJHONSON');
        $admin->setFirstname('ricki');
        $admin->setAdresse('LOT A 7');
        $admin->setZipcode('7546');
        $admin->setCity('Paris');
        $admin->setPassword(
            $this->passwordEncoder->hashPassword($admin, 'admin')
        );
        $admin->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        $faker = Faker\Factory::create("fr_FR");
        for ($usr=1; $usr <= 5 ; $usr++) { 
            $user = new Users();
            $user->setEmail($faker->email);
            $user->setLastname($faker->lastName);
            $user->setFirstname($faker->firstName);
            $user->setAdresse($faker->streetAddress);
            $user->setZipcode(str_replace(" ","",$faker->postcode));
            $user->setCity($faker->city);
            $user->setPassword(
                $this->passwordEncoder->hashPassword($user, 'user')
            );

            $manager->persist($user);
        }

        $manager->flush();
    }
}
