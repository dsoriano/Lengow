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

namespace Lengow\Form\Base;

use Lengow\Lengow;
use Thelia\Form\BaseForm;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class LengowExcludeBrandCreateForm
 * @package Lengow\Form\Base
 * @author TheliaStudio
 */
class LengowExcludeBrandCreateForm extends BaseForm
{
    const FORM_NAME = "lengow_exclude_brand_create";

    public function buildForm()
    {
        $translationKeys = $this->getTranslationKeys();
        $fieldsIdKeys = $this->getFieldsIdKeys();

        $this->addBrandIdField($translationKeys, $fieldsIdKeys);
    }

    protected function addBrandIdField(array $translationKeys, array $fieldsIdKeys)
    {
        $this->formBuilder->add("brand_id", "integer", array(
            "label" => $this->translator->trans($this->readKey("brand_id", $translationKeys), [], Lengow::MESSAGE_DOMAIN),
            "label_attr" => ["for" => $this->readKey("brand_id", $fieldsIdKeys)],
            "required" => true,
            "constraints" => array(
                new NotBlank(),
            ),
            "attr" => array(
            )
        ));
    }

    public function getName()
    {
        return static::FORM_NAME;
    }

    public function readKey($key, array $keys, $default = '')
    {
        if (isset($keys[$key])) {
            return $keys[$key];
        }

        return $default;
    }

    public function getTranslationKeys()
    {
        return array();
    }

    public function getFieldsIdKeys()
    {
        return array(
            "brand_id" => "lengow_exclude_brand_brand_id",
        );
    }
}
