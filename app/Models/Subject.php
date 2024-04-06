<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class Subject extends Model
{
    use HasApiTokens, HasFactory,HasRoles, Notifiable, LogsActivity;
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            //Customizing the log name
            ->useLogName('Subject')
            //Log changes to all the $fillable
            ->logFillable()
            //Customizing the description
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}")
            //Logging only the changed attributes
            ->logOnlyDirty()
            //Prevent save logs items that have no changed attribute
            ->dontSubmitEmptyLogs();
    }
    protected $table = 'subjects';
    protected $fillable = [
        'subject_name', 'subject_code'
    ];
    public function courses()
    {
        return $this->belongsToMany(Course::class);
    }
}
