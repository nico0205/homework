<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 10/12/17
 * Time: 16:36
 */

namespace App\Exceptions;

use Throwable;

/**
 * Class CurlBase64ImageException
 */
class CurlBase64ImageException extends \Exception
{
    /**
     * CurlBase64ImageException constructor.
     *
     * @param string         $url
     * @param int            $httpCode
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(string $url, int $httpCode, int $code = 0, Throwable $previous = null)
    {
        $message = sprintf(
            'Can\'t retrieve base 64 image from %s. Status code is %d',
            $url,
            $httpCode
        );
        parent::__construct($message, $code, $previous);
    }
}
