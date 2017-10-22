<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 04.03.2017
 * Time: 15:24
 */

namespace ForkPlayer\Utils;


class OutputFormat
{
    const OUTPUT_FORMAT = 'OUTPUT_FORMAT';

    const XML = 'xml';
    const JSON = 'json';

    private $mimeTypes = array(self::JSON => 'application/json', self::XML => 'application/xml');

    private $format;

    /**
     * OutputFormat constructor.
     */
    public function __construct()
    {
        $outputFormat = self::XML;

        if (!empty($_SERVER[self::OUTPUT_FORMAT])) {
            $outputFormat = strtolower($_SERVER[self::OUTPUT_FORMAT]);
        }

        $this->format = empty($this->mimeTypes[$outputFormat]) ? self::XML : $outputFormat;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @return string
     */
    public function getHeader()
    {
        return 'Content-type:' . $this->mimeTypes[$this->format] . ';  charset=UTF-8';
    }
}