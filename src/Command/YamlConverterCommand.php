<?php

namespace Doctrine2Eloquent\Command;

use Doctrine2Eloquent\Converter\YamlConverter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class YamlConverterCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('doctrine2eloquent:yamlconverter')
            ->setDescription('Greet someone')
            ->addOption(
                'yaml_path',
                null,
                InputOption::VALUE_REQUIRED,
                'YAML Path'
            )->addOption(
                'model_path',
                null,
                InputOption::VALUE_REQUIRED,
                'Model Path'
            )->addOption(
                'model_namespace',
                null,
                InputOption::VALUE_REQUIRED,
                'Model Path'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $yamlConverter =  new YamlConverter();
        $yamlConverter->setYamlPath($input->getOption('yaml_path'));
        $yamlConverter->setModelPath($input->getOption('model_path'));
        $yamlConverter->setModelNamespace($input->getOption('model_namespace'));
        $yamlConverter->convert();
    }
}

