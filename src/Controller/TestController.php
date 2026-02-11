<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\DisciplineRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function index(UserRepository $userRepository, DisciplineRepository $disciplineRepository): Response
    {
        $users = $userRepository->findAll();
        $disciplines = $disciplineRepository->findAll();
        
        return $this->json([
            'users' => count($users),
            'user_emails' => array_map(fn($u) => $u->getEmail(), $users),
            'disciplines' => count($disciplines),
            'discipline_names' => array_map(fn($d) => $d->getName(), $disciplines),
        ]);
    }
}