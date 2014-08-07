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
use Lengow\Tools\Session;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Thelia\Core\Event\Image\ImageEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Translation\Translator;
use Thelia\ImportExport\Export\ExportHandler;
use Thelia\Model\Cart;
use Thelia\Model\CartItem;
use Thelia\Model\CategoryQuery;
use Thelia\Model\Country;
use Thelia\Model\Currency;
use Thelia\Model\Lang;
use Thelia\Model\ModuleQuery;
use Thelia\Model\ProductQuery;
use Thelia\Model\ProductSaleElements;
use Thelia\Model\ProductSaleElementsQuery;
use Thelia\Module\BaseModule;
use Thelia\Module\DeliveryModuleInterface;
use Thelia\Module\Exception\DeliveryException;
use Thelia\TaxEngine\Calculator;

/**
 * Class LengowExport
 * @package Lengow\Export
 * @author Benjamin Perche <bperche@openstudio.fr>
 */
class LengowExport extends ExportHandler
{
    /** @var  Container */
    protected $fakeContainer;

    /** @var  Session */
    protected $session;

    /** @var Cart */
    protected $cart;

    /** @var  \Thelia\Model\Module[] */
    protected $deliveryModules;

    /** @var BaseModule[] */
    protected $moduleInstances = array();

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        /**
         * Define tools to emulate a cart for delivery postage computing
         */
        $this->fakeContainer = new Container();
        $request = new Request();
        $this->session = new Session();

        $request->setSession($this->session);
        $this->container->set('request', $request);

        /**
         * Get delivery modules
         */
        $this->deliveryModules = ModuleQuery::create()
            ->filterByActivate(1)
            ->filterByType(BaseModule::DELIVERY_MODULE_TYPE, Criteria::EQUAL)
            ->find()
        ;

