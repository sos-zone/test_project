<?php

namespace TestBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
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
            $rowNum = 0;
            $saved = 0;
            $skipped = 0;
            $errList = [];
            $testMode = 'test' == $input->getOption('testMode') ? true : false;
            $productList = [];
            $returnErr = false;

            $file = new \SplFileObject($input->getArgument('filePath'));
            $reader = new CsvReader($file);
            $reader->setHeaderRowNumber(0);

            $headers = $reader->getColumnHeaders();
            if (in_array([Product::CODE], $headers) ||
                in_array([Product::NAME], $headers) ||
                in_array([Product::DESCRIPTION], $headers) ||
                in_array([Product::STOCK], $headers) ||
                in_array([Product::COST_IN_GBP], $headers)) {

                array_push($errList, new ProductError($rowNum, ProductValidator::INVALID_DATA_HEADER));
                $returnErr = true;
            }

            if ($returnErr) {
                $output->writeln('<error>Invalid data headers! Please, check it.</error>');
            } else {
                foreach ($reader as $stock) {

                    if ('Product Code' == $stock[Product::CODE]) {
                        continue;
                    }

                    $rowNum++;
                    $now = new \DateTime();

                    $isNext = false;
                    for ($i = 0; $i < 5; $i++) {
                        /* is $stock contains 5 items*/
                        if (!isset($stock[$i])) {
                            $skipped++;
                            array_push($errList, new ProductError($rowNum, ProductValidator::INVALID_DATA));
                            $isNext = true;
                            break;
                        }

                        /* convert Discontinued into bool */
                        if (isset($stock[Product::DISCONTINUED]) && 'yes' == $stock[Product::DISCONTINUED]) {
                            $stock[Product::DISCONTINUED] = true;
                        } else {
                            $stock[Product::DISCONTINUED] = false;
                        }
                    }
                    if ($isNext) {
                        continue;
                    }

                    if (
                        strlen($stock[Product::CODE]) > ProductValidator::MAX_CODE_LENGTH ||
                        strlen($stock[Product::NAME]) > ProductValidator::MAX_NAME_LENGTH ||
                        strlen($stock[Product::DESCRIPTION]) > ProductValidator::MAX_CODE_LENGTH
                    ) {
                        $skipped++;
                        array_push($errList, new ProductError($rowNum, ProductValidator::TOO_LONG_DATA));
                        continue;
                    }

                    if ('' == $stock[3]) {
                        $skipped++;
                        array_push($errList, new ProductError($rowNum, ProductValidator::EMPTY_STOCK));
                        continue;
                    }

                    $product = new Product;
                    $product
                        ->setCode($stock[Product::CODE])
                        ->setName($stock[Product::NAME])
                        ->setDescription($stock[Product::DESCRIPTION])
                        ->setStock($stock[Product::STOCK])
                        ->setCostInGBP($stock[Product::COST_IN_GBP])
                        ->setDiscontinued($stock[Product::DISCONTINUED])
                        ->setStmtimestamp($now);

                    if ($this->getContainer()->get('product.validator')->isTooFewStocks($product) ||
                        $this->getContainer()->get('product.validator')->isTooSmallCost($product)
                    ) {

                        $skipped++;
                        array_push($errList, new ProductError($rowNum, ProductValidator::TOO_SMALL_STOCK));
                        continue;
                    }

                    if ($this->getContainer()->get('product.validator')->isTooBigCost($product)) {
                        $skipped++;
                        array_push($errList, new ProductError($rowNum, ProductValidator::TOO_BIG_STOCK));
                        continue;
                    }

                    if ($product->isDiscontinued()) {
                        $product->setDtmdiscontinued($now);
                    }

                    if ($this->getContainer()->get('product.validator')->isProductExists($product)) {
                        $skipped++;
                        array_push($errList, new ProductError($rowNum, ProductValidator::DUPLICATE_CODE));
                        continue;
                    }

                    array_push($productList, $product);

                    $saved++;
                }

                if (!$testMode && !empty($productList)) {
                    $em = $this->getContainer()->get('doctrine')->getEntityManager();
                    foreach ($productList as $product) {
                        $em->persist($product);
                    }
                    $em->flush();
                }

                $output->writeln('<comment>Action successfully complited!</comment>');
                $output->writeln('<info>Total stock(s): ' . $rowNum . '</info>');

                if ($testMode) {
                    $output->writeln('Candidate to add into DB, stock(s): ' . $saved);
                } else {
                    $output->writeln('Saved stock(s): ' . $saved);
                }

                $output->writeln('Skipped stock(s): ' . $skipped);
                foreach ($errList as $error) {
                    $output->writeln('<fg=red>Line ' . $error->getRowNum() . ': ' . $error->getMessage() . '</fg=red>');
                }
            }
        }
    }
}