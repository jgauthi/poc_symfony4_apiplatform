<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $data = [
            ['title' => 'My blog post 1', 'author' => 'John Doe', 'content' => 'Lorem Ipsu'],
            ['title' => 'My blog post 2', 'author' => 'Jane Doe', 'content' => 'Dolor color...'],
        ];

        foreach ($data as ['title' => $title, 'author' => $author, 'content' => $content]) {
            $post = new BlogPost();
            $post->setTitle($title)
                ->setSlug( preg_replace('#[^a-z0-9]{1,}#i', '-', strtolower($title)) )
                ->setAuthor($author)
                ->setContent($content)
                ->setPublished(new \DateTime('now'));

            $manager->persist($post);
        }

        $manager->flush();
    }
}
