<?php
namespace App\Repository;

use App\Entity\File;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;

use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method File|null find($id, $lockMode = null, $lockVersion = null)
 * @method File|null findOneBy(array $criteria, array $orderBy = null)
 * @method File[]    findAll()
 * @method File[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, File::class);
    }

    // Retourne le nombre de dossiers d'un utilisateur
	public function getUserFilesCount(\App\Entity\User $user)
    {
    $qb = $this->createQueryBuilder('f');
    $qb->innerJoin('f.userFiles', 'uf', Expr\Join::WITH, $qb->expr()->eq('uf.email', '?1'));
    $qb->select($qb->expr()->count('f'));
    $qb->setParameter(1, $user->getEmail());
    $query = $qb->getQuery();
    $singleScalar = $query->getSingleScalarResult();
    return $singleScalar;
    }

	/**
	* @return File[] Retourne les dossiers a afficher d'un utilisateur
	*/
    public function getUserDisplayedFiles(\App\Entity\User $user, $firstRecordIndex, $maxRecord)
    {
    $qb = $this->createQueryBuilder('f');
    $qb->innerJoin('f.userFiles', 'uf', Expr\Join::WITH, $qb->expr()->eq('uf.email', '?1'));
    $qb->orderBy('f.name', 'ASC');
    $qb->setFirstResult($firstRecordIndex);
    $qb->setMaxResults($maxRecord);
    $qb->setParameter(1, $user->getEmail());
    $query = $qb->getQuery();
    $results = $query->getResult();
    return $results;
    }

    // Retourne le premier dossier d'un utilisateur (dans l'ordre d'affichage)
    public function getUserFirstFile(\App\Entity\User $user): ?File
    {
    $qb = $this->createQueryBuilder('f');
    $qb->innerJoin('f.userFiles', 'uf', Expr\Join::WITH, $qb->expr()->eq('uf.email', '?1'));
    $qb->orderBy('f.name', 'ASC');
    $qb->setMaxResults(1);
    $qb->setParameter(1, $user->getEmail());
    $query = $qb->getQuery();
    $results = $query->getOneOrNullResult();
    return $results;
    }
}
