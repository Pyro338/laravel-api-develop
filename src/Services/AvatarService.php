<?php

namespace Gamebetr\Api\Services;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Spatie\Image\Image;
use Spatie\Image\Manipulations;

class AvatarService
{
    /**
     * Get avatar path.
     * @param string $filename
     * @param string $extension
     * @return string|null
     */
    public function path(string $filename, string $extension) : ?string
    {
        if (! file_exists(storage_path('app/public').'/'.$filename.'.'.$extension)) {
            abort(404);
        }

        return storage_path('app/public').'/'.$filename.'.'.$extension;
    }

    /**
     * Resize avatar.
     * @param string $filename
     * @param string $extension
     * @param int $width
     * @param int $height
     * @return string
     */
    public function resize(string $filename, string $extension, int $width, int $height) : string
    {
        if (! in_array($width.'x'.$height, config('api.available_avatar_sizes', []))) {
            abort(404);
        }
        if (! file_exists(storage_path('app/public').'/'.$filename.'.'.$extension)) {
            abort(404);
        }
        if (! file_exists(storage_path('app/public').'/'.$filename.'_'.$width.'x'.$height.'.'.$extension)) {
            $image = Image::load(storage_path('app/public').'/'.$filename.'.'.$extension);
            $image->fit(Manipulations::FIT_CROP, $width, $height);
            $image->optimize();
            $image->save(storage_path('app/public').'/'.$filename.'_'.$width.'x'.$height.'.'.$extension);
        }

        return storage_path('app/public').'/'.$filename.'_'.$width.'x'.$height.'.'.$extension;
    }

    /**
     * Get avatar.
     * @param string $image
     * @return string
     */
    public function get(string $image)
    {
        $image = explode('.', $image);
        if (count($image) != 2) {
            abort(404);
        }
        $filename = explode('_', $image[0]);
        $extension = $image[1];
        if (count($filename) == 2) {
            $dimensions = explode('x', $filename[1]);
        }
        $filename = $filename[0];
        if (! isset($dimensions)) {
            $path = $this->path($filename, $extension);
        } else {
            if (count($dimensions) != 2) {
                abort(404);
            }
            if (! in_array($dimensions[0].'x'.$dimensions[1], config('api.available_avatar_sizes', []))) {
                abort(404);
            }
            $path = $this->resize($filename, $extension, $dimensions[0], $dimensions[1]);
        }

        return $path;
    }

    /**
     * Upload image.
     */
    public function upload(UploadedFile $file, Model $model)
    {
        if (! method_exists($model, 'avatar')) {
            throw new Exception('Invalid model', 500);
        }
        if ($model->avatar) {
            $this->unlink($model->avatar->uuid, $model->avatar->extension);
            $model->avatar->delete();
        }
        $extension = $file->getClientOriginalExtension();
        $avatar = $model->avatar()->create([
            'extension' => $extension,
        ]);
        try {
            $file->storeAs(null, $avatar->uuid.'.'.$extension, 'public');
        } catch (Exception $e) {
            $avatar->delete();
            throw $e;
        }

        return $avatar;
    }

    /**
     * Unlink all images.
     * @param string $filename
     * @param string $extension
     */
    public function unlink(string $filename, string $extension)
    {
        if (file_exists(storage_path('app/public').'/'.$filename.'.'.$extension)) {
            unlink(storage_path('app/public').'/'.$filename.'.'.$extension);
        }
        foreach (glob(storage_path('app/public').'/'.$filename.'_*.'.$extension) as $file) {
            unlink($file);
        }
    }
}
