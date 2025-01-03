<?php

namespace PcbPlus\PcbCpq\Models;

use Illuminate\Database\Eloquent\Model;

class FactorOption extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cpq_factor_options';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'factor_id',
        'name',
        'show_name',
        'value',
        'description',
        'sort_order',
        'copy_id',
    ];
}
