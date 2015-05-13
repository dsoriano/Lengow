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

use Lengow\Form\Base\LengowExcludeCategoryUpdateForm as BaseLengowExcludeCategoryUpdateForm;

/**
 * Class LengowExcludeCategoryUpdateForm
 * @package Lengow\Form
 */
class LengowExcludeCategoryUpdateForm extends BaseLengowExcludeCategoryUpdateForm
{
    public function getTranslationKeys()
    {
        return array(
            "id" => "id",
            "category_id" => "category_id",
        );
    }
}
