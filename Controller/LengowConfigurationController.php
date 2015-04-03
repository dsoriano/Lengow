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

namespace Lengow\Controller;

use Lengow\Event\LengowExcludeBrandEvent;
use Lengow\Event\LengowExcludeBrandEvents;
use Lengow\Event\LengowExcludeCategoryEvent;
use Lengow\Event\LengowExcludeCategoryEvents;
use Lengow\Event\LengowExcludeProductEvent;
use Lengow\Event\LengowExcludeProductEvents;
use Lengow\Event\LengowIncludeAttributeEvent;
use Lengow\Event\LengowIncludeAttributeEvents;
use Lengow\Form\LengowConfigForm;
use Lengow\Lengow;
use Lengow\Model\LengowExcludeBrand;
use Lengow\Model\LengowExcludeBrandQuery;
use Lengow\Model\LengowExcludeCategory;
use Lengow\Model\LengowExcludeCategoryQuery;
use Lengow\Model\LengowExcludeProduct;
use Lengow\Model\LengowExcludeProductQuery;
use Lengow\Model\LengowIncludeAttribute;
use Lengow\Model\LengowIncludeAttributeQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Component\Form\Form;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\HttpFoundation\JsonResponse;
use Thelia\Core\HttpFoundation\Response;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Model\Base\BrandQuery;
use Thelia\Model\ConfigQuery;
use Thelia\Model\Map\BrandI18nTableMap;
use Thelia\Model\Map\BrandTableMap;
use Thelia\Model\Map\CategoryI18nTableMap;
use Thelia\Model\Map\CategoryTableMap;
use Thelia\Model\Map\ProductTableMap;
use Thelia\Model\Product;
use Thelia\Model\ProductQuery;

/**
 * Class LengowConfigurationController
 * @package Lengow\Controller
 * @author Benjamin Perche <bperche@openstudio.fr>
 */
class LengowConfigurationController extends BaseAdminController
{
    public function configure()
    {
        if (null !== $response = $this->checkAuth([AdminResources::MODULE], ['Lengow'], AccessManager::UPDATE)) {
            return $response;
        }

        $errorMessage = null;
        $successMessage = null;

        $form = new LengowConfigForm($this->getRequest());

        try {
            $boundForm = $this->validateForm($form);

            ConfigQuery::write("lengow_min_quantity_export", $boundForm->get("min-stock")->getData());
            ConfigQuery::write("lengow_cache_time", $boundForm->get("front-cache-time")->getData());
            ConfigQuery::write("lengow_free_delivery_price", $boundForm->get("free-shipping-amount")->getData());
            ConfigQuery::write("lengow_delivery_price", $boundForm->get("delivery-price")->getData());

            // Rewriting IDs for Lengow
            $this->updateIdsForLengow($boundForm);
        } catch (FormValidationException $e) {
            $errorMessage = $this->createStandardFormValidationErrorMessage($e);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
        }

        if (null !== $errorMessage) {
            $this->setupFormErrorContext(
                "Lengow",
                $errorMessage,
                $form
            );
        } else {
            if ($this->getRequest()->request->get("save_mode") === "close") {
                $this->redirectToRoute("admin.module");
            }

            $successMessage = $this->getTranslator()->trans(
                'Configuration successfully saved',
                [],
                Lengow::MESSAGE_DOMAIN
            );
        }

        return $this->render(
            "module-configure",
            array(
                "module_code" => "Lengow",
                "success_message" => $successMessage,
            )
        );
    }

