<?php

namespace App\Controller;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TaskController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route('/', methods: ['GET'])]
    public function index(): Response
    {
        $tasks = $this->em->getRepository(Task::class)->findBy([], ['id' => 'DESC']);

        return $this->render('tasks.html.twig', ['tasks' => $tasks]);
    }

    #[Route('/tasks', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $this->checkToken($request);
        $task = new Task();
        $task->title = $request->request->getString('title');
        $this->em->persist($task);
        $this->em->flush();

        return $this->redirect('/');
    }

    #[Route('/tasks/{id}/update', methods: ['POST'])]
    public function update(Task $task, Request $request): Response
    {
        $this->checkToken($request);
        $task->title = $request->request->getString('title');
        $this->em->flush();

        return $this->redirect('/');
    }

    #[Route('/tasks/{id}/toggle', methods: ['POST'])]
    public function toggle(Task $task, Request $request): Response
    {
        $this->checkToken($request);
        $task->done = !$task->done;
        $this->em->flush();

        return $this->redirect('/');
    }

    #[Route('/tasks/{id}/delete', methods: ['POST'])]
    public function delete(Task $task, Request $request): Response
    {
        $this->checkToken($request);
        $this->em->remove($task);
        $this->em->flush();

        return $this->redirect('/');
    }

    private function checkToken(Request $request): void
    {
        if (!$this->isCsrfTokenValid('tasks', $request->request->getString('_token'))) {
            throw $this->createAccessDeniedException();
        }
    }
}
