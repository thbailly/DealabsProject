<?php

namespace App\DataFixtures;

use App\Entity\ApiToken;
use App\Entity\Comment;
use App\Entity\Group;
use App\Entity\User;
use App\Entity\Plan;
use App\Entity\PromoCode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        $user = $this->createTestUser($manager);
        $userWithSavedDeals = $this->createUserWithSavedDeals($manager);
        $this->createAdmin($manager);
        $this->loadWeeklyPlans($faker, $manager, $user);
        for ($i = 0; $i < 15; $i++) {
            $plan = new Plan();
            $plan->setTitle($faker->name);
            $plan->setPrice($faker->randomNumber());
            $plan->setNormalPrice($faker->randomNumber());
            $plan->setDescription($faker->text);
            $plan->setEndDate($faker->dateTime);
            $plan->setStartDate($faker->dateTime);
            $plan->setLink($faker->url);
            $plan->setRateValue($faker->numberBetween(50, 200));

            $user = new User();
            $user->setEmail($faker->email);
            $user->setUsername($faker->name);
            $user->setPassword($this->passwordEncoder->encodePassword($user, 'test'));
            $user->setRoles(['ROLE_USER']);

            $group = new Group();
            $group->setName($faker->name);

            for ($y = 0; $y < $faker->numberBetween(2, 40); $y++) {
                $com = new Comment();
                $com->setText($faker->text);
                $com->setDate($faker->dateTime);
                $com->setAuthor($user);
                $plan->addComments($com);
            }
            $group->addDeal($plan);

            $promocode = new PromoCode();
            $promocode->setTitle($faker->name);
            $promocode->setDescription($faker->text);
            $promocode->setEndDate($faker->dateTime);
            $promocode->setStartDate($faker->dateTime);
            $promocode->setLink($faker->url);
            $promocode->setRateValue($faker->numberBetween(50, 200));
            $promocode->setValue($faker->randomDigit);
            $promocode->setTypeCode($faker->word);
            $promocode->setCode($faker->word);
            $group->addDeal($promocode);
            for ($y = 0; $y < $faker->numberBetween(3, 15); $y++) {
                $comCode = new Comment();
                $comCode->setText($faker->text);
                $comCode->setDate($faker->dateTime);
                $comCode->setAuthor($user);
                $promocode->addComments($comCode);
            }

            $plan->setAuthor($user);
            $promocode->setAuthor($user);

            $userWithSavedDeals->addDeal($plan);

            $manager->persist($group);
            $manager->persist($plan);
            $manager->persist($promocode);
            $manager->persist($user);
        }
        $manager->persist($userWithSavedDeals);
        $manager->flush();
    }

    public function loadWeeklyPlans($faker, $manager, $user)
    {
        for ($i = 0; $i < 20; $i++) {
            $plan = new Plan();
            $plan->setTitle($faker->name);
            $plan->setPrice($faker->randomNumber());
            $plan->setNormalPrice($faker->randomNumber());
            $plan->setDescription($faker->text);
            $plan->setEndDate($faker->dateTime);
            $plan->setStartDate(new \DateTime());
            $plan->setLink($faker->url);
            $plan->setRateValue($faker->numberBetween(50, 200));
            $group = new Group();
            $group->setName($faker->name);
            for ($y = 0; $y < $faker->numberBetween(1, 12); $y++) {
                $com = new Comment();
                $com->setText($faker->text);
                $com->setDate($faker->dateTime);
                $com->setAuthor($user);
                $plan->addComments($com);
            }
            $group->addDeal($plan);
            $plan->setAuthor($user);
            $manager->persist($plan);
            $manager->persist($group);
        }
    }

    public function createTestUser($manager)
    {
        $user = new User();
        $user->setEmail('test@test.com');
        $user->setUsername('test');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'test'));
        $user->setRoles(['ROLE_USER']);
        $manager->persist($user);
        return $user;
    }

    public function createAdmin($manager)
    {
        $user = new User();
        $user->setEmail('admin@admin.com');
        $user->setUsername('admin');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'admin'));
        $user->setRoles(['ROLE_ADMIN']);
        $apiToken = new ApiToken($user);
        $manager->persist($user);
        $manager->persist($apiToken);
        return $user;
    }

    public function createUserWithSavedDeals($manager)
    {
        $userWithSavedDeals = new User();
        $userWithSavedDeals->setEmail('savedDeals@savedDeals.com');
        $userWithSavedDeals->setUsername('savedDeals');
        $userWithSavedDeals->setPassword($this->passwordEncoder->encodePassword($userWithSavedDeals, 'test'));
        $userWithSavedDeals->setRoles(['ROLE_USER']);
        $manager->persist($userWithSavedDeals);
        return $userWithSavedDeals;
    }
}
