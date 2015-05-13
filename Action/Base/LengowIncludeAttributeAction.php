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

namespace Lengow\Action\Base;

use Lengow\Model\Map\LengowIncludeAttributeTableMap;
use Lengow\Event\LengowIncludeAttributeEvent;
use Lengow\Event\LengowIncludeAttributeEvents;
use Lengow\Model\LengowIncludeAttributeQuery;
use Lengow\Model\LengowIncludeAttribute;
use Thelia\Action\BaseAction;
use Thelia\Core\Event\ToggleVisibilityEvent;
use Thelia\Core\Event\UpdatePositionEvent;
use Propel\Runtime\Propel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\TheliaEvents;
use \Thelia\Core\Event\TheliaFormEvent;

/**
 * Class LengowIncludeAttributeAction
 * @package Lengow\Action
 * @author TheliaStudio
 */
class LengowIncludeAttributeAction extends BaseAction implements EventSubscriberInterface
{
    public function create(LengowIncludeAttributeEvent $event)
    {
        $this->createOrUpdate($event, new LengowIncludeAttribute());
    }

    public function update(LengowIncludeAttributeEvent $event)
    {
        $model = $this->getLengowIncludeAttribute($event);

        $this->createOrUpdate($event, $model);
    }

    public function delete(LengowIncludeAttributeEvent $event)
    {
        $this->getLengowIncludeAttribute($event)->delete();
    }

    protected function createOrUpdate(LengowIncludeAttributeEvent $event, LengowIncludeAttribute $model)
    {
        $con = Propel::getConnection(LengowIncludeAttributeTableMap::DATABASE_NAME);
        $con->beginTransaction();

        try {
            if (null !== $id = $event->getId()) {
                $model->setId($id);
            }

            if (null !== $attributeId = $event->getAttributeId()) {
                $model->setAttributeId($attributeId);
            }

            $model->save($con);

            $con->commit();
        } catch (\Exception $e) {
            $con->rollback();

            throw $e;
        }

        $event->setLengowIncludeAttribute($model);
    }

    protected function getLengowIncludeAttribute(LengowIncludeAttributeEvent $event)
    {
        $model = LengowIncludeAttributeQuery::create()->findPk($event->getId());

        if (null === $model) {
            throw new \RuntimeException(sprintf(
                "The 'lengow_include_attribute' id '%d' doesn't exist",
                $event->getId()
            ));
        }

        return $model;
    }

    public function beforeCreateFormBuild(TheliaFormEvent $event)
    {
    }

    public function beforeUpdateFormBuild(TheliaFormEvent $event)
    {
    }

    public function afterCreateFormBuild(TheliaFormEvent $event)
    {
    }

    public function afterUpdateFormBuild(TheliaFormEvent $event)
    {
    }

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
        return array(
            LengowIncludeAttributeEvents::CREATE => array("create", 128),
            LengowIncludeAttributeEvents::UPDATE => array("update", 128),
            LengowIncludeAttributeEvents::DELETE => array("delete", 128),
            TheliaEvents::FORM_BEFORE_BUILD . ".lengow_include_attribute_create" => array("beforeCreateFormBuild", 128),
            TheliaEvents::FORM_BEFORE_BUILD . ".lengow_include_attribute_update" => array("beforeUpdateFormBuild", 128),
            TheliaEvents::FORM_AFTER_BUILD . ".lengow_include_attribute_create" => array("afterCreateFormBuild", 128),
            TheliaEvents::FORM_AFTER_BUILD . ".lengow_include_attribute_update" => array("afterUpdateFormBuild", 128),
        );
    }
}
