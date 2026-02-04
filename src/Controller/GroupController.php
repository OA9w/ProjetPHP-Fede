<?php

namespace App\Controller;

use App\Entity\Group;
use App\Repository\GroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/groups')]
class GroupController extends AbstractController
{
    #[Route('', name: 'group_index', methods: ['GET'])]
    public function index(GroupRepository $repo): Response
    {
        $rows = $repo->findAllWithCount();

        return $this->render('group/index.html.twig', [
            'rows' => $rows,
        ]);
    }

    #[Route('/{id}', name: 'group_show', methods: ['GET'])]
    public function show(Group $group): Response
    {
        return $this->render('group/show.html.twig', [
            'group' => $group,
        ]);
    }
}
