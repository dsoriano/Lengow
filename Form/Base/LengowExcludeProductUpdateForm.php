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

use Lengow\Form\LengowExcludeProductCreateForm as ChildLengowExcludeProductCreateForm;
use Lengow\Form\Type\LengowExcludeProductIdType;

/**
 * Class LengowExcludeProductForm
 * @package Lengow\Form
 * @author TheliaStudio
 */
class LengowExcludeProductUpdateForm extends ChildLengowExcludeProductCreateForm
{
    const FORM_NAME = "lengow_exclude_product_update";

    public function buildForm()
    {
        parent::buildForm();

        $this->formBuilder
            ->add("id", LengowExcludeProductIdType::TYPE_NAME)
        ;
    }
}
