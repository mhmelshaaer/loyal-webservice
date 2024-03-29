<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActionLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'data_row_id',
        'data_type',
        'scope_id',
        'action_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        
    ];

    /**
     * Get the Scope that owns the ActionLog.
     */
    public function scope()
    {
        return $this->belongsTo('App\Models\Scope');
    }

    /**
     * Get the Action that owns the ActionLog.
     */
    public function action()
    {
        return $this->belongsTo('App\Models\Action');
    }
}
