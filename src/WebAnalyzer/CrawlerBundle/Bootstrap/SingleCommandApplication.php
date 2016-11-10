<?php
/**
 * Copyright (c) Eduard Sukharev
 * Standard MIT License. See LICENSE.txt for full license text.
 */

namespace WebAnalyzer\CrawlerBundle\Bootstrap;

use WebAnalyzer\CrawlerBundle\Command\AnalyzePageCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Eduard Sukharev <eduard.sukharev@opensoftdev.ru>
 */
class SingleCommandApplication extends Application
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @param string $name
     * @param string $version
     * @param ContainerInterface $container
     */
    public function __construct($name, $version, ContainerInterface $container)
    {
        parent::__construct($name, $version);

        $this->container = $container;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Gets the name of the command based on input.
     *
     * @param InputInterface $input The input interface
     *
     * @return string The command name
     */
    protected function getCommandName(InputInterface $input)
    {
        return 'web_analyzer:analyze_page';
    }

    /**
     * Gets the default commands that should always be available.
     *
     * @return array An array of default Command instances
     */
    protected function getDefaultCommands()
    {
        // Keep the core default commands to have the HelpCommand
        // which is used when using the --help option
        $defaultCommands = parent::getDefaultCommands();

        $defaultCommands[] = new AnalyzePageCommand();

        return $defaultCommands;
    }

    /**
     * Overridden so that the application doesn't expect the command
     * name to be the first argument.
     */
    public function getDefinition()
    {
        $inputDefinition = parent::getDefinition();
        // clear out the normal first argument, which is the command name
        $inputDefinition->setArguments();

        return $inputDefinition;
    }
}
