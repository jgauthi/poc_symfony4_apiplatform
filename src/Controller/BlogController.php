<?php
namespace App\Controller;

use App\Entity\BlogPost;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/blog")
*/
class BlogController extends AbstractController
{
    /**
     * @Route("/", name="blog_list", defaults={"page": 5}, requirements={"page"="\d+"}, methods={"GET"})
    */
    public function list(Request $request, $page = 1)
    {
        $limit = $request->get('limit', 10);
        $repository = $this->getDoctrine()->getRepository(BlogPost::class);
        $data = $repository->findAll();

        return $this->json(
            [
                'page'  =>  $page,
                'limit' => $limit,
                'data'  =>  array_map(function(BlogPost $item){
                    return $this->generateUrl('blog_by_id', ['id' => $item->getId()]);
                }, $data),
            ]
        );
    }

    /**
     * @Route("/post/{id}", name="blog_by_id", requirements={"id"="\d+"}, methods={"GET"})
    */
    public function post(int $id)
    {
        return $this->json(
            $this->getDoctrine()->getRepository(BlogPost::class)->find($id)
        );
    }

    /**
     * @Route("/post/author/{author}", name="blog_by_author", methods={"GET"})
     */
    public function postByAuthor(BlogPost $post)
    {
        return $this->json($post);
    }

    /**
     * @Route("/post/{slug}", name="blog_by_slug", methods={"GET"})
     * The below annotation is not required when $post is typehinted by BlogPost and route parameter name matches any fields on this entity
     * @ParamConverter("post", class="App:BlogPost")
     * alternative: ParamConverter("post", class="App:BlogPost", options={"mapping": {"slug": "slug"}})
    */
    public function postBySlug($post)
    {
        return $this->json($post);
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

    /**
     * @Route("/post/{id}", name="blog_delete_by_id", requirements={"id"="\d+"}, methods={"DELETE"})
     */
    public function delete(BlogPost $post)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
