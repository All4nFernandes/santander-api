<?php

namespace App\Controller;

use App\Dto\TransacaoRealizarDto;
use App\Repository\ContaRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final class TransacaoController extends AbstractController
{
    #[Route('/transacoes', name: 'Trasacao_realizar', methods: ['POST'])]
    public function realizar(
        #[MapRequestPayload(acceptFormat: 'json')]
        TransacaoRealizarDto $entrada,
        ContaRepository $contaRepository,

        EntityManager $entityManager,

    ): JsonResponse {

        // 1. validar se a entrada tem id de origem / id de destino / valor -> se tem e > zero

        $erros = [];

        if (!$entrada->getIdUsuarioOrigem()) {
            array_push(
                $erros,
                ['message' => 'Informe a conta de origem']
            );
        }
        if (!$entrada->getIdUsuarioDestino()) {
            array_push(
                $erros,
                ['message' => 'Informe a conta de destino']
            );
        }
        if (!$entrada->getValor()) {
            array_push(
                $erros,
                ['message' => 'Informe o valor']
            );
        }
        if ((float)$entrada->getValor() <= 0) {
            array_push(
                $erros,
                ['message' => 'O valor deve ser maior que zero']
            );
        }


        // 2. validar se as constas existem

        if ($entrada->getIdUsuarioOrigem() === $entrada->getIdUsuarioDestino()) {
            array_push(
                $erros,
                ['message' => 'As contas devem ser diferentes!']
            );
        }
        // 3. validar se a origem tem saldo sulficiente

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/TransacaoController.php',
        ]);
    }
}
