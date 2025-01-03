<?php

namespace PcbPlus\PcbCpq\Models;

use Illuminate\Database\Eloquent\Model;

class LeadtimeOption extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cpq_leadtime_options';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'leadtime_id',
        'name',
        'show_name',
        'min_days',
        'max_days',
        'price',
        'is_default',
        'sort_order',
        'copy_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_default' => 'bool',
    ];
}
