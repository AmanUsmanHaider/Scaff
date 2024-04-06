<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class Course extends Model
{
    use HasFactory;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            //Customizing the log name
            ->useLogName('Permission Group')
            //Log changes to all the $fillable
            ->logFillable()
            //Customizing the description
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}")
            //Logging only the changed attributes
            ->logOnlyDirty()
            //Prevent save logs items that have no changed attribute
            ->dontSubmitEmptyLogs();
    }
    protected $table = 'courses';
    protected $fillable = [
        'course_name', 'course_code'
    ];
    public function subjects()
    {
        return $this->belongsToMany(Subject::class);
    }
}
