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
use WebAnalyzer\CrawlerBundle\Analyzer\AnalyzerFactory;
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
            $results = [];
            $results = $this->analyzeHtml($uri, $results);
            $results = $this->analyzeNs($uri, $results);

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
     * @param array $results
     * @return array|AnalysisResult[]
     */
    private function analyzeHtml($uri, $results = array())
    {
        $htmlAnalyzers = $this->getAnalyzerFactory()->createHtmlAnalyzers();

        $response = $this->makeRequest($uri);
        $respnseBody = $response->getBody()->getContents();
        foreach ($htmlAnalyzers as $htmlAnalyzer) {
            $results[] = $htmlAnalyzer->analyze($respnseBody);
        }
        return $results;
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
     * @param array $results
     * @return array|AnalysisResult[]
     */
    protected function analyzeNs($uri, $results = array())
    {
        $nsAnalyzers = $this->getAnalyzerFactory()->createNsAnalyzers();
        if (!preg_match('/(?:\w+\:\/\/)?(?:.+?\.)?(?<host>[\w-]+\.\w+)(?:\/.*)?/', $uri, $matches)) {
            throw new Exception(sprintf('Unable to parse domain name from "%s"', $uri));
        }
        $nsLookupResults = dns_get_record($matches['host'], DNS_NS);
        foreach ($nsAnalyzers as $nsAnalyzer) {
            $results[] = $nsAnalyzer->analyze($nsLookupResults);
        }

        return $results;
    }

    /**
     * @return LoggerInterface
     */
    private function getLogger()
    {
        return $this->getContainer()->get('logger');
    }

    /**
     * @return AnalyzerFactory
     */
    private function getAnalyzerFactory()
    {
        return $this->getContainer()->get('web_analyzer.analyzer_factory');
    }

    /**
     * @return Container
     */
    private function getContainer()
    {
        return $this->getApplication()->getContainer();
    }
}
