<?php
/**
 * Copyright (c) Eduard Sukharev
 * Standard MIT License. See LICENSE.txt for full license text.
 */

namespace WebAnalyzer\CrawlerBundle\EventSubscriber\Analyzer\Ns;

use WebAnalyzer\CrawlerBundle\Mapping\SignaturesMapping;

/**
 * @author Eduard Sukharev <eduard.sukharev@opensoftdev.ru>
 */
class DynDnsAnalyzer extends AbstractNsAnalyzer
{
    public function getSignatures()
    {
        return [
            'target' => new SignaturesMapping(
                [
                    'dynect.net',
                    'dns.dyn.com',
                ]
            ),
        ];
    }

    /**
     * @return string
     */
    protected function getTechnologyName()
    {
        return "Dyn";
    }
}
