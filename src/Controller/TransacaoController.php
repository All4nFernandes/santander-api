<?php

namespace App\Controller;

use App\Dto\TransacaoDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class TransacaoController extends AbstractController
{
    #[Route('/transacao', name: 'novaTrasacao', methods: ['GET'])]
    public function transacao(
        TransacaoDto $transacaoDto

    ): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/TransacaoController.php',
        ]);
    }
}
