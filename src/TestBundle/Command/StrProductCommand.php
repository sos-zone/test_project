<?php

namespace TestBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
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
            $rowNum = 0;
            $saved = 0;
            $skipped = 0;
            $errList = [];
            $testMode = 'test' == $input->getOption('testMode') ? true : false;
            $productsList = [];
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

                if (is_array($fieldBlankResult = $this->getContainer()->get('product.validator')
                    ->getBlankFieldsError($csvReader, $productFields))) {
                    if (count($errList)>0) {
                        $errList = array_merge($errList, $fieldBlankResult);
                    } else {
                        $errList = $fieldBlankResult;
                    }
                }

                if (is_array($tooSmallProducts = $this->getContainer()->get('product.validator')
                    ->getTooSmallProductsError($csvReader, $productFields))) {
                    if (count($errList)>0) {
                        $errList = array_merge($errList, $tooSmallProducts);
                    } else {
                        $errList = $tooSmallProducts;
                    }
                }

                if (is_array($tooBigCostProducts = $this->getContainer()->get('product.validator')
                    ->getTooBigCostProductsError($csvReader, $productFields))) {
                    if (count($errList)>0) {
                        $errList = array_merge($errList, $tooBigCostProducts);
                    } else {
                        $errList = $tooBigCostProducts;
                    }
                }

//                $rowNum = $result->getTotalProcessedCount();
//                $saved = $result->getSuccessCount();
//                $skipped = $result->getErrorCount();

//                $storage = [];
//                $workflow->addWriter(new CallbackWriter(function ($row) use ($storage) {
//                    array_push($storage, $row);
//                }));
//
//                $results = $workflow->process();


                if (is_array($correctProducts = $this->getContainer()->get('product.validator')
                    ->getCorrectProducts($csvReader, $productFields))) {

                    foreach ($correctProducts as $csvProduct) {

                        $now = new \DateTime();

                        if (! $product = $this->getContainer()->get('product.validator')->isProductExists($csvProduct[Product::CODE])) {
                            $product = new Product;
                            $product->setCode($csvProduct[Product::CODE]);
                        }

                        /* convert Discontinued into bool */
                        if (isset($csvProduct[Product::DISCONTINUED]) && 'yes' == $csvProduct[Product::DISCONTINUED]) {
                            $csvProduct[Product::DISCONTINUED] = true;
                        } else {
                            $csvProduct[Product::DISCONTINUED] = false;
                        }

                        $product
                            ->setName($csvProduct[Product::NAME])
                            ->setDescription($csvProduct[Product::DESCRIPTION])
                            ->setStock($csvProduct[Product::STOCK])
                            ->setCost($csvProduct[Product::COST])
                            ->setDiscontinued($csvProduct[Product::DISCONTINUED])
                            ->setStmtimestamp($now);


                        if ($product->isDiscontinued()) {
                            $product->setDtmdiscontinued($now);
                        }

                        array_push($productsList, $product);
                        $saved++;
                    }
                }

                if (!$testMode && !empty($productsList)) {
                    $em = $this->getContainer()->get('doctrine')->getManager();
                    /** @var Product $product */
                    foreach ($productsList as $product) {
                        $em->persist($product);
                    }
                    $em->flush();
                }

                $output->writeln('<comment>Action successfully complited!</comment>');
                $output->writeln('<info>Total stock(s): ' . $rowNum . '</info>');

                if ($testMode) {
                    $output->writeln('Candidate to add (update) into DB, stock(s): ' . $saved);
                } else {
                    $output->writeln('Saved (updated) stock(s): ' . $saved);
                }

                $output->writeln('Skipped stock(s): ' . $skipped);
                foreach ($errList as $error) {
                    $output->writeln('<fg=red>Line ' . $error->getRowNum() . ': ' . $error->getMessage() . '</fg=red>');
                }
            }
        }
    }
}