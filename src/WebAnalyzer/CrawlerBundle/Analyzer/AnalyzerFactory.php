<?php
/**
 * Copyright (c) Eduard Sukharev
 * Standard MIT License. See LICENSE.txt for full license text.
 */

namespace WebAnalyzer\CrawlerBundle\Analyzer;

use Psr\Log\LoggerInterface;
use WebAnalyzer\CrawlerBundle\Analyzer\Html\AbstractHtmlAnalyzer;
use WebAnalyzer\CrawlerBundle\Analyzer\Ns\AbstractNsAnalyzer;

/**
 * @author Eduard Sukharev <eduard.sukharev@opensoftdev.ru>
 */
class AnalyzerFactory
{
    /**
     * @var array|string[]
     */
    private $analyzerClasses;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     * @param array $analyzerClasses
     */
    public function __construct(
        LoggerInterface $logger,
        array $analyzerClasses
    ) {
        $this->logger = $logger;
        $this->analyzerClasses = $analyzerClasses;
    }

    /**
     * @return array|AbstractHtmlAnalyzer[]
     */
    public function createHtmlAnalyzers()
    {
        return $this->createAnalyzers('html');
    }

    /**
     * @return array|AbstractNsAnalyzer[]
     */
    public function createNsAnalyzers()
    {
        return $this->createAnalyzers('ns');
    }

    /**
     * @param string $type
     *
     */
    private function createAnalyzers($type)
    {
        $analyzers = [];
        foreach ($this->analyzerClasses[$type] as $analyzerName) {
            $analyzerNamespace = ucfirst(strtolower($type));
            $this->logger->info(sprintf('Instantiating %s analyzer: %s', $analyzerNamespace, $analyzerName));
            $analyzerClass = sprintf('%s\%s\%s', __NAMESPACE__, $analyzerNamespace, $analyzerName);
            $analyzer = new $analyzerClass($this->logger);
            if (!$analyzer instanceof AnalyzerInterface) {
                $this->logger->warning(sprintf('Improper configuration: %s seem to be not an Analyzer', $analyzerName));
                continue;
            }
            $analyzers[] = $analyzer;
        }

        return $analyzers;
    }
} 