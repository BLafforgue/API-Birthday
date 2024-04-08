<?php

namespace App\DataFixtures;

use App\Factory\BirthdayFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        BirthdayFactory::createMany(
            5,
            static function(int $i) {
                return ['name' => "Name $i"];
            }
        );

        UserFactory::createMany(
            5,
            static function(int $i) {
                return ['email' => "test$i@email.com", 'password' => '$2y$13$ml.abR9goYQu/q1dFyq1deMgzTYri.2.oOA9oTcM9CvfnJszvKCMm'];
            }
        );

        $manager->flush();
    }
}
