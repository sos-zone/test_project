<?php

namespace TestBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Security\Acl\Exception\Exception;
use Symfony\Component\Validator\Constraints as Assert;
use Ddeboer\DataImport\Reader\CsvReader;
use Symfony\Component\Validator\Constraints\DateTime;
use TestBundle\Entity\Product;
use TestBundle\Services\ProductValidator;
use Ddeboer\DataImport\Exception\ValidationException;

class StrProductCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('action:execute')
            ->setDescription('upload file')
            ->addArgument('filePath', InputArgument::REQUIRED, 'Enter csv file path: ')
            ->addOption(
                'testMode',
                null,
                InputOption::VALUE_REQUIRED,
                'Is it test mode?',
                1
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!is_file($input->getArgument('filePath'))) {
            $output->writeln('<error>File not found! Please, check path.</error>');
        } else {
            $now = new \DateTime();
            $errList = [];
            $testMode = 'test' == $input->getOption('testMode') ? true : false;
            $returnErr = false;
            $converterManager = $this->getContainer()->get('converter.manager');
            $productValidator = $this->getContainer()->get('product.validator');

            $file = new \SplFileObject($input->getArgument('filePath'));
            $csvReader = new CsvReader($file);
            $csvReader->setHeaderRowNumber(0);

            $headers = $csvReader->getColumnHeaders();

            if (in_array([Product::CODE], $headers) ||
                in_array([Product::NAME], $headers) ||
                in_array([Product::DESCRIPTION], $headers) ||
                in_array([Product::STOCK], $headers) ||
                in_array([Product::COST], $headers)
            ) {
                $returnErr = true;
            }

            if ($returnErr) {
                $output->writeln('<error>Invalid data headers! Please, check it.</error>');
            } else {

                $workflow = $this->getContainer()->get('workflow.manager')->getWorkflowInstance($csvReader);
                if (is_null($workflow)) {
                    throw new Exception('Runtime error');
                }

                $workflow = $this->getContainer()->get('workflow.manager')->getWorkflowInstance($csvReader);
                $workflow = $converterManager->setStringToIntConverter($workflow);
                $workflow = $converterManager->setStringToDecimalConverter($workflow);
                $workflow = $productValidator->setCorrectProductFilters($workflow);
                $workflow = $converterManager->setProductDiscontinuedConverter($workflow);
                $workflow = $converterManager->setMappingValueConverter($workflow);
                $workflow = $this->getContainer()->get('writer.manager')->setDoctrineWriter($workflow, $testMode);
                $workflow = $this->getContainer()->get('writer.manager')->setConsoleWriter($workflow);
                $result = $this->getContainer()->get('workflow.manager')->execute($workflow);


                $output->writeln('<comment>Action successfully complited!</comment>');
                $output->writeln('<info>Total stock(s): ' . $result->getTotalProcessedCount() . '</info>');

                if ($testMode) {
                    $output->writeln('Candidate to add (update) into DB, stock(s): ' . $result->getSuccessCount());
                } else {
                    $output->writeln('Saved (updated) stock(s): ' . $result->getSuccessCount());
                }

                $output->writeln('Skipped stock(s): ' . $result->getErrorCount());

                /** @var ValidationException $exception */
                foreach ($result->getExceptions() as $exception) {
                    foreach ($exception->getViolations() as $violation) {
                        $output->writeln('<fg=red>Error '.$violation->getMessage().'</fg=red>');
                    }
                }
            }
        }
    }
}