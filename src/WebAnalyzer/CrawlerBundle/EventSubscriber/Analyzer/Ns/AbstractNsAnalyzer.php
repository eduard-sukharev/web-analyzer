<?php
/**
 * Copyright (c) Eduard Sukharev
 * Standard MIT License. See LICENSE.txt for full license text.
 */

namespace WebAnalyzer\CrawlerBundle\EventSubscriber\Analyzer\Ns;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use WebAnalyzer\CrawlerBundle\EventSubscriber\Event\NsLookupEvent;
use WebAnalyzer\CrawlerBundle\Mapping\AnalysisResult;
use WebAnalyzer\CrawlerBundle\Mapping\SignaturesMapping;

/**
 * Abstract technology analyzer. Defines Common behavior of all analyzers
 */
abstract class AbstractNsAnalyzer implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @return SignaturesMapping[]
     */
    abstract protected function getSignatures();

    /**
     * Technology name as to be appeared in results output
     * @return string
     */
    abstract protected function getTechnologyName();

    public static function getSubscribedEvents()
    {
        return [
            NsLookupEvent::NAME => 'onNsLookup',
        ];
    }

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param NsLookupEvent $nsLookupEvent
     */
    public function onNsLookup(NsLookupEvent $nsLookupEvent)
    {
        foreach ($nsLookupEvent->getNsLookupData() as $nsLookupResult) {
            foreach ($this->getSignatures() as $key => $signature) {
                if (!array_key_exists($key, $nsLookupResult)) {
                    $this->logger->warning(
                        sprintf(
                            'Incorrect %s analyzer setup: ns lookup result contains no %s key',
                            $this->getTechnologyName(),
                            $key
                        )
                    );
                }
                foreach ($signature->getPossibleSignatures() as $possibleSignature) {
                    if (stripos($nsLookupResult[$key], $possibleSignature) !== false) {
                        $nsLookupEvent->addAnalysisResult(new AnalysisResult($this->getTechnologyName(), true));

                        return;
                    }
                }
            }
        }
        $nsLookupEvent->addAnalysisResult(new AnalysisResult($this->getTechnologyName(), false));

        return;
    }
}