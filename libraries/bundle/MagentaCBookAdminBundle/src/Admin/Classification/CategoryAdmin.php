<?php

namespace Magenta\Bundle\CBookAdminBundle\Admin\Classification;

use Knp\Menu\ItemInterface as MenuItemInterface;
use Magenta\Bundle\CBookAdminBundle\Admin\BaseAdminTrait;
use Magenta\Bundle\CBookAdminBundle\Form\Type\OrgAwareCategorySelectorType;
use Magenta\Bundle\CBookModelBundle\Entity\Classification\Category;
use Magenta\Bundle\CBookModelBundle\Entity\Classification\Context;
use Magenta\Bundle\CBookModelBundle\Entity\Organisation\IndividualGroup;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\ClassificationBundle\Admin\CategoryAdmin as SonataCategoryAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class CategoryAdmin extends SonataCategoryAdmin
{
    use BaseAdminTrait {
        configureRoutes as protected configureRoutesTrait;
//		configureFormFields as protected configureFormFieldsTrait;
    }

    /**
     * @var Category
     */
    protected $subject;

    public function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->add('createCategory');
    }

    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        parent::configureTabMenu($menu, $action, $childAdmin);
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        parent::configureFormFields($formMapper);
        $formMapper->remove('parent');
        $formMapper->remove('media');
        $formMapper->remove('position');
        /** @var Category $subject */
        $subject = $this->getSubject();
        $position = $this->hasSubject() && null !== $this->getSubject()->getPosition() ? $this->getSubject()->getPosition() : 0;

        $groupQuery = $this->getFilterByOrganisationQueryForModel(IndividualGroup::class);
        $formMapper->with('Options', [])
            ->add('enabled', CheckboxType::class, [
                'required' => false,
                'help' => 'If a book is not enabled, no one can view it except Admins.',
            ])
            ->add('public', CheckboxType::class, [
                'required' => false,
                'help' => 'To set whether a book is public or private.',
            ])
            ->add('accessGrantedGroups', ModelType::class, [
                'query' => $groupQuery,
                'btn_add' => false,
                'required' => false,
                'property' => 'name',
                'multiple' => true,
                'help' => 'Access Granted Groups enable the selected groups to view private books. This has no effects when a book is public.',
            ])
            ->add('accessDeniedGroups', ModelType::class, [
                'query' => $groupQuery,
                'btn_add' => false,
                'required' => false,
                'property' => 'name',
                'multiple' => true,
                'help' => 'Access Denied Groups prevent the selected groups from viewing public books. This has no effects when a book is private.',
            ])
            ->end();

        $parentSelectable = false;
        /** @var Category $subject */
        $subject = $this->getSubject();

        if (empty($subject)) {
            $parentSelectable = true;
        } else {
            if (empty($subject->getId())) {
                $parentSelectable = true;
            } else {
                if (null !== $subject->getParent()) {
                    $parentSelectable = true;
                }
            }
        }

        if ($parentSelectable) {
            $formMapper
                ->with('General')
                ->add('parent', OrgAwareCategorySelectorType::class, [
                    'organisation' => $this->getCurrentOrganisation(),
                    'category' => $this->getSubject() ?: null,
                    'model_manager' => $this->getModelManager(),
                    'class' => $this->getClass(),
                    'required' => true,
                    'context' => $this->getConfigurationPool()->getContainer()->get('doctrine')->getRepository(Context::class)->find(Context::DEFAULT_CONTEXT),
                    // $this->getSubject()->getContext(),
                    'btn_add' => false,
                ])
                ->end();
        }

        //		$keys = $formMapper->keys();
//		$key  = array_pop($keys);
//		array_unshift($keys, $key);
//		$formMapper->reorder($keys);
    }
}
