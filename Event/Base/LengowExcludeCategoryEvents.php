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
 * Class LengowExcludeCategoryEvents
 * @package Lengow\Event\Base
 * @author TheliaStudio
 */
class LengowExcludeCategoryEvents
{
    const CREATE = ChildLengowEvents::LENGOW_EXCLUDE_CATEGORY_CREATE;
    const UPDATE = ChildLengowEvents::LENGOW_EXCLUDE_CATEGORY_UPDATE;
    const DELETE = ChildLengowEvents::LENGOW_EXCLUDE_CATEGORY_DELETE;
}
