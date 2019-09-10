<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\UsersType;
use App\Entity\Posts;
use App\Repository\UsersRepository;
use App\Service\DeleteRest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/users")
 */
class UsersController extends AbstractController
{   

    public function __construct(DeleteRest $deleteRest)
    {
        $this->deleteRest = $deleteRest;
    }
    /**
     * @Route("/", name="users_index", methods={"GET"})
     */
    public function index(UsersRepository $usersRepository): Response
    {
        
        return $this->render('users/index.html.twig', [
            'users' => $usersRepository->findAll(),
            "geo" => $usersRepository->findUserWithGeo(),
        ]);
    }

    /**
     * @Route("/{id}", name="users_show", methods={"GET"})
     */
    public function show(Users $user): Response
    {
        return $this->render('users/show.html.twig', [
            'user' => $user,
        ]);
    }
     /**
     * @Route("post/{id}", name="user_post_delete", methods={"DELETE"})
     */
    public function deleteUserPost(Request $request, Posts $post): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $id = $post->getId();
            $entityManager->remove($post);
            $entityManager->flush();
            // erasing on api with delete
            $this->deleteRest->delete('https://jsonplaceholder.typicode.com/posts', $id);
        }
        // redirect to user page
        return $this->redirectToRoute('users_show', ['id' => $post->getUserId()->getId()]);
    }
    
}
