<?php

namespace PcbPlus\PcbCpq\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PcbPlus\PcbCpq\Models\Factor;
use PcbPlus\PcbCpq\Models\FactorOption;

class FactorImport implements ToCollection, WithHeadingRow
{
    /**
     * @var \PcbPlus\PcbCpq\Models\Product
     */
    protected $product;

    /**
     * @param \PcbPlus\PcbCpq\Models\Product $product
     */
    public function __construct($product)
    {
        $this->product = $product;
    }

    /**
     * @param \Illuminate\Support\Collection $rows
     */
    public function collection(Collection $rows)
    {
        $factorItems = $this->buildGroups($rows);

        foreach ($factorItems as $factorItem) {
            $factor = Factor::firstOrCreate(
                [
                    'product_id' => $this->product->id,
                    'code' => $factorItem['code'],
                ],
                [
                    'name' => $factorItem['name'],
                    'show_name' => $factorItem['show_name'],
                    'description' => $factorItem['description'],
                    'is_optional' => $factorItem['is_optional'],
                ]
            );

            if (isset($factorItem['options']) && $factor->is_optional) {
                foreach ($factorItem['options'] as $optionItem) {
                    FactorOption::firstOrCreate(
                        [
                            'factor_id' => $factor->id,
                            'value' => $optionItem['value'],
                        ],
                        [
                            'name' => $optionItem['name'],
                            'show_name' => $optionItem['show_name'],
                            'description' => $optionItem['description'],
                        ]
                    );
                }
            }
        }
    }

    /**
     * @param \Illuminate\Support\Collection $rows
     * @return array
     */
    public function buildGroups($rows)
    {
        return $rows->reduce(function ($items, $row) {
            $factorName = $row['factor_name'] ?? '';
            $factorShowName = $row['factor_show_name'] ?? '';
            $factorCode = $row['factor_code'] ?? '';
            $factorDescription = $row['factor_description'] ?? '';
            $factorIsOptional = (bool) $row['factor_is_optional'] ?? false;
            $optionName = $row['option_name'] ?? '';
            $optionShowName = $row['option_show_name'] ?? '';
            $optionValue = $row['option_value'] ?? '';
            $optionDescription = $row['option_description'] ?? '';

            if ($factorName) {
                $items[] = [
                    'name' => $factorName,
                    'show_name' => $factorShowName,
                    'code' => $factorCode,
                    'description' => $factorDescription,
                    'is_optional' => $factorIsOptional,
                ];
            }

            $factorIndex = count($items) - 1;

            if ($optionName && $factorIndex >= 0) {
                if (! isset($items[$factorIndex]['options'])) {
                    $items[$factorIndex]['options'] = [];
                }

                $items[$factorIndex]['options'][] = [
                    'name' => $optionName,
                    'show_name' => $optionShowName,
                    'value' => $optionValue,
                    'description' => $optionDescription,
                ];
            }

            return $items;
        }, []);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'factor_name',
            'factor_show_name',
            'factor_code',
            'factor_description',
            'factor_is_optional',
            'option_name',
            'option_show_name',
            'option_value',
            'option_description',
        ];
    }
}
