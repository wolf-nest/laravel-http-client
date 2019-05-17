<?php

/*
 * This file is part of the tlwl/http-client.
 * 
 * (c) 悟玄 <roc9574@sina.com>
 * 
 * This source file is subject to the MIT license that is bundled.
 * with this source code in the file LICENSE.
 */

namespace Tlwl\HttpClient\Traits;

use Tlwl\HttpClient\Response;
use Tlwl\HttpClient\Arrayable;
use Tlwl\HttpClient\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Tlwl\HttpClient\Exceptions\InvalidConfigException;
use Tlwl\HttpClient\Exceptions\InvalidArgumentException;

/**
 * Trait ResponseCastable.
 *
 * @author 杨鹏 <yangpeng1@dgg.net>
 */
trait ResponseCastable
{
    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param string|null                         $type
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    protected static function castResponseToType(ResponseInterface $response, $type = null)
    {
        $response = Response::buildFromPsrResponse($response);
        $response->getBody()->rewind();

        switch ($type ?? 'array') {
            case 'collection':
                return $response->toCollection();
                break;
            case 'array':
                return $response->toArray();
                break;
            case 'object':
                return $response->toObject();
            case 'json':
                return $response->toJson();
            case 'raw':
                return $response;
            default:
                if (!is_subclass_of($type, Arrayable::class)) {
                    throw new InvalidConfigException(sprintf(
                        'Config key "response_type" classname must be an instanceof %s',
                        Arrayable::class
                    ));
                }
                return new $type($response);
        }
    }

    /**
     * @param mixed       $response
     * @param string|null $type
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    protected static function detectAndCastResponseToType($response, $type = null)
    {
        switch (true) {
            case $response instanceof ResponseInterface:
                $response = Response::buildFromPsrResponse($response);
                break;
            case $response instanceof Arrayable:
                $response = new Response(200, [], json_encode($response->toArray()));

                break;
            case ($response instanceof Collection) || is_array($response) || is_object($response):
                $response = new Response(200, [], json_encode($response));

                break;
            case is_scalar($response):
                $response = new Response(200, [], $response);

                break;
            default:
                throw new InvalidArgumentException(sprintf('Unsupported response type "%s"', gettype($response)));
        }
        return static::castResponseToType($response, $type);
    }
}
