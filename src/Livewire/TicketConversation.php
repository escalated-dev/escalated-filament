<?php

namespace Escalated\Filament\Livewire;

use Escalated\Laravel\Models\CannedResponse;
use Escalated\Laravel\Models\Reply;
use Escalated\Laravel\Models\Ticket;
use Escalated\Laravel\Services\TicketService;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Livewire\Component;

class TicketConversation extends Component implements HasForms
{
    use InteractsWithForms;

    public int $ticketId;

    public string $replyBody = '';

    public bool $isInternalNote = false;

    public ?int $cannedResponseId = null;

    public function mount(int $ticketId): void
    {
        $this->ticketId = $ticketId;
    }

    public function getTicketProperty(): Ticket
    {
        return Ticket::findOrFail($this->ticketId);
    }

    public function getRepliesProperty()
    {
        return Reply::where('ticket_id', $this->ticketId)
            ->with('author')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function getPinnedNotesProperty()
    {
        return Reply::where('ticket_id', $this->ticketId)
            ->where('is_internal_note', true)
            ->where('is_pinned', true)
            ->with('author')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('cannedResponseId')
                    ->label('Insert Canned Response')
                    ->options(
                        CannedResponse::forAgent(auth()->id())->pluck('title', 'id')
                    )
                    ->searchable()
                    ->placeholder('Select a canned response...')
                    ->live()
                    ->afterStateUpdated(function ($state) {
                        if ($state) {
                            $response = CannedResponse::find($state);
                            if ($response) {
                                $this->replyBody = $response->body;
                            }
                            $this->cannedResponseId = null;
                        }
                    }),

                Forms\Components\RichEditor::make('replyBody')
                    ->label('')
                    ->placeholder('Type your reply...')
                    ->required(),

                Forms\Components\Toggle::make('isInternalNote')
                    ->label('Internal Note')
                    ->helperText('Internal notes are only visible to agents.'),
            ]);
    }

    public function sendReply(): void
    {
        $this->validate([
            'replyBody' => 'required|string|min:1',
        ]);

        $ticket = $this->ticket;
        $service = app(TicketService::class);

        if ($this->isInternalNote) {
            $service->addNote($ticket, auth()->user(), $this->replyBody);
        } else {
            $service->reply($ticket, auth()->user(), $this->replyBody);
        }

        $this->replyBody = '';
        $this->isInternalNote = false;

        Notification::make()
            ->title($this->isInternalNote ? 'Internal note added' : 'Reply sent')
            ->success()
            ->send();
    }

    public function togglePin(int $replyId): void
    {
        $reply = Reply::findOrFail($replyId);

        if ($reply->ticket_id !== $this->ticketId) {
            return;
        }

        $reply->update(['is_pinned' => ! $reply->is_pinned]);

        Notification::make()
            ->title($reply->is_pinned ? 'Note pinned' : 'Note unpinned')
            ->success()
            ->send();
    }

    public function render()
    {
        return view('escalated-filament::livewire.ticket-conversation', [
            'replies' => $this->replies,
            'pinnedNotes' => $this->pinnedNotes,
        ]);
    }
}
