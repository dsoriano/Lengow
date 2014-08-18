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
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

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
            ->add("min-stock", "integer", array(
                "label" => $translator->trans("Minimum available product sale element", [], Lengow::MESSAGE_DOMAIN),
                "label_attr" =>  ["for" => "min-stock"],
                "required" => true,
                "constraints" => array(
                    new GreaterThanOrEqual(["value" => 0]),
                ),
            ))
            ->add("delivery-price", "integer", array(
                "label" => $translator->trans("Delivery price", [], Lengow::MESSAGE_DOMAIN),
                "label_attr" =>  ["for" => "delivery-price"],
                "required" => true,
                "constraints" => array(
                    new GreaterThanOrEqual(["value" => 0]),
                ),
            ))
            ->add("free-shipping-amount", "integer", array(
                "label" => $translator->trans("Product's price for free shipping", [], Lengow::MESSAGE_DOMAIN),
                "label_attr" =>  ["for" => "free-shipping-amount"],
                "required" => false,
                "constraints" => array(
                    new GreaterThanOrEqual(["value" => 0]),
                ),
            ))
            ->add("front-cache-time", "integer", array(
                "label" => $translator->trans("Cache time for front controller (in seconds)", [], Lengow::MESSAGE_DOMAIN),
                "label_attr" =>  ["for" => "front-cache-time"],
                "required" => false,
                "constraints" => array(
                    new GreaterThanOrEqual(["value" => 0]),
                ),
            ))
        ;
    }

    /**
     * @return string the name of you form. This name must be unique
     */
    public function getName()
    {
        return "lengow-configuration-form";
    }

} 