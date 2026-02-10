<?php

use Escalated\Filament\Actions\ApplyMacroAction;
use Escalated\Filament\Actions\AssignTicketAction;
use Escalated\Filament\Actions\ChangePriorityAction;
use Escalated\Filament\Actions\ChangeStatusAction;
use Escalated\Filament\Actions\FollowTicketAction;
use Escalated\Filament\Actions\PinReplyAction;

it('AssignTicketAction has correct default name', function () {
    expect(AssignTicketAction::getDefaultName())->toBe('assignTicket');
});

it('ChangeStatusAction has correct default name', function () {
    expect(ChangeStatusAction::getDefaultName())->toBe('changeStatus');
});

it('ChangePriorityAction has correct default name', function () {
    expect(ChangePriorityAction::getDefaultName())->toBe('changePriority');
});

it('ApplyMacroAction has correct default name', function () {
    expect(ApplyMacroAction::getDefaultName())->toBe('applyMacro');
});

it('FollowTicketAction has correct default name', function () {
    expect(FollowTicketAction::getDefaultName())->toBe('followTicket');
});

it('PinReplyAction has correct default name', function () {
    expect(PinReplyAction::getDefaultName())->toBe('pinReply');
});

it('AssignTicketAction extends Filament Table Action', function () {
    expect(AssignTicketAction::class)
        ->toExtend(\Filament\Tables\Actions\Action::class);
});

it('ChangeStatusAction extends Filament Table Action', function () {
    expect(ChangeStatusAction::class)
        ->toExtend(\Filament\Tables\Actions\Action::class);
});

it('ChangePriorityAction extends Filament Table Action', function () {
    expect(ChangePriorityAction::class)
        ->toExtend(\Filament\Tables\Actions\Action::class);
});

it('ApplyMacroAction extends Filament Table Action', function () {
    expect(ApplyMacroAction::class)
        ->toExtend(\Filament\Tables\Actions\Action::class);
});

it('FollowTicketAction extends Filament Table Action', function () {
    expect(FollowTicketAction::class)
        ->toExtend(\Filament\Tables\Actions\Action::class);
});

it('PinReplyAction extends Filament Table Action', function () {
    expect(PinReplyAction::class)
        ->toExtend(\Filament\Tables\Actions\Action::class);
});
