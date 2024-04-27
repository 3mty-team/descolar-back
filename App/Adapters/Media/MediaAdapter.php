<?php

namespace Descolar\Adapters\Media;

use Descolar\Adapters\Media\Exceptions\ExtensionNotSupportedException;
use Descolar\Adapters\Media\Exceptions\UploadMediaException;
use Descolar\Adapters\Media\Types\Image;
use Descolar\Adapters\Media\Types\Video;
use Descolar\Managers\Media\Interfaces\IMedia;
use Descolar\Managers\Media\Interfaces\IMediaManager;
use Descolar\Managers\Media\Interfaces\IMediaType;

use Descolar\Data\Entities\Media\Media as MediaEntity;

class MediaAdapter implements IMediaManager
{

    private function getMediaPath(): string
    {
        $path = DIR_ROOT . "/App/Adapters/Media/Storage/";
        $path = str_replace("\\", DIRECTORY_SEPARATOR, $path);
        return str_replace("/", DIRECTORY_SEPARATOR, $path);
    }

    private function getExtensionType(string $type): IMediaType
    {
        $extension = strtolower($type);

        return match ($extension) {
            'image/jpeg', 'image/jpg', 'image/png', 'image/webp',  => new Image(),
            'video/mp4', 'video/avi', 'video/mov', 'video/mpeg', 'video/webm'  => new Video(),
            default => throw new ExtensionNotSupportedException("Extension not supported: $extension")
        };
    }

    /**
     * @param array{name: string, type: string, tmp_name: string, weight: int} $media the media to be saved
     * @return IMedia the saved media
     */
    private function saveMedia(array $media): IMedia
    {
        $extensionType = $this->getExtensionType($media['type']);


        $extension = pathinfo($media['name'], PATHINFO_EXTENSION);
        $newName = uniqid(base64_encode($media['name'])) . ".$extension";
        $url = $this->getMediaPath() . $newName;

        $result = move_uploaded_file($media['tmp_name'], $url);

        if(!$result) {
            throw new UploadMediaException("Error on file upload: $media[name]");
        }

        [$width, $height] = getimagesize($url) ?: [0, 0];

        return new Media($media['name'], $extensionType, $url, [$width, $height], $media['weight']);
    }

    #[\Override] public function saveMediaList(): array
    {
        $files = &$_FILES;
        $medias = [];

        if(!isset($files['image'])) {
            return [];
        }

        $fileList = $files['image'];
        $countFiles = count($fileList['name']);

        for($i = 0; $i < $countFiles; $i++) {

            $fileName = $fileList['name'][$i];

            if($fileList['error'][$i] !== 0) {
                throw new UploadMediaException("Error on file upload: $fileName");
            }

            $actualMedia = [
                'name' => $fileName,
                'type' => $fileList['type'][$i],
                'tmp_name' => $fileList['tmp_name'][$i],
                'weight' => $fileList['size'][$i]
            ];

            $medias[] = $this->saveMedia($actualMedia);
        }

        return $medias;
    }

    #[\Override] public function removeMedia(IMedia $media): void
    {
        if(!file_exists($media->getUrl())) {
            throw new UploadMediaException("File not found: {$media->getName()}");
        }

        $result = unlink($media->getUrl());

        if(!$result) {
            throw new UploadMediaException("Error on file delete: {$media->getName()}");
        }
    }

    #[\Override] public function disableMedia(IMedia $media): void
    {
        if(!file_exists($media->getUrl())) {
            throw new UploadMediaException("File not found: {$media->getName()}");
        }

        $extension = pathinfo($media->getUrl(), PATHINFO_EXTENSION);

        $result = rename($media->getUrl(), $this->getMediaPath() . $media->getName() . ".{$extension}_disabled");

        if(!$result) {
            throw new UploadMediaException("Error on file disable: {$media->getName()}");
        }
    }

    #[\Override] public function getMedias(): array
    {
        $medias = [];

        $files = scandir($this->getMediaPath());

        foreach($files as $file) {
            if($file === '.' || $file === '..') {
                continue;
            }

            $url = $this->getMediaPath() . $file;

            [$width, $height] = getimagesize($url) ?: [0, 0];

            $media = new Media($file, $this->getExtensionType(mime_content_type($url)), $url, [$width, $height], filesize($url));
            $medias[] = $media;
        }

        return $medias;
    }

    #[\Override] public function generateMedia(MediaEntity $media): IMedia
    {
        $mediaExtensionType = match ($media->getMediaType()->value) {
            'image' => new Image(),
            'video' => new Video(),
            default => throw new ExtensionNotSupportedException("Extension not supported: {$media->getMediaType()}")
        };

        $mediaName = pathinfo($media->getPath(), PATHINFO_FILENAME);

        return new Media($mediaName, $mediaExtensionType, $media->getPath(), [0, 0], 0);
    }
}