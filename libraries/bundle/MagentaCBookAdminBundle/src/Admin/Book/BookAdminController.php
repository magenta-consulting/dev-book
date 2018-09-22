<?php

namespace Magenta\Bundle\CBookAdminBundle\Admin\Book;

use Magenta\Bundle\CBookAdminBundle\Admin\BaseCRUDAdminController;
use Magenta\Bundle\CBookAdminBundle\Service\Organisation\OrganisationService;
use Magenta\Bundle\CBookModelBundle\Entity\Book\Book;
use Magenta\Bundle\CBookModelBundle\Entity\Classification\Category;
use Magenta\Bundle\CBookModelBundle\Entity\Classification\CategoryItem\BookCategoryItem;
use Magenta\Bundle\CBookModelBundle\Service\ServiceContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BookAdminController extends BaseCRUDAdminController {
	
	/** @var BookAdmin $admin */
	protected $admin;
	
	public function createBookAction(Request $request) {
		$categoryId = $request->request->getInt('category-id');
		$name       = $request->request->get('item-name');
		
		$registry = $this->getDoctrine();
		$catRepo  = $registry->getRepository(Category::class);
		$cat      = $catRepo->find($categoryId);
		
		if($request->isMethod('post')) {
			$catItem = new BookCategoryItem();
			$book    = new Book();
			$book->setName($name);
			
			$context = new ServiceContext();
			$context->setType(ServiceContext::TYPE_ADMIN_CLASS);
			$context->setAttribute('parent', $this->admin->getParent());
			
			$book->setOrganisation($this->get(OrganisationService::class)->getCurrentOrganisation($context));
			$catItem->setItem($book);
			$catItem->setCategory($cat);
			
			$manager = $this->get('doctrine.orm.default_entity_manager');
			$manager->persist($book);
			$manager->flush();
			
		}
		
		return new RedirectResponse($this->admin->generateUrl('show', [ 'id' => $book->getId() ]));
	}
	
	public function renderWithExtraParams($view, array $parameters = [], Response $response = null) {
		if($parameters['action'] === 'show') {
			/** @var Book $book */
			$book                             = $this->admin->getSubject();
			$accessCode                       = "1";
			$employeeCode                     = "1";
			$parameters['base_book_template'] = '@MagentaCBookAdmin/standard_layout.html.twig';
			$parameters['book']               = $book;
			$parameters['mainContentItem']    = $book;
			$parameters['subContentItems']    = $book->getRootChapters();
			$parameters['accessCode']         = $accessCode;
			$parameters['employeeCode']       = $employeeCode;
		}
		
		return parent::renderWithExtraParams($view, $parameters, $response);
	}
	
	/**
	 * @param Book $object
	 *
	 * @return RedirectResponse
	 */
	protected function redirectTo($object) {
		$request = $this->getRequest();
		
		if(null !== $request->get('btn_create_and_edit')) {
			return new RedirectResponse($this->admin->generateUrl('show', [ 'id' => $object->getId() ]));
		}
		
		return parent::redirectTo($object);
	}
	
	public function createAction() {
		return parent::createAction();
	}
	
	public function showAction($id = null) {
		$this->admin->setTemplate('show', '@MagentaCBookAdmin/Admin/Book/Book/CRUD/show.html.twig');
		
		return parent::showAction($id);
	}
	
	public function listAction() {
		$this->admin->setTemplate('list', '@MagentaCBookAdmin/Admin/Book/Book/CRUD/list.html.twig');
		
		return parent::listAction();
	}
}
