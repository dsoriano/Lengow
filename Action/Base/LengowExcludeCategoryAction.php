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

use Lengow\Model\Map\LengowExcludeCategoryTableMap;
use Lengow\Event\LengowExcludeCategoryEvent;
use Lengow\Event\LengowExcludeCategoryEvents;
use Lengow\Model\LengowExcludeCategoryQuery;
use Lengow\Model\LengowExcludeCategory;
use Thelia\Action\BaseAction;
use Thelia\Core\Event\ToggleVisibilityEvent;
use Thelia\Core\Event\UpdatePositionEvent;
use Propel\Runtime\Propel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\TheliaEvents;
use \Thelia\Core\Event\TheliaFormEvent;

/**
 * Class LengowExcludeCategoryAction
 * @package Lengow\Action
 * @author TheliaStudio
 */
class LengowExcludeCategoryAction extends BaseAction implements EventSubscriberInterface
{
    public function create(LengowExcludeCategoryEvent $event)
    {
        $this->createOrUpdate($event, new LengowExcludeCategory());
    }

    public function update(LengowExcludeCategoryEvent $event)
    {
        $model = $this->getLengowExcludeCategory($event);

        $this->createOrUpdate($event, $model);
    }

    public function delete(LengowExcludeCategoryEvent $event)
    {
        $this->getLengowExcludeCategory($event)->delete();
    }

    protected function createOrUpdate(LengowExcludeCategoryEvent $event, LengowExcludeCategory $model)
    {
        $con = Propel::getConnection(LengowExcludeCategoryTableMap::DATABASE_NAME);
        $con->beginTransaction();

        try {
            if (null !== $id = $event->getId()) {
                $model->setId($id);
            }

            if (null !== $categoryId = $event->getCategoryId()) {
                $model->setCategoryId($categoryId);
            }

            $model->save($con);

            $con->commit();
        } catch (\Exception $e) {
            $con->rollback();

            throw $e;
        }

        $event->setLengowExcludeCategory($model);
    }

    protected function getLengowExcludeCategory(LengowExcludeCategoryEvent $event)
    {
        $model = LengowExcludeCategoryQuery::create()->findPk($event->getId());

        if (null === $model) {
            throw new \RuntimeException(sprintf(
                "The 'lengow_exclude_category' id '%d' doesn't exist",
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
            LengowExcludeCategoryEvents::CREATE => array("create", 128),
            LengowExcludeCategoryEvents::UPDATE => array("update", 128),
            LengowExcludeCategoryEvents::DELETE => array("delete", 128),
            TheliaEvents::FORM_BEFORE_BUILD . ".lengow_exclude_category_create" => array("beforeCreateFormBuild", 128),
            TheliaEvents::FORM_BEFORE_BUILD . ".lengow_exclude_category_update" => array("beforeUpdateFormBuild", 128),
            TheliaEvents::FORM_AFTER_BUILD . ".lengow_exclude_category_create" => array("afterCreateFormBuild", 128),
            TheliaEvents::FORM_AFTER_BUILD . ".lengow_exclude_category_update" => array("afterUpdateFormBuild", 128),
        );
    }
}
