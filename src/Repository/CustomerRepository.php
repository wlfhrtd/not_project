<?php

namespace App\Repository;

use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Customer>
 *
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerRepository extends ServiceEntityRepository
{
    public const PAGINATOR_PER_PAGE = 50;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    public function add(Customer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Customer $entity, bool $flush = false): void
    {
        /*
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
        */

        $entity->setStatus(Customer::STATUS_CUSTOMER_DISABLED);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.status != :val')
            ->setParameter('val', Customer::STATUS_CUSTOMER_DISABLED)
            ->orderBy('c.lastName', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAllWithDisabled(): array
    {
        return $this->findBy([], ['lastName' => 'ASC', 'id' => 'ASC']);
    }

    public function getCustomerPaginator(int $offset): Paginator
    {
        $query = $this->createQueryBuilder('c')
            ->andWhere('c.status != :val')
            ->setParameter('val', Customer::STATUS_CUSTOMER_DISABLED)
            ->orderBy('c.lastName', 'ASC')
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset)
            ->getQuery();

        return new Paginator($query);
    }

    // select2 & AjaxController functionality
    public function filterByKey($key)
    {
        $query = $this->createQueryBuilder('c')
            ->andWhere("concat(c.lastName, ' ', c.firstName, ' ', c.middleName) LIKE :key")
            ->setParameter('key', $key . '%')
            ->orderBy('c.lastName', 'ASC')
            ->getQuery();

        return $query->execute();
    }

//    /**
//     * @return Customer[] Returns an array of Customer objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Customer
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
