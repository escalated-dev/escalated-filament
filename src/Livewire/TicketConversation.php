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

    public string|array $replyBody = '';

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
                    ->label(__('escalated-filament::filament.livewire.conversation.insert_canned_response'))
                    ->options(
                        CannedResponse::forAgent(auth()->id())->pluck('title', 'id')
                    )
                    ->searchable()
                    ->placeholder(__('escalated-filament::filament.livewire.conversation.select_canned_response'))
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
                    ->placeholder(__('escalated-filament::filament.livewire.conversation.type_reply'))
                    ->required(),

                Forms\Components\Toggle::make('isInternalNote')
                    ->label(__('escalated-filament::filament.livewire.conversation.internal_note_label'))
                    ->helperText(__('escalated-filament::filament.livewire.conversation.internal_note_helper')),
            ]);
    }

    public function sendReply(): void
    {
        $this->validate([
            'replyBody' => 'required|min:1',
        ]);

        // Handle Tiptap JSON structure from RichEditor
        $bodyContent = $this->replyBody;
        if (is_array($bodyContent)) {
            // Extract text from Tiptap JSON structure
            $bodyContent = $this->extractTextFromTiptap($bodyContent);
        }

        $ticket = $this->ticket;
        $service = app(TicketService::class);

        if ($this->isInternalNote) {
            $service->addNote($ticket, auth()->user(), $bodyContent);
        } else {
            $service->reply($ticket, auth()->user(), $bodyContent);
        }

        $this->replyBody = '';
        $this->isInternalNote = false;

        Notification::make()
            ->title($this->isInternalNote ? __('escalated-filament::filament.livewire.conversation.notification_note_added') : __('escalated-filament::filament.livewire.conversation.notification_reply_sent'))
            ->success()
            ->send();
    }

    /**
     * Extract plain text or HTML from Tiptap JSON structure
     */
    protected function extractTextFromTiptap(array $data): string
    {
        if (! isset($data['content']) || ! is_array($data['content'])) {
            return '';
        }

        $html = '';

        foreach ($data['content'] as $node) {
            $html .= $this->processTiptapNode($node);
        }

        return $html;
    }

    /**
     * Process individual Tiptap node recursively
     */
    protected function processTiptapNode(array $node): string
    {
        $type = $node['type'] ?? '';

        if ($type === 'text') {
            return $this->processTextNode($node);
        }

        $content = $this->processChildNodes($node);

        return $this->wrapContentInBlockNode($type, $content, $node);
    }

    /**
     * Process child nodes recursively
     */
    protected function processChildNodes(array $node): string
    {
        if (! isset($node['content']) || ! is_array($node['content'])) {
            return '';
        }

        return collect($node['content'])
            ->map(fn ($child) => $this->processTiptapNode($child))
            ->join('');
    }

    /**
     * Process text node with marks (bold, italic, etc.)
     */
    protected function processTextNode(array $node): string
    {
        $text = $node['text'] ?? '';

        if (! isset($node['marks']) || ! is_array($node['marks'])) {
            return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        }

        return collect($node['marks'])
            ->reduce(fn ($text, $mark) => $this->applyTextMark($text, $mark), htmlspecialchars($text, ENT_QUOTES, 'UTF-8'));
    }

    /**
     * Apply individual text mark (bold, italic, link, etc.)
     */
    protected function applyTextMark(string $text, array $mark): string
    {
        return match ($mark['type'] ?? '') {
            'bold' => "<strong>{$text}</strong>",
            'italic' => "<em>{$text}</em>",
            'underline' => "<u>{$text}</u>",
            'link' => $this->wrapTextInLink($text, $mark),
            'code' => "<code>{$text}</code>",
            'strike' => "<s>{$text}</s>",
            default => $text,
        };
    }

    /**
     * Wrap text in link tag with proper attributes
     */
    protected function wrapTextInLink(string $text, array $mark): string
    {
        $href = htmlspecialchars($mark['attrs']['href'] ?? '#', ENT_QUOTES, 'UTF-8');
        $target = isset($mark['attrs']['target']) ? ' target="'.htmlspecialchars($mark['attrs']['target'], ENT_QUOTES, 'UTF-8').'"' : '';

        return "<a href=\"{$href}\"{$target}>{$text}</a>";
    }

    /**
     * Wrap content in appropriate block-level HTML tag
     */
    protected function wrapContentInBlockNode(string $type, string $content, array $node): string
    {
        return match ($type) {
            'paragraph' => "<p>{$content}</p>",
            'heading' => $this->wrapInHeading($content, $node),
            'bulletList' => "<ul>{$content}</ul>",
            'orderedList' => "<ol>{$content}</ol>",
            'listItem' => "<li>{$content}</li>",
            'blockquote' => "<blockquote>{$content}</blockquote>",
            'codeBlock' => "<pre><code>{$content}</code></pre>",
            'hardBreak' => '<br>',
            'horizontalRule' => '<hr>',
            default => $content,
        };
    }

    /**
     * Wrap content in heading tag with proper level
     */
    protected function wrapInHeading(string $content, array $node): string
    {
        $level = min(max((int) ($node['attrs']['level'] ?? 1), 1), 6);

        return "<h{$level}>{$content}</h{$level}>";
    }

    public function togglePin(int $replyId): void
    {
        $reply = Reply::findOrFail($replyId);

        if ($reply->ticket_id !== $this->ticketId) {
            return;
        }

        $reply->update(['is_pinned' => ! $reply->is_pinned]);

        Notification::make()
            ->title($reply->is_pinned ? __('escalated-filament::filament.livewire.conversation.note_pinned') : __('escalated-filament::filament.livewire.conversation.note_unpinned'))
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
