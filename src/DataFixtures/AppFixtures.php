<?php

namespace App\DataFixtures;

use App\Factory\BirthdayFactory;
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

        $manager->flush();
    }
}
