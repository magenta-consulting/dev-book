<?php

namespace Magenta\Bundle\CBookModelBundle\Entity\Book;

use Bean\Component\Book\Model\Book as BookModel;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Magenta\Bundle\CBookModelBundle\Entity\Classification\CategoryItem\BookCategoryItem;

/**
 * @ORM\Entity()
 * @ORM\Table(name="book__book")
 */
class Book extends \Bean\Bundle\BookBundle\Doctrine\Orm\Book {
	const STATUS_DRAFT = 'DRAFT';
	
	function __construct() {
		parent::__construct();
		$this->locale = 'en';
		$this->status = self::STATUS_DRAFT;
	}
	
	/**
	 * @ORM\OneToMany(
	 *     targetEntity="Magenta\Bundle\CBookModelBundle\Entity\Classification\CategoryItem\BookCategoryItem",
	 *     mappedBy="item", cascade={"persist"}, orphanRemoval=true
	 * )
	 * @ORM\OrderBy({"position"="ASC"})
	 *
	 * @var \Doctrine\Common\Collections\Collection $bookCategoryItems ;
	 */
	protected $bookCategoryItems;
	
	public function addBookCategoryItem(BookCategoryItem $bc) {
		$this->bookCategoryItems->add($bc);
		$bc->setItem($this);
	}
	
	public function removeBookCategoryItem(BookCategoryItem $bc) {
		$this->bookCategoryItems->removeElement($bc);
		$bc->setItem(null);
	}
	
	/**
	 * @ORM\OneToMany(targetEntity="Chapter", cascade={"persist","merge"}, orphanRemoval=true, mappedBy="book")
	 */
	protected $chapters;
	
	/**
	 * @var Collection
	 * @ORM\OneToMany(targetEntity="Bean\Component\CreativeWork\Model\CreativeWork", cascade={"persist","merge"}, orphanRemoval=true, mappedBy="partOf")
	 */
	protected $parts;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Magenta\Bundle\CBookModelBundle\Entity\Organisation\Organisation", inversedBy="books")
	 * @ORM\JoinColumn(name="id_organisation", referencedColumnName="id", onDelete="CASCADE")
	 */
	protected $organisation;

//	/**
//	 * @var boolean|null
//	 * @ORM\Column(type="boolean", nullable=true)
//	 */
//	protected $abridged;
//
//	/**
//	 * @var integer|null
//	 * @ORM\Column(type="integer", nullable=true)
//	 */
//	protected $numberOfPages;
//
//	/**
//	 * @var string|null
//	 * @ORM\Column(type="string", nullable=true)
//	 */
//	protected $bookEdition;
//
//	/**
//	 * @var string|null
//	 * @ORM\Column(type="string", nullable=true)
//	 */
//	protected $bookFormat;
//
//	/**
//	 * @var string|null
//	 * @ORM\Column(type="string", nullable=true)
//	 */
//	protected $isbn;
	
	/**
	 * @return mixed
	 */
	public function getOrganisation() {
		return $this->organisation;
	}
	
	/**
	 * @param mixed $organisation
	 */
	public function setOrganisation($organisation): void {
		$this->organisation = $organisation;
	}
	
	/**
	 * @return Collection
	 */
	public function getBookCategoryItems(): Collection {
		return $this->bookCategoryItems;
	}
	
	/**
	 * @param Collection $bookCategoryItems
	 */
	public function setBookCategoryItems(Collection $bookCategoryItems): void {
		$this->bookCategoryItems = $bookCategoryItems;
	}
}
