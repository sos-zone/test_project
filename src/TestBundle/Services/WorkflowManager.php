<?php

namespace TestBundle\Services;

use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Workflow\StepAggregator;
use Ddeboer\DataImport\Reader\CsvReader;

class WorkflowManager
{
    /**
     * get workflow
     *
     * @return mixed
     */
    public function getWorkflowInstance($reader)
    {
        if ($reader instanceof CsvReader) {
            $workflow = new StepAggregator($reader);
            $workflow->setSkipItemOnFailure(true);

            return $workflow;
        } else {
            return;
        }
    }

    public function execute(Workflow $workflow)
    {
        return $workflow->process();
    }
}
