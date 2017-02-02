<?php

namespace Lengow\Controller\Base;

use Lengow\FileFormat\Archive\AbstractArchiveBuilder;
use Lengow\FileFormat\Formatting\AbstractFormatter;
use Lengow\ImportExport\Export\DocumentsExportInterface;
use Lengow\ImportExport\Export\ExportHandler;
use Lengow\ImportExport\Export\ImagesExportInterface;
use Thelia\Model\Lang;
use Thelia\Core\HttpFoundation\Response;
use Lengow\ImportExport as ImportExportEvent;
use Thelia\Core\Event\TheliaEvents;
use Lengow\Export\LengowExport;
use Lengow\Export\LengowFormatter;
use Lengow\Lengow;
use Thelia\Exception\FileException;
use Thelia\Model\ConfigQuery;

trait ExportControllerTrait
{
    /**
     * @var Lang
     */
    protected $lang;

    /**
     * @param AbstractFormatter $formatter
     * @param ExportHandler $handler
     * @param AbstractArchiveBuilder $archiveBuilder
     * @param Lang $lang
     * @param bool $includeImages
     * @param bool $includeDocuments
     * @return Response
     *
     * Processes an export by returning a response with the export's content.
     */
    protected function processExport(
        AbstractFormatter $formatter,
        ExportHandler $handler,
        AbstractArchiveBuilder $archiveBuilder = null,
        Lang $lang = null,
        $includeImages = false,
        $includeDocuments = false,
        $rangeDate = null
    ) {
        /**
         * Build an event containing the formatter and the handler.
         * Used for specific configuration (e.g: XML node names)
         */

        $event = new ImportExportEvent($formatter, $handler);

        $filename = $handler->getFilename() . "." . $formatter->getExtension();

        if ($rangeDate !== null) {
            $handler->setRangeDate($rangeDate);
        }

        if ($archiveBuilder === null) {
            $data = $handler->buildData($lang);

            $formattedContent = $formatter
                ->setOrder($handler->getOrder())
                ->encode($data)
            ;

            return new Response(
                $formattedContent,
                200,
                [
                    "Content-Type" => $formatter->getMimeType(),
                ]
            );
        } else {
            $event->setArchiveBuilder($archiveBuilder);

            if ($includeImages && $handler instanceof ImagesExportInterface) {
                $this->processExportImages($handler, $archiveBuilder);

                $handler->setImageExport(true);
            }

            if ($includeDocuments && $handler instanceof DocumentsExportInterface) {
                $this->processExportDocuments($handler, $archiveBuilder);

                $handler->setDocumentExport(true);
            }

            $data = $handler
                ->buildData($lang)
                ->setLang($lang)
            ;

            $this->dispatch(TheliaEvents::EXPORT_BEFORE_ENCODE, $event);

            $formattedContent = $formatter
                ->setOrder($handler->getOrder())
                ->encode($data)
            ;

            $this->dispatch(TheliaEvents::EXPORT_AFTER_ENCODE, $event->setContent($formattedContent));


            $archiveBuilder->addFileFromString(
                $event->getContent(),
                $filename
            );

            return $archiveBuilder->buildArchiveResponse($handler->getFilename());
        }
    }

    /**
     * @param ImagesExportInterface  $handler
     * @param AbstractArchiveBuilder $archiveBuilder
     *
     * Procedure that add images in the export's archive
     */
    protected function processExportImages(ImagesExportInterface $handler, AbstractArchiveBuilder $archiveBuilder)
    {
        foreach ($handler->getImagesPaths() as $name => $documentPath) {
            $archiveBuilder->addFile(
                $documentPath,
                $handler::IMAGES_DIRECTORY,
                is_integer($name) ? null : $name
            );
        }
    }

    /**
     * @param DocumentsExportInterface $handler
     * @param AbstractArchiveBuilder   $archiveBuilder
     *
     * Procedure that add documents in the export's archive
     */
    protected function processExportDocuments(DocumentsExportInterface $handler, AbstractArchiveBuilder $archiveBuilder)
    {
        foreach ($handler->getDocumentsPaths() as $name => $documentPath) {
            $archiveBuilder->addFile(
                $documentPath,
                $handler::DOCUMENTS_DIRECTORY,
                is_integer($name) ? null : $name
            );
        }
    }

    public function getCacheTime()
    {
        /**
         * default is 1 hour
         */

        return (int) ConfigQuery::read("lengow_cache_time", 3600);
    }

    protected function buildExportDatas()
    {
        $cachePath = $this->getLengowFileCachePath();
        $info = $this->getLengowFileInfo();

        $handler = new LengowExport($this->container);
        $formatter = new LengowFormatter($this->container);

        if ($info === null || $info->getMTime() <= time() - $this->getCacheTime()) {
            $response = $this->processExport(
                $formatter,
                $handler,
                null,
                $this->lang
                //$this->getSession()->getLang()
            );

            $data = $response->getContent();

            file_put_contents($cachePath, $data);
        } else {
            $data = file_get_contents($cachePath);

            $response = new Response(
                $data,
                200,
                [
                    "Content-Type" => $formatter->getMimeType(),
                ]
            );
        }

        return $response;
    }

    protected function getLengowFileCachePath()
    {
        $envCacheDir = THELIA_CACHE_DIR . $this->container->getParameter('kernel.environment');
        return $this->buildPath($envCacheDir . DS . 'lengow' . DS . 'export.cache', 'file', true, true);
    }

    protected function getLengowFileInfo()
    {
        $cachePath = $this->getLengowFileCachePath();
        return file_exists($cachePath) ? new \SplFileInfo($cachePath) : null;
    }

    public function buildPath($path, $checkRead = false, $checkWrite = false, $create = 'none')
    {
        if (!file_exists($path)) {
            $parent = dirname($path);

            if (!file_exists($parent)) {
                $this->buildPath($parent, false, true, 'dir');

            } elseif (!is_writable($parent)) {
                $this->throwFileException(
                    "The directory %parent is not writable, so the directory %path could not be created",
                    [
                        "%parent" => $parent,
                        "%path" => $path,
                    ]
                );
            } else {
                if ($create === 'file') {
                    if (!touch($path)) {
                        $this->throwFileException(
                            "Unable to create the file %path",
                            ["%path"=>$path]
                        );
                    }
                } elseif ($create === 'dir') {
                    if (!mkdir($path)) {
                        $this->throwFileException(
                            "Unable to create the directory %path",
                            ["%path"=>$path]
                        );
                    }
                }
            }
        } else {
            if ($checkRead && !is_readable($path)) {
                $this->throwFileException("The file/directory %file is not readable", ["%file" => $path]);
            }

            if ($checkWrite && !is_writable($path)) {
                $this->throwFileException("The file/directory %file is not writable", ["%file" => $path]);
            }
        }

        return $path;
    }

    protected function throwFileException($message, $args)
    {
        throw new FileException(
            $this->getTranslator()->trans($message, $args, Lengow::MESSAGE_DOMAIN)
        );
    }
}