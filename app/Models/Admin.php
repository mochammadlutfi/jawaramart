<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\AdminEmailVerificationNotification;
use App\Notifications\AdminResetPasswordNotification as Notification;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    use HasRoles;
    public function guardName()
    {
      return 'admin';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Custom password reset notification.
     * 
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new Notification($token));
    }

    /**
     * Send email verification notice.
     * 
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new AdminEmailVerificationNotification);
    }

    
    protected $appends = [
        'avatar_url'
    ];

    
    public function getAvatarUrlAttribute($value)
    {
        if($this->attributes['avatar']){
            return asset($this->attributes['avatar']);
        }else{
            return asset('images/avatar.jpg');
        }
    }

}
