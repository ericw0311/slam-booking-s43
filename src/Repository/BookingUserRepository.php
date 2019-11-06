<?php
namespace App\Repository;

use App\Entity\BookingUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

/**
 * @method BookingUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookingUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookingUser[]    findAll()
 * @method BookingUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookingUser::class);
    }

    public function getBookingUsers(\App\Entity\Booking $booking)
  	{
  	$qb = $this->createQueryBuilder('bu');
  	$qb->select('uf.id userFileID');
  	$qb->addSelect('bu.oorder');
  	$qb->where('bu.booking = :booking')->setParameter('booking', $booking);
  	$qb->innerJoin('bu.userFile', 'uf');
  	$qb->orderBy('bu.oorder', 'ASC');
  	$query = $qb->getQuery();
  	$results = $query->getResult();
  	return $results;
  	}
}
