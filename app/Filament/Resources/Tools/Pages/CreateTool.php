<?php

namespace App\Filament\Resources\Tools\Pages;

use App\Filament\Resources\Tools\ToolResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateTool extends CreateRecord
{
    protected static string $resource = ToolResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (blank($data['slug'] ?? null)) {
            $data['slug'] = Str::slug($data['name'] ?? '') ?: 'tool-'.Str::random(6);
        }

        return $data;
    }
}
