<?php

namespace App\DTO;

class ChannelDTO
{
    public function __construct(
        public string $id,
        public string $title,
        public ?string $handle,
        public ?string $thumbnail
    ) {}
}
