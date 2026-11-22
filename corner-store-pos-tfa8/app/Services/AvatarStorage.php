<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Format;
use Intervention\Image\ImageManager;

class AvatarStorage
{
    public function store(UploadedFile $file): string
    {
        try {
            $manager = ImageManager::usingDriver(Driver::class, decodeAnimation: false, strip: true);
            $image = $manager->decodePath($file->getRealPath())
                ->cover(256, 256)
                ->encodeUsingFormat(Format::JPEG, quality: 85);
        } catch (\Throwable $exception) {
            report($exception);

            throw ValidationException::withMessages([
                'avatar' => 'This image could not be prepared. Please choose another image.',
            ]);
        }

        $path = 'avatars/'.Str::uuid().'.jpg';

        if (! Storage::disk('public')->put($path, (string) $image)) {
            throw ValidationException::withMessages([
                'avatar' => 'The profile picture could not be saved. Please try again.',
            ]);
        }

        return $path;
    }
}
