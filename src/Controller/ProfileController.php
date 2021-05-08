<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     */
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('task_list');
        }
        return $this->render('profile/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    /**
     * @Route("/update/image", name="upload_image")
     */
    public function updateProfileImage(Request $request, FlashBagInterface $flashMessage)
    {
        if ($request->files->get("image")) {
            $image = $request->files->get("image");
            $image_name = $image->getClientOriginalName();
            $image->move($this->getParameter("image_directory"), $image_name);
            $user = $this->getUser();
            $user->setImage($image_name);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $flashMessage->add("success", "Profile modifié !");
            return $this->redirectToRoute('profile');
        } else {
            return $this->redirectToRoute('profile');
        }
    }
}
