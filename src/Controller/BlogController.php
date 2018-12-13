<?php
namespace App\Controller;

use App\Entity\BlogPost;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/blog")
*/
class BlogController extends AbstractController
{
    private const POSTS = [
        [ 'id' => 1, 'title' => 'title 1', 'slug' => 'title1' ],
        [ 'id' => 2, 'title' => 'title 2', 'slug' => 'title2' ],
        [ 'id' => 3, 'title' => 'title 3', 'slug' => 'title3' ],
    ];

    /**
     * @Route("/", name="blog_list", defaults={"page": 5}, requirements={"page"="\d+"})
    */
    public function list(Request $request, $page = 1)
    {
        $limit = $request->get('limit', 10);

        return $this->json(
            [
                'page'  =>  $page,
                'limit' => $limit,
                'data'  =>  array_map(function($item){
                    return $this->generateUrl('blog_by_slug', ['slug' => $item['slug']]);
                }, self::POSTS),
            ]
        );
    }

    /**
     * @Route("/post/{$id}", name="blog_by_id", requirements={"id"="\d+"})
    */
    public function post($id)
    {
        return $this->json(
            self::POSTS[array_search($id, array_column(self::POSTS, 'id'))]
        );
    }

    /**
     * @Route("/post/{slug}", name="blog_by_slug")
    */
    public function postBySlug($slug)
    {
        return $this->json(
            self::POSTS[array_search($slug, array_column(self::POSTS, 'slug'))]
        );
    }

    /**
     * @Route("/add", name="blog_add", methods={"POST"})
    */
    public function add(Request $request)
    {
        $serializer = $this->get('serializer');
        $em = $this->getDoctrine()->getManager();

        $blogPost = $serializer->deserialize($request->getContent(), BlogPost::class, 'json');
        $em->persist($blogPost);
        $em->flush();

        return $this->json($blogPost);
    }
}
