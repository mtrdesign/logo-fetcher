<?php

namespace MTRDesign\LaravelLogoFetcher\Exceptions;

class UnexpectedException extends LogoFetcherException
{
    /**
     * @var \Exception
     */
    protected $exception;

    public function setException(\Exception $exception)
    {
        $this->exception = $exception;
    }
}