<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Posts\PostResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (blank($data['slug'] ?? null)) {
            $data['slug'] = Str::slug($data['title'] ?? '') ?: 'post-'.Str::random(6);
        }

        return $data;
    }
}
