<?php declare(strict_types=1);

namespace Swoft\Http\Message\Concern;

/**
 * Class InteractsWithInput
 *
 * @since 2.0
 */
trait InteractsWithInput
{
    /**
     * Retrieve a server variable from the request
     *
     * @param null|string $key
     * @param null|mixed  $default
     *
     * @return array|string|mixed
     */
    public function server(string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->getServerParams();
        }

        return $this->getServerParams()[$key] ?? $default;
    }

    /**
     * Retrieve a header from the request
     *
     * @param null|string $key
     * @param null|mixed  $default
     *
     * @return array|string|mixed
     */
    public function header(string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->getHeaders();
        }
        return $this->getHeader($key) ?? $default;
    }

    /**
     * Retrieve a query string from the request
     *
     * @param null|string $key
     * @param null|mixed  $default
     *
     * @return array|string|mixed
     */
    public function query(string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->getQueryParams();
        }
        return $this->getQueryParams()[$key] ?? $default;
    }

    /**
     * Retrieve a post item from the request
     *
     * @param null|string $key
     * @param null|mixed  $default
     *
     * @return array|string|mixed
     */
    public function post(string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->getParsedBody();
        }
        return $this->getParsedBody()[$key] ?? $default;
    }

    /**
     * Retrieve a input item from the request
     *
     * @param null|string $key
     * @param null|mixed  $default
     *
     * @return array|string|mixed
     */
    public function input(string $key = null, $default = null)
    {
        $inputs = $this->getQueryParams() + $this->getParsedBody();
        if ($key === null) {
            return $inputs;
        }
        return $inputs[$key] ?? $default;
    }

    /**
     * Retrieve a cookie from the request
     *
     * @param null|string $key
     * @param null|mixed  $default
     *
     * @return array|string|mixed
     */
    public function cookie(string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->getCookieParams();
        }
        return $this->getCookieParams()[$key] ?? $default;
    }

    /**
     * Retrieve raw body from the request
     *
     * @param null|mixed $default
     *
     * @return array|string|mixed
     */
    public function raw($default = null)
    {
        $body = $this->getBody();
        $raw  = $default;

        if ($body instanceof SwooleStream) {
            $raw = $body->getContents();
        }

        return $raw;
    }

    /**
     * Retrieve a json format raw body from the request,
     * The Content-Type of request must be equal to 'application/json'
     * When Content-Type is not vaild or can not found the key result,
     * The method will always return $default.
     *
     * @param null|string $key
     * @param null|mixed  $default
     *
     * @return array|string|mixed
     */
    public function json(string $key = null, $default = null)
    {
        try {
            $contentType = $this->getHeader('content-type');
            if (!$contentType || false === \stripos($contentType[0], 'application/json')) {
                throw new \InvalidArgumentException(sprintf('Invalid Content-Type of the request, expects %s, %s given',
                    'application/json', ($contentType ? current($contentType) : 'null')));
            }
            $body = $this->getBody();
            if ($body instanceof SwooleStream) {
                $raw         = $body->getContents();
                $decodedBody = JsonHelper::decode($raw, true);
            }
        } catch (\Exception $e) {
            return $default;
        }
        if ($key === null) {
            return $decodedBody ?? $default;
        } else {
            return ArrayHelper::get($decodedBody, $key, $default);
        }
    }

    /**
     * Retrieve a upload item from the request
     *
     * @param string|null $key
     * @param null        $default
     *
     * @return array|\Swoft\Web\UploadedFile|null
     */
    public function file(string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->getUploadedFiles();
        }
        return $this->getUploadedFiles()[$key] ?? $default;
    }
}