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
use TestBundle\Helper\ProductError;
use TestBundle\Services\ProductValidator;

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

                $productFields = [
                    Product::CODE,
                    Product::NAME,
                    Product::DESCRIPTION,
                    Product::STOCK,
                    Product::COST,
                ];

                $workflow = $this->getContainer()->get('workflow.manager')->getWorkflowInstance($csvReader);
                if (is_null($workflow)) {
                    throw new Exception('Runtime error');
                }

                /*  Get errors row instancies  */
                if (is_array($fieldBlankResult = $this->getContainer()->get('product.validator')
                    ->getBlankFieldsErrors($workflow, $productFields))) {
                    if (count($errList)>0) {
                        $errList = array_merge($errList, $fieldBlankResult);
                    } else {
                        $errList = $fieldBlankResult;
                    }
                }

                if (is_array($tooSmallProducts = $this->getContainer()->get('product.validator')
                    ->getTooSmallProductsError($workflow, $productFields))) {
                    if (count($errList)>0) {
                        $errList = array_merge($errList, $tooSmallProducts);
                    } else {
                        $errList = $tooSmallProducts;
                    }
                }

                if (is_array($tooBigCostProducts = $this->getContainer()->get('product.validator')
                    ->getTooBigCostProductsError($workflow, $productFields))) {
                    if (count($errList)>0) {
                        $errList = array_merge($errList, $tooBigCostProducts);
                    } else {
                        $errList = $tooBigCostProducts;
                    }
                }

                $workflow = $this->getContainer()->get('product.validator')->setCorrectProductFilters($workflow, $productFields);
                $workflow = $this->getContainer()->get('converter.manager')->setMappingValueConverter($workflow);
//                $workflow = $this->getContainer()->get('converter.manager')->setProductDiscontinuedConverter($workflow);
//                $workflow = $this->getContainer()->get('converter.manager')->setDiscontinuedProductDateConverter($workflow, $now);
                $workflow = $this->getContainer()->get('writer.manager')->setDoctrineWriter($workflow, $testMode);
                $result = $this->getContainer()->get('workflow.manager')->execute($workflow);


                $output->writeln('<comment>Action successfully complited!</comment>');
                $output->writeln('<info>Total stock(s): ' . $result->getTotalProcessedCount() . '</info>');

                if ($testMode) {
                    $output->writeln('Candidate to add (update) into DB, stock(s): ' . $result->getSuccessCount());
                } else {
                    $output->writeln('Saved (updated) stock(s): ' . $result->getSuccessCount());
                }

                $output->writeln('Skipped stock(s): ' . $result->getErrorCount());
                foreach ($errList as $error) {
//                    $output->writeln('<fg=red>Line ' . $error->getRowNum() . ': ' . $error->getMessage() . '</fg=red>');
                }
            }
        }
    }
}