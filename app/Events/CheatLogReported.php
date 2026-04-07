<?php
 
namespace App\Events;
 
use App\Models\CheatLog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
 
class CheatLogReported implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
 
    public $cheatLog;
 
    public function __construct(CheatLog $cheatLog)
    {
        $this->cheatLog = $cheatLog->load(['ujianPeserta.user', 'ujianPeserta.ujian']);
    }
 
    public function broadcastOn(): array
    {
        return [
            new Channel('cheat-logs'), // Public for simple demo, can be Private for Guru role
        ];
    }
 
    public function broadcastAs(): string
    {
        return 'CheatLogReportedEvent';
    }
}
