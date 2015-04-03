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

namespace Lengow\Export;

use Lengow\Model\LengowExcludeBrandQuery;
use Lengow\Model\LengowExcludeCategoryQuery;
use Lengow\Model\LengowExcludeProductQuery;
use Lengow\Model\LengowIncludeAttributeQuery;
use Lengow\Model\Map\LengowExcludeBrandTableMap;
use Lengow\Model\Map\LengowExcludeCategoryTableMap;
use Lengow\Model\Map\LengowExcludeProductTableMap;
use Lengow\Model\Map\LengowIncludeAttributeTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Collection\ArrayCollection;
use Propel\Runtime\Collection\ObjectCollection;

use Symfony\Component\Validator\Constraints\Collection;

use Thelia\Core\Event\Image\ImageEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\ImportExport\Export\ExportHandler;

use Thelia\Model\Country;
use Thelia\Model\Currency;
use Thelia\Model\Lang;
use Thelia\Model\Product;

use Thelia\Model\AttributeCombinationQuery;
use Thelia\Model\CategoryQuery;
use Thelia\Model\ConfigQuery;
use Thelia\Model\ProductQuery;
use Thelia\Model\ProductSaleElementsQuery;

use Thelia\Model\Map\AttributeAvI18nTableMap;
use Thelia\Model\Map\AttributeAvTableMap;
use Thelia\Model\Map\AttributeCombinationTableMap;
use Thelia\Model\Map\AttributeI18nTableMap;
use Thelia\Model\Map\AttributeTableMap;
use Thelia\Model\Map\BrandI18nTableMap;
use Thelia\Model\Map\BrandTableMap;
use Thelia\Model\Map\CategoryI18nTableMap;
use Thelia\Model\Map\CategoryTableMap;
use Thelia\Model\Map\ProductI18nTableMap;
use Thelia\Model\Map\ProductSaleElementsTableMap;
use Thelia\Model\Map\ProductTableMap;

use Thelia\TaxEngine\Calculator;
use Thelia\Tools\I18n;

/**
 * Class LengowExport
 * @package Lengow\Export
 * @author Benjamin Perche <bperche@openstudio.fr>
 */
class LengowExport extends ExportHandler
{
    /**
     * @return string|array
     *
     * Define all the type of formatters that this can handle
     * return a string if it handle a single type ( specific exports ),
     * or an array if multiple.
     *
     * Thelia types are defined in \Thelia\Core\FileFormat\FormatType
     *
     * example:
     * return array(
     *     FormatType::TABLE,
     *     FormatType::UNBOUNDED,
     * );
     */
    public function getHandledTypes()
    {
        return LengowType::LENGOW_EXPORT;
    }

