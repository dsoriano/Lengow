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

use Lengow\Form\Base\LengowExcludeCategoryCreateForm as BaseLengowExcludeCategoryCreateForm;

/**
 * Class LengowExcludeCategoryCreateForm
 * @package Lengow\Form
 */
class LengowExcludeCategoryCreateForm extends BaseLengowExcludeCategoryCreateForm
{
    public function getTranslationKeys()
    {
        return array(
            "category_id" => "Category id",
        );
    }
}
