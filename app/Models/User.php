<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles;


    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function profile(): MorphOne
    {
        return $this->morphOne(Profile::class, 'profileable');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'participant_id');
    }

    public function chief()
    {
        return $this->hasMany(User::class, 'id');
    }

    public function certificate()
    {
        return $this->belongsTo(Certificate::class, 'id', 'user_id');
    }

    // public function toMail(object $notifiable): MailMessage
    // {
    //     $url = url('/invoice/' . $this->invoice->id);

    //     return (new MailMessage)
    //         ->greeting('Hello!')
    //         ->line('One of your invoices has been paid!')
    //         ->lineIf($this->amount > 0, "Amount paid: {$this->amount}")
    //         ->action('View Invoice', $url)
    //         ->line('Thank you for using our application!');
    // }
}