    /**
     * @param  Lang                         $lang
     * @return ModelCriteria|array|BaseLoop
     */
    public function buildDataSet(Lang $lang)
    {
        /**
         * Prevents time limit hints
         */
        set_time_limit(0);

        /**
         * Country / Currency
         */
        $shopCountry = Country::getShopLocation();
        $defaultCurrency = Currency::getDefaultCurrency();
        $defaultCurrencyCode = $defaultCurrency->getCode();
        $locale = $lang->getLocale();

        /**
         * Images events
         */
        $event = new ImageEvent($this->getRequest());
        $eventSmallImage = new ImageEvent($this->getRequest());
        $eventSmallImage->setHeight(100);
        $eventSmallImage->setWidth(100);

        /**
         * Tools
         */
        $calculator = new Calculator();

        /**
         * Configuration variables
         */
        $minStock = ConfigQuery::read("lengow_min_quantity_export", 3);
        $deliveryPrice = ConfigQuery::read("lengow_delivery_price", "5.90");
        $freeDeliveryAmount = ConfigQuery::read("lengow_free_delivery_price", 60);

        // Exclude categories
        $excludeCategories = LengowExcludeCategoryQuery::create()->select([LengowExcludeCategoryTableMap::CATEGORY_ID])->find()->toArray();
        $excludeCategories[] = -1;

        // Exclude brands
        $excludeBrands = LengowExcludeBrandQuery::create()->select([LengowExcludeBrandTableMap::BRAND_ID])->find()->toArray();
        $excludeBrands[] = -1;

        // Exclude some products
        $excludeProducts = LengowExcludeProductQuery::create()->select([LengowExcludeProductTableMap::PRODUCT_ID])->find()->toArray();
        $excludeProducts[] = -1;

        /**
         * Build categories tree ( id => top level name )
         */
        $categories = $this->generateTree($locale);

        $productsQuery = ProductQuery::create()
            ->orderById()
            ->useProductSaleElementsQuery()
                ->where(ProductSaleElementsTableMap::QUANTITY ." > 0")
                ->having("COUNT(".ProductSaleElementsTableMap::ID.") > ?", $minStock, \PDO::PARAM_INT)
            ->endUse()
            ->useProductCategoryQuery(null, Criteria::LEFT_JOIN)
                ->addAsColumn("category_ID", CategoryTableMap::ID)
                ->filterByDefaultCategory(1)
                ->useCategoryQuery(null, Criteria::LEFT_JOIN)
                    ->useCategoryI18nQuery()
                        ->addAsColumn("rubric_TITLE", CategoryI18nTableMap::TITLE)
                    ->endUse()
                ->endUse()
            ->endUse()
            ->useProductI18nQuery()
                ->addAsColumn("product_TITLE", ProductI18nTableMap::TITLE)
                ->addAsColumn("product_DESCRIPTION", ProductI18nTableMap::DESCRIPTION)
            ->endUse()
            ->useBrandQuery(null, Criteria::LEFT_JOIN)
                ->useBrandI18nQuery()
                    ->addAsColumn("brand_TITLE", BrandI18nTableMap::TITLE)
                ->endUse()
            ->endUse()
            ->useProductCategoryQuery()
                ->useCategoryQuery()
                    ->filterById($excludeCategories, Criteria::NOT_IN)
                    ->filterByVisible(1)
                ->endUse()
            ->endUse()
            ->useProductCategoryQuery()
                ->useCategoryQuery()
                    ->filterById($excludeCategories, Criteria::NOT_IN)
                    ->filterByVisible(1)
                ->endUse()
            ->endUse()
            ->useBrandQuery()
                ->filterById($excludeBrands, Criteria::NOT_IN)
            ->endUse()
            ->filterById($excludeProducts, Criteria::NOT_IN)
            ->filterByVisible(1)
            ->groupById()
            ->select(ProductTableMap::getFieldNames())
        ;

        I18n::addI18nCondition(
            $productsQuery,
            BrandI18nTableMap::TABLE_NAME,
            BrandTableMap::ID,
            BrandI18nTableMap::ID,
            BrandI18nTableMap::LOCALE,
            $locale
        );

        I18n::addI18nCondition(
            $productsQuery,
            ProductI18nTableMap::TABLE_NAME,
            ProductTableMap::ID,
            ProductI18nTableMap::ID,
            ProductI18nTableMap::LOCALE,
            $locale
        );

        I18n::addI18nCondition(
            $productsQuery,
            CategoryI18nTableMap::TABLE_NAME,
            CategoryTableMap::ID,
            CategoryI18nTableMap::ID,
            CategoryI18nTableMap::LOCALE,
            $locale
        );

        /** @var ProductQuery $productsQuery */
        $products = $this->fetch(
            $productsQuery->find(),
            $productIds
        );

        /**
         * Get product sale elements filtered by products ids
         */
        $productSaleElements = ProductSaleElementsQuery::create()
            ->filterByProductId($productIds, Criteria::IN)
            ->orderByProductId(Criteria::DESC)
            ->find()
        ;

        $attributes = $this->getAttributesTable($productSaleElements, $locale);

        /** @var \Thelia\Model\ProductSaleElements $productSaleElement */
        $productSaleElement = $productSaleElements->pop();

        $dataSet = [];

        /** @var \Thelia\Model\Product $product */
        foreach ($products as $product) {
            /**
             * Generate the rows
             */
            $row = [];

            /**
             * Add attributes columns
             */
            foreach ($attributes["attributes"] as $attribute) {
                $row[$attribute] = null;
            }

            $attributeList = isset($attributes["data"][$product->getId()]["attributes"]) ?
                $attributes["data"][$product->getId()]["attributes"] :
                []
            ;

            $row["attributes"] = implode(",", $attributeList);

            /**
             * Add product's title, description and category
             */
            $row["id"] = $product->getId();
            $row["parent_id"] = $product->getId();
            $row["is_parent"] = "1";
            $row["ref"] = $product->getRef();
            $row["title"] = $product->getVirtualColumn("product_TITLE");
            $row["breadcrumb"] = $categories[(int) $product->getVirtualColumn("category_ID")];
            $row["brand"] = $product->getVirtualColumn("brand_TITLE");
            $row["updated_at"] = $product->getUpdatedAt($lang->getDatetimeFormat());
            $row["url"] = $this->formatUrl($product->getUrl($locale));
            $row["currency"] = $defaultCurrencyCode;

            $description = $product->getVirtualColumn("product_DESCRIPTION");
            $description = str_replace("&nbsp;", "", strip_tags($description));
            $description = str_replace("\r\n", " ", $description);
            $description = str_replace("Caractéristiques :", "", $description);
            $row["description"] = trim($description);
            /**
             * Compute the images url
             */
            $row["url_image"] = null;
            $row["url_image_small"] = null;
            $images = $product->getProductImages();

            if ($images->count() > 0) {
                /** @var \Thelia\Model\ProductImage $image */
                $image = $images->get(0);

                $sourceFilepath = $image->getUploadDir() . DS . $image->getFile();
                $cacheSubdirectory = basename($image->getUploadDir());

                $event->setSourceFilepath($sourceFilepath);
                $event->setCacheSubdirectory($cacheSubdirectory);

                /**
                 * Get real size image link
                 */
                $dispatcher = $this->container->get('event_dispatcher');
                // Dispatch image processing event
                $dispatcher->dispatch(TheliaEvents::IMAGE_PROCESS, $event);
                $row["url_image"] = $this->formatUrl($event->getFileUrl());

                /**
                 * Get small size image link
                 */
                $eventSmallImage->setSourceFilepath($sourceFilepath);
                $eventSmallImage->setCacheSubdirectory($cacheSubdirectory);

                $dispatcher->dispatch(TheliaEvents::IMAGE_PROCESS, $eventSmallImage);
                $row["url_image_small"] = $this->formatUrl($eventSmallImage->getFileUrl());
            }

            /**
             * Add pse_related_columns
             */
            $row["is_new"] = null;
            $row["price"] = null;
            $row["postage"] = null;
            $row["crossed_out_price"] = null;
            $row["stock"] = null;
            $row["promo"] = null;
            $row["ean"] = null;

            /**
             * Get everything about product sale elements
             */
            $pseRows = [];
            while ($productSaleElement !== null && $productSaleElement->getProductId() == $product->getId()) {
                $pseRow = $row;

                $pseRow["id"] .= "_" . $productSaleElement->getId();
                $pseRow["ref"] = $productSaleElement->getRef();
                $pseRow["ean"] = $productSaleElement->getEanCode();
                $pseRow["is_parent"] = "0";
                $pseRow["is_new"] = $productSaleElement->getNewness();
                $prices = $productSaleElement->getPricesByCurrency($defaultCurrency);
                $calculator->load($product, $shopCountry);
                $promoPrice = $calculator->getTaxedPrice($prices->getPromoPrice());
                $price = $calculator->getTaxedPrice($prices->getPrice());

                $pseRow["price"] = round($productSaleElement->getPromo() ? $promoPrice : $price, 2);

                /**
                 * Delivery postage and prices
                 */
                $pseRow["postage"] = $pseRow["price"] >= $freeDeliveryAmount ? "0" : $deliveryPrice;
                $pseRow["crossed_out_price"] = $productSaleElement->getPromo() ? round($price, 2) : null;

                $pseRow["stock"] = $productSaleElement->getQuantity();

                $pseRow["promo"] = (int) $productSaleElement->getPromo();

                /**
                 * Set Attributes / Attribute av
                 */
                if (isset($attributes["data"][$product->getId()]["pse"][$productSaleElement->getId()])) {
                    $attributeRow = $attributes["data"][$product->getId()]["pse"][$productSaleElement->getId()];

                    for ($i = 0; $i < count($attributeRow["attributes"]); ++$i) {
                        $pseRow[$attributeRow["attributes"][$i]] = $attributeRow["attributes_av"][$i];
                    }
                }

                $pseRows[] = $pseRow;
                $productSaleElement = $productSaleElements->pop();
            }

            $dataSet[] = $row;

            foreach ($pseRows as $pseRow) {
                $dataSet[] = $pseRow;
            }
        }

        return $dataSet;
    }

