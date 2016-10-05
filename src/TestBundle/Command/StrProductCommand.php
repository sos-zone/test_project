<?php
namespace TestBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use TestBundle\Entity\Tblproductdata;

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

                $strProduct = $this->getContainer()->get('strProduct.repository')->findOneBy(
                    [
                        'strProductCode' => $stock[0]
                    ]
                );
                if ($strProduct) {
                    $skipped++;
                    continue;
                }

                $tblProductData = new Tblproductdata;
                $tblProductData
                    ->setStrproductcode($stock[0])
                    ->setProductName($stock[1])
                    ->setStrproductdesc($stock[2])
                    ->setStmtimestamp(new \DateTime())
                ;

                $em = $this->getContainer()->get('doctrine')->getEntityManager();
                $em->persist($tblProductData);
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