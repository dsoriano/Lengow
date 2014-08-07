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
        $envCacheDir = THELIA_CACHE_DIR . $this->container->getParameter("kernel.environment");

        $cachePath = $this->buildPath($envCacheDir . DS . "lengow" . DS . "export.cache", 'file', true, true);

        $info = null;
        if (file_exists($cachePath)) {
            $info = new \SplFileInfo($cachePath);
        }

        $handler = new LengowExport($this->container);
        $formatter = new LengowFormatter($this->container);

        if ($info === null || $info->getMTime() <= time() - $this->getCacheTime()) {
            $response = $this->processExport(
                $formatter,
                $handler,
                null,
                $this->getSession()->getLang()
            );

            file_put_contents($cachePath, $response->getContent());
        } else {
            $response = Response::create(
                file_get_contents($cachePath),
                200,
                [
                    "Content-Type" => $formatter->getMimeType(),
                    "Content-Disposition" =>
                        "attachment; filename=\"" . $formatter::FILENAME . "." . $formatter->getExtension() . "\"",
                ]
            );
        }

        return $response;
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
                            "Unable to create the file %path",["%path"=>$path]
                        );
                    }
                } elseif ($create === 'dir') {
                    if (!mkdir($path)) {
                        $this->throwFileException(
                            "Unable to create the directory %path",["%path"=>$path]
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
