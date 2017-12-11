<?php declare(strict_types=1);
/**
 * PHP version 7
 *
 * Created by PhpStorm.
 * User: nicolas
 * Date: 11/12/17
 * Time: 12:35
 *
 * @category   homework
 *
 * @package    App\Exceptions
 *
 * @subpackage App\Exceptions
 *
 * @author     Nicolas Demay <nicolas.demay@actiane.com>
 */

namespace App\Exceptions;

use Throwable;

/**
 * Class NotAnUrlException
 */
class NotAnUrlException extends \Exception
{
    /**
     * NotAnUrlException constructor.
     *
     * @param string         $url
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(string $url, int $code = 0, Throwable $previous = null)
    {
        $message = sprintf('%s is not a valid url', $url);
        parent::__construct($message, $code, $previous);
    }
}
