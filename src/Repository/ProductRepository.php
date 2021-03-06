<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public const PAGINATOR_PER_PAGE = 50;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function add(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws Exception
     */
    public function addPessimistic(Product $entity)
    {
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            // make 'changeset' before find()
            $newName = $entity->getName();
            $newQuantityInStock = $entity->getQuantityInStock();
            $newDescription = $entity->getDescription();
            $newPrice = $entity->getPrice();
            // load db row - all proxies lose changes
            $product = $this->find($entity->getId(), LockMode::PESSIMISTIC_WRITE);
            // apply changeset
            $product->setName($newName);
            $product->setQuantityInStock($newQuantityInStock);
            $product->setDescription($newDescription);
            $product->setPrice($newPrice);
            // product.imageFilename, product.status are successfully handled by entity listeners
            // congratz you're doctrine jr. from now on
            $em->persist($product);
            $em->flush();
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function hide(Product $entity, bool $flush = false)
    {
        $entity->setStatus(Product::STATUS_PRODUCT_HIDDEN);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAll(): array
    {
        return $this->findBy([], ['name' => 'ASC', 'id' => 'ASC']);
    }

    public function findAllHideHidden(): array
    {
        return $this->createQueryBuilder('p')
            // hide hidden
            ->andWhere('p.status != :val')
            ->setParameter('val', Product::STATUS_PRODUCT_HIDDEN)
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function getProductPaginator(int $offset): Paginator
    {
        $query = $this->createQueryBuilder('p')
            // hide hidden
            ->andWhere('p.status != :val')
            ->setParameter('val', Product::STATUS_PRODUCT_HIDDEN)
            ->orderBy('p.name', 'ASC')
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset)
            ->getQuery()
        ;

        return new Paginator($query);
    }

    public function filterByKey($key)
    {
        $query = $this->createQueryBuilder('p')
            // hide hidden
            ->andWhere('p.status != :val')
            ->setParameter('val', Product::STATUS_PRODUCT_HIDDEN)
            ->andWhere('lower(p.name) LIKE :key')
            ->setParameter('key', '%' . $key . '%')
            ->orderBy('p.name', 'ASC')
            ->getQuery()
        ;

        return $query->execute();
    }

    public function findOneByIdHideHidden($id)
    {
        try {

            return $this->createQueryBuilder('p')
                ->andWhere('p.status != :hidden AND p.id = :id')
                ->setParameters([
                    'hidden' => Product::STATUS_PRODUCT_HIDDEN,
                    'id' => $id,
                ])
                ->getQuery()
                ->getOneOrNullResult()
                ;

        } catch (NonUniqueResultException) {
            // should never happen since select by autogenerated id
            return null;
        }
    }

//    /**
//     * @return Product[] Returns an array of Product objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
