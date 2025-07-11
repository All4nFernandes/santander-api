<?php

namespace App\Controller;

use App\Dto\ContaDto;
use App\Dto\TransacaoExtratoDto;
use App\Dto\TransacaoRealizarDto;
use App\Entity\Conta;
use App\Entity\Transacao;
use App\Repository\ContaRepository;
use App\Repository\TransacaoRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    ): Response|JsonResponse {





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
        if ((float) $entrada->getValor() <= 0) {
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
                'message' => 'Conta de origem não encontrada!',
            ], 404);
        }

        $contaDestino = $contaRepository->findByUsuarioId($entrada->getIdUsuarioDestino());
        if (!$contaDestino) {
            return $this->json([
                'message' => 'Conta de destino não encontrada!',
            ], 404);
        }


        // 3. validar se a conta origem tem saldo sulficiente

        if ((float) $contaOrigem->getSaldo() < (float) $entrada->getValor()) {

            return $this->json([
                'message' => 'Saldo Insulficiente',
            ], );
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


    /*
            Entrada:
                idUsuario - quem deseja ver o seu extrato

            Procedimento:
                validar se o usuário existe

                buscar as transações

                montar a saida

            Saída:

            [
                {
                    "id" : 1,
                    "valor" : "199.90",
                    "dataHora" : "2025-07-04 00:11:33",
                    "tipo" : "", // RECEBEU / ENVIOU
                    "origem" : {
                        "",
                    }

                }
            ]

        */

    // /api/transacoes/{idUsuario}/extrato
    #[Route("/transacoes/{idUsuario}/extrato", name: 'transacoes_extrato', methods: ['GET'])]
    public function gerarExtrato(
        int $idUsuario,
        ContaRepository $contaRepository,
        TransacaoRepository $transacaoRepository
    ): JsonResponse {

        $conta = $contaRepository->findByUsuarioId($idUsuario);
        if (!$conta) {
            return $this->json([
                "message" => "Usuario não encontrado!",
            ], 404);

        }

        $trasacoes = $transacaoRepository->findByContaOrigemAndContaDestino($conta->getId());

        $saida = [];
        foreach ($trasacoes as $transacao) {

            $transacaoDto = new TransacaoExtratoDto();
            $transacaoDto->setId($transacao->getId());
            $transacaoDto->setValor($transacao->getValor());
            $transacaoDto->setDatahora($transacao->getDataHora());

            if ($conta->getId() === $transacao->getContaOrigem()->getId()) {
                $transacaoDto->setTipo('ENVIOU');
            } elseif ($conta->getId() === $transacao->getContaDestino()->getId()) {
                $transacaoDto->setTipo('RECEBEU');
            }

            $origem = $transacao->getContaOrigem();
            $contaOrigemDto = new ContaDto();
            $contaOrigemDto->setId($origem->getUsuario()->getId());
            $contaOrigemDto->setNome($origem->getUsuario()->getNome());
            $contaOrigemDto->setCpf($origem->getUsuario()->getCpf());
            $contaOrigemDto->setNumeroConta($origem->getNumero());

            $transacaoDto->setOrigem($contaOrigemDto);

            $destino = $transacao->getContaDestino();
            $contaDestinoDto = new ContaDto();
            $contaDestinoDto->setId($destino->getUsuario()->getId());
            $contaDestinoDto->setNome($destino->getUsuario()->getNome());
            $contaDestinoDto->setCpf($destino->getUsuario()->getCpf());
            $contaDestinoDto->setNumeroConta($destino->getNumero());

            $transacaoDto->setDestino($contaDestinoDto);


            array_push($saida, $transacaoDto);
        }
        return $this->json($saida);
    }

}
