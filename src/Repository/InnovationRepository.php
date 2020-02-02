<?php
namespace App\Repository;

use App\Entity\Innovation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

/**
 * @method Innovation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Innovation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Innovation[]    findAll()
 * @method Innovation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InnovationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Innovation::class);
    }

    // Liste des innovations non lues par l'utilisateur
    public function getUnreadInnovations($userFileInnovationQB)
    {
    $qb = $this->createQueryBuilder('i');
    $qb->where($qb->expr()->not($qb->expr()->exists($userFileInnovationQB->getDQL())));
    $query = $qb->getQuery();
    $results = $query->getResult();
    return $results;
    }
}
