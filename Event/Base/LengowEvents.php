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

namespace Lengow\Event\Base;

/**
 * Class LengowEvents
 * @package Lengow\Event\Base
 * @author TheliaStudio
 */
class LengowEvents
{
    const LENGOW_EXCLUDE_CATEGORY_CREATE = "action.lengow_exclude_category.create";
    const LENGOW_EXCLUDE_CATEGORY_UPDATE = "action.lengow_exclude_category.update";
    const LENGOW_EXCLUDE_CATEGORY_DELETE = "action.lengow_exclude_category.delete";
    const LENGOW_EXCLUDE_BRAND_CREATE = "action.lengow_exclude_brand.create";
    const LENGOW_EXCLUDE_BRAND_UPDATE = "action.lengow_exclude_brand.update";
    const LENGOW_EXCLUDE_BRAND_DELETE = "action.lengow_exclude_brand.delete";
    const LENGOW_EXCLUDE_PRODUCT_CREATE = "action.lengow_exclude_product.create";
    const LENGOW_EXCLUDE_PRODUCT_UPDATE = "action.lengow_exclude_product.update";
    const LENGOW_EXCLUDE_PRODUCT_DELETE = "action.lengow_exclude_product.delete";
    const LENGOW_INCLUDE_ATTRIBUTE_CREATE = "action.lengow_include_attribute.create";
    const LENGOW_INCLUDE_ATTRIBUTE_UPDATE = "action.lengow_include_attribute.update";
    const LENGOW_INCLUDE_ATTRIBUTE_DELETE = "action.lengow_include_attribute.delete";
}
