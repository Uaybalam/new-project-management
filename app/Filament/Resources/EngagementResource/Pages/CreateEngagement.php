<?php

namespace App\Filament\Resources\EngagementResource\Pages;

use App\Filament\Resources\EngagementResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEngagement extends CreateRecord
{
    protected static string $resource = EngagementResource::class;
    protected function getCreatedNotificationRedirectUrl(): ?string
{
    return static::getUrl('index');
}

protected function getCancelRedirectUrl(): string
{
    return static::getUrl('index');
}
}
