<?php

namespace App\Controller;

use App\Dto\TransacaoRealizarDto;
use App\Entity\Transacao;
use App\Repository\ContaRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/api")]
final class TransacaoController extends AbstractController
{
    #[Route('/transacoes', name: 'Trasacao_realizar', methods: ['POST'])]
    public function realizar(
        #[MapRequestPayload(acceptFormat: 'json')]
        TransacaoRealizarDto $entrada,
        ContaRepository $contaRepository,

        EntityManagerInterface $entityManager

    ): Response {

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
        if (count($erros) > 0) {
            return $this->json($erros, 422);
        }


        // 2. validar se as constas existem

        if ($entrada->getIdUsuarioOrigem() === $entrada->getIdUsuarioDestino()) {
            array_push(
                $erros,
                ['message' => 'As contas devem ser diferentes!'],
            );
        }

        $contaOrigem = $contaRepository->findByUsuarioId($entrada->getIdUsuarioOrigem());
        if (!$contaOrigem) {
            return $this->json([
                'message' => 'Conta de origem não encontrada!'
            ], 404);
        }

        $contaDestino = $contaRepository->findByUsuarioId($entrada->getIdUsuarioDestino());
        if (!$contaDestino) {
            return $this->json([
                'message' => 'Conta de destino não encontrada!'
            ], 404);
        }


        // 3. validar se a conta origem tem saldo sulficiente

        if ((float)$contaOrigem->getSaldo() <  (float)$entrada->getValor()) {

            return $this->json([
                'message' => 'Saldo Insulficiente'
            ],);
        }
        // realizar a transação e salvar no banco

        $saldo = (float) $contaOrigem->getSaldo();
        $valorT = (float) $entrada->getValor();
        $saldoDestino = (float) $contaDestino->getSaldo();

        $contaOrigem->setSaldo($saldo - $valorT);
        $entityManager->persist($contaOrigem);

        $contaDestino->setSaldo($valorT + $saldoDestino);
        $entityManager->persist($contaDestino);

        $transacao = new Transacao();
        $transacao->setDataHora((new DateTime));
        $transacao->setValor($entrada->getValor());
        $transacao->setContaOrigem($contaOrigem);
        $transacao->setContaDestino($contaDestino);
        $entityManager->persist($transacao);

        $entityManager->flush();
        return new Response(status: 204);
    }
}