    /**
     * Updating IDs to exclude or include for Lengow
     * @param \Symfony\Component\Form\Form $boundForm
     * @throws \Exception
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function updateIdsForLengow(Form $boundForm)
    {
        // Attributes
        $this->dispatch(LengowIncludeAttributeEvents::DELETE_ALL, new LengowIncludeAttributeEvent());

        foreach ($boundForm->get('allowed-attributes-ids')->getData() as $id) {
            $event = new LengowIncludeAttributeEvent();
            $event->setAttributeId($id);
            $this->dispatch(LengowIncludeAttributeEvents::CREATE, $event);
        }

        // Brands
        $this->dispatch(LengowExcludeBrandEvents::DELETE_ALL, new LengowExcludeBrandEvent());

        foreach ($boundForm->get('exclude-brands-ids')->getData() as $id) {
            $event = new LengowExcludeBrandEvent();
            $event->setBrandId($id);
            $this->dispatch(LengowExcludeBrandEvents::CREATE, $event);
        }

        // Categories
        $this->dispatch(LengowExcludeCategoryEvents::DELETE_ALL, new LengowExcludeCategoryEvent());

        foreach ($boundForm->get('exclude-categories-ids')->getData() as $id) {
            $event = new LengowExcludeCategoryEvent();
            $event->setCategoryId($id);
            $this->dispatch(LengowExcludeCategoryEvents::CREATE, $event);
        }

        // Products
        $this->dispatch(LengowExcludeProductEvents::DELETE_ALL, new LengowExcludeProductEvent());

        foreach (array_unique($boundForm->get('exclude-products-ids')->getData()) as $id) {
            $event = new LengowExcludeProductEvent();
            $event->setProductId($id);
            $this->dispatch(LengowExcludeProductEvents::CREATE, $event);
        }
    }

    public function searchProducts()
    {
        if (null !== $this->checkAuth([AdminResources::MODULE], ['Lengow'], AccessManager::VIEW)) {
            return Response::create("false");
        }

        $locale = $this->getSession()->getAdminEditionLang()->getLocale();
        $search = $this->getRequest()->query->get('q', '');
        $productQuery = ProductQuery::create()
            ->select([ProductTableMap::ID, ProductTableMap::REF, ProductTableMap::BRAND_ID])
            ->useProductCategoryQuery(null, Criteria::LEFT_JOIN)
                ->addAsColumn("category_ID", CategoryTableMap::ID)
                ->filterByDefaultCategory(1)
                ->useCategoryQuery(null, Criteria::LEFT_JOIN)
                    ->useCategoryI18nQuery()
                        ->filterByLocale($locale)
                        ->addAsColumn("rubric_TITLE", CategoryI18nTableMap::TITLE)
                    ->endUse()
                ->endUse()
            ->endUse()
            ->useBrandQuery()
                ->useBrandI18nQuery()
                    ->addAsColumn('brand_NAME', BrandI18nTableMap::TITLE)
                    ->filterByLocale($locale)
                ->endUse()
            ->endUse()
            ->filterByRef("%$search%", Criteria::LIKE)
            ->find()
            ->toArray()
        ;

        $res = array(
            'brands' => array(),
            'categories' => array(),
        );

        foreach ($productQuery as $product) {
            $resprod = array(
                'id' => $product[ProductTableMap::ID],
                'ref' => $product[ProductTableMap::REF],
            );

            // Brands filter
            if ($product[ProductTableMap::BRAND_ID] == 0) {
                $res['brands'][] = $resprod;
            } else {
                if (!array_key_exists($product['brand_NAME'], $res['brands'])) {
                    $res['brands'][$product['brand_NAME']] = array();
                }

                $res['brands'][$product['brand_NAME']][] = $resprod;
            }

             // Categories filter
            if ($product['category_ID'] == 0) {
                $res['categories'][] = $resprod;
            } else {
                if (!array_key_exists($product['rubric_TITLE'], $res['categories'])) {
                    $res['categories'][$product['rubric_TITLE']] = array();
                }

                $res['categories'][$product['rubric_TITLE']][] = $resprod;
            }
        }

        return JsonResponse::create($res);
    }

    public function searchProduct()
    {
        if (null !== $this->checkAuth([AdminResources::MODULE], ['Lengow'], AccessManager::VIEW)) {
            return Response::create("false");
        }

        $search = $this->getRequest()->query->get('q', '');

        $product = ProductQuery::create()
            ->select([ProductTableMap::ID, ProductTableMap::REF])
            ->filterByRef($search)
            ->find()
            ->toArray()
        ;

        return (count($product) > 0) ?  JsonResponse::create(array_pop($product)) : Response::create("false");
    }
}
