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

use Lengow\Event\Base\LengowIncludeAttributeEvents as BaseLengowIncludeAttributeEvents;

/**
 * Class LengowIncludeAttributeEvents
 * @package Lengow\Event
 */
class LengowIncludeAttributeEvents extends BaseLengowIncludeAttributeEvents
{
    const DELETE_ALL = LengowEvents::LENGOW_INCLUDE_ATTRIBUTE_DELETE_ALL;
}
