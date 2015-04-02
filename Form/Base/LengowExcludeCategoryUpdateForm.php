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

use Lengow\Form\LengowExcludeCategoryCreateForm as ChildLengowExcludeCategoryCreateForm;
use Lengow\Form\Type\LengowExcludeCategoryIdType;

/**
 * Class LengowExcludeCategoryForm
 * @package Lengow\Form
 * @author TheliaStudio
 */
class LengowExcludeCategoryUpdateForm extends ChildLengowExcludeCategoryCreateForm
{
    const FORM_NAME = "lengow_exclude_category_update";

    public function buildForm()
    {
        parent::buildForm();

        $this->formBuilder
            ->add("id", LengowExcludeCategoryIdType::TYPE_NAME)
        ;
    }
}
