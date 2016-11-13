<?php
/**
 * Copyright (c) Eduard Sukharev
 * Standard MIT License. See LICENSE.txt for full license text.
 */
namespace WebAnalyzer\CrawlerBundle\EventSubscriber\Event;

/**
 * @author Eduard Sukharev <eduard.sukharev@opensoftdev.ru>
 */
class NsLookupEvent extends AbstractAnalyzerEvent
{
    const NAME = 'NsLookupEvent';

    /**
     * @var array
     */
    private $nsLookupData;

    /**
     * @param array $nsLookupData
     */
    public function __construct(array $nsLookupData)
    {
        $this->nsLookupData = $nsLookupData;
    }

    /**
     * @return array
     */
    public function getNsLookupData()
    {
        return $this->nsLookupData;
    }
}