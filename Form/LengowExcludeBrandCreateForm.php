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

use Lengow\Form\Base\LengowExcludeBrandCreateForm as BaseLengowExcludeBrandCreateForm;

/**
 * Class LengowExcludeBrandCreateForm
 * @package Lengow\Form
 */
class LengowExcludeBrandCreateForm extends BaseLengowExcludeBrandCreateForm
{
    public function getTranslationKeys()
    {
        return array(
            "brand_id" => "Brand id",
        );
    }
}
