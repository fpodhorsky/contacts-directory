<?php

namespace App\Repository;

use App\Entity\Contact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Order;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Contact>
 */
class ContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
    }

    public function getPaginatedContacts(int $currentPage = 1, int $limit = 10): array
    {
        $offset = ($currentPage - 1) * $limit;

        $qb = $this->createQueryBuilder('c');
        $qb->orderBy('c.last_name', 'ASC');
        if ($offset) {
            $qb->setFirstResult($offset);
        }
        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }
}