    protected function fetch(ArrayCollection $col, &$ids)
    {
        $objCol = [];
        $ids = [];

        foreach ($col as $array) {
            $obj = new Product();

            $ids[] = $array["Id"];
            $obj->setId($array["Id"]);
            $obj->setRef($array["Ref"]);
            $obj->setBrandId($array["BrandId"]);
            $obj->setVirtualColumn("brand_TITLE", $array["brand_TITLE"]);
            $obj->setUpdatedAt($array["UpdatedAt"]);
            $obj->setTaxRuleId($array["TaxRuleId"]);

            $obj->setVirtualColumn("product_TITLE", $array["product_TITLE"]);
            $obj->setVirtualColumn("product_DESCRIPTION", $array["product_DESCRIPTION"]);
            $obj->setVirtualColumn("rubric_TITLE", $array["rubric_TITLE"]);
            $obj->setVirtualColumn("category_ID", $array["category_ID"]);

            $obj->setNew(false);
            $objCol[] = $obj;
        }

        return $objCol;
    }

    public function generateTree($locale)
    {
        /**
         * Get Ids and Titles
         */
        $CategoriesQuery = CategoryQuery::create()
            ->useCategoryI18nQuery()
                ->addAsColumn("category_TITLE", CategoryI18nTableMap::TITLE)
            ->endUse()
            ->addAsColumn("parent", CategoryTableMap::PARENT)
            ->addAsColumn("id", CategoryTableMap::ID)
            ->select([
                "id",
                "category_TITLE",
                "parent",
            ])
        ;

        I18n::addI18nCondition(
            $CategoriesQuery,
            CategoryI18nTableMap::TABLE_NAME,
            CategoryTableMap::ID,
            CategoryI18nTableMap::ID,
            CategoryI18nTableMap::LOCALE,
            $locale
        );

        /** @var  $topTitles */
        $rawCategoriesTree = $CategoriesQuery->find();

        /**
         * Format the arrays correctly
         */
        $ids = [];
        $titles = [];
        foreach ($rawCategoriesTree as $idParent) {
            $ids[$idParent["id"]] = (int) $idParent["parent"];
            $titles[$idParent["id"]] = $idParent["category_TITLE"];
        }

        /**
         * Then build the breadcrumbs
         */
        $breadcrumbs = [];

        foreach ($ids as $id => $parent) {
            $breadcrumbTable = [];

            $parentBreadcrumb = null;
            $parents = [];

            /**
             * Build parent tree or get the parent breadcrumb if exists
             */
            while ($parent !== 0) {
                if (!isset($breadcrumbs[$parent])) {
                    array_unshift($parents, $parent);
                    $parent = $ids[$parent];
                } else {
                    $parentBreadcrumb = $breadcrumbs[$parent];
                    break;
                }
            }

            /**
             * Build the parents breadcrumbs if needed
             */
            while (null !== $parentId = array_pop($parents)) {
                $buildArray =  [];
                if (null !== $parentBreadcrumb) {
                    array_push($buildArray, $parentBreadcrumb);
                }

                array_push($buildArray, $titles[$parentId]);

                $parentBreadcrumb = implode(" > ", $buildArray);
                $breadcrumbs[$parentId] = $parentBreadcrumb;
            }

            if (null !== $parentBreadcrumb) {
                array_push($breadcrumbTable, $parentBreadcrumb);
            }

            array_push($breadcrumbTable, $titles[$id]);
            $breadcrumbs[$id] = implode(" > ", $breadcrumbTable);
        }

        return $breadcrumbs;
    }

