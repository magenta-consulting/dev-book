<?php

namespace App\Controller;

use Bean\Bundle\BookBundle\Doctrine\Orm\Book;
use Bean\Bundle\BookBundle\Doctrine\Orm\Chapter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BeanPlaygroundController extends Controller {
	
	public function __invoke(Book $data) {
		/** @var ArrayCollection $chapters */
		$chapters = $data->getChapters();
		
		$criteria = Criteria::create();
		$expr     = Criteria::expr();
		
		$criteria->where(
			$expr->eq('partOf', $data)
		);
		
		return $chapters->matching($criteria);
	}
	
	/**
	 * @Route("/bean/playground", name="bean_playground")
	 */
	public function index() {
		$registry = $this->get('doctrine');
		$chapRepo = $registry->getRepository(Chapter::class);
		$chap     = $chapRepo->findAll()[0];
		$chap     = $chapRepo->find(6);
		$qb       = $this->container->get('doctrine.orm.default_entity_manager')->createQueryBuilder();
		
		$qb->select('c')->from(Chapter::class, 'c')
		   ->join('c.partOf', 'partOf');;
		$chapter = $qb->setFirstResult(0)->getQuery()->getResult();
		
		return $this->render('bean_playground/index.html.twig', [
			'controller_name' => 'BeanPlaygroundController',
			'chap'            => $chapter
		]);
	}
}