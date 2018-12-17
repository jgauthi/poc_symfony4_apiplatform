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
    private const USERNAMES = ['admin', 'auteur', 'some_api_user', 'user'];
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
        foreach (self::USERNAMES as $userName) {
            $user = new User();
            $user->setUsername($userName)
                ->setEmail("{$userName}@symfony.local")
                ->setFullname($this->faker->firstName)
                ->setName($this->faker->lastName)
            ;
            $user->setPassword($this->passwordEncode->encodePassword($user, self::PASSWORD));

            $this->addReference('user_'. $userName, $user);
            $manager->persist($user);
        }

        $manager->flush();
    }

    public function loadBlogPost(ObjectManager $manager): void
    {
        for($i = 0; $i < 100; $i++) {
            $post = new BlogPost();
            $post->setTitle($this->faker->realText(30))
                ->setSlug( $this->faker->slug )
                ->setAuthor($this->getRandomUser())
                ->setContent($this->faker->realText())
                ->setPublished($this->faker->dateTimeThisYear);

            $this->setReference("blog_post_$i", $post);

            $manager->persist($post);
        }

        $manager->flush();
    }

    public function loadComment(ObjectManager $manager): void
    {
        for($i = 0; $i < 100; $i++) {
            for($j = 0; $j < rand(1,10); $j++) {
                $comment = new Comment();
                $comment->setAuthor($this->getRandomUser())
                    ->setContent($this->faker->realText())
                    ->setPublished($this->faker->dateTimeThisYear)
                    ->setBlogPost($this->getReference("blog_post_$i"))
                ;

                $manager->persist($comment);
            }
        }

        $manager->flush();
    }

    private function getRandomUser(): User
    {
        $randomUsername = self::USERNAMES[mt_rand(0, count(self::USERNAMES) - 1)];
        $user = $this->getReference('user_' . $randomUsername);

        return $user;
    }
}
