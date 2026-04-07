<?php

namespace App\Traits;

trait ImageCompressor
{
    /**
     * Compress and save an image.
     *
     * @param string $sourcePath Original image path
     * @param string $destinationPath Target saved path
     * @param int $quality Compression quality 0-100
     * @param int $maxWidth Maximum scaled dimension width
     * @return bool
     */
    protected function compressAndSaveImage($sourcePath, $destinationPath, $quality = 75, $maxWidth = 1200)
    {
        $info = @getimagesize($sourcePath);
        if (!$info) {
            return copy($sourcePath, $destinationPath);
        }

        $width = $info[0];
        $height = $info[1];
        $mime = $info['mime'];

        $newWidth = $width;
        $newHeight = $height;
        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int)($height * ($maxWidth / $width));
        }

        $image = null;
        switch ($mime) {
            case 'image/jpeg':
                $image = @imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $image = @imagecreatefrompng($sourcePath);
                break;
            case 'image/webp':
                $image = @imagecreatefromwebp($sourcePath);
                break;
        }

        if (!$image) {
            return copy($sourcePath, $destinationPath);
        }

        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG/WEBP
        if ($mime == 'image/png' || $mime == 'image/webp') {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        $result = false;
        switch ($mime) {
            case 'image/jpeg':
                $result = imagejpeg($newImage, $destinationPath, $quality);
                break;
            case 'image/png':
                $pngQuality = round((100 - $quality) / 100 * 9);
                $result = imagepng($newImage, $destinationPath, $pngQuality);
                break;
            case 'image/webp':
                $result = imagewebp($newImage, $destinationPath, $quality);
                break;
        }

        imagedestroy($image);
        imagedestroy($newImage);

        return $result;
    }
}
