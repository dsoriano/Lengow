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

use Lengow\Event\Base\LengowExcludeBrandEvents as BaseLengowExcludeBrandEvents;

/**
 * Class LengowExcludeBrandEvents
 * @package Lengow\Event
 */
class LengowExcludeBrandEvents extends BaseLengowExcludeBrandEvents
{
    const DELETE_ALL = LengowEvents::LENGOW_EXCLUDE_BRAND_DELETE_ALL;
}
