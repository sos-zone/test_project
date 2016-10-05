<?php
namespace TestBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use TestBundle\Entity\Product;

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
            $total = 0;
            $saved = 0;
            $skipped = 0;
            $content = fopen($input->getArgument('filePath'), "r");

            while (($stock = fgetcsv($content)) !== false) {

                /* check is $stock contains 5 items. if no - go to next $stock */
                $isNext = false;
                for ($i=0; $i<5; $i++) {
                    if (!isset($stock[$i])) {
                        $skipped++;
                        $isNext = true;
                        break;
                    }
                }
                if ($isNext) {
                    continue;
                }

                if ('Product Code' == $stock[0]) {
                    continue;
                }

                $total++;

                if (strlen($stock[0])>10 || strlen($stock[1])>50 || strlen($stock[2])>255) {
                    $skipped++;
                    continue;
                }

                if ($stock[0]<5 && $stock[3]<10) {
                    $skipped++;
                    continue;
                }

                $strProduct = $this->getContainer()->get('product.repository')->findByCode($stock[0]);
                if ($strProduct) {
                    $skipped++;
                    continue;
                }

                $product = new Product;
                $product
                    ->setCode($stock[0])
                    ->setName($stock[1])
                    ->setDescription($stock[2])
                    ->setStmtimestamp(new \DateTime())
                ;

                $em = $this->getContainer()->get('doctrine')->getEntityManager();
                $em->persist($product);
                $em->flush();
                $saved++;
            }

            $output->writeln('Action successfully complited!');
            $output->writeln('Total stock(s): '.$total);
            $output->writeln('Saved stock(s): '.$saved);
            $output->writeln('Skipped stock(s): '.$skipped);
        }
    }
}