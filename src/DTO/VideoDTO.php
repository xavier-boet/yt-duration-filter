<?php

namespace App\DTO;

class VideoDTO
{
    public function __construct(
        public string $id,
        public string $channelId,
        public string $title,
        public int $duration,
        public \DateTimeImmutable $publishedAt
    ) {}
}
