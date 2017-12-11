<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 10/12/17
 * Time: 16:35
 */

namespace App\Service;

use App\Exceptions\CurlBase64ImageException;
use App\Exceptions\NotAnImageException;
use App\Exceptions\NotAnUrlException;

/**
 * Class CurlBase64Image
 */
class CurlBase64Image
{
    public static $allowedType = [
        'image/jpeg',
        'image/gif',
        'image/png',
        'image/bmp',
    ];

    /**
     * @param string $url
     *
     * @return string
     *
     * @throws CurlBase64ImageException
     * @throws NotAnUrlException
     * @throws NotAnImageException
     */
    public function getBase64EncodedImage(string $url): string
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new NotAnUrlException($url);
        }

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'User-Agent: curl/7.39.0');
        curl_setopt($curl, CURLOPT_HEADER, false);

        $data = curl_exec($curl);

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $type = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
        curl_close($curl);

        if (!\in_array($type, self::$allowedType, true)) {
            throw new NotAnImageException($type);
        }

        if (200 !== $httpCode) {
            throw new CurlBase64ImageException($url, $httpCode);
        }

        return base64_encode($data);
    }
}
