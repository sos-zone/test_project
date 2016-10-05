<?php

namespace TestBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Validator\Constraints\DateTime;
use TestBundle\Entity\Product;
use TestBundle\Helper\ProductError;
use TestBundle\Services\ProductErrorManager;

class StrProductCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('action:execute')
            ->setDescription('upload file')
            ->addArgument('filePath', InputArgument::REQUIRED, 'Enter csv file path like \'\': ')
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

            $content = fopen($input->getArgument('filePath'), "r");

            while (($stock = fgetcsv($content)) !== false) {

                if ('Product Code' == $stock[0]) {
                    continue;
                }

                $rowNum++;
                $now = new \DateTime();

                $isNext = false;
                for ($i=0; $i<5; $i++) {
                    /* is $stock contains 5 items*/
                    if (!isset($stock[$i])) {
                        $skipped++;
                        array_push($errList, new ProductError($rowNum, ProductErrorManager::INVALID_DATA));
                        $isNext = true;
                        break;
                    }

                    /* convert Discontinued into bool */
                    if(isset($stock[5]) && 'yes' == $stock[5]) {
                        $stock[5] = true;
                    } else {
                        $stock[5] = false;
                    }
                }
                if ($isNext) {
                    continue;
                }

                if (strlen($stock[0])>10 || strlen($stock[1])>50 || strlen($stock[2])>255) {
                    $skipped++;
                    array_push($errList, new ProductError($rowNum, ProductErrorManager::TOO_LONG_DATA));
                    continue;
                }

                if (''==$stock[3]) {
                    $skipped++;
                    array_push($errList, new ProductError($rowNum, ProductErrorManager::EMPTY_STOCK));
                    continue;
                }

                $product = new Product;
                $product
                    ->setCode($stock[0])
                    ->setName($stock[1])
                    ->setDescription($stock[2])
                    ->setStock($stock[3])
                    ->setCostInGBP($stock[4])
                    ->setDiscontinued($stock[5])
                    ->setStmtimestamp($now)
                ;

                if ($product->getCostInGBP()<5 && $product->getStock()<10) {
                    $skipped++;
                    array_push($errList, new ProductError($rowNum, ProductErrorManager::TOO_SMALL_STOCK));
                    continue;
                }

                if ($product->getCostInGBP()>1000) {
                    $skipped++;
                    array_push($errList, new ProductError($rowNum, ProductErrorManager::TOO_BIG_STOCK));
                    continue;
                }

                if ($product->isDiscontinued()) {
                    $product->setDtmdiscontinued($now);
                }

                $strProduct = $this->getContainer()->get('product.repository')->findByCode($stock[0]);
                if ($strProduct) {
                    $skipped++;
                    array_push($errList, new ProductError($rowNum, ProductErrorManager::DUPLICATE_CODE));
                    continue;
                }

                $em = $this->getContainer()->get('doctrine')->getEntityManager();
                $em->persist($product);
                $em->flush();
                $saved++;
            }

            $output->writeln('<comment>Action successfully complited!</comment>');
            $output->writeln('<info>Total stock(s): '.$rowNum.'</info>');
            $output->writeln('Saved stock(s): '.$saved);
            $output->writeln('Skipped stock(s): '.$skipped);
            foreach ($errList as $error) {
                $output->writeln('<fg=red>'.$error->getRowNum().'. '.$error->getMessage().'</fg=red>');
            }
        }
    }
}