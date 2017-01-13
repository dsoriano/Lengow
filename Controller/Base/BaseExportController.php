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

use Thelia\Controller\Admin\BaseAdminController;
use Lengow\ImportExport as ImportExportEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Event\UpdatePositionEvent;
use Lengow\FileFormat\Archive\AbstractArchiveBuilder;
use Lengow\FileFormat\Archive\ArchiveBuilderManagerTrait;
use Lengow\FileFormat\Formatting\AbstractFormatter;
use Lengow\FileFormat\Formatting\FormatterManagerTrait;
use Thelia\Core\HttpFoundation\Response;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Loop\Export as ExportLoop;
use Thelia\Form\Definition\AdminForm;
use Thelia\Form\Exception\FormValidationException;
use Lengow\ImportExport\Export\DocumentsExportInterface;
use Lengow\ImportExport\Export\ExportHandler;
use Lengow\ImportExport\Export\ImagesExportInterface;
use Thelia\Model\ExportCategoryQuery;
use Thelia\Model\ExportQuery;
use Thelia\Model\Lang;
use Thelia\Model\LangQuery;

/**
 * Class ExportController
 * @package Thelia\Controller\Admin
 * @author Manuel Raynaud <manu@raynaud.io>
 */
class BaseExportController extends BaseAdminController
{
    use ArchiveBuilderManagerTrait;
    use FormatterManagerTrait;

    public function indexAction()
    {
        if (null !== $response = $this->checkAuth([AdminResources::EXPORT], [], [AccessManager::VIEW])) {
            return $response;
        }

        $this->setOrders();

        return $this->render('export');
    }

    /**
     * @param  integer  $id
     * @return Response
     *
     * This method is called when the route /admin/export/{id}
     * is called with a POST request.
     */
    public function export($id)
    {
        if (null === $export = $this->getExport($id)) {
            return $this->pageNotFound();
        }

        /**
         * Get needed services
         */
        $archiveBuilderManager = $this->getArchiveBuilderManager($this->container);
        $formatterManager = $this->getFormatterManager($this->container);

        /**
         * Define and validate the form
         */
        $form = $this->createForm(AdminForm::EXPORT);
        $errorMessage = null;

        try {
            $boundForm = $this->validateForm($form);

            $lang = LangQuery::create()->findPk(
                $boundForm->get("language")->getData()
            );

            $archiveBuilder = null;

            /**
             * Get the formatter and the archive builder if we have to compress the file(s)
             */

            /** @var \Lengow\FileFormat\Formatting\AbstractFormatter $formatter */
            $formatter = $formatterManager->get(
                $boundForm->get("formatter")->getData()
            );

            if ($boundForm->get("do_compress")->getData()) {
                /** @var \Thelia\Core\FileFormat\Archive\ArchiveBuilderInterface $archiveBuilder */
                $archiveBuilder = $archiveBuilderManager->get(
                    $boundForm->get("archive_builder")->getData()
                );
            }

            $rangeDate = null;

            if ($boundForm->get('range_date_start')->getData() && $boundForm->get('range_date_end')->getData()) {
                $rangeDate = [];
                $rangeDate['start'] = $boundForm->get('range_date_start')->getData();
                $rangeDate['end'] = $boundForm->get('range_date_end')->getData();
            }

            /*
             * Return the generated Response
             */

            return $this->processExport(
                $formatter,
                $export->getHandleClassInstance($this->container),
                $archiveBuilder,
                $lang,
                $boundForm->get("images")->getData(),
                $boundForm->get("documents")->getData(),
                $rangeDate
            );
        } catch (FormValidationException $e) {
            $errorMessage = $this->createStandardFormValidationErrorMessage($e);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
        }

        /**
         * If has an error, display it
         */
        if (null !== $errorMessage) {
            $form->setErrorMessage($errorMessage);

            $this->getParserContext()
                ->addForm($form)
                ->setGeneralError($errorMessage)
            ;
        }

        return $this->exportView($id);
    }



