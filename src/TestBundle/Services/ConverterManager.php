<?php

namespace TestBundle\Services;

use Ddeboer\DataImport\ValueConverter\MappingValueConverter;
use TestBundle\Services\ArrayValueConverterMap;
use Ddeboer\DataImport\Workflow;
use Symfony\Component\Validator\Constraints\DateTime;
use TestBundle\Entity\Product;
use Ddeboer\DataImport\Step\ConverterStep;
use Ddeboer\DataImport\Step\MappingStep;

class ConverterManager
{
    public function setMappingValueConverter(Workflow $workflow)
    {
        $codeMappingStep = (new MappingStep())->map('['.Product::CODE.']', '[strProductCode]');
        $nameMappingStep = (new MappingStep())->map('['.Product::NAME.']', '[strProductName]');
        $descMappingStep = (new MappingStep())->map('['.Product::DESCRIPTION.']', '[strProductDesc]');
        $stockMappingStep = (new MappingStep())->map('['.Product::STOCK.']', '[strProductStock]');
        $costMappingStep = (new MappingStep())->map('['.Product::COST.']', '[strProductCost]');
        $discontinuedMappingStep = (new MappingStep())->map('['.Product::DISCONTINUED.']', '[strProductDiscontinued]');

        /** @var Workflow $workflow */
        $workflow->addStep($codeMappingStep);
        $workflow->addStep($nameMappingStep);
        $workflow->addStep($descMappingStep);
        $workflow->addStep($stockMappingStep);
        $workflow->addStep($costMappingStep);
        $workflow->addStep($discontinuedMappingStep);


        return $workflow;
    }

    public function setProductDiscontinuedConverter(Workflow $workflow)
    {
        $converter = new ArrayValueConverterMap([
            Product::DISCONTINUED => [function ($input) {
                if ('' == $input) {
                    return "0";
                } else if ('yes' == $input || 'true' == $input) {
                    return str_replace($input, "1", $input);
                } else {
                    return str_replace($input, "0", $input);
                }
            }]
        ]);

        $converterStep = (new ConverterStep())
            ->add($converter);

        /** @var Workflow $workflow */
        $workflow->addStep($converterStep);


        return $workflow;
    }

    public function setDiscontinuedProductDateConverter(Workflow $workflow, $now)
    {
        $converter = new ArrayValueConverterMap([
            Product::DISCONTINUED => [function ($input) {
                if ('' == $input) {
                    return 'false';
                } else if ('yes' == $input | 'true' == $input) {
                    return str_replace('yes', 'true', $input);
                } else {
                    return str_replace($input, 'false', $input);
                }
            }]
        ]);

        $converterStep = (new ConverterStep())
            ->add($converter);

        /** @var Workflow $workflow */
        $workflow->addStep($converterStep);


        return $workflow;
    }

}