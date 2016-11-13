<?php
/**
 * Copyright (c) Eduard Sukharev
 * Standard MIT License. See LICENSE.txt for full license text.
 */

namespace WebAnalyzer\CrawlerBundle\EventSubscriber\Event;

use Symfony\Component\EventDispatcher\Event;
use WebAnalyzer\CrawlerBundle\Mapping\AnalysisResult;

class AbstractAnalyzerEvent extends Event
{
    /**
     * @var AnalysisResult[]
     */
    protected $analysisResults = [];

    /**
     * @return AnalysisResult[]
     */
    public function getAnalysisResults()
    {
        return $this->analysisResults;
    }

    /**
     * @param AnalysisResult $analysisResult
     */
    public function addAnalysisResult(AnalysisResult $analysisResult)
    {
        $this->analysisResults[] = $analysisResult;
    }
}