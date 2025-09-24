<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    /**
     * Determine whether the user can view any tickets.
     */
    public function viewAny(User $user)
    {
        // Admin يرى كل التذاكر
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can view the ticket.
     */
    public function view(User $user, Ticket $ticket)
    {
        if($user->role === 'admin') return true;
        if($user->role === 'organizer') {
            return $ticket->event->organizer_id === $user->id;
        }
        if($user->role === 'attendee') {
            return $ticket->attendee_id === $user->id;
        }
        return false;
    }

    /**
     * Determine whether the user can create tickets.
     */
    public function create(User $user)
    {
        // فقط الـAttendee يمكنه شراء تذكرة
        return $user->role === 'attendee';
    }

    /**
     * Determine whether the user can update the ticket.
     */
    public function update(User $user, Ticket $ticket)
    {
        if($user->role === 'admin') return true;
        if($user->role === 'organizer') {
            return $ticket->event->organizer_id === $user->id;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the ticket.
     */
    public function delete(User $user, Ticket $ticket)
    {
        return $this->update($user, $ticket);
    }
}