        foreach ($this->deliveryModules as $deliveryModule) {
            $this->moduleInstances[] = $moduleInstance = $deliveryModule->getModuleInstance($this->container);
        }

    }


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
     * @param  Lang $lang
     * @return ModelCriteria|array|BaseLoop
     */
    public function buildDataSet(Lang $lang)
    {
        $shopCountry = Country::getShopLocation();
        $defaultCurrency = Currency::getDefaultCurrency();
        $defaultCurrencyCode = $defaultCurrency->getCode();

        $event = new ImageEvent($this->getRequest());
        $locale = $lang->getLocale();

        $calculator = new Calculator();

        $pses = ProductSaleElementsQuery::create()
            ->orderByProductId()
            ->find()
        ;

        $products = ProductQuery::create()
            ->orderById(Criteria::DESC)
            ->find()
        ;

        /** @var \Thelia\Model\Product $product */
        $product = null;
        $title = null;
        $description = null;
        $categoriesCache = [];

        $featureCache = [];
        $featureAvCache = [];
        $attributesCache = [];
        $attributesAvCache = [];

        $dataSet = [];

        /** @var \Thelia\Model\ProductSaleElements $pse */
        foreach ($pses as $pse) {
            if ($product === null || $product->getId() !== $pse->getProductId()) {
                $product = $products->pop();

                if ($product->getId() !== $pse->getProductId()) {
                    throw new \LogicException(
                        Translator::getInstance()->trans(
                            "The product id can't be different on product sale elements. Please check your sql foreign key constraints"
                        )
                    );
                }

                $product->setLocale($locale);

                $title = $product->getTitle();
                $description = $product->getDescription();

                if (!isset($categoriesCache[$product->getDefaultCategoryId()])) {
                    $categoriesCache[$product->getDefaultCategoryId()] = CategoryQuery::create()
                        ->findPk($product->getDefaultCategoryId())
                        ->setLocale($locale)
                        ->getTitle()
                    ;
                }
            }

            $row = [];

            $row["id"] = $pse->getId();

            /**
             * Add product's title, description and category
             */
            $row["title"] = $title;
            $row["description"] = $description;
            $row["category"] = $categoriesCache[$product->getDefaultCategoryId()];

            $row["postage"] = $this->getPostage($pse, $shopCountry);

            /**
             * Compute Attributes
             */
            $attributes = [];
            foreach ($pse->getAttributeCombinations() as $attributeCombination) {
                $attributeId = $attributeCombination->getAttributeId();
                if (!array_key_exists($attributeId, $attributesCache)) {
                    $attributesCache[$attributeId] = $this->escape(
                        $attributeCombination
                            ->getAttribute()
                            ->setLocale($locale)
                            ->getTitle()
                    );
                }

                $attributeAvId = $attributeCombination->getAttributeAvId();
                if (!array_key_exists($attributeAvId, $attributesAvCache)) {
                    $attributesAvCache[$attributeAvId] = $this->escape(
                        $attributeCombination
                            ->getAttributeAv()
                            ->setLocale($locale)
                            ->getTitle()
                    );
                }

                $attributes[] = $attributesCache[$attributeId] . ":" . $attributesAvCache[$attributeAvId];
            }

            $row["attributes"] = implode(",", $attributes);

            /**
             * Get product Taxed price
             */
            $prices = $pse->getPricesByCurrency($defaultCurrency);
            $calculator->load($product, $shopCountry);
            $promoPrice = $calculator->getTaxedPrice($prices->getPromoPrice());
            $price = $calculator->getTaxedPrice($prices->getPrice());


            $row["price"] = $pse->getPromo() ? $promoPrice : $price;
            $row["crossed_out_price"] = $pse->getPromo() ? $price : null;

            $row["stock"] = $pse->getQuantity();
            $row["url"] = $pse->getProduct()->getUrl($locale);
            $row["weight"] = $pse->getWeight(); // Kg
            $row["currency"] = $defaultCurrencyCode;

            /**
             * Compute the image url
             */
            $row["url_image"] = null;
            $images = $product->getProductImages();

            if ($images->count() > 0) {
                /** @var \Thelia\Model\ProductImage $image */
                $image = $images->get(0);

                $event->setSourceFilepath($image->getUploadDir() . DS . $image->getFile());
                $event->setCacheSubdirectory(basename($image->getUploadDir()));

                $dispatcher = $this->container->get('event_dispatcher');
                // Dispatch image processing event
                $dispatcher->dispatch(TheliaEvents::IMAGE_PROCESS, $event);
                $row["url_image"] = $event->getFileUrl();
            }

            $dataSet[] = $row;

        }

        return $dataSet;
    }

    public function escape($value)
    {
        $value =  str_replace(":", " ", $value);
        $value = str_replace(",", " ", $value);

        return $value;
    }

    /**
     * @param $weight
     * @param Country $shopCountry
     * @throws \RuntimeException
     * @throws DeliveryException
     *
     * This method is similar to Thelia Smarty plugin CartPostage
     */
    public function getPostage(ProductSaleElements $pse, Country $shopCountry)
    {
        $postage = null;

        for ($i = 0; array_key_exists($i, $this->deliveryModules); ++$i) {

            $deliveryModule = $this->deliveryModules[$i];
            $moduleInstance = $this->moduleInstances[$i];

            if (false === $moduleInstance instanceof DeliveryModuleInterface) {
                throw new \RuntimeException(sprintf("delivery module %s is not a Thelia\Module\DeliveryModuleInterface", $deliveryModule->getCode()));
            }

            try {
                // Check if module is valid, by calling isValidDelivery(),
                // or catching a DeliveryException.
                if ($moduleInstance->isValidDelivery($shopCountry)) {
                    $this->setProductIntoCart($pse);
                    $postage = $moduleInstance->getPostage($shopCountry);
                }
            } catch (DeliveryException $e) {
                // Module is not available
            }
        }

        return $postage;
    }

    public function setProductIntoCart(ProductSaleElements $pse)
    {
        $this->cart = new Cart();
        $this->session->setCart($this->cart);

        $cartItem = new CartItem();
        $cartItem->setProductSaleElements($pse);
        $cartItem->setQuantity(1);

        $this->cart->addCartItem($cartItem);
    }
}