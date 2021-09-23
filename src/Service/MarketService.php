<?php
namespace App\Service;

class MarketService
{
    const BASE_IMAGE_URL = 'https://picsum.photos/id';

    public function getImageUrl($id, $width = 200, $height = 300): string
    {
        return self::BASE_IMAGE_URL . '/' . $id . '/' . $width . '/' . $height;
    }
}
