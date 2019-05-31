<?php
namespace App\DataFixtures;

use App\Entity\User;
use App\Security\TokenGenerator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    const USERS =
    [
        'superadmin'    => ['enabled' => true, 'roles' => [User::ROLE_SUPERADMIN]],
        'admin'         => ['enabled' => false, 'roles' => [User::ROLE_ADMIN]],
        'editor'        => ['enabled' => true, 'roles' => [User::ROLE_EDITOR]],
        'writer'        => ['enabled' => true, 'roles' => [User::ROLE_WRITER]],
        'commentator'   => ['enabled' => true, 'roles' => [User::ROLE_COMMENTATOR]],
    ];
    const PASSWORD = 'local';

    private $faker;
    private $passwordEncode;
    private $tokenGenerator;

    /**
     * UserFixtures constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param TokenGenerator $tokenGenerator
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder, TokenGenerator $tokenGenerator)
    {
        $this->faker = \Faker\Factory::create();
        $this->passwordEncode = $passwordEncoder;
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * @param ObjectManager $manager
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        foreach (self::USERS as $userName => ['enabled' => $enabled, 'roles' => $roles]) {
            $user = new User();
            $user->setUsername($userName)
                ->setEmail("{$userName}@symfony.local")
                ->setFullname($this->faker->firstName)
                ->setName($this->faker->lastName)
                ->setRoles($roles)
                ->setEnabled($enabled)
            ;
            $user->setPassword($this->passwordEncode->encodePassword($user, self::PASSWORD));

            if (!$enabled) {
                $user->setConfirmationToken( $this->tokenGenerator->getRandomSecureToken());
            }

            $this->addReference('user_'. $userName, $user);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
