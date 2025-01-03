<?php

namespace PcbPlus\PcbCpq\Models;

use Illuminate\Database\Eloquent\Model;

class Leadtime extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cpq_leadtimes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'title',
        'is_conditional',
        'condition_expression',
        'condition_description',
        'sort_order',
        'copy_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function options()
    {
        return $this->hasMany(LeadtimeOption::class);
    }
}
