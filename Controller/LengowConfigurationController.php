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
use Symfony\Component\Form\Form;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Model\ConfigQuery;

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
        LengowIncludeAttributeQuery::create()->deleteAll();

        foreach ($boundForm->get('allowed-attributes-ids')->getData() as $id) {
            $lengowAttribute = new LengowIncludeAttribute();
            $lengowAttribute->setAttributeId($id);
            $lengowAttribute->save();
        }

        // Brands
        LengowExcludeBrandQuery::create()->deleteAll();

        foreach ($boundForm->get('exclude-brands-ids')->getData() as $id) {
            $lengowBrand = new LengowExcludeBrand();
            $lengowBrand->setBrandId($id);
            $lengowBrand->save();
        }

        // Categories
        LengowExcludeCategoryQuery::create()->deleteAll();

        foreach ($boundForm->get('exclude-categories-ids')->getData() as $id) {
            $lengowCategory = new LengowExcludeCategory();
            $lengowCategory->setCategoryId($id);
            $lengowCategory->save();
        }

        // Products
        LengowExcludeProductQuery::create()->deleteAll();

        foreach ($boundForm->get('exclude-products-ids')->getData() as $id) {
            $lengowProduct = new LengowExcludeProduct();
            $lengowProduct->setProductId($id);
            $lengowProduct->save();
        }
    }
}
