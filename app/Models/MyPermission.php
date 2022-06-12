<?php

namespace App\Models;

use Spatie\Permission\Models\Permission;

class MyPermission extends Permission
{

    protected $appends = [
        'module'
    ];


    // public function modules()
    // {
    //     return $this->belongsTo(Module::class, 'module_id');
    // }
    
    
    public function getModuleAttribute($value)
    {
        $module =  $this->belongsTo(Module::class, 'module_id')->first();
        // if($this->modules->name){
        //     return asset($this->attributes['avatar']);
        // }else{
        //     return asset('images/avatar.jpg');
        // }
        if($module){
            return $module->name;
        }else{
            return;
        }
    }

}