    /**
     * @param  integer  $id
     * @return Response
     *
     * This method is called when the route /admin/export/{id}
     * is called with a GET request.
     *
     * It returns a modal view if the request is an AJAX one,
     * otherwise it generates a "normal" back-office page
     */
    public function exportView($id)
    {
        if (null === $export = $this->getExport($id)) {
            return $this->pageNotFound();
        }

        /**
         * Use the loop to inject the same vars in the Template engine
         */
        $loop = new ExportLoop($this->container);

        $loop->initializeArgs([
            "id" => $export->getId()
        ]);

        $query = $loop->buildModelCriteria();
        $result= $query->find();

        $results = $loop->parseResults(
            new LoopResult($result)
        );

        $parserContext = $this->getParserContext();

        /** @var \Thelia\Core\Template\Element\LoopResultRow $row */
        foreach ($results as $row) {
            foreach ($row->getVarVal() as $name => $value) {
                $parserContext->set($name, $value);
            }
        }

        /**
         * Inject conditions in template engine,
         * It is used to display or not the checkboxes "Include images"
         * and "Include documents"
         */
        $this->getParserContext()
            ->set("HAS_IMAGES", $export->hasImages($this->container))
            ->set("HAS_DOCUMENTS", $export->hasDocuments($this->container))
            ->set("CURRENT_LANG_ID", $this->getSession()->getLang()->getId())
        ;

        /** Then render the form */
        if ($this->getRequest()->isXmlHttpRequest()) {
            return $this->render("ajax/export-modal");
        } else {
            return $this->render("export-page");
        }
    }


    public function changePosition()
    {
        if (null !== $response = $this->checkAuth([AdminResources::EXPORT], [], [AccessManager::UPDATE])) {
            return $response;
        }

        $query = $this->getRequest()->query;

        $mode = $query->get("mode");
        $id = $query->get("id");
        $value = $query->get("value");

        $this->getExport($id);

        $event = new UpdatePositionEvent($id, $this->getMode($mode), $value);
        $this->dispatch(TheliaEvents::EXPORT_CHANGE_POSITION, $event);

        $this->setOrders(null, "manual");

        return $this->render('export');
    }

    public function changeCategoryPosition()
    {
        if (null !== $response = $this->checkAuth([AdminResources::EXPORT], [], [AccessManager::UPDATE])) {
            return $response;
        }

        $query = $this->getRequest()->query;

        $mode = $query->get("mode");
        $id = $query->get("id");
        $value = $query->get("value");

        $this->getCategory($id);

        $event = new UpdatePositionEvent($id, $this->getMode($mode), $value);
        $this->dispatch(TheliaEvents::EXPORT_CATEGORY_CHANGE_POSITION, $event);

        $this->setOrders("manual");

        return $this->render('export');
    }

    public function getMode($action)
    {
        if ($action === "up") {
            $mode = UpdatePositionEvent::POSITION_UP;
        } elseif ($action === "down") {
            $mode = UpdatePositionEvent::POSITION_DOWN;
        } else {
            $mode = UpdatePositionEvent::POSITION_ABSOLUTE;
        }

        return $mode;
    }

    protected function setOrders($category = null, $export = null)
    {
        if ($category === null) {
            $category = $this->getRequest()->query->get("category_order", "manual");
        }

        if ($export === null) {
            $export = $this->getRequest()->query->get("export_order", "manual");
        }

        $this->getParserContext()
            ->set("category_order", $category)
        ;

        $this->getParserContext()
            ->set("export_order", $export)
        ;
    }

    protected function getExport($id)
    {
        $export = ExportQuery::create()->findPk($id);

        if (null === $export) {
            throw new \ErrorException(
                $this->getTranslator()->trans(
                    "There is no id \"%id\" in the exports",
                    [
                        "%id" => $id
                    ]
                )
            );
        }

        return $export;
    }

    protected function getCategory($id)
    {
        $category = ExportCategoryQuery::create()->findPk($id);

        if (null === $category) {
            throw new \ErrorException(
                $this->getTranslator()->trans(
                    "There is no id \"%id\" in the export categories",
                    [
                        "%id" => $id
                    ]
                )
            );
        }

        return $category;
    }
}
