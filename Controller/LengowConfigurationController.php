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
            ConfigQuery::write("lengow_category_exclude", $boundForm->get("exclude-categories-ids")->getData());
            ConfigQuery::write("lengow_free_delivery_price", $boundForm->get("free-shipping-amount")->getData());
            ConfigQuery::write("lengow_delivery_price", $boundForm->get("delivery-price")->getData());
            ConfigQuery::write("lengow_allowed_attributes_id", $boundForm->get("allowed-attributes-ids")->getData());

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
                "Configuration successfully saved"
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
}
