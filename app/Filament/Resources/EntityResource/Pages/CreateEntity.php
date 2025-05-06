<?php

namespace App\Filament\Resources\EntityResource\Pages;

use App\Filament\Resources\EntityResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Shareholder;

class CreateEntity extends CreateRecord
{
    protected static string $resource = EntityResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $entity = static::getModel()::create($data);

        if (!empty($data['shareholders'])) {
            foreach ($data['shareholders'] as $item) {
                if (!isset($item['shareholdable_type'], $item['shareholdable_id'], $item['percentage'])) {
                    continue;
                }

                Shareholder::create([
                    'entity_id' => $entity->id,
                    'shareholdable_type' => $item['shareholdable_type'],
                    'shareholdable_id' => $item['shareholdable_id'],
                    'percentage' => $item['percentage'],
                ]);
            }
        }

        return $entity;
    }
}
