<?php

namespace App\DataFixtures;

use App\Entity\{BlogPost, Comment, User};
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncode;
    private $faker;
    private const USERS =
    [
        'superadmin'    => ['roles' => [User::ROLE_SUPERADMIN]],
        'admin'         => ['roles' => [User::ROLE_ADMIN]],
        'editor'        => ['roles' => [User::ROLE_EDITOR]],
        'writer'        => ['roles' => [User::ROLE_WRITER]],
        'commentator'   => ['roles' => [User::ROLE_COMMENTATOR]],
    ];
    private const PASSWORD = 'local';

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncode = $passwordEncoder;
        $this->faker = \Faker\Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadUser($manager);
        $this->loadBlogPost($manager);
        $this->loadComment($manager);
    }

    public function loadUser(ObjectManager $manager): void
    {
        foreach (self::USERS as $userName => ['roles' => $roles]) {
            $user = new User();
            $user->setUsername($userName)
                ->setEmail("{$userName}@symfony.local")
                ->setFullname($this->faker->firstName)
                ->setName($this->faker->lastName)
                ->setRoles($roles)
            ;
            $user->setPassword($this->passwordEncode->encodePassword($user, self::PASSWORD));

            $this->addReference('user_'. $userName, $user);
            $manager->persist($user);
        }

        $manager->flush();
    }

    public function loadBlogPost(ObjectManager $manager): void
    {
        $userRolesSupport = [
            User::ROLE_SUPERADMIN,
            User::ROLE_ADMIN,
            User::ROLE_WRITER,
        ];

        for($i = 0; $i < 100; $i++) {
            $post = new BlogPost();
            $post->setTitle($this->faker->realText(30))
                ->setSlug( $this->faker->slug )
                ->setAuthor($this->getRandomUser($userRolesSupport))
                ->setContent($this->faker->realText())
                ->setPublished($this->faker->dateTimeThisYear);

            $this->setReference("blog_post_$i", $post);

            $manager->persist($post);
        }

        $manager->flush();
    }

    public function loadComment(ObjectManager $manager): void
    {
        $userRolesSupport = [
            User::ROLE_SUPERADMIN,
            User::ROLE_ADMIN,
            User::ROLE_WRITER,
            User::ROLE_COMMENTATOR,
        ];

        for($i = 0; $i < 100; $i++) {
            for($j = 0; $j < rand(1,10); $j++) {
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

    private function getRandomUser($supportRoles = []): User
    {
        $randomUsername = array_rand(self::USERS);

        if (!empty($supportRoles) && !count(array_intersect(self::USERS[$randomUsername]['roles'], $supportRoles))) {
            return $this->getRandomUser($supportRoles);
        }

        $user = $this->getReference('user_' . $randomUsername);
        return $user;
    }
}
