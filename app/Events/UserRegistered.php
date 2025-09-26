<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class UserRegistered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public ?string $temporaryPassword;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, ?string $temporaryPassword = null)
    {
        $this->user = $user;
        $this->temporaryPassword = $temporaryPassword;
    }
}
