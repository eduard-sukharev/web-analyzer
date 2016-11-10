<?php
/**
 * Copyright (c) Eduard Sukharev
 * Standard MIT License. See LICENSE.txt for full license text.
 */

namespace WebAnalyzer\CrawlerBundle\Analyzer\Ns;

use Psr\Log\LoggerInterface;
use WebAnalyzer\CrawlerBundle\Analyzer\AnalyzerInterface;
use WebAnalyzer\CrawlerBundle\Mapping\AnalysisResult;
use WebAnalyzer\CrawlerBundle\Mapping\SignaturesMapping;

/**
 * Abstract technology analyzer. Defines Common behavior of all analyzers
 */
abstract class AbstractNsAnalyzer implements AnalyzerInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @return SignaturesMapping[]
     */
    abstract protected function getSignatures();

    /**
     * Technology name as to be appeared in results output
     * @return string
     */
    abstract protected function getTechnologyName();

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param array $nsLookupResults
     * @return AnalysisResult
     */
    public function analyze($nsLookupResults)
    {
        foreach ($nsLookupResults as $nsLookupResult) {
            foreach ($this->getSignatures() as $key => $signature) {
                if (!array_key_exists($key, $nsLookupResult)) {
                    $this->logger->warning(
                        sprintf(
                            'Incorrect %s analyzer setup: ns lookup result contains no %s key',
                            $this->getTechnologyName(),
                            $key
                        )
                    );
                }
                foreach ($signature->getPossibleSignatures() as $possibleSignature) {
                    if (stripos($nsLookupResult[$key], $possibleSignature) !== false) {
                        return new AnalysisResult($this->getTechnologyName(), true);
                    }
                }
            }
        }

        return new AnalysisResult($this->getTechnologyName(), false);
    }
}