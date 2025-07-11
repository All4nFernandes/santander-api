<?php

namespace App\Repository;

use App\Entity\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Usuario>
 */
class UsuarioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Usuario::class);
    }

        /**
         * @return Usuario Retorna um objeto de Usuario ou nulo
         */
        public function findByCpf($cpf): Usuario | null
        {
            return $this->createQueryBuilder('u')
                ->Where('u.cpf = :cpf')
                ->setParameter('cpf', $cpf)
                ->getQuery()
                ->getOneOrNullResult()
            ;
        }
        public function Login($cpf, $senha): Usuario | null
        {
            return $this->createQueryBuilder('u')
            ->Where('u.cpf = :cpf')
            ->andWhere('u.senha = :senha')
            ->setParameter('$cpf', $cpf)
            ->setParameter('$senha', $senha)
            ->getQuery()
            ->getOneOrNullResult();
        }


    //    public function findOneBySomeField($value): ?Usuario
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
