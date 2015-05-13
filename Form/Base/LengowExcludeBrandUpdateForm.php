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

use Lengow\Form\LengowExcludeBrandCreateForm as ChildLengowExcludeBrandCreateForm;
use Lengow\Form\Type\LengowExcludeBrandIdType;

/**
 * Class LengowExcludeBrandForm
 * @package Lengow\Form
 * @author TheliaStudio
 */
class LengowExcludeBrandUpdateForm extends ChildLengowExcludeBrandCreateForm
{
    const FORM_NAME = "lengow_exclude_brand_update";

    public function buildForm()
    {
        parent::buildForm();

        $this->formBuilder
            ->add("id", LengowExcludeBrandIdType::TYPE_NAME)
        ;
    }
}
