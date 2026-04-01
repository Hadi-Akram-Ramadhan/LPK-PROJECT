<?php

namespace App\Events;

use App\Models\UjianPeserta;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CheatLogApproved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ujianPeserta;

    /**
     * Create a new event instance.
     */
    public function __construct(UjianPeserta $ujianPeserta)
    {
        $this->ujianPeserta = $ujianPeserta;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Broadcast ke channel milik peserta yang sedang diblokir
        return [
            new PrivateChannel('exam.blocked.' . $this->ujianPeserta->user_id),
        ];
    }
    
    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'CheatLogApprovedEvent';
    }
}
