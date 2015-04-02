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

namespace Lengow\Action;

use Lengow\Action\Base\LengowExcludeProductAction as  BaseLengowExcludeProductAction;
use Lengow\Event\LengowExcludeProductEvent;
use Lengow\Event\LengowExcludeProductEvents;
use Lengow\Model\LengowExcludeProductQuery;

/**
 * Class LengowExcludeProductAction
 * @package Lengow\Action
 */
class LengowExcludeProductAction extends BaseLengowExcludeProductAction
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        $subscribedEvents = parent::getSubscribedEvents();
        $subscribedEvents[LengowExcludeProductEvents::DELETE_ALL] = array('deleteAll', 128);

        return $subscribedEvents;
    }

    /**
     * Deletes all the attributes IDs to include
     * @param LengowExcludeProductEvent $event
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function deleteAll(LengowExcludeProductEvent $event)
    {
        LengowExcludeProductQuery::create()->deleteAll();
    }
}
