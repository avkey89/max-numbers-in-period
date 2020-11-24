<?php

namespace App\DataFixtures;

use App\Entity\Visit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private array $visits;

    public function __construct()
    {
        $currentTime = time();
        $minDate = $currentTime - 3600;
        $maxDate = $currentTime + 3600;
        for ($i = 0; $i < 1000; $i++) {
            $this->visits[] = [
                'status' => mt_rand(1, 2),
                'visited_at' => $this->randomDate($minDate, $maxDate)
            ];
        }
    }

    private function randomDate($minDate, $maxDate)
    {
        $randomTimestamp = mt_rand($minDate, $maxDate);
        return (new \DateTimeImmutable())->setTimestamp($randomTimestamp);
    }

    public function load(ObjectManager $manager)
    {

        foreach ($this->visits as $visit) {
            $user = new Visit($visit['status'], $visit['visited_at']);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
