<?php
namespace App\DataFixtures;

use App\Entity\{BlogPost, Comment, User};
use App\Security\TokenGenerator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    const NB_FIXTURE = 10;
    private $faker;

    public function __construct()
    {
        $this->faker = \Faker\Factory::create();
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $userRolesSupport = [
            User::ROLE_SUPERADMIN,
            User::ROLE_ADMIN,
            User::ROLE_WRITER,
            User::ROLE_COMMENTATOR,
        ];

        for($i = 0; $i < BlogPostFixtures::NB_FIXTURE; $i++) {
            for($j = 0; $j < rand(1, self::NB_FIXTURE); $j++) {
                $comment = new Comment();
                $comment->setAuthor($this->getRandomUser($userRolesSupport))
                    ->setContent($this->faker->realText())
                    ->setPublished($this->faker->dateTimeThisYear)
                    ->setBlogPost($this->getReference("blog_post_$i"))
                ;

                $manager->persist($comment);
            }
        }

        $manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    public function getDependencies(): array
    {
        return [
            BlogPostFixtures::class,
            UserFixtures::class,
        ];
    }

    /**
     * @param array $supportRoles
     * @return User
     */
    private function getRandomUser($supportRoles = []): User
    {
        $randomUsername = array_rand(UserFixtures::USERS);

        if (!empty($supportRoles) && !count(array_intersect(UserFixtures::USERS[$randomUsername]['roles'], $supportRoles))) {
            return $this->getRandomUser($supportRoles);
        }

        /** @var User $user */
        $user = $this->getReference('user_' . $randomUsername);
        return $user;
    }
}
