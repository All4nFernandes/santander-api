<?php

namespace App\Controller;

use App\Dto\UsuarioContaDto;
use App\Dto\UsuarioDto;
use App\Entity\Conta;
use App\Entity\Usuario;
use App\Repository\ContaRepository;
use App\Repository\UsuarioRepository;
use Doctrine\Inflector\Rules\NorwegianBokmal\Uninflected;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/api")]
class UsuarioController extends AbstractController
{
    #[Route('/usuarios', name: 'usuarios_criar', methods: ['POST'])]
    public function criar(
        #[MapRequestPayload(acceptFormat: 'json')]
        UsuarioDto $usuarioDto,
        UsuarioRepository $usuarioRepository,


        EntityManagerInterface $entityManager

    ): JsonResponse {

        

        $erros = [];



        if (!($usuarioDto->getNome())) {
            $erros[] = ["message" => "Nome é obrigatório!"];
        }
        if (!($usuarioDto->getEmail())) {
            $erros[] = ["message" => "Email é obrigatório!"];
        }

        if (!($usuarioDto->getTelefone())) {
            $erros[] = ["message" => "Telefone é obrigatório!"];
        }
        if (!($usuarioDto->getCpf())) {
            $erros[] = ["message" => "Cpf é obrigatório!"];
        }
        if (!($usuarioDto->getSenha())) {
            $erros[] = ["message" => "Senha é obrigatório!"];
        }

        if (count($erros) > 0) {
            return $this->json($erros, 422);
        }

        //valida se o cpf ja esta cadastrado
        $usuarioExixstente = $usuarioRepository->findByCpf($usuarioDto->getCpf());

        if($usuarioExixstente){
            return $this->json([ 
                "message" => "O CPF informado já está cadastrado"
            ],409);
        }

        // login
        $usuario = $usuarioRepository->Login($usuarioDto->getCpf(), $usuarioDto->getSenha());
        if(!$usuario){
            return $this->json([ 
                "message" => "Cpf ou Senha incorretos"
            ],401);
        }

        // converte o DTO em entidade usuário
        $usuario = new Usuario();
        $usuario->setCpf($usuarioDto->getCpf());
        $usuario->setNome($usuarioDto->getNome());
        $usuario->setEmail($usuarioDto->getEmail());
        $usuario->setSenha($usuarioDto->getSenha());
        $usuario->setTelefone($usuarioDto->getTelefone());

        // criar o registro na tb usuario
        $entityManager->persist($usuario);
        
        // instanciar o objeto conta
        $conta = new Conta();
        $numeroConta = preg_replace('/\D/',"", uniqid());
        // $numeroConta = rand(1,99999);
        $conta->setNumero($numeroConta);
        $conta->setSaldo("0");
        $conta->setUsuario($usuario);

        $entityManager->persist($conta);
        $entityManager->flush();


        // criar registro na tb conta
        // retornar os dados de usuário e conta
        $usuarioContaDto = new UsuarioContaDto();
        $usuarioContaDto->setId($conta->getUsuario()->getId());
        $usuarioContaDto->setNome($conta->getUsuario()->getNome());
        $usuarioContaDto->setCpf($conta->getUsuario()->getCpf());
        $usuarioContaDto->setEmail($conta->getUsuario()->getEmail());
        $usuarioContaDto->setTelefone($conta->getUsuario()->getTelefone());
        $usuarioContaDto->setNumeroConta($conta->getNumero());
        $usuarioContaDto->setSaldo($conta->getSaldo());

        return $this->json($usuarioContaDto, 201);
    }

    #[Route(path:"/usuarios/{id}", name:"usuarios_buscar", methods: ["GET"])]
    public function buscarPorId(
        int $id,
        ContaRepository $contaRepository
    ){
        $conta = $contaRepository->findByUsuarioId($id);
        if(!$conta){
            return $this->json([
                'message' => 'Usuário não encontrado'
            ], 400);
        }
        
    }
}
