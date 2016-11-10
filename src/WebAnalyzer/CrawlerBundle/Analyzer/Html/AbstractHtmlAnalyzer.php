<?php
/**
 * Copyright (c) Eduard Sukharev
 * Standard MIT License. See LICENSE.txt for full license text.
 */

namespace WebAnalyzer\CrawlerBundle\Analyzer\Html;

use DOMElement;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use WebAnalyzer\CrawlerBundle\Analyzer\AnalyzerInterface;
use WebAnalyzer\CrawlerBundle\Mapping\AnalysisResult;
use WebAnalyzer\CrawlerBundle\Mapping\SignaturesMapping;

/**
 * Abstract technology analyzer. Defines Common behavior of all analyzers
 */
abstract class AbstractHtmlAnalyzer implements AnalyzerInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @return SignaturesMapping[]
     */
    abstract public function getSignatures();

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
     * @param string $responseBody
     * @return AnalysisResult
     */
    public function analyze($responseBody)
    {
        $crawler = new Crawler($responseBody);

        foreach ($this->getSignatures() as $key => $signatureMapping) {
            $signatureNodes = $crawler->filterXPath($key);
            /** @var DOMElement $element */
            foreach ($signatureNodes as $element) {
                foreach ($signatureMapping->getPossibleSignatures() as $possibleSignature) {
                    $content = preg_replace('/\s\s+/', ' ', trim($element->textContent));
                    $this->logger->debug('Element text: ' . $content);

                    if (stripos($content, $possibleSignature) !== false) {
                        $this->logger->debug(sprintf('Found signature %s by %s', $possibleSignature, $key));

                        return new AnalysisResult($this->getTechnologyName(), true);
                    }
                }
            }
        }

        return new AnalysisResult($this->getTechnologyName(), false);
    }
}