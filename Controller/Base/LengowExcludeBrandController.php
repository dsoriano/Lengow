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

namespace Lengow\Controller\Base;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Thelia\Controller\Admin\AbstractCrudController;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Tools\URL;
use Lengow\Event\LengowExcludeBrandEvent;
use Lengow\Event\LengowExcludeBrandEvents;
use Lengow\Model\LengowExcludeBrandQuery;

/**
 * Class LengowExcludeBrandController
 * @package Lengow\Controller\Base
 * @author TheliaStudio
 */
class LengowExcludeBrandController extends AbstractCrudController
{
    public function __construct()
    {
        parent::__construct(
            "lengow_exclude_brand",
            "id",
            "order",
            AdminResources::MODULE,
            LengowExcludeBrandEvents::CREATE,
            LengowExcludeBrandEvents::UPDATE,
            LengowExcludeBrandEvents::DELETE,
            null,
            null,
            "Lengow"
        );
    }

    /**
     * Return the creation form for this object
     */
    protected function getCreationForm()
    {
        return $this->createForm("lengow_exclude_brand.create");
    }

    /**
     * Return the update form for this object
     */
    protected function getUpdateForm($data = array())
    {
        if (!is_array($data)) {
            $data = array();
        }

        return $this->createForm("lengow_exclude_brand.update", "form", $data);
    }

    /**
     * Hydrate the update form for this object, before passing it to the update template
     *
     * @param mixed $object
     */
    protected function hydrateObjectForm($object)
    {
        $data = array(
            "id" => $object->getId(),
            "brand_id" => $object->getBrandId(),
        );

        return $this->getUpdateForm($data);
    }

    /**
     * Creates the creation event with the provided form data
     *
     * @param mixed $formData
     * @return \Thelia\Core\Event\ActionEvent
     */
    protected function getCreationEvent($formData)
    {
        $event = new LengowExcludeBrandEvent();

        $event->setBrandId($formData["brand_id"]);

        return $event;
    }

    /**
     * Creates the update event with the provided form data
     *
     * @param mixed $formData
     * @return \Thelia\Core\Event\ActionEvent
     */
    protected function getUpdateEvent($formData)
    {
        $event = new LengowExcludeBrandEvent();

        $event->setId($formData["id"]);
        $event->setBrandId($formData["brand_id"]);

        return $event;
    }

    /**
     * Creates the delete event with the provided form data
     */
    protected function getDeleteEvent()
    {
        $event = new LengowExcludeBrandEvent();

        $event->setId($this->getRequest()->request->get("lengow_exclude_brand_id"));

        return $event;
    }

    /**
     * Return true if the event contains the object, e.g. the action has updated the object in the event.
     *
     * @param mixed $event
     */
    protected function eventContainsObject($event)
    {
        return null !== $this->getObjectFromEvent($event);
    }

    /**
     * Get the created object from an event.
     *
     * @param mixed $event
     */
    protected function getObjectFromEvent($event)
    {
        return $event->getLengowExcludeBrand();
    }

    /**
     * Load an existing object from the database
     */
    protected function getExistingObject()
    {
        return LengowExcludeBrandQuery::create()
            ->findPk($this->getRequest()->query->get("lengow_exclude_brand_id"))
        ;
    }

    /**
     * Returns the object label form the object event (name, title, etc.)
     *
     * @param mixed $object
     */
    protected function getObjectLabel($object)
    {
        return '';
    }

    /**
     * Returns the object ID from the object
     *
     * @param mixed $object
     */
    protected function getObjectId($object)
    {
        return $object->getId();
    }

    /**
     * Render the main list template
     *
     * @param mixed $currentOrder , if any, null otherwise.
     */
    protected function renderListTemplate($currentOrder)
    {
        $this->getParser()
            ->assign("order", $currentOrder)
        ;

        return $this->render("lengow-exclude-brands");
    }

    /**
     * Render the edition template
     */
    protected function renderEditionTemplate()
    {
        $this->getParserContext()
            ->set(
                "lengow_exclude_brand_id",
                $this->getRequest()->query->get("lengow_exclude_brand_id")
            )
        ;

        return $this->render("lengow-exclude-brand-edit");
    }

    /**
     * Must return a RedirectResponse instance
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function redirectToEditionTemplate()
    {
        $id = $this->getRequest()->query->get("lengow_exclude_brand_id");

        return new RedirectResponse(
            URL::getInstance()->absoluteUrl(
                "/admin/module/Lengow/lengow_exclude_brand/edit",
                [
                    "lengow_exclude_brand_id" => $id,
                ]
            )
        );
    }

    /**
     * Must return a RedirectResponse instance
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function redirectToListTemplate()
    {
        return new RedirectResponse(
            URL::getInstance()->absoluteUrl("/admin/module/Lengow/lengow_exclude_brand")
        );
    }
}
