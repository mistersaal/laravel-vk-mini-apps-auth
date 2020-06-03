<?php


namespace Mistersaal\VkMiniAppsAuth\Exceptions;
use \Symfony\Component\HttpKernel\Exception\HttpException;


use Throwable;

class VkSignException extends HttpException
{
    public function __construct($message = "", $code = 403, Throwable $previous = null)
    {
        parent::__construct($code, $message, $previous);
    }
}
