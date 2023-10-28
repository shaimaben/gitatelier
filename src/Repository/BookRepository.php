<?php

namespace App\Repository;

use App\Entity\Book;
use App\Form\SearchType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

//    /**
//     * @return Book[] Returns an array of Book objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Book
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

public function searchbyreferance ($ref) {
    return $this->createQueryBuilder('book')
    ->where('book.ref LIKE :ref')
    ->setParameter('ref',$ref)
    ->getQuery()
    ->getResult();
    }

    public function affichagebyauthor(){
        $query= $this->createQueryBuilder('b')
        ->join('b.book', 'a')
        ->orderBy('a.username','Asc')
        ->getQuery();
       return $query ->getResult();
    
    }

    public function findbookbyauth()
    {
        $query = $this->createQueryBuilder('b')
            ->select('b', 'COUNT(b.id) as bookCount')
            ->where('b.publicationDate < :year')
            ->setParameter('year',2023)
            ->groupBy('b.id')
            ->having('bookCount > 35');
    
        return $query->getQuery()->getResult();
    }

    public function updatecategory()
    {
        $qb = $this->createQueryBuilder('b')
            ->update()
            ->set('b.category', ':newCategory')
            ->where('b.book IN (SELECT a.id FROM App\Entity\Author a WHERE a.username = :authorName)')
            ->setParameter('newCategory', 'Romance')
            ->setParameter('authorName', 'William Shakespeare')
            ->getQuery()
            ->execute();
    }

public function publishedbooks()
    {
        $querry = $this->createQueryBuilder('b')
            ->select('SUM(CASE WHEN b.published = true THEN 1 ELSE 0 END) AS publishedCount')
            ->addSelect('SUM(CASE WHEN b.published = false THEN 1 ELSE 0 END) AS nonPublishedCount');

        $result = $querry->getQuery()->getSingleResult();

        return [
            'publishedCount' => $result['publishedCount'],
            'nonPublishedCount' => $result['nonPublishedCount'],
        ];
    }

    
    public function sommebook()
{
    $entityManager = $this->getEntityManager();

    $dql = "SELECT COUNT(b.id) AS scienceFictionSomme
            FROM App\Entity\Book b
            WHERE b.category = 'Science Fiction'";

    $query = $entityManager->createQuery($dql);
    $result = $query->getSingleResult();

    return $result['scienceFictionSomme'];
}
public function findBooksbyDate()
{
    $entityManager = $this->getEntityManager();
    $startDate = new \DateTime("2014-01-01");
    $endDate = new \DateTime("2018-12-31");

    $dql = "SELECT b FROM App\Entity\Book b 
            WHERE b.published = true
            AND b.publicationDate >= :startDate
            AND b.publicationDate <= :endDate";
      
    $query = $entityManager->createQuery($dql)
        ->setParameter('startDate', $startDate)
        ->setParameter('endDate', $endDate);

    return $query->getResult();
}

}
