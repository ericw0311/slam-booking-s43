<?php

namespace App\Repository;

use App\Entity\InnovationUserFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

/**
 * @method InnovationUserFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method InnovationUserFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method InnovationUserFile[]    findAll()
 * @method InnovationUserFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InnovationUserFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InnovationUserFile::class);
    }

    // Construit le Query Builder d'acces aux innovations lues par un utilisateur dossier
    public function getUserFileInnovationQB(\App\Entity\UserFile $userFile)
    {
        $qb = $this->createQueryBuilder('iuf');
        $qb->where('iuf.innovation = i.id');
        $qb->innerJoin('iuf.userFile', 'uf', Expr\Join::WITH, 'uf.id = '.$userFile->getId());
        return $qb;
    }
}
