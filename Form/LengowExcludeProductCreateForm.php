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

use Lengow\Form\Base\LengowExcludeProductCreateForm as BaseLengowExcludeProductCreateForm;

/**
 * Class LengowExcludeProductCreateForm
 * @package Lengow\Form
 */
class LengowExcludeProductCreateForm extends BaseLengowExcludeProductCreateForm
{
    public function getTranslationKeys()
    {
        return array(
            "product_id" => "Product id",
        );
    }
}
