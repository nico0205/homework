<?php declare(strict_types=1);
/**
 * PHP version 7
 *
 * Created by PhpStorm.
 * User: nicolas
 * Date: 11/12/17
 * Time: 13:26
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
 * Class NotAnImageException
 */
class NotAnImageException extends \Exception
{
    /**
     * NotAnImageException constructor.
     *
     * @param string         $type
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(string $type, int $code = 0, Throwable $previous = null)
    {
        $message = sprintf('%s is not an allowed type', $type);
        parent::__construct($message, $code, $previous);
    }
}
