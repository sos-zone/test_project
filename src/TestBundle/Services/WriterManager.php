<?php

namespace TestBundle\Services;

use Ddeboer\DataImport\ValueConverter\MappingValueConverter;
use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Writer\DoctrineWriter;
use TestBundle\Entity\Product;

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
            $doctrineWriter = new DoctrineWriter($this->em, 'TestBundle:Product');
            $workflow->addWriter($doctrineWriter);
        }

        return $workflow;
    }

}