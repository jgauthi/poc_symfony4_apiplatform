<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
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
            ->setPassword('local')
            ->setEmail('admin@symfony.local')
            ->setFullname('Administrateur')
            ->setName('Admin')
        ;

        $this->addReference('user_admin', $user);
        $manager->persist($user);
        $manager->flush();
    }

    public function loadBlogPost(ObjectManager $manager): void
    {
        $user = $this->getReference('user_admin');
        $data = [
            ['title' => 'My blog post 1', 'content' => 'Lorem Ipsu'],
            ['title' => 'My blog post 2', 'content' => 'Dolor color...'],
            ['title' => 'My blog post 3', 'content' => 'Consectetur adipiscing elit'],
        ];

        foreach ($data as ['title' => $title, 'content' => $content]) {
            $post = new BlogPost();
            $post->setTitle($title)
                ->setSlug( preg_replace('#[^a-z0-9]{1,}#i', '-', strtolower($title)) )
                ->setAuthor($user)
                ->setContent($content)
                ->setPublished(new \DateTime('now'));

            $manager->persist($post);
        }

        $manager->flush();
    }

    public function loadComment(ObjectManager $manager): void
    {

    }
}
