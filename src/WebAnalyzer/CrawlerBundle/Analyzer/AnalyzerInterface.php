<?php
/**
 * Copyright (c) Eduard Sukharev
 * Standard MIT License. See LICENSE.txt for full license text.
 */

namespace WebAnalyzer\CrawlerBundle\Analyzer;

use Psr\Log\LoggerInterface;

/**
 * @author Eduard Sukharev <eduard.sukharev@opensoftdev.ru>
 */
interface AnalyzerInterface
{
    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger);
}