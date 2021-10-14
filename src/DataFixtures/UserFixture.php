<?php
namespace App\DataFixtures;

use App\Entity\User;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends BaseFixture
{
    /**
    * @var UserPasswordHasherInterface
     */
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(100, 'main_users', function ($i) {
            $user = new User();
            $user->setName($this->faker->name())
            ->setEmail('user_' . $i.'@example.com')
            ->setBirthDate(new DateTime($this->faker->date()))
            ->setAddress($this->faker->address)
            ->setPostalCode($this->faker->numberBetween(1111111111, 9999999999))
            ->setPassword($this->hasher->hashPassword($user, 'test'));

            return $user;
        });

        $this->createMany(10, 'main_admins', function ($i) {
            $user = new User();
            $user->setName($this->faker->name())
            ->setEmail('admin_' . $i.'@example.com')
            ->setBirthDate(new DateTime($this->faker->date()))
            ->setAddress($this->faker->address)
            ->setRoles(['ROLE_ADMIN','ROLE_ADMIN_PRODUCT'])
            ->setPostalCode($this->faker->numberBetween(1111111111, 9999999999))
            ->setPassword($this->hasher->hashPassword($user, 'test'));

            return $user;
        });

        $manager->flush();
    }
}
