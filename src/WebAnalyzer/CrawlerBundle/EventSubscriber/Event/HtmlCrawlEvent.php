<?php
/**
 * Copyright (c) Eduard Sukharev
 * Standard MIT License. See LICENSE.txt for full license text.
 */
namespace WebAnalyzer\CrawlerBundle\EventSubscriber\Event;

/**
 * @author Eduard Sukharev <eduard.sukharev@opensoftdev.ru>
 */
class HtmlCrawlEvent extends AbstractAnalyzerEvent
{
    const NAME = 'HtmlCrawlEvent';

    /**
     * Raw HTML body string
     * @var string
     */
    private $rawHtml;

    /**
     * @param string $rawHtml
     */
    public function __construct($rawHtml)
    {
        $this->rawHtml = $rawHtml;
    }

    /**
     * @return string
     */
    public function getRawHtml()
    {
        return $this->rawHtml;
    }
}