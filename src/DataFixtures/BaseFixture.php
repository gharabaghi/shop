<?php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

abstract class BaseFixture extends Fixture
{
    /** @var ObjectManager */
    private $manager;

    /**@var Generator */
    protected $faker;

    private $referencesIndex = [];

    abstract protected function loadData(ObjectManager $em);

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $this->faker = Factory::create();

        $this->loadData($manager);
    }

    protected function createMany(int $count, string $groupName, callable $factory)
    {
        for ($i = 0; $i < $count; $i++) {
            $entity = $factory($i);
            if (null === $entity) {
                throw new \LogicException('Did you forget to return the entity object from your callback to BaseFixture::createMany()?');
            }
            $this->manager->persist($entity);
            // store for usage later as groupName_#COUNT#
            $this->addReference(sprintf('%s_%d', $groupName, $i), $entity);
        }
    }

    protected function getRandomReference(string $groupName)
    {
        if (!isset($this->referencesIndex[$groupName])) {
            $this->referencesIndex[$groupName] = [];

            foreach ($this->referenceRepository->getReferences() as $key => $value) {
                if (strpos($key, $groupName . '_') === 0) {
                    $this->referencesIndex[$groupName][] = $key;
                }
            }
        }
        if (empty($this->referencesIndex[$groupName])) {
            throw new \Exception(sprintf('Cannot find any reference for class %s', $groupName));
        }

        $randomReferenceKey = $this->faker->randomElement($this->referencesIndex[$groupName]);

        return $this->getReference($randomReferenceKey);
    }

    protected function getRandomReferences(string $groupName, int $count)
    {
        $references = [];
        while (count($references) < $count) {
            $references[] = $this->getRandomReference($groupName);
        }
        return $references;
    }
}