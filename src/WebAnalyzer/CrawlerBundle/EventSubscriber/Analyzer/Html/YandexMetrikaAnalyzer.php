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
class YandexMetrikaAnalyzer extends AbstractHtmlAnalyzer
{
    /**
     * {@inheritdoc}
     */
    protected function getTechnologyName()
    {
        return "Ya.M";
    }

    /**
     * {@inheritdoc}
     */
    public function getSignatures()
    {
        return [
            '//script' => new SignaturesMapping(
                [
                    'new Ya.Metrika',
                    'yandex.ru/metrika',
                ]
            ),
        ];
    }
}
