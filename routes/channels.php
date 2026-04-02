<?php

use Illuminate\Support\Facades\Broadcast;

// exam.blocked.{userId}
// Channel untuk mendengarkan perintah "Unblock" dari Guru/Admin
Broadcast::channel('exam.blocked.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId && $user->role === 'murid';
});
