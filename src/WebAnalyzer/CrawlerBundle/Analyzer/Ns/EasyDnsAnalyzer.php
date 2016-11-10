<?php
/**
 * Copyright (c) Eduard Sukharev
 * Standard MIT License. See LICENSE.txt for full license text.
 */

namespace WebAnalyzer\CrawlerBundle\Analyzer\Ns;

use WebAnalyzer\CrawlerBundle\Mapping\SignaturesMapping;

/**
 * @author Eduard Sukharev <eduard.sukharev@opensoftdev.ru>
 */
class EasyDnsAnalyzer extends AbstractNsAnalyzer
{
    public function getSignatures()
    {
        return [
            'target' => new SignaturesMapping(
                [
                    'easydns.com',
                    'easydns.org',
                    'easydns.net',
                    'easydns.info',
                ]
            ),
        ];
    }

    /**
     * @return string
     */
    protected function getTechnologyName()
    {
        return "EasyDns";
    }
}