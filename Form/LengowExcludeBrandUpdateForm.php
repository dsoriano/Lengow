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

use Lengow\Form\Base\LengowExcludeBrandUpdateForm as BaseLengowExcludeBrandUpdateForm;

/**
 * Class LengowExcludeBrandUpdateForm
 * @package Lengow\Form
 */
class LengowExcludeBrandUpdateForm extends BaseLengowExcludeBrandUpdateForm
{
    public function getTranslationKeys()
    {
        return array(
            "id" => "id",
            "brand_id" => "brand_id",
        );
    }
}
