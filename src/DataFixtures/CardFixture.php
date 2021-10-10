<?php
namespace App\DataFixtures;

use App\Entity\Card;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CardFixture extends BaseFixture implements DependentFixtureInterface
{
    public function loadData(ObjectManager $manager)
    {
        $this->createMany(50, 'main_cards', function ($i) {
            $card = new Card();
            $card->setCount($this->faker->numberBetween(1, 5))
            ->setUser($this->getRandomReference('main_users'))
            ->setProduct($this->getRandomReference('main_products'))
            ;

            return $card;
        });

        $manager->flush();
    }

    public function getDependencies()
    {
        return[
            UserFixture::class,
            ProductFixture::class
        ];
    }
}
