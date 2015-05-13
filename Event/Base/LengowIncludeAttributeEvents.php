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

use Lengow\Event\LengowEvents as ChildLengowEvents;

/*
 * Class LengowIncludeAttributeEvents
 * @package Lengow\Event\Base
 * @author TheliaStudio
 */
class LengowIncludeAttributeEvents
{
    const CREATE = ChildLengowEvents::LENGOW_INCLUDE_ATTRIBUTE_CREATE;
    const UPDATE = ChildLengowEvents::LENGOW_INCLUDE_ATTRIBUTE_UPDATE;
    const DELETE = ChildLengowEvents::LENGOW_INCLUDE_ATTRIBUTE_DELETE;
}
