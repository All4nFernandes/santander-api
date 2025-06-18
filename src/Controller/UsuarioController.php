<?php

namespace App\Controller;

use App\Dto\UsuarioDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/api")]
final class UsuarioController extends AbstractController
{
    #[Route('/usuarios', name: 'usuarios_criar', methods: ['POST'])]
     public function Criar(
        #[MapRequestPayload(acceptFormart:'json')]
        UsuarioDto $usuarioDto
        ):JsonResponse{
            dd($usuarioDto);
        }
}


