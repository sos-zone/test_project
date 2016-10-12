<?php

namespace TestBundle\Services;

use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Writer\DoctrineWriter;

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
            $doctrineWriter = new DoctrineWriter($this->em, 'TestBundle:Product', 'strProductCode');
            $doctrineWriter->disableTruncate();
            $workflow->addWriter($doctrineWriter);
        }

        return $workflow;
    }

}