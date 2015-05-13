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
use Lengow\Event\LengowIncludeAttributeEvent;
use Lengow\Event\LengowIncludeAttributeEvents;
use Lengow\Model\LengowIncludeAttributeQuery;

/**
 * Class LengowIncludeAttributeController
 * @package Lengow\Controller\Base
 * @author TheliaStudio
 */
class LengowIncludeAttributeController extends AbstractCrudController
{
    public function __construct()
    {
        parent::__construct(
            "lengow_include_attribute",
            "id",
            "order",
            AdminResources::MODULE,
            LengowIncludeAttributeEvents::CREATE,
            LengowIncludeAttributeEvents::UPDATE,
            LengowIncludeAttributeEvents::DELETE,
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
        return $this->createForm("lengow_include_attribute.create");
    }

    /**
     * Return the update form for this object
     */
    protected function getUpdateForm($data = array())
    {
        if (!is_array($data)) {
            $data = array();
        }

        return $this->createForm("lengow_include_attribute.update", "form", $data);
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
            "attribute_id" => $object->getAttributeId(),
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
        $event = new LengowIncludeAttributeEvent();

        $event->setAttributeId($formData["attribute_id"]);

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
        $event = new LengowIncludeAttributeEvent();

        $event->setId($formData["id"]);
        $event->setAttributeId($formData["attribute_id"]);

        return $event;
    }

    /**
     * Creates the delete event with the provided form data
     */
    protected function getDeleteEvent()
    {
        $event = new LengowIncludeAttributeEvent();

        $event->setId($this->getRequest()->request->get("lengow_include_attribute_id"));

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
        return $event->getLengowIncludeAttribute();
    }

    /**
     * Load an existing object from the database
     */
    protected function getExistingObject()
    {
        return LengowIncludeAttributeQuery::create()
            ->findPk($this->getRequest()->query->get("lengow_include_attribute_id"))
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

        return $this->render("lengow-include-attributes");
    }

    /**
     * Render the edition template
     */
    protected function renderEditionTemplate()
    {
        $this->getParserContext()
            ->set(
                "lengow_include_attribute_id",
                $this->getRequest()->query->get("lengow_include_attribute_id")
            )
        ;

        return $this->render("lengow-include-attribute-edit");
    }

    /**
     * Must return a RedirectResponse instance
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function redirectToEditionTemplate()
    {
        $id = $this->getRequest()->query->get("lengow_include_attribute_id");

        return new RedirectResponse(
            URL::getInstance()->absoluteUrl(
                "/admin/module/Lengow/lengow_include_attribute/edit",
                [
                    "lengow_include_attribute_id" => $id,
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
            URL::getInstance()->absoluteUrl("/admin/module/Lengow/lengow_include_attribute")
        );
    }
}
