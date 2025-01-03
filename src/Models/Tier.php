<?php

namespace PcbPlus\PcbCpq\Models;

use Illuminate\Database\Eloquent\Model;

class Tier extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cpq_tiers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'rule_id',
        'price',
        'condition_expression',
        'condition_description',
        'sort_order',
        'copy_id',
    ];
}
