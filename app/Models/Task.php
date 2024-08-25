<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'due_date',
        'status',
        'user_id',
        'category_id'
    ];

    protected $searchable = [
        'title',
        'description',
        'due_date',
        'status',
    ];

    protected $filterable = [
        'due_date',
        'status',
    ];
    /**
     * @belongsTo relationship with user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        // adding pending as status if not send from store request
        static::creating(function ($task) {
            if (!request()->has('status')) {
                $task->status = 'pending';
            }
        });
        // Adding user_id for task on saving
        static::saving(function ($task) {
            if (Auth::check()) {
                $task->user_id = Auth::id();
            }
        });
    }

    public function getSearchable()
    {
        return $this->searchable;
    }

    public function getFilterable()
    {
        return $this->filterable;
    }
}
