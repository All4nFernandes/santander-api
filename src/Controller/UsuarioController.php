<?php

namespace App\Controller;

use App\Dto\UsuarioDto;
use App\Entity\Usuario;
use App\Repository\UsuarioRepository;
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

        // converte o DTO em entidade usuário
        $usuario = new Usuario();
        $usuario->setCpf($usuarioDto->getCpf());
        $usuario->setNome($usuarioDto->getNome());
        $usuario->setEmail($usuarioDto->getEmail());
        $usuario->setSenha($usuarioDto->getSenha());
        $usuario->setTelefone($usuarioDto->getTelefone());

        // criar o registro na tb usuario
        $entityManager->persist($usuario);
        $entityManager->flush();

        return $this->json($usuario);
    }
}
