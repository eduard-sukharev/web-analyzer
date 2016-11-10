<?php
/**
 * Copyright (c) Eduard Sukharev
 * Standard MIT License. See LICENSE.txt for full license text.
 */

namespace WebAnalyzer\CrawlerBundle\Mapping;

/**
 * @author Eduard Sukharev <eduard.sukharev@opensoftdev.ru>
 */
class AnalysisResult
{
    /**
     * @var string
     */
    private $technologyName;

    /**
     * @var bool
     */
    private $isFound;

    /**
     * @param string $technologyName
     * @param boolean $isFound
     */
    public function __construct($technologyName, $isFound)
    {
        $this->technologyName = $technologyName;
        $this->isFound = $isFound;
    }

    /**
     * @return boolean
     */
    public function isFound()
    {
        return $this->isFound;
    }

    /**
     * @return string
     */
    public function getTechnologyName()
    {
        return $this->technologyName;
    }
}