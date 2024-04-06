<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Models\Permission;

class Module extends Model
{
    use HasFactory,LogsActivity;
    /**
     * Activity Log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            //Customizing the log name
            ->useLogName('Module')
            //Log changes to all the $fillable
            ->logFillable()
            //Customizing the description
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}")
            //Logging only the changed attributes
            ->logOnlyDirty()
            //Prevent save logs items that have no changed attribute
            ->dontSubmitEmptyLogs();
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $table = 'modules';
    protected $fillable = [
        'name','slug','route_name','created_at','updated_at'
    ];
    /**
     * Relationship
     *
     */
    public function permissions(){
        return $this->hasMany(Permission::class,'module_id');
    }
    public function user(){
        return $this->belongsToMany(User::class,'user_modules','module_id','user_id');
    }
    public function seo() {
        return $this->morphOne(SeoDetails::class, 'seoable');
    }
}
