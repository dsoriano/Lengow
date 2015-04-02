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

namespace Lengow\Event;

use Lengow\Event\Base\LengowEvents as BaseLengowEvents;

/**
 * Class LengowEvents
 * @package Lengow\Event
 * @author TheliaStudio
 */
class LengowEvents extends BaseLengowEvents
{
    const LENGOW_EXCLUDE_CATEGORY_DELETE_ALL = 'action.lengow_exclude_category.delete_all';
    const LENGOW_EXCLUDE_BRAND_DELETE_ALL = 'action.lengow_exclude_brand.delete_all';
    const LENGOW_EXCLUDE_PRODUCT_DELETE_ALL = 'action.lengow_exclude_product.delete_all';
    const LENGOW_INCLUDE_ATTRIBUTE_DELETE_ALL = 'action.lengow_include_attribute.delete_all';
}
