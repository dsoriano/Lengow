<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Lengow\Form;

use Lengow\Lengow;
use Lengow\Model\Base\LengowExcludeCategoryQuery;
use Lengow\Model\LengowExcludeBrand;
use Lengow\Model\LengowExcludeBrandQuery;
use Lengow\Model\LengowExcludeCategory;
use Lengow\Model\LengowExcludeProduct;
use Lengow\Model\LengowExcludeProductQuery;
use Lengow\Model\LengowIncludeAttribute;
use Lengow\Model\LengowIncludeAttributeQuery;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\ExecutionContextInterface;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;
use Thelia\Model\Attribute;
use Thelia\Model\AttributeI18nQuery;
use Thelia\Model\AttributeQuery;
use Thelia\Model\Brand;
use Thelia\Model\BrandI18nQuery;
use Thelia\Model\BrandQuery;
use Thelia\Model\Category;
use Thelia\Model\CategoryI18nQuery;
use Thelia\Model\CategoryQuery;
use Thelia\Model\ConfigQuery;
use Thelia\Model\Product;
use Thelia\Model\ProductQuery;

/**
 * Class LengowConfigForm
 * @package Lengow\Form
 * @author Benjamin Perche <bperche@openstudio.fr>
 */
class LengowConfigForm extends BaseForm
{
    /**
     *
     * in this function you add all the fields you need for your Form.
     * Form this you have to call add method on $this->formBuilder attribute :
     *
     * $this->formBuilder->add("name", "text")
     *   ->add("email", "email", array(
     *           "attr" => array(
     *               "class" => "field"
     *           ),
     *           "label" => "email",
     *           "constraints" => array(
     *               new \Symfony\Component\Validator\Constraints\NotBlank()
     *           )
     *       )
     *   )
     *   ->add('age', 'integer');
     *
     * @return null
     */
    protected function buildForm()
    {
        // Retrieving choice options
        $locale = $this->request->getSession()->getAdminEditionLang()->getLocale();

        // Attributes
        $attributesOpts = array();

        /** @var Attribute $attribute */
        foreach (AttributeQuery::create()->find() as $attribute) {
            $attributesOpts[$attribute->getId()] = AttributeI18nQuery::create()
                ->filterByLocale($locale)
                ->filterByAttribute($attribute)
                ->findOne()
                ->getTitle()
            ;
        }

        // Brands
        $brandsOpts = array();

        /** @var Brand $brand */
        foreach (BrandQuery::create()->find() as $brand) {
            $brandsOpts[$brand->getId()] = BrandI18nQuery::create()
                ->filterByLocale($locale)
                ->filterByBrand($brand)
                ->findOne()
                ->getTitle()
            ;
        }

        // Categories
        $categoriesOpts = array();

        /** @var Category $category */
        foreach (CategoryQuery::create()->find() as $category) {
            $categoriesOpts[$category->getId()] = CategoryI18nQuery::create()
                ->filterByLocale($locale)
                ->filterByCategory($category)
                ->findOne()
                ->getTitle()
            ;
        }

        // Products
        $productsOpts = array();

        /** @var Product $product */
        foreach (ProductQuery::create()->find() as $product) {
            $productsOpts[$product->getId()] = $product->getRef();
        }


        // Retrieving values for Lengow

        // Attributes
        $lengowAttributes = [];

        /** @var LengowIncludeAttribute $attribute */
        foreach (LengowIncludeAttributeQuery::create()->find() as $attribute) {
            $lengowAttributes[] = $attribute->getAttributeId();
        }

        // Brands
        $lengowBrands = [];

        /** @var LengowExcludeBrand $brand */
        foreach (LengowExcludeBrandQuery::create()->find() as $brand) {
            $lengowBrands[] = $brand->getBrandId();
        }

        // Categories
        $lengowCategories = [];

        /** @var LengowExcludeCategory $category */
        foreach (LengowExcludeCategoryQuery::create()->find() as $category) {
            $lengowCategories[] = $category->getCategoryId();
        }

        // Attributes
        $lengowProducts = [];

        /** @var LengowExcludeProduct $product */
        foreach (LengowExcludeProductQuery::create()->find() as $product) {
            $lengowProducts[] = $product->getProductId();
        }


        // Building form
        // Be careful to cast numerical data into numbers. It might raise an exception otherwise.
        $this->formBuilder
            ->add("min-stock", "number", array(
                "label" => $this->trans('Minimum available product sale element'),
                "label_attr" =>  ["for" => "min-stock"],
                "required" => true,
                "constraints" => array(
                    new GreaterThanOrEqual(["value" => 0]),
                ),
                "data" => intval(ConfigQuery::read("lengow_min_quantity_export", 0)),
            ))
            ->add("delivery-price", "number", array(
                "label" => $this->trans('Delivery price'),
                "label_attr" =>  ["for" => "delivery-price"],
                "required" => true,
                "constraints" => array(
                    new GreaterThanOrEqual(["value" => 0]),
                ),
                "data" => floatval(ConfigQuery::read("lengow_delivery_price", 0)),
            ))
            ->add("free-shipping-amount", "number", array(
                "label" => $this->trans("Product's price for free shipping"),
                "label_attr" =>  ["for" => "free-shipping-amount"],
                "required" => false,
                "constraints" => array(
                    new GreaterThanOrEqual(["value" => 0]),
                ),
                "data" => floatval(ConfigQuery::read("lengow_free_delivery_price", 0)),
            ))
            ->add("front-cache-time", "integer", array(
                "label" => $this->trans("Cache time for front controller (in seconds)"),
                "label_attr" =>  ["for" => "front-cache-time"],
                "required" => false,
                "constraints" => array(
                    new GreaterThanOrEqual(["value" => 0]),
                ),
                "data" => intval(ConfigQuery::read("lengow_cache_time", 3600)),
            ))
            ->add('allowed-attributes-ids', 'choice', array(
                'expanded' => false,
                'multiple' => true,
                'label' => $this->trans('Attributes to include in the export'),
                'label_attr' => [
                    'for' => 'allowed-attributes-ids',
                ],
                'required' => false,
                'constraints' => [
                    new Callback([
                        'methods' => [
                            [$this, 'checkAttributes']
                        ]
                    ])
                ],
                'choices' => $attributesOpts,
                'data' => $lengowAttributes,
            ))
            ->add('exclude-categories-ids', 'choice', array(
                'expanded' => false,
                'multiple' => true,
                'label' => $this->trans('Categories to exclude from the export'),
                'label_attr' => [
                    'for' => 'exclude-categories-ids',
                ],
                'required' => false,
                'constraints' => [
                    new Callback([
                        'methods' => [
                            [$this, 'checkCategories']
                        ]
                    ])
                ],
                'choices' => $categoriesOpts,
                'data' => $lengowCategories,
            ))
            ->add('exclude-brands-ids', 'choice', array(
                'expanded' => false,
                'multiple' => true,
                'label' => $this->trans('Brands to exclude from the export'),
                'label_attr' => [
                    'for' => 'exclude-brands-ids',
                ],
                'required' => false,
                'constraints' => [
                    new Callback([
                        'methods' => [
                            [$this, 'checkBrands']
                        ]
                    ])
                ],
                'choices' => $brandsOpts,
                'data' => $lengowBrands,
            ))
            ->add('exclude-products-ids', 'choice', array(
                'expanded' => false,
                'multiple' => true,
                'label' => $this->trans('Specific products to exclude from the export'),
                'label_attr' => [
                    'for' => 'exclude-brands-ids',
                ],
                'required' => false,
                'constraints' => [
                    new Callback([
                        'methods' => [
                            [$this, 'checkProducts']
                        ]
                    ])
                ],
                'choices' => $productsOpts,
                'data' => $lengowProducts,
            ))
        ;
    }

