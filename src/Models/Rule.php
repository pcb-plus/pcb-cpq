<?php

namespace PcbPlus\PcbCpq\Models;

use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cpq_rules';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cost_id',
        'price',
        'is_unit',
        'multiplier_expression',
        'multiplier_description',
        'is_conditional',
        'condition_expression',
        'condition_description',
        'is_tiered',
        'sort_order',
        'copy_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tiers()
    {
        return $this->hasMany(Tier::class);
    }
}
