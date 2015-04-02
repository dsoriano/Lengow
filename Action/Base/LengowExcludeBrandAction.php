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

use Lengow\Model\Map\LengowExcludeBrandTableMap;
use Lengow\Event\LengowExcludeBrandEvent;
use Lengow\Event\LengowExcludeBrandEvents;
use Lengow\Model\LengowExcludeBrandQuery;
use Lengow\Model\LengowExcludeBrand;
use Thelia\Action\BaseAction;
use Thelia\Core\Event\ToggleVisibilityEvent;
use Thelia\Core\Event\UpdatePositionEvent;
use Propel\Runtime\Propel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\TheliaEvents;
use \Thelia\Core\Event\TheliaFormEvent;

/**
 * Class LengowExcludeBrandAction
 * @package Lengow\Action
 * @author TheliaStudio
 */
class LengowExcludeBrandAction extends BaseAction implements EventSubscriberInterface
{
    public function create(LengowExcludeBrandEvent $event)
    {
        $this->createOrUpdate($event, new LengowExcludeBrand());
    }

    public function update(LengowExcludeBrandEvent $event)
    {
        $model = $this->getLengowExcludeBrand($event);

        $this->createOrUpdate($event, $model);
    }

    public function delete(LengowExcludeBrandEvent $event)
    {
        $this->getLengowExcludeBrand($event)->delete();
    }

    protected function createOrUpdate(LengowExcludeBrandEvent $event, LengowExcludeBrand $model)
    {
        $con = Propel::getConnection(LengowExcludeBrandTableMap::DATABASE_NAME);
        $con->beginTransaction();

        try {
            if (null !== $id = $event->getId()) {
                $model->setId($id);
            }

            if (null !== $brandId = $event->getBrandId()) {
                $model->setBrandId($brandId);
            }

            $model->save($con);

            $con->commit();
        } catch (\Exception $e) {
            $con->rollback();

            throw $e;
        }

        $event->setLengowExcludeBrand($model);
    }

    protected function getLengowExcludeBrand(LengowExcludeBrandEvent $event)
    {
        $model = LengowExcludeBrandQuery::create()->findPk($event->getId());

        if (null === $model) {
            throw new \RuntimeException(sprintf(
                "The 'lengow_exclude_brand' id '%d' doesn't exist",
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
            LengowExcludeBrandEvents::CREATE => array("create", 128),
            LengowExcludeBrandEvents::UPDATE => array("update", 128),
            LengowExcludeBrandEvents::DELETE => array("delete", 128),
            TheliaEvents::FORM_BEFORE_BUILD . ".lengow_exclude_brand_create" => array("beforeCreateFormBuild", 128),
            TheliaEvents::FORM_BEFORE_BUILD . ".lengow_exclude_brand_update" => array("beforeUpdateFormBuild", 128),
            TheliaEvents::FORM_AFTER_BUILD . ".lengow_exclude_brand_create" => array("afterCreateFormBuild", 128),
            TheliaEvents::FORM_AFTER_BUILD . ".lengow_exclude_brand_update" => array("afterUpdateFormBuild", 128),
        );
    }
}
