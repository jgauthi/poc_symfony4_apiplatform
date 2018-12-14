<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncode;
    private $faker;

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
        $user = new User();
        $user->setUsername('admin')
            ->setEmail('admin@symfony.local')
            ->setFullname('Administrateur')
            ->setName('Admin')
        ;
        $user->setPassword($this->passwordEncode->encodePassword($user, 'local'));

        $this->addReference('user_admin', $user);
        $manager->persist($user);
        $manager->flush();
    }

    public function loadBlogPost(ObjectManager $manager): void
    {
        $user = $this->getReference('user_admin');

        for($i = 0; $i < 100; $i++) {
            $post = new BlogPost();
            $post->setTitle($this->faker->realText(30))
                ->setSlug( $this->faker->slug )
                ->setAuthor($user)
                ->setContent($this->faker->realText())
                ->setPublished($this->faker->dateTimeThisYear);

            $this->setReference("blog_post_$i", $post);

            $manager->persist($post);
        }

        $manager->flush();
    }

    public function loadComment(ObjectManager $manager): void
    {
        $user = $this->getReference('user_admin');

        for($i = 0; $i < 100; $i++) {
            for($j = 0; $j < rand(1,10); $j++) {
                $comment = new Comment();
                $comment->setAuthor($user)
                    ->setContent($this->faker->realText())
                    ->setPublished($this->faker->dateTimeThisYear)
                    ->setBlogPost($this->getReference("blog_post_$i"))
                ;

                $manager->persist($comment);
            }
        }

        $manager->flush();
    }
}
