<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 *
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public const PAGINATOR_PER_PAGE = 50;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function add(Order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws Exception
     */
    public function addPessimistic(Order $entity, array $transaction)
    {
        $em = $this->getEntityManager();

        $productRepository = $em->getRepository(Product::class);

        $conn = $em->getConnection();
        $conn->beginTransaction();

        try {
            $productIDs = [];
            $newValues = [];
            foreach ($transaction as $row) {
                $productIDs[] = $row[0];
                $newValues[] = $row[1];
            }
            $products = [];
            foreach ($productIDs as $productID) {
                $products[] = $productRepository->find($productID, LockMode::PESSIMISTIC_WRITE);
            }
            foreach ($products as $k => $product) {
                $product->setQuantityInStock($newValues[$k]);
            }

            $em->persist($entity);
            $em->flush();
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    public function remove(Order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function hide(Order $entity, bool $flush = false)
    {
        $entity->setStatus(Order::STATUS_ORDER_CANCELED);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws Exception
     */
    public function hidePessimistic(Order $entity, array $transaction)
    {
        $em = $this->getEntityManager();

        $productRepository = $em->getRepository(Product::class);

        $conn = $em->getConnection();
        $conn->beginTransaction();

        try {
            $productIDs = [];
            $newValues = [];
            foreach ($transaction as $row) {
                $productIDs[] = $row[0];
                $newValues[] = $row[1];
            }
            $products = [];
            foreach ($productIDs as $productID) {
                $products[] = $productRepository->find($productID, LockMode::PESSIMISTIC_WRITE);
            }
            foreach ($products as $k => $product) {
                $product->setQuantityInStock($newValues[$k]);
            }

            $entity->setStatus(Order::STATUS_ORDER_CANCELED);

            $em->persist($entity);
            $em->flush();
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    public function findAll(): array
    {
        return $this->findBy([], ['updatedAt' => 'DESC', 'id' => 'DESC']);
    }

    public function findAllHideCanceled(): array
    {
        return $this->createQueryBuilder('o')
            // hide canceled
            ->andWhere('o.status != :val')
            ->setParameter('val', Order::STATUS_ORDER_CANCELED)
            ->orderBy('o.updatedAt', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function getOrderPaginator(int $offset): Paginator
    {
        $query = $this->createQueryBuilder('o')
            // hide canceled
            ->andWhere('o.status != :val')
            ->setParameter('val', Order::STATUS_ORDER_CANCELED)
            ->orderBy('o.updatedAt', 'DESC')
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset)
            ->getQuery();

        return new Paginator($query);
    }

//    /**
//     * @return Order[] Returns an array of Order objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Order
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
