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

use Lengow\Form\Base\LengowExcludeProductUpdateForm as BaseLengowExcludeProductUpdateForm;

/**
 * Class LengowExcludeProductUpdateForm
 * @package Lengow\Form
 */
class LengowExcludeProductUpdateForm extends BaseLengowExcludeProductUpdateForm
{
    public function getTranslationKeys()
    {
        return array(
            "id" => "id",
            "product_id" => "product_id",
        );
    }
}
