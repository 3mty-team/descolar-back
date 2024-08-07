<?php

namespace Descolar\Adapters\Media;

use Descolar\Adapters\Media\Exceptions\ExtensionNotSupportedException;
use Descolar\Adapters\Media\Exceptions\UploadMediaException;
use Descolar\Adapters\Media\Types\Image;
use Descolar\Adapters\Media\Types\Video;
use Descolar\Data\Entities\Media\Media as MediaEntity;
use Descolar\Managers\Env\EnvReader;
use Descolar\Managers\Media\Interfaces\IMedia;
use Descolar\Managers\Media\Interfaces\IMediaManager;
use Descolar\Managers\Media\Interfaces\IMediaType;

class MediaAdapter implements IMediaManager
{

    private string $PRODUCTION_HOST = "https://internal-api.descolar.fr/v1";
    private string $MEDIA_PATH = "/App/Adapters/Media/Storage/";

    private function getMediaURL(): string
    {
        $path = $this->MEDIA_PATH;
        $path = str_replace("\\", DIRECTORY_SEPARATOR, $path);

        return preg_replace('/\\\\(?=\/)/', '',$this->PRODUCTION_HOST . str_replace("/", DIRECTORY_SEPARATOR, $path));
    }

    private function getMediaPath(): string
    {
        $path = DIR_ROOT . $this->MEDIA_PATH;
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

    private function scanMedia(array $media): void
    {
        $needToSecure = EnvReader::getInstance()->get('SECURE_UPLOADS') ?: false;

        if(!$needToSecure) {
            return;
        }

        $path = $media['tmp_name'];
        $scanResult = shell_exec("clamscan " . escapeshellarg($path));

        if(!str_contains($scanResult, 'OK')) {
            throw new UploadMediaException("File is suspicious: $media[name]");
        }
    }

    /**
     * @param array{name: string, type: string, tmp_name: string, weight: int} $media the media to be saved
     * @return IMedia the saved media
     */
    private function saveMedia(array $media): IMedia
    {
        $extensionType = $this->getExtensionType($media['type']);

        $this->scanMedia($media);

        $extension = pathinfo($media['name'], PATHINFO_EXTENSION);
        $newName = uniqid(base64_encode($media['name'])) . ".$extension";
        $url = $this->getMediaURL() . $newName;

        $movedImage = $this->getMediaPath() . $newName;
        $result = move_uploaded_file($media['tmp_name'], $movedImage);

        if(!$result) {
            throw new UploadMediaException("Error on file upload: $media[name]");
        }

        [$width, $height] = getimagesize($movedImage) ?: [0, 0];

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