<?php

namespace App\Marker;

use DateInterval;
use DateTimeImmutable;
use RuntimeException;

final class NotificationMarker
{

    public const FILE_NAME = 'last_notified.txt';

    public const STORAGE_FOLDER = __DIR__ . '/../../storage';

    private array $options = [];
    public function __construct(?array $options = [])
    {
        $this->setOptions($options);
    }

    public function filePath(): string
    {
        return rtrim($this->options['storage_folder']) . DIRECTORY_SEPARATOR . $this->options['file_name'];
    }

    public function setOptions(?array $options = []): array
    {
        $defaultOptions = [
            'storage_folder' => self::STORAGE_FOLDER,
            'file_name' => self::FILE_NAME,
            'date_format' => 'Y-m-d H:i:s',
        ];
        $this->options = array_merge($defaultOptions, $options);
        $this->options['file_path'] = $this->filePath();

        return $this->options;
    }

    private function readFile(): ?string
    {
        if (!file_exists($this->options['file_path'])) {
            return null;
        }
        return file_get_contents($this->options['file_path']);
    }

    public function getLastNotifiedTime(): ?DateTimeImmutable
    {
        $date = $this->readFile();
        if (!$date) {
            return null;
        }
        return DateTimeImmutable::createFromFormat($this->options['date_format'], $date);
    }

    public function setLastNotifiedTime(DateTimeImmutable $dateTime): false|int
    {
        if (!file_exists($this->options['file_path'])) {
            $this->createFile();
        }
        echo "Notified: " . $dateTime->format($this->options['date_format']) . "\n";
        return file_put_contents($this->options['file_path'], $dateTime->format($this->options['date_format']));
    }

    public function markAsNotified(): bool
    {
        return $this->setLastNotifiedTime(new DateTimeImmutable());
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    private function createFile(): void
    {
        if (!is_dir($this->options['storage_folder'])) {
            if (!mkdir($this->options['storage_folder'], 0777, true)) {
                throw new RuntimeException("Creation of directory failed ({$this->options['storage_folder']}).");
            }
        }
        if (!file_exists($this->filePath())) {
            if (file_put_contents($this->filePath(), '') === false) {
                throw new RuntimeException("Creation of file failed ({$this->filePath()}).");
            }
        }
    }
    public function timeFromLastNotification(): ?DateInterval
    {
        if (null === $this->getLastNotifiedTime()) {
            return null;
        }
        return (new DateTimeImmutable())->diff($this->getLastNotifiedTime());
    }

}