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

use Lengow\Form\LengowIncludeAttributeCreateForm as ChildLengowIncludeAttributeCreateForm;
use Lengow\Form\Type\LengowIncludeAttributeIdType;

/**
 * Class LengowIncludeAttributeForm
 * @package Lengow\Form
 * @author TheliaStudio
 */
class LengowIncludeAttributeUpdateForm extends ChildLengowIncludeAttributeCreateForm
{
    const FORM_NAME = "lengow_include_attribute_update";

    public function buildForm()
    {
        parent::buildForm();

        $this->formBuilder
            ->add("id", LengowIncludeAttributeIdType::TYPE_NAME)
        ;
    }
}
