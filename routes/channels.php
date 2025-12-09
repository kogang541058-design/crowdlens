<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Admin channel - only admins can listen
Broadcast::channel('admin-notifications', function ($user) {
    // Check if the user is an admin
    return auth()->guard('admin')->check();
});

// User private channel for receiving admin responses
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
