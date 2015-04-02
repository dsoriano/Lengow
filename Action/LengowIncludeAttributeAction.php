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

use Lengow\Action\Base\LengowIncludeAttributeAction as  BaseLengowIncludeAttributeAction;
use Lengow\Event\LengowIncludeAttributeEvent;
use Lengow\Event\LengowIncludeAttributeEvents;
use Lengow\Model\LengowIncludeAttributeQuery;

/**
 * Class LengowIncludeAttributeAction
 * @package Lengow\Action
 */
class LengowIncludeAttributeAction extends BaseLengowIncludeAttributeAction
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
        $subscribedEvents[LengowIncludeAttributeEvents::DELETE_ALL] = array('deleteAll', 128);

        return $subscribedEvents;
    }

    /**
     * Deletes all the attributes IDs to include
     * @param LengowIncludeAttributeEvent $event
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function deleteAll(LengowIncludeAttributeEvent $event)
    {
        LengowIncludeAttributeQuery::create()->deleteAll();
    }
}
