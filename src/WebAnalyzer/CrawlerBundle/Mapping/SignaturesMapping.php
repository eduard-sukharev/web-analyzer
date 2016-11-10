<?php
/**
 * Copyright (c) Eduard Sukharev
 * Standard MIT License. See LICENSE.txt for full license text.
 */

namespace WebAnalyzer\CrawlerBundle\Mapping;

/**
 * @author Eduard Sukharev <eduard.sukharev@opensoftdev.ru>
 */
class SignaturesMapping
{
    /**
     * @var array|string[]
     */
    private $possibleSignatures;

    public function __construct(array $possibleSignature)
    {
        $this->possibleSignatures = $possibleSignature;
    }

    /**
     * @return array|string[]
     */
    public function getPossibleSignatures()
    {
        return $this->possibleSignatures;
    }
}