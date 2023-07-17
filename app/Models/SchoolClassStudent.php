<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SchoolClassStudent extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'school_class_id',
        'student_id'
    ];

    /**
     * Get the Student that is in the class.
     */
    public function student(): HasOne
    {
        return $this->hasOne(Student::class, 'id', 'student_id');
    }
}
