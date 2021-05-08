<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class TaskController extends AbstractController
{
    private $taskRepository;
    private $flashMessage;

    public function __construct(
        TaskRepository $taskRepository,
        FlashBagInterface $flashMessage
    ) {
        $this->taskRepository = $taskRepository;
        $this->flashMessage = $flashMessage;
    }

    /**
     * @Route("/", name="task_list")
     */

    public function index()
    {
        $tasks = $this->taskRepository->findAll();
        return $this->render('home.html.twig', [
            "tasks" => $tasks
        ]);
    }

    /**
     * @Route("/task/{id}", name="task_show")
     */

    public function showTask($id)
    {
        $task = $this->taskRepository->find($id);
        return $this->render('show.html.twig', [
            "task" => $task
        ]);
    }

    /**
     * @Route("/create/task", name="task_create")
     */

    public function createTask(Request $request)
    {
        $task = new Task();

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $task = $form->getData();
            $user = $this->getUser();
            $task->setUser($user);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();
            $this->flashMessage->add("success", "Note ajoutée !");
            return $this->redirectToRoute('task_list');
        }

        return $this->render('add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/task/{id}", name="task_edit")
     */

    public function editTask(Task $task, Request $request)
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $task = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();
            $this->flashMessage->add("success", "Note modifiée !");
            return $this->redirectToRoute('task_list');
        }

        return $this->render('edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/task/{id}", name="task_delete")
     */

    public function deleteTask(Task $task)
    {

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($task);
        $entityManager->flush();
        $this->flashMessage->add("success", "Note supprimée !");
        return $this->redirectToRoute('task_list');
    }
}
