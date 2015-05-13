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

namespace Lengow\Form\Type\Base;

use Thelia\Core\Form\Type\Field\AbstractIdType;
use Lengow\Model\LengowIncludeAttributeQuery;

/**
 * Class LengowIncludeAttribute
 * @package Lengow\Form\Base
 * @author TheliaStudio
 */
class LengowIncludeAttributeIdType extends AbstractIdType
{
    const TYPE_NAME = "lengow_include_attribute_id";

    protected function getQuery()
    {
        return new LengowIncludeAttributeQuery();
    }

    public function getName()
    {
        return static::TYPE_NAME;
    }
}
