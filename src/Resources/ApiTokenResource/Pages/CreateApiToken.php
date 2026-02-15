<?php

namespace Escalated\Filament\Resources\ApiTokenResource\Pages;

use Escalated\Filament\Resources\ApiTokenResource;
use Escalated\Laravel\Escalated;
use Escalated\Laravel\Models\ApiToken;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateApiToken extends CreateRecord
{
    protected static string $resource = ApiTokenResource::class;

    /**
     * Store the plain text token after creation so we can display it once.
     */
    protected ?string $plainTextToken = null;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Override the default create to use ApiToken::createToken which handles hashing.
     */
    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $userModel = Escalated::newUserModel();
        $user = $userModel->newQuery()->findOrFail($data['tokenable_id']);

        $expiresAt = ! empty($data['expires_in_days'])
            ? now()->addDays((int) $data['expires_in_days'])
            : null;

        $result = ApiToken::createToken(
            $user,
            $data['name'],
            $data['abilities'] ?? ['*'],
            $expiresAt,
        );

        $this->plainTextToken = $result['plainTextToken'];

        return $result['token'];
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('API Token Created')
            ->body("Copy your token now. It will not be shown again:\n\n**{$this->plainTextToken}**")
            ->success()
            ->persistent()
            ->actions([
                \Filament\Notifications\Actions\Action::make('copy')
                    ->label('Copy Token')
                    ->icon('heroicon-o-clipboard-document')
                    ->color('gray')
                    ->extraAttributes([
                        'x-on:click' => "window.navigator.clipboard.writeText('{$this->plainTextToken}')",
                    ]),
            ]);
    }
}
