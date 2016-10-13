<?php

namespace TestBundle\Services;

use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Writer\DoctrineWriter;
use TestBundle\Entity\Product;

use Ddeboer\DataImport\Writer\ConsoleTableWriter;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Helper\Table;

class WriterManager
{
    private $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    public function setDoctrineWriter(Workflow $workflow, $testMode = true)
    {
        if (! $testMode) {
            $doctrineWriter = new DoctrineWriter($this->em, 'TestBundle:Product', Product::PRODUCT_DB_FIELDS['CODE']);
            $doctrineWriter->disableTruncate();
            $workflow->addWriter($doctrineWriter);
        }

        return $workflow;
    }

    public function setConsoleWriter(Workflow $workflow)
    {
        $output = new ConsoleOutput();
        $output->setVerbosity(ConsoleOutput::VERBOSITY_DEBUG);

        $table = new Table($output);

        $table->setStyle('compact');

        $workflow->addWriter(new ConsoleTableWriter($output, $table));

        return $workflow;
    }

}