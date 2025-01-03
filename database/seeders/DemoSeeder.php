<?php

namespace PcbPlus\PcbCpq\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoSeeder extends Seeder
{
    public function run()
    {
        $versions = [
            [
                'name' => 'version 1.0',
                'number' => '44a9d483-3146-49e9-bc77-2813a633a095',
                'is_submitted' => false,
                'is_active' => false,
                'products' => [
                    [
                        'name' => 'pcb',
                        'show_name' => 'pcb',
                        'code' => 'pcb',
                        'factors' => [
                            [
                                'name' => 'Quantity',
                                'show_name' => '数量',
                                'code' => 'qty',
                                'description' => '单片或连片数量',
                                'is_optional' => false,
                            ],
                            [
                                'name' => 'X Size (mm)',
                                'show_name' => 'X 尺寸（毫米）',
                                'code' => 'x_size_mm',
                                'description' => '单片或连片尺寸',
                                'is_optional' => false,
                            ],
                            [
                                'name' => 'Y Size (mm)',
                                'show_name' => 'Y 尺寸（毫米）',
                                'code' => 'y_size_mm',
                                'description' => '单片或连片尺寸',
                                'is_optional' => false,
                            ],
                            [
                                'name' => 'Layers',
                                'show_name' => '层数',
                                'code' => 'layers',
                                'description' => '',
                                'is_optional' => true,
                                'options' => [
                                    [
                                        'name' => '1 Layer',
                                        'show_name' => '1 层',
                                        'value' => '1_layer',
                                        'description' => '',
                                    ],
                                    [
                                        'name' => '2 Layers',
                                        'show_name' => '2 层',
                                        'value' => '2_layers',
                                        'description' => '',
                                    ],
                                    [
                                        'name' => '4 Layers',
                                        'show_name' => '4 层',
                                        'value' => '4_layers',
                                        'description' => '',
                                    ]
                                ]
                            ],
                        ],
                        'costs' => [
                            [
                                'name' => 'Basic Cost',
                                'show_name' => '工程费',
                                'code' => 'basic_cost',
                                'rules' => [
                                    [
                                        'price' => '200',
                                        'is_unit' => false,
                                        'multiplier_expression' => '',
                                        'multiplier_description' => '',
                                        'is_conditional' => false,
                                        'condition_expression' => '',
                                        'condition_description' => '',
                                        'is_tiered' => false,
                                        'tiers' => [],
                                    ],
                                ],
                            ],
                            [
                                'name' => 'Board Cost',
                                'show_name' => '板费',
                                'code' => 'board_cost',
                                'rules' => [
                                    [
                                        'price' => '500',
                                        'is_unit' => true,
                                        'multiplier_expression' => 'x_size_mm * y_size_mm * qty / 1000 / 1000',
                                        'multiplier_description' => '总面积',
                                        'is_conditional' => true,
                                        'condition_expression' => 'layers in ["1_layer", "2_layers"]',
                                        'condition_description' => '层数 在 [1层, 2层]',
                                        'is_tiered' => true,
                                        'tiers' => [
                                            [
                                                'price' => '480',
                                                'condition_expression' => 'x_size_mm * y_size_mm * qty / 1000 / 1000 >= 2 and x_size_mm * y_size_mm * qty / 1000 / 1000 < 10',
                                                'condition_description' => '总面积 >= 2 and 总面积 < 10',
                                            ],
                                            [
                                                'price' => '450',
                                                'condition_expression' => 'x_size_mm * y_size_mm * qty / 1000 / 1000 >= 10',
                                                'condition_description' => '总面积 >= 10',
                                            ],
                                        ],
                                    ],
                                    [
                                        'price' => '600',
                                        'is_unit' => true,
                                        'multiplier_expression' => 'x_size_mm * y_size_mm * qty / 1000 / 1000',
                                        'multiplier_description' => '总面积',
                                        'is_conditional' => true,
                                        'condition_expression' => 'layers == "4_layer"',
                                        'condition_description' => '层数 等于 4层',
                                        'is_tiered' => true,
                                        'tiers' => [
                                            [
                                                'price' => '580',
                                                'condition_expression' => 'x_size_mm * y_size_mm * qty / 1000 / 1000 >= 2 and x_size_mm * y_size_mm * qty / 1000 / 1000 < 10',
                                                'condition_description' => '总面积 >= 2 and 总面积 < 10',
                                            ],
                                            [
                                                'price' => '550',
                                                'condition_expression' => 'x_size_mm * y_size_mm * qty / 1000 / 1000 > 10',
                                                'condition_description' => '总面积 > 10',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'leadtimes' => [
                            [
                                'title' => '5-6 天',
                                'is_conditional' => true,
                                'condition_expression' => 'layers in ["1_layer", "2_layers"] and x_size_mm * y_size_mm * qty / 1000 / 1000 < 10',
                                'condition_description' => '层数 在 [1层, 2层] and 总面积 < 10',
                                'options' => [
                                    [
                                        'name' => '5-6 days',
                                        'show_name' => '5-6 天',
                                        'min_days' => 5,
                                        'max_days' => 6,
                                        'price' => 0,
                                        'is_default' => true,
                                    ],
                                    [
                                        'name' => '3-4 days',
                                        'show_name' => '3-4 天',
                                        'min_days' => 3,
                                        'max_days' => 4,
                                        'price' => 200,
                                        'is_default' => false,
                                    ],
                                ],
                            ],
                            [
                                'title' => '8-10 天',
                                'is_conditional' => true,
                                'condition_expression' => 'layers in ["1_layer", "2_layers"] and x_size_mm * y_size_mm * qty / 1000 / 1000 >= 10',
                                'condition_description' => '层数 在 [1层, 2层] and 总面积 >= 10',
                                'options' => [
                                    [
                                        'name' => '8-10 days',
                                        'show_name' => '8-10 天',
                                        'min_days' => 8,
                                        'max_days' => 10,
                                        'price' => 0,
                                        'is_default' => true,
                                    ],
                                    [
                                        'name' => '7-8 days',
                                        'show_name' => '7-8 天',
                                        'min_days' => 7,
                                        'max_days' => 8,
                                        'price' => 220,
                                        'is_default' => false,
                                    ],
                                ],
                            ],
                            [
                                'title' => '7-8 天',
                                'is_conditional' => true,
                                'condition_expression' => 'layers == "4_layer" and x_size_mm * y_size_mm * qty / 1000 / 1000 < 10',
                                'condition_description' => '层数 等于 4层 and 总面积 < 10',
                                'options' => [
                                    [
                                        'name' => '7-8 days',
                                        'show_name' => '7-8 天',
                                        'min_days' => 7,
                                        'max_days' => 8,
                                        'price' => 0,
                                        'is_default' => true,
                                    ],
                                    [
                                        'name' => '5-6 days',
                                        'show_name' => '5-6 天',
                                        'min_days' => 5,
                                        'max_days' => 6,
                                        'price' => 250,
                                        'is_default' => false,
                                    ],
                                ],
                            ],
                            [
                                'title' => '10-12 天',
                                'is_conditional' => true,
                                'condition_expression' => 'layers == "4_layer" and x_size_mm * y_size_mm * qty / 1000 / 1000 >= 10',
                                'condition_description' => '层数 等于 4层 and 总面积 >= 10',
                                'options' => [
                                    [
                                        'name' => '10-12 days',
                                        'show_name' => '10-12 天',
                                        'min_days' => 10,
                                        'max_days' => 12,
                                        'price' => 0,
                                        'is_default' => true,
                                    ],
                                    [
                                        'name' => '9-10 days',
                                        'show_name' => '9-10 天',
                                        'min_days' => 9,
                                        'max_days' => 10,
                                        'price' => 300,
                                        'is_default' => false,
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => 'Stencil',
                        'show_name' => '钢网',
                        'code' => 'stencil',
                        'factors' => [
                            [
                                'name' => 'Stencil Size',
                                'show_name' => '钢网尺寸',
                                'code' => 'size',
                                'description' => '',
                                'is_optional' => true,
                                'options' => [
                                    [
                                        'name' => '370*470mm',
                                        'show_name' => '370*470mm',
                                        'value' => '370*470mm',
                                        'description' => '',
                                    ],
                                    [
                                        'name' => '420*520mm',
                                        'show_name' => '420*520mm',
                                        'value' => '420*520mm',
                                        'description' => '',
                                    ],
                                    [
                                        'name' => '450*550mm',
                                        'show_name' => '450*550mm',
                                        'value' => '450*550mm',
                                        'description' => '',
                                    ]
                                ]
                            ],
                        ],
                        'costs' => [
                            [
                                'name' => 'Stencil Cost',
                                'show_name' => '钢网费',
                                'code' => 'stencil_cost',
                                'rules' => [
                                    [
                                        'price' => '160',
                                        'is_unit' => true,
                                        'multiplier_expression' => 'qty',
                                        'multiplier_description' => '数量',
                                        'is_conditional' => true,
                                        'condition_expression' => 'size in ["370*470mm", "420*520mm"]',
                                        'condition_description' => '尺寸 在 [370*470mm, 420*520mm]',
                                        'is_tiered' => false,
                                        'tiers' => [],
                                    ],
                                    [
                                        'price' => '200',
                                        'is_unit' => true,
                                        'multiplier_expression' => 'qty',
                                        'multiplier_description' => '数量',
                                        'is_conditional' => true,
                                        'condition_expression' => 'size == "450*550mm"',
                                        'condition_description' => '尺寸 等于 "450*550mm"',
                                        'is_tiered' => false,
                                        'tiers' => [],
                                    ],
                                ],
                            ],
                        ],
                        'leadtimes' => [
                            [
                                'title' => '5-6 天',
                                'is_conditional' => true,
                                'condition_expression' => 'layers in ["1_layer", "2_layers"] and x_size_mm * y_size_mm * qty / 1000 / 1000 < 10',
                                'condition_description' => '层数 在 [1层, 2层] and 总面积 < 10',
                                'options' => [
                                    [
                                        'name' => '5-6 days',
                                        'show_name' => '5-6 天',
                                        'min_days' => 5,
                                        'max_days' => 6,
                                        'price' => 0,
                                        'is_default' => true,
                                    ],
                                    [
                                        'name' => '3-4 days',
                                        'show_name' => '3-4 天',
                                        'min_days' => 3,
                                        'max_days' => 4,
                                        'price' => 200,
                                        'is_default' => false,
                                    ],
                                ],
                            ],
                            [
                                'title' => '1-2 天',
                                'is_conditional' => false,
                                'condition_expression' => '',
                                'condition_description' => '',
                                'options' => [
                                    [
                                        'name' => '1-2 days',
                                        'show_name' => '1-2 天',
                                        'min_days' => 1,
                                        'max_days' => 2,
                                        'price' => 0,
                                        'is_default' => true,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        ];

        $timestamp = Carbon::now();

        foreach ($versions as $version) {
            $versionId = DB::table('cpq_versions')->insertGetId([
                'name' => $version['name'],
                'number' => $version['number'],
                'is_submitted' => $version['is_submitted'],
                'is_active' => $version['is_active'],
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ]);

            foreach ($version['products'] as $product) {
                $productId = DB::table('cpq_products')->insertGetId([
                    'version_id' => $versionId,
                    'name' => $product['name'],
                    'show_name' => $product['show_name'],
                    'code' => $product['code'],
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp
                ]);

                foreach ($product['factors'] as $factor) {
                    $factorId = DB::table('cpq_factors')->insertGetId([
                        'product_id' => $productId,
                        'name' => $factor['name'],
                        'show_name' => $factor['show_name'],
                        'code' => $factor['code'],
                        'description' => $factor['description'],
                        'is_optional' => $factor['is_optional'],
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp
                    ]);

                    if ($factor['is_optional']) {
                        foreach ($factor['options'] as $factorOption) {
                            DB::table('cpq_factor_options')->insert([
                                'factor_id' => $factorId,
                                'name' => $factorOption['name'],
                                'show_name' => $factorOption['show_name'],
                                'value' => $factorOption['value'],
                                'description' => $factorOption['description'],
                                'created_at' => $timestamp,
                                'updated_at' => $timestamp
                            ]);
                        }
                    }
                }

                foreach ($product['costs'] as $cost) {
                    $costId = DB::table('cpq_costs')->insertGetId([
                        'product_id' => $productId,
                        'name' => $cost['name'],
                        'show_name' => $cost['show_name'],
                        'code' => $cost['code'],
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ]);

                    foreach ($cost['rules'] as $rule) {
                        $ruleId = DB::table('cpq_rules')->insertGetId([
                            'cost_id' => $costId,
                            'price' => $rule['price'],
                            'is_unit' => $rule['is_unit'],
                            'multiplier_expression' => $rule['multiplier_expression'],
                            'multiplier_description' => $rule['multiplier_description'],
                            'is_conditional' => $rule['is_conditional'],
                            'condition_expression' => $rule['condition_expression'],
                            'condition_description' => $rule['condition_description'],
                            'is_tiered' => $rule['is_tiered'],
                            'created_at' => $timestamp,
                            'updated_at' => $timestamp,
                        ]);

                        if ($rule['is_tiered']) {
                            foreach ($rule['tiers'] as $tier) {
                                $tierId = DB::table('cpq_tiers')->insertGetId([
                                    'rule_id' => $ruleId,
                                    'price' => $tier['price'],
                                    'condition_expression' => $tier['condition_expression'],
                                    'condition_description' => $tier['condition_description'],
                                    'created_at' => $timestamp,
                                    'updated_at' => $timestamp,
                                ]);
                            }
                        }
                    }
                }

                foreach ($product['leadtimes'] as $leadtime) {
                    $leadtimeId = DB::table('cpq_leadtimes')->insertGetId([
                        'product_id' => $productId,
                        'title' => $leadtime['title'],
                        'is_conditional' => $leadtime['is_conditional'],
                        'condition_expression' => $leadtime['condition_expression'],
                        'condition_description' => $leadtime['condition_description'],
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ]);

                    foreach ($leadtime['options'] as $leadtimeOption) {
                        DB::table('cpq_leadtime_options')->insert([
                            'leadtime_id' => $leadtimeId,
                            'name' => $leadtimeOption['name'],
                            'show_name' => $leadtimeOption['show_name'],
                            'min_days' => $leadtimeOption['min_days'],
                            'max_days' => $leadtimeOption['max_days'],
                            'price' => $leadtimeOption['price'],
                            'is_default' => $leadtimeOption['is_default'],
                            'created_at' => $timestamp,
                            'updated_at' => $timestamp,
                        ]);
                    }
                }
            }
        }
    }
}
