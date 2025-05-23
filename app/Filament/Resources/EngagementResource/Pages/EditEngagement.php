<?php

namespace App\Filament\Resources\EngagementResource\Pages;

use App\Filament\Resources\EngagementResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEngagement extends EditRecord
{
    protected static string $resource = EngagementResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function getSavedNotificationRedirectUrl(): ?string
    {
        return static::getUrl('index');
    }

    protected function getCancelRedirectUrl(): string
    {
        return static::getUrl('index');
    }
}
