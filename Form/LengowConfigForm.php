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
use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\ExecutionContextInterface;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;
use Thelia\Model\AttributeQuery;
use Thelia\Model\CategoryQuery;
use Thelia\Model\ConfigQuery;
use Thelia\Model\Map\AttributeTableMap;
use Thelia\Model\Map\CategoryTableMap;

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
        $translator = Translator::getInstance();

        $this->formBuilder
            ->add("min-stock", "number", array(
                "label" => $translator->trans("Minimum available product sale element", [], Lengow::MESSAGE_DOMAIN),
                "label_attr" =>  ["for" => "min-stock"],
                "required" => true,
                "constraints" => array(
                    new GreaterThanOrEqual(["value" => 0]),
                ),
                "data" => ConfigQuery::read("lengow_min_quantity_export", 0),
            ))
            ->add("delivery-price", "number", array(
                "label" => $translator->trans("Delivery price", [], Lengow::MESSAGE_DOMAIN),
                "label_attr" =>  ["for" => "delivery-price"],
                "required" => true,
                "constraints" => array(
                    new GreaterThanOrEqual(["value" => 0]),
                ),
                "data" => ConfigQuery::read("lengow_delivery_price", 0),
            ))
            ->add("free-shipping-amount", "number", array(
                "label" => $translator->trans("Product's price for free shipping", [], Lengow::MESSAGE_DOMAIN),
                "label_attr" =>  ["for" => "free-shipping-amount"],
                "required" => false,
                "constraints" => array(
                    new GreaterThanOrEqual(["value" => 0]),
                ),
                "data" => ConfigQuery::read("lengow_free_delivery_price", 0),
            ))
            ->add("front-cache-time", "integer", array(
                "label" => $translator->trans("Cache time for front controller (in seconds)", [], Lengow::MESSAGE_DOMAIN),
                "label_attr" =>  ["for" => "front-cache-time"],
                "required" => false,
                "constraints" => array(
                    new GreaterThanOrEqual(["value" => 0]),
                ),
                "data" => ConfigQuery::read("lengow_cache_time", 3600),
            ))
            ->add("allowed-attributes-ids", "text", array(
                "label" => $translator->trans("Allowed attributes ids (separated by comas)", [], Lengow::MESSAGE_DOMAIN),
                "label_attr" =>  ["for" => "allowed-attributes-ids"],
                "required" => false,
                "constraints" => [
                    new Callback(
                        [
                            "methods" => [
                                [$this, "checkAttributes"]
                            ]
                        ]
                    )
                ],
                "data" => ConfigQuery::read("lengow_allowed_attributes_id"),
            ))
            ->add("exclude-categories-ids", "text", array(
                "label" => $translator->trans("Categories ids to exclude from the export (separated by comas)", [], Lengow::MESSAGE_DOMAIN),
                "label_attr" =>  ["for" => "exclude-categories-ids"],
                "required" => false,
                "constraints" => [
                    new Callback(
                        [
                            "methods" => [
                                [$this, "checkCategories"]
                            ]
                        ]
                    )
                ],
                "data" => ConfigQuery::read("lengow_category_exclude"),
            ))
        ;
    }

    public function checkCategories($value, ExecutionContextInterface $context)
    {
        $value = str_replace("#\s#", "", $value);

        if (!empty($value)) {
            if (!preg_match("#^(\d+,)*\d+$#", $value)) {
                $context->addViolation(
                    Translator::getInstance()->trans(
                        "This field is not valid, it must be like '1,2,3'"
                    )
                );
            } else {
                $ids = explode(",", $value);

                $existingIds = CategoryQuery::create()
                    ->filterById($ids, Criteria::IN)
                    ->select(CategoryTableMap::ID)
                    ->find()
                    ->toArray()
                ;

                $notExistingIds = array_diff($ids, $existingIds);

                if (!empty($notExistingIds)) {
                    $context->addViolation(
                        Translator::getInstance()->trans(
                            "This category ids %ids doesn't exist",
                            [
                                "%ids" => implode(", ", $notExistingIds)
                            ]
                        )
                    );
                }
            }
        }
    }

    public function checkAttributes($value, ExecutionContextInterface $context)
    {
        $value = str_replace("#\s#", "", $value);

        if (!empty($value)) {
            if (!preg_match("#^(\d+,)*\d+$#", $value)) {
                $context->addViolation(
                    Translator::getInstance()->trans(
                        "This field is not valid, it must be like '1,2,3'"
                    )
                );
            } else {
                $ids = explode(",", $value);

                $existingIds = AttributeQuery::create()
                    ->filterById($ids, Criteria::IN)
                    ->select(AttributeTableMap::ID)
                    ->find()
                    ->toArray()
                ;

                $notExistingIds = array_diff($ids, $existingIds);

                if (!empty($notExistingIds)) {
                    $context->addViolation(
                        Translator::getInstance()->trans(
                            "This attribute ids %ids doesn't exist",
                            [
                                "%ids" => implode(", ", $notExistingIds)
                            ]
                        )
                    );
                }
            }
        }
    }

    /**
     * @return string the name of you form. This name must be unique
     */
    public function getName()
    {
        return "lengow-configuration-form";
    }
}
