<?php
/**
 * Copyright (c) Eduard Sukharev
 * Standard MIT License. See LICENSE.txt for full license text.
 */

namespace WebAnalyzer\CrawlerBundle\EventSubscriber\Analyzer\Html;

use WebAnalyzer\CrawlerBundle\Mapping\SignaturesMapping;

/**
 * @author Eduard Sukharev <eduard.sukharev@opensoftdev.ru>
 */
class GoogleAnalyticsAnalyzer extends AbstractHtmlAnalyzer
{
    /**
     * {@inheritdoc}
     */
    protected function getTechnologyName()
    {
        return "GA";
    }

    /**
     * {@inheritdoc}
     */
    public function getSignatures()
    {
        return [
            '//script' => new SignaturesMapping(
                [
                    '.google-analytics.com/ga.js',
                    'ga.async = true;',
                ]
            ),
        ];
    }
}