    /**
     * Checking if submitted attribute IDs are real attribute IDs.
     * @param $value
     * @param ExecutionContextInterface $context
     */
    public function checkAttributes($value, ExecutionContextInterface $context)
    {
        $this->doCheck('Attribute', $value, $context);
    }

    /**
     * Checking if submitted category IDs are real category IDs.
     * @param $value
     * @param ExecutionContextInterface $context
     */
    public function checkCategories($value, ExecutionContextInterface $context)
    {
        $this->doCheck('Category', $value, $context);
    }

    /**
     * Checking if submitted brand IDs are real brand IDs.
     * @param $value
     * @param ExecutionContextInterface $context
     */
    public function checkBrands($value, ExecutionContextInterface $context)
    {
        $this->doCheck('Brand', $value, $context);
    }



    /**
     * Checking if submitted brand IDs are real brand IDs.
     * @param $value
     * @param ExecutionContextInterface $context
     */
    public function checkProducts($value, ExecutionContextInterface $context)
    {
        $this->doCheck('Product', $value, $context);
    }


    /**
     * Checking if submitted entity IDs are real entity IDs. It is the core method for this.
     * @param string $entityType Entity type ('Attribute', 'Category', 'Brand' or 'Product').
     * @param $value
     * @param ExecutionContextInterface $context
     */
    protected function doCheck($entityType, $value, ExecutionContextInterface &$context)
    {
        $entityIds = array();
        $queryclass = '\\Thelia\\Model\\'.$entityType.'Query';

        /** @var Brand $entity */
        foreach ($queryclass::create()->find() as $entity) {
            $entityIds[] = $entity->getId();
        }

        $notExistingIds = array();

        foreach ($value as $entityId) {
            if (!in_array($entityId, $entityIds)) {
                $notExistingIds[] = $entityId;
            }
        }

        if (!empty($notExistingIds)) {
            $context->addViolation($this->trans(
                'The following %entity_type IDs does not exist: %ids.',
                [
                    '%entity_type' => strtolower($entityType),
                    '%ids' => implode(', ', $notExistingIds)
                ]
            ));
        }
    }

    /**
     * @return string the name of you form. This name must be unique
     */
    public function getName()
    {
        return "lengow-configuration-form";
    }

    /**
     * Shortcut for translation
     * @param $id
     * @param array $parameters
     * @param string $domain
     * @return string
     */
    protected function trans($id, array $parameters = array(), $domain = Lengow::MESSAGE_DOMAIN)
    {
        is_null($this->translator) and $this->translator = Translator::getInstance();
        return $this->translator->trans($id, $parameters, $domain);
    }
}
