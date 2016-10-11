<?php

namespace TestBundle\Services;

use Ddeboer\DataImport\ValueConverter\MappingValueConverter;
use Ddeboer\DataImport\ValueConverter\ArrayValueConverterMap;
use Ddeboer\DataImport\Workflow;
use Symfony\Component\Validator\Constraints\DateTime;
use TestBundle\Entity\Product;
use Ddeboer\DataImport\Step\ConverterStep;

class ConverterManager
{
    public function setMappingValueConverter(Workflow $workflow)
    {
        $converter = new MappingValueConverter([
            'strProductCode' => Product::CODE,
            'strProductName' => Product::NAME,
            'strProductDesc' => Product::DESCRIPTION,
            'strProductStock' => Product::STOCK,
            'strProductCost' => Product::COST,
            'strProductDiscontinued' => Product::DISCONTINUED,
        ]);

//        call_user_func($converter, 'strProductCode');
//        call_user_func($converter, 'strProductName');
//        call_user_func($converter, 'strProductDesc');
//        call_user_func($converter, 'strProductStock');
//        call_user_func($converter, 'strProductCost');
//        call_user_func($converter, 'strProductDiscontinued');

        $converterStep = (new ConverterStep())
            ->add($converter);

        /** @var Workflow $workflow */
        $workflow->addStep($converterStep);


        return $workflow;
    }

    public function setProductDiscontinuedConverter(Workflow $workflow)
    {
        $converter = new ArrayValueConverterMap([
            Product::DISCONTINUED => [function ($input) {
                if ('' == $input) {
                    return 'false';
                } else if ('yes' == $input || 'true' == $input) {
                    return str_replace($input, 'true', $input);
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

    public function setDiscontinuedProductDateConverter(Workflow $workflow, $now)
    {
        $converter = new ArrayValueConverterMap([
            Product::DISCONTINUED => function ($input) {
                if ('' == $input) {
                    return 'false';
                } else if ('yes' == $input | 'true' == $input) {
                    return str_replace('yes', 'true', $input);
                } else {
                    return str_replace($input, 'false', $input);
                }
            }
        ]);

        $converterStep = (new ConverterStep())
            ->add($converter);

        /** @var Workflow $workflow */
        $workflow->addStep($converterStep);


        return $workflow;
    }

}