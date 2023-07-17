<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolClass extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'school_id',
        'employee_id',
        'wonde_id'
    ];

    /**
     * Get the Studenst for the SchoolClass.
     */
    public function students(): HasMany
    {
        return $this->hasMany(SchoolClassStudent::class);
    }

    /**
     * Get the Lessons for the SchoolClass.
     */
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }
}
