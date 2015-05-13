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

use Lengow\Form\Base\LengowIncludeAttributeUpdateForm as BaseLengowIncludeAttributeUpdateForm;

/**
 * Class LengowIncludeAttributeUpdateForm
 * @package Lengow\Form
 */
class LengowIncludeAttributeUpdateForm extends BaseLengowIncludeAttributeUpdateForm
{
    public function getTranslationKeys()
    {
        return array(
            "id" => "id",
            "attribute_id" => "attribute_id",
        );
    }
}
