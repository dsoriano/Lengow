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

namespace Lengow;

use Thelia\Core\Event\ActionEvent;
use Lengow\FileFormat\Archive\AbstractArchiveBuilder;
use Lengow\FileFormat\Formatting\AbstractFormatter;
use Lengow\FileFormat\Formatting\FormatterData;
use Lengow\ImportExport\AbstractHandler;
use Lengow\ImportExport\Export\ExportHandler;

/**
 * Class Export
 * @package Thelia\Core\Event\ImportExport
 * @author Benjamin Perche <bperche@openstudio.fr>
 */
class ImportExport extends ActionEvent
{
    /** @var  \Lengow\ImportExport\AbstractHandler */
    protected $handler;

    /** @var  \Lengow\FileFormat\Formatting\AbstractFormatter */
    protected $formatter;

    /** @var  FormatterData */
    protected $data;

    /** @var  \Lengow\FileFormat\Archive\AbstractArchiveBuilder */
    protected $archiveBuilder;

    /** @var  mixed */
    protected $content;

    public function __construct(
        AbstractFormatter $formatter = null,
        AbstractHandler $handler = null,
        FormatterData $data = null,
        AbstractArchiveBuilder $archiveBuilder = null
    ) {
        $this->archiveBuilder = $archiveBuilder;
        $this->formatter = $formatter;
        $this->handler = $handler;
        $this->data = $data;
    }

    /**
     * @param  AbstractArchiveBuilder $archiveBuilder
     * @return $this
     */
    public function setArchiveBuilder(AbstractArchiveBuilder $archiveBuilder)
    {
        $this->archiveBuilder = $archiveBuilder;

        return $this;
    }

    /**
     * @return \Lengow\FileFormat\Archive\AbstractArchiveBuilder
     */
    public function getArchiveBuilder()
    {
        return $this->archiveBuilder;
    }

    /**
     * @param  AbstractFormatter $formatter
     * @return $this
     */
    public function setFormatter(AbstractFormatter $formatter)
    {
        $this->formatter = $formatter;

        return $this;
    }

    /**
     * @return \Lengow\FileFormat\Formatting\AbstractFormatter
     */
    public function getFormatter()
    {
        return $this->formatter;
    }

    /**
     * @param  \Lengow\ImportExport\Export\ExportHandler $handler
     * @return $this
     */
    public function setHandler(ExportHandler $handler)
    {
        $this->handler = $handler;

        return $this;
    }

    /**
     * @return \Lengow\ImportExport\Export\ExportHandler
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param  FormatterData $data
     * @return $this
     */
    public function setData(FormatterData $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return \Lengow\FileFormat\Formatting\FormatterData
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    public function isArchive()
    {
        return $this->archiveBuilder !== null;
    }
}
