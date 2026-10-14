<?php
namespace Easysite\Library\Image;

/**
 * Raster image processing on GD: load, scale, save in another format / with compression.
 * Output format is taken from the destination file extension (png, jpg/jpeg, webp, gif).
 * Vector images (svg) are not supported — check with Image::isRaster() first.
 *
 *   Image::load('8.png')->fit(20, 20)->save('8_small.png');
 *   Image::load('photo.jpg')->resize(800, 800)->save('photo.webp', 80);
 */
class Image
{
    private const RASTER = ['png', 'jpg', 'jpeg', 'webp', 'gif'];

    private function __construct(private \GdImage $image) {}

    /** Whether the file can be processed by this class (by extension). */
    public static function isRaster(string $path): bool
    {
        return in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), self::RASTER, true);
    }

    /** @throws \RuntimeException when the file is missing or is not an image GD can read. */
    public static function load(string $path): self
    {
        $contents = is_readable($path) ? file_get_contents($path) : false;
        $image = $contents !== false ? @imagecreatefromstring($contents) : false;
        if ($image === false) {
            throw new \RuntimeException("Image: cannot read {$path}");
        }
        imagealphablending($image, false);
        imagesavealpha($image, true);

        return new self($image);
    }

    public function width(): int
    {
        return imagesx($this->image);
    }

    public function height(): int
    {
        return imagesy($this->image);
    }

    /**
     * Scales down proportionally so the image fits into $maxWidth x $maxHeight.
     * Never upscales; the result keeps its own aspect ratio (no padding).
     */
    public function resize(int $maxWidth, int $maxHeight): self
    {
        $ratio = min($maxWidth / $this->width(), $maxHeight / $this->height(), 1);
        $width = max(1, (int) round($this->width() * $ratio));
        $height = max(1, (int) round($this->height() * $ratio));

        return $this->copyTo($this->canvas($width, $height), 0, 0, $width, $height);
    }

    /**
     * Exactly $width x $height: the image is scaled to fit inside and centered on a
     * transparent canvas — non-square sources get padding instead of being distorted.
     */
    public function fit(int $width, int $height): self
    {
        $ratio = min($width / $this->width(), $height / $this->height());
        $w = max(1, (int) round($this->width() * $ratio));
        $h = max(1, (int) round($this->height() * $ratio));

        return $this->copyTo($this->canvas($width, $height), intdiv($width - $w, 2), intdiv($height - $h, 2), $w, $h);
    }

    /**
     * Saves to $path, format by extension. $quality 0–100: jpg/webp quality,
     * for png it is mapped to the zlib compression level (lossless either way).
     */
    public function save(string $path, int $quality = 85): bool
    {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $quality = max(0, min(100, $quality));

        return match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'png' => imagepng($this->image, $path, (int) round(9 - $quality * 9 / 100)),
            'jpg', 'jpeg' => imagejpeg($this->flatten(), $path, $quality),
            'webp' => imagewebp($this->image, $path, $quality),
            'gif' => imagegif($this->image, $path),
            default => throw new \InvalidArgumentException("Image: unsupported output format {$path}"),
        };
    }

    /** Transparent truecolor canvas. */
    private function canvas(int $width, int $height): \GdImage
    {
        $canvas = imagecreatetruecolor($width, $height);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        imagefill($canvas, 0, 0, imagecolorallocatealpha($canvas, 0, 0, 0, 127));

        return $canvas;
    }

    private function copyTo(\GdImage $canvas, int $x, int $y, int $width, int $height): self
    {
        imagecopyresampled($canvas, $this->image, $x, $y, 0, 0, $width, $height, $this->width(), $this->height());
        $this->image = $canvas;

        return $this;
    }

    /** jpg has no alpha channel: transparent areas become white instead of black. */
    private function flatten(): \GdImage
    {
        $flat = imagecreatetruecolor($this->width(), $this->height());
        imagefill($flat, 0, 0, imagecolorallocate($flat, 255, 255, 255));
        imagecopy($flat, $this->image, 0, 0, 0, 0, $this->width(), $this->height());

        return $flat;
    }
}
