<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine whether the user can view any orders.
     */
    public function viewAny(User $user)
    {
        // Admin فقط يقدر يشوف كل الطلبات
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can view a specific order.
     */
    public function view(User $user, Order $order)
    {
        if ($user->role === 'admin') return true;
        if ($user->role === 'attendee') {
            return $order->attendee_id === $user->id;
        }
        if ($user->role === 'organizer') {
            return $order->event->organizer_id === $user->id;
        }
        return false;
    }

    /**
     * Determine whether the user can create orders.
     */
    public function create(User $user)
    {
        // فقط الـ Attendee يقدر يعمل Order
        return $user->role === 'attendee';
    }

    /**
     * Determine whether the user can update the order.
     */
    public function update(User $user, Order $order)
    {
        if ($user->role === 'admin') return true;
        if ($user->role === 'organizer') {
            return $order->event->organizer_id === $user->id;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the order.
     */
    public function delete(User $user, Order $order)
    {
        return $this->update($user, $order);
    }
}
