<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixture extends BaseFixture
{
    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(50, 'main_products', function ($i)  {
            $product = new Product();
            $product->setName($this->faker->company. ' '.$this->faker->word())
            ->setVisit($this->faker->numberBetween(10,400))
            ->setDescription($this->faker->realText(500))
            ->setImageId($this->faker->numberBetween(1,999))
            ->setPrice($this->faker->numberBetween(220,99999)*$this->faker->randomElement([10,100,1000]));

            return $product;
        });
        
        $manager->flush();
    }
}
