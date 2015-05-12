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

namespace Lengow\Controller;

use Lengow\Export\LengowExport;
use Lengow\Export\LengowFormatter;
use Lengow\Lengow;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Thelia\Controller\Admin\ExportController as BaseExportController;
use Thelia\Core\HttpFoundation\Response;
use Thelia\Exception\FileException;
use Thelia\Model\ConfigQuery;

/**
 * Class ExportController
 * @package Lengow\Controller
 * @author Benjamin Perche <bperche@openstudio.fr>
 */
class ExportController extends BaseExportController
{
    public function getCacheTime()
    {
        /**
         * default is 1 hour
         */

        return (int) ConfigQuery::read("lengow_cache_time", 3600);
    }

    public function lengowExport()
    {
        return Response::create($this->buildExportDatas());
    }

    public function lengowManualExport()
    {
        $this->buildExportDatas();
        $response = new BinaryFileResponse($this->getLengowFileInfo());
        $response->headers->set('Content-Type', 'text/csv');
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'lengow.csv'
        );
        return $response;
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
                $this->getSession()->getLang()
            );

            $data = $response->getContent();

            file_put_contents($cachePath, $data);
        } else {
            $data = file_get_contents($cachePath);
        }

        return $data;
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
