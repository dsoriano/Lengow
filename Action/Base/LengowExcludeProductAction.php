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

use Lengow\Model\Map\LengowExcludeProductTableMap;
use Lengow\Event\LengowExcludeProductEvent;
use Lengow\Event\LengowExcludeProductEvents;
use Lengow\Model\LengowExcludeProductQuery;
use Lengow\Model\LengowExcludeProduct;
use Thelia\Action\BaseAction;
use Thelia\Core\Event\ToggleVisibilityEvent;
use Thelia\Core\Event\UpdatePositionEvent;
use Propel\Runtime\Propel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\TheliaEvents;
use \Thelia\Core\Event\TheliaFormEvent;

/**
 * Class LengowExcludeProductAction
 * @package Lengow\Action
 * @author TheliaStudio
 */
class LengowExcludeProductAction extends BaseAction implements EventSubscriberInterface
{
    public function create(LengowExcludeProductEvent $event)
    {
        $this->createOrUpdate($event, new LengowExcludeProduct());
    }

    public function update(LengowExcludeProductEvent $event)
    {
        $model = $this->getLengowExcludeProduct($event);

        $this->createOrUpdate($event, $model);
    }

    public function delete(LengowExcludeProductEvent $event)
    {
        $this->getLengowExcludeProduct($event)->delete();
    }

    protected function createOrUpdate(LengowExcludeProductEvent $event, LengowExcludeProduct $model)
    {
        $con = Propel::getConnection(LengowExcludeProductTableMap::DATABASE_NAME);
        $con->beginTransaction();

        try {
            if (null !== $id = $event->getId()) {
                $model->setId($id);
            }

            if (null !== $productId = $event->getProductId()) {
                $model->setProductId($productId);
            }

            $model->save($con);

            $con->commit();
        } catch (\Exception $e) {
            $con->rollback();

            throw $e;
        }

        $event->setLengowExcludeProduct($model);
    }

    protected function getLengowExcludeProduct(LengowExcludeProductEvent $event)
    {
        $model = LengowExcludeProductQuery::create()->findPk($event->getId());

        if (null === $model) {
            throw new \RuntimeException(sprintf(
                "The 'lengow_exclude_product' id '%d' doesn't exist",
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
            LengowExcludeProductEvents::CREATE => array("create", 128),
            LengowExcludeProductEvents::UPDATE => array("update", 128),
            LengowExcludeProductEvents::DELETE => array("delete", 128),
            TheliaEvents::FORM_BEFORE_BUILD . ".lengow_exclude_product_create" => array("beforeCreateFormBuild", 128),
            TheliaEvents::FORM_BEFORE_BUILD . ".lengow_exclude_product_update" => array("beforeUpdateFormBuild", 128),
            TheliaEvents::FORM_AFTER_BUILD . ".lengow_exclude_product_create" => array("afterCreateFormBuild", 128),
            TheliaEvents::FORM_AFTER_BUILD . ".lengow_exclude_product_update" => array("afterUpdateFormBuild", 128),
        );
    }
}