    protected function getAttributesTable(ObjectCollection $productSaleElements, $locale)
    {
        // Include allowed attributes
        $allowedAttributes = LengowIncludeAttributeQuery::create()->select([LengowIncludeAttributeTableMap::ATTRIBUTE_ID])->find()->toArray();

        $attributesQuery = AttributeCombinationQuery::create()
            ->filterByProductSaleElements($productSaleElements)
            ->useAttributeAvQuery(null, Criteria::LEFT_JOIN)
                ->useAttributeAvI18nQuery()
                    ->addAsColumn("attribute_av", AttributeAvI18nTableMap::TITLE)
                ->endUse()
            ->endUse()
            ->useAttributeQuery(null, Criteria::LEFT_JOIN)
                ->_if(!empty($allowedAttributes))
                    ->filterById($allowedAttributes, Criteria::IN)
                ->_endif()
                ->orderByPosition()
                ->useAttributeI18nQuery()
                    ->addAsColumn("attribute", AttributeI18nTableMap::TITLE)
                ->endUse()
            ->endUse()
            ->useProductSaleElementsQuery()
                ->addAsColumn("product_id", ProductSaleElementsTableMap::PRODUCT_ID)
            ->endUse()
            ->select([
                AttributeCombinationTableMap::PRODUCT_SALE_ELEMENTS_ID,
                "product_id",
                "attribute",
                "attribute_av",
            ])
        ;

        I18n::addI18nCondition(
            $attributesQuery,
            AttributeAvI18nTableMap::TABLE_NAME,
            AttributeAvTableMap::ID,
            AttributeAvI18nTableMap::ID,
            AttributeAvI18nTableMap::LOCALE,
            $locale
        );

        I18n::addI18nCondition(
            $attributesQuery,
            AttributeI18nTableMap::TABLE_NAME,
            AttributeTableMap::ID,
            AttributeI18nTableMap::ID,
            AttributeI18nTableMap::LOCALE,
            $locale
        );

        $attributes = $attributesQuery
            ->find()
            ->toArray()
        ;

        $formattedTable = [];
        $data = [];
        $formattedTable["data"] = &$data;
        $formattedTable["attributes"] = [];

        foreach ($attributes as $attribute) {
            $productId = $attribute["product_id"];
            $pseId = $attribute[AttributeCombinationTableMap::PRODUCT_SALE_ELEMENTS_ID];

            if (!isset($data[$productId])) {
                $data[$productId] = [
                    "pse" => [],
                    "attributes" => [],
                ];
            }

            if (!isset($data[$productId]["pse"][$pseId])) {
                $data[$productId]["pse"][$pseId] = [
                    "attributes" => [],
                    "attributes_av" => [],
                ];
            }

            $row = &$data[$productId];

            if (!in_array($attribute["attribute"], $formattedTable["attributes"])) {
                $formattedTable["attributes"][] = $attribute["attribute"];
            }

            if (!in_array($attribute["attribute"], $row["attributes"])) {
                $row["attributes"][] = $attribute["attribute"];
            }

            $pseRow = &$row["pse"][$pseId];
            $pseRow["attributes"][] = $attribute["attribute"];
            $pseRow["attributes_av"][] = $attribute["attribute_av"];
        }

        return $formattedTable;
    }

    public function getOrder()
    {
        return [
            "id",
            "parent_id",
            "is_parent",
            "ref",
            "title",
            "description",
            "breadcrumb",
            "brand",
            "updated_at",
            "url",
            "url_image",
            "url_image_small",
            "ean",
            "price",
            "crossed_out_price",
            "currency",
            "promo",
            "stock",
            "postage",
            "is_new",
            "attributes",
            // Then the attributes
        ];
    }

    /**
     * Remove accents from URL
     * @param string $url
     * @return string
     */
    private function formatUrl($url)
    {
        return iconv('UTF-8', 'ASCII//TRANSLIT', $url);
    }
}
