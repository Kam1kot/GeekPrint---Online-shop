<?php
function resizeAndCrop($sourcePath, $destPath, $destPathAlt, $newWidth, $newHeight)
{
    // Тип изображения
    $imageIngo = getimagesize($sourcePath);
    $mime = $imageIngo["mime"];

    switch ($mime) {
        case "image/jpeg":
            $sourceImage = imagecreatefromjpeg($sourcePath);
            break;
        case "image/png":
            $sourceImage = imagecreatefrompng($sourcePath);
            break;
        default:
            throw new Exception("Неподерживаемый формат изображения");
    }

    // Исходные размеры
    list($origWidth, $origHeight) = $imageIngo;

    // Соотношение сторон
    $srcRatio = $origWidth / $origHeight;
    $destRatio = $newWidth / $newHeight;

    if ($srcRatio > $destRatio) {
        // Ориг шире -> огран по высоте
        $cropHeight = $origHeight;
        $cropWidth = $origHeight * $destRatio;
        $srcX = ($origWidth - $cropWidth) /2;
        $srcY = 0;
    } else {
        // Ориг выше -> огран по ширине
        $cropWidth = $origWidth;
        $cropHeight = $origWidth / $destRatio;
        $srcX = 0;
        $srcY = ($origHeight - $cropHeight) /2;
    }

    // Новое изображние
    $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

    // Поддержка прозрачности PNG
    if ($mime == 'image/png') {
        imagealphablending($resizedImage, false);
        imagesavealpha($resizedImage, true);
        $transparent = imagecolorallocatealpha($resizedImage,0,0,0,127);
        imagefill($resizedImage,0,0, $transparent);
    }

    // Обрезка и ресайз
    imagecopyresampled(
        $resizedImage, 
        $sourceImage,
        0,
        0, // Координаты в новом изображнии
        $srcX,
        $srcY,
        $newWidth,
        $newHeight, // Размеры новые
        $cropWidth,
        $cropHeight // Размер кропа
    );

    // Сохранение
    switch ($mime) {
        case 'image/jpeg':
            imagejpeg($sourceImage, $destPathAlt, 90);
            imagejpeg($resizedImage, $destPath, 90);
            break;
        case 'image/png':
            imagepng($sourceImage, $destPathAlt);
            imagepng($resizedImage, $destPath);
            break;
    }
    
    // Освободить память
    imagedestroy($sourceImage);
    imagedestroy($resizedImage);

    return true;
}

function resizeImageByWidth($sourcePath, $destPath, $destPathAlt, $newWidth) {
    $imageIngo = getimagesize($sourcePath);
    $mime = $imageIngo["mime"];

    switch ($mime) {
        case "image/jpeg":
            $sourceImage = imagecreatefromjpeg($sourcePath);
            break;
        case "image/png":
            $sourceImage = imagecreatefrompng($sourcePath);
            break;
        default:
            throw new Exception("Неподерживаемый формат изображения");
    }

    list($origWidth, $origHeight) = $imageIngo;

    $newHeight = intval(($newWidth / $origWidth) * $origHeight);

    $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

    // Поддержка прозрачности PNG
    if ($mime == 'image/png') {
        imagealphablending($resizedImage, false);
        imagesavealpha($resizedImage, true);
        $transparent = imagecolorallocatealpha($resizedImage,0,0,0,127);
        imagefill($resizedImage,0,0, $transparent);
    }
    // Обрезка и ресайз
    imagecopyresampled(
        $resizedImage, 
        $sourceImage,
        0,
        0, // Координаты в новом изображнии
        0,
        0,
        $newWidth,
        $newHeight, // Размеры новые
        $origWidth,
        $origHeight // Размер кропа
    );

    // Сохранение
    switch ($mime) {
        case 'image/jpeg':
            imagejpeg($sourceImage, $destPathAlt, 90);
            imagejpeg($resizedImage, $destPath, 90);
            break;
        case 'image/png':
            imagepng($sourceImage, $destPathAlt);
            imagepng($resizedImage, $destPath);
            break;
    }

    imagedestroy($resizedImage);
    imagedestroy($sourceImage);

    return true;
}
?>