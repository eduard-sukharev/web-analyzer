<?php
/**
 * Copyright (c) Eduard Sukharev
 * Standard MIT License. See LICENSE.txt for full license text.
 */

namespace WebAnalyzer\CrawlerBundle\Command;

use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;
use WebAnalyzer\CrawlerBundle\EventSubscriber\Event\AbstractAnalyzerEvent;
use WebAnalyzer\CrawlerBundle\EventSubscriber\Event\HtmlCrawlEvent;
use WebAnalyzer\CrawlerBundle\EventSubscriber\Event\NsLookupEvent;
use WebAnalyzer\CrawlerBundle\Mapping\AnalysisResult;

/**
 * @author Eduard Sukharev <eduard.sukharev@opensoftdev.ru>
 */
class AnalyzePageCommand extends Command
{
    private $guzzleRetries = 0;

    protected function configure()
    {
        $this
            ->setName('web_analyzer:analyze_page')
            ->setDescription("Checks whether a web page contains defined technologies according to markers")
            ->addArgument('uri', InputArgument::REQUIRED, 'Website uri to search for technologies in');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $uri = $input->getArgument('uri');

        try {
            $htmlEvent = $this->analyzeHtml($uri);
            $htmlAnalysisResult = $htmlEvent->getAnalysisResults();

            $nsEvent = $this->analyzeNs($uri);
            $nsAnalysisResult = $nsEvent->getAnalysisResults();

            /** @var AnalysisResult[] $results */
            $results = array_merge($htmlAnalysisResult, $nsAnalysisResult);
            foreach ($results as $result) {
                $output->writeln(sprintf('Using %s: %s', $result->getTechnologyName(), $result->isFound() ? 'yes' : 'no'));
            }
        } catch (Exception $e) {
            $this->getLogger()->error($e->getMessage());
            $this->getLogger()->error($e->getTraceAsString());
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

            return 1;
        }


        return 0;
    }

    /**
     * @param string $uri
     * @return AbstractAnalyzerEvent
     */
    private function analyzeHtml($uri)
    {
        $response = $this->makeRequest($uri);
        $responseBody = $response->getBody()->getContents();

        $htmlCrawlEvent = new HtmlCrawlEvent($responseBody);

        $this->getEventDispatcher()->dispatch(HtmlCrawlEvent::NAME, $htmlCrawlEvent);

        return $htmlCrawlEvent;
    }

    /**
     * @param string $uri
     * @return ResponseInterface
     */
    private function makeRequest($uri)
    {
        try {
            $client = new HttpClient();
            $headers = $this->getContainer()->getParameter('crawler_headers');
            $response = $client->request('GET', $uri, ['headers' => $headers]);
        } catch (ClientException $e) {
            if ($this->guzzleRetries > 5) {
                $this->getLogger()->error(sprintf('Reached maximum download limit for %s', $uri));
                $this->getLogger()->error($e->getMessage());

                throw $e;
            }

            $this->guzzleRetries++;

            return $this->makeRequest($uri);
        }

        return $response;
    }

    /**
     * @param string $uri
     *
     * @return AbstractAnalyzerEvent
     *
     * @throws Exception
     */
    protected function analyzeNs($uri)
    {
        if (!preg_match('/(?:\w+\:\/\/)?(?:.+?\.)?(?<host>[\w-]+\.\w+)(?:\/.*)?/', $uri, $matches)) {
            throw new Exception(sprintf('Unable to parse domain name from "%s"', $uri));
        }
        $nsLookupResults = dns_get_record($matches['host'], DNS_NS);

        $nsLookupEvent = new NsLookupEvent($nsLookupResults);

        $this->getEventDispatcher()->dispatch(NsLookupEvent::NAME, $nsLookupEvent);

        return $nsLookupEvent;
    }

    /**
     * @return LoggerInterface
     */
    private function getLogger()
    {
        return $this->getContainer()->get('logger');
    }

    /**
     * @return EventDispatcher
     */
    private function getEventDispatcher()
    {
        return $this->getContainer()->get('event_dispatcher');
    }

    /**
     * @return Container
     */
    private function getContainer()
    {
        return $this->getApplication()->getContainer();
    }
}
