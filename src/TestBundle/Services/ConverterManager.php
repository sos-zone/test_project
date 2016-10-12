<?php

namespace TestBundle\Services;

use TestBundle\Services\ArrayValueConverterMap;
use Ddeboer\DataImport\Workflow;
use TestBundle\Entity\Product;
use Ddeboer\DataImport\Step\ConverterStep;
use Ddeboer\DataImport\Step\MappingStep;

class ConverterManager
{
    public function setMappingValueConverter(Workflow $workflow)
    {
        $codeMappingStep = (new MappingStep())->map('['.Product::CODE.']', '['.Product::PRODUCT_DB_FIELDS['CODE'].']');
        $nameMappingStep = (new MappingStep())->map('['.Product::NAME.']', '['.Product::PRODUCT_DB_FIELDS['NAME'].']');
        $descMappingStep = (new MappingStep())->map('['.Product::DESCRIPTION.']', '['.Product::PRODUCT_DB_FIELDS['DESCRIPTION'].']');
        $stockMappingStep = (new MappingStep())->map('['.Product::STOCK.']', '['.Product::PRODUCT_DB_FIELDS['STOCK'].']');
        $costMappingStep = (new MappingStep())->map('['.Product::COST.']', '['.Product::PRODUCT_DB_FIELDS['COST'].']');
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

    public function setStringToIntConverter(Workflow $workflow)
    {
        $StringToIntConverter = new ArrayValueConverterMap([
            'strProductStock' => [function ($input) {
                if (is_string($input)) {
                    return (int)str_replace(' ', '', $input);
                }
            }]
        ]);

        $converterStep = (new ConverterStep())
            ->add($StringToIntConverter);

        /** @var Workflow $workflow */
        $workflow->addStep($converterStep);


        return $workflow;
    }

    public function setStringToDecimalConverter(Workflow $workflow)
    {
        $stringToDecimalConverter = new ArrayValueConverterMap([
            'strProductCost' => [function ($input) {
                if (is_string($input)) {
                    return floatval(str_replace(' ', '', $input));
                }
            }]
        ]);

        $converterStep = (new ConverterStep())
            ->add($stringToDecimalConverter);

        /** @var Workflow $workflow */
        $workflow->addStep($converterStep);


        return $workflow;
    }

    public function setProductDiscontinuedConverter(Workflow $workflow)
    {
        $converter = new ArrayValueConverterMap([
            'strProductDiscontinued' => [function ($input) {
                if ('' == $input) {
                    return 0;
                } else if ('yes' == $input || 'true' == $input) {
                    return str_replace($input, 1, $input);
                } else {
                    return str_replace($input, 0, $input);
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