<?php

namespace TestBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Validator\Constraints as Assert;
use Ddeboer\DataImport\Workflow\StepAggregator;

use Ddeboer\DataImport\Reader\CsvReader;
use Ddeboer\DataImport\Step\FilterStep;
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

            $workflow = new StepAggregator($csvReader);

            $headers = $csvReader->getColumnHeaders();

            if (in_array([Product::CODE], $headers) ||
                in_array([Product::NAME], $headers) ||
                in_array([Product::DESCRIPTION], $headers) ||
                in_array([Product::STOCK], $headers) ||
                in_array([Product::COST], $headers)) {

                array_push($errList, new ProductError($rowNum, ProductValidator::INVALID_DATA_HEADER));
                $returnErr = true;
            }

            if ($returnErr) {
                $output->writeln('<error>Invalid data headers! Please, check it.</error>');
            } else {

                $validator = $this->getContainer()->get('validator');

                $validatorFilter = new \Ddeboer\DataImport\Filter\ValidatorFilter($validator);

                $validatorFilter->throwExceptions(true);
                $validatorFilter->setStrict(false);

                $validatorFilter->add(Product::CODE, new Assert\NotBlank());
                $validatorFilter->add(Product::NAME, new Assert\NotBlank());
                $validatorFilter->add(Product::DESCRIPTION, new Assert\NotBlank());
                $validatorFilter->add(Product::STOCK, new Assert\NotBlank());
                $validatorFilter->add(Product::COST, new Assert\NotBlank());

                $validatorFilter->add(Product::CODE, new Assert\NotNull());
                $validatorFilter->add(Product::NAME, new Assert\NotNull());
                $validatorFilter->add(Product::DESCRIPTION, new Assert\NotNull());
                $validatorFilter->add(Product::STOCK, new Assert\NotNull());
                $validatorFilter->add(Product::COST, new Assert\NotNull());

                $validatorFilter->add(Product::STOCK, new Assert\LessThan(ProductValidator::MIN_STOCK_COUNT));

                $validatorFilter->add(Product::COST, new Assert\LessThan(ProductValidator::MIN_COST));

                $validatorFilter->add(Product::COST, new Assert\GreaterThan(ProductValidator::MAX_COST));

                $filterStep = (new FilterStep())
                    ->add($validatorFilter);

                $result = $workflow
                    ->setSkipItemOnFailure(true)
                    ->addStep($filterStep)
                    ->process();

                $a = 1;

//                $storage = [];
//                $workflow->addWriter(new CallbackWriter(function ($row) use ($storage) {
//                    array_push($storage, $row);
//                }));
//
//                $results = $workflow->process();


//                foreach ($csvReader as $stock) {
//
//                    $rowNum++;
//                    $now = new \DateTime();
//
////                    $validator = $this->getContainer()->get('validator');
////                    if (
////                        count($validator->validate($stock[Product::NAME])) >0 ||
////                        count($validator->validate($stock[Product::CODE])) >0 ||
////                        count($validator->validate($stock[Product::DESCRIPTION])) >0 ||
////                        count($validator->validate($stock[Product::STOCK])) >0 ||
////                        count($validator->validate($stock[Product::COST])) >0
////                    ) {
////                        $skipped++;
////                        array_push($errList, new ProductError($rowNum, ProductValidator::INVALID_DATA));
////                        continue;
////                    }
//
//                    if ('' == $stock[Product::CODE] ||
//                        '' == $stock[Product::NAME] ||
//                        '' == $stock[Product::DESCRIPTION] ||
//                        '' == $stock[Product::STOCK] ||
//                        '' == $stock[Product::COST]
//                        ) {
//                        $skipped++;
//                        array_push($errList, new ProductError($rowNum, ProductValidator::EMPTY_STOCK));
//                        continue;
//                    }
//
//                    /* convert Discontinued into bool */
//                    if (isset($stock[Product::DISCONTINUED]) && 'yes' == $stock[Product::DISCONTINUED]) {
//                        $stock[Product::DISCONTINUED] = true;
//                    } else {
//                        $stock[Product::DISCONTINUED] = false;
//                    }
//
//                    if (
//                        strlen($stock[Product::CODE]) > ProductValidator::MAX_CODE_LENGTH ||
//                        strlen($stock[Product::NAME]) > ProductValidator::MAX_NAME_LENGTH ||
//                        strlen($stock[Product::DESCRIPTION]) > ProductValidator::MAX_DESCRIPTION_LENGTH
//                    ) {
//                        $skipped++;
//                        array_push($errList, new ProductError($rowNum, ProductValidator::TOO_LONG_DATA));
//                        continue;
//                    }
//
//                    if (! $product = $this->getContainer()->get('product.validator')->isProductExists($stock[Product::CODE])) {
//                        $product = new Product;
//                        $product->setCode($stock[Product::CODE]);
//                    }
//
//                    $product
//                        ->setName($stock[Product::NAME])
//                        ->setDescription($stock[Product::DESCRIPTION])
//                        ->setStock($stock[Product::STOCK])
//                        ->setCost($stock[Product::COST])
//                        ->setDiscontinued($stock[Product::DISCONTINUED])
//                        ->setStmtimestamp($now);
//
//                    if ($this->getContainer()->get('product.validator')->isTooFewStocks($product) ||
//                        $this->getContainer()->get('product.validator')->isTooSmallCost($product)
//                    ) {
//
//                        $skipped++;
//                        array_push($errList, new ProductError($rowNum, ProductValidator::TOO_SMALL_STOCK));
//                        continue;
//                    }
//
//                    if ($this->getContainer()->get('product.validator')->isTooBigCost($product)) {
//                        $skipped++;
//                        array_push($errList, new ProductError($rowNum, ProductValidator::TOO_BIG_STOCK));
//                        continue;
//                    }
//
//                    if ($product->isDiscontinued()) {
//                        $product->setDtmdiscontinued($now);
//                    }
//
//                    array_push($productsList, $product);
//                    $saved++;
//                }

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