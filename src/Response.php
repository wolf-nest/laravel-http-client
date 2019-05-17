<?php

/*
 * This file is part of the tlwl/http-client.
 * 
 * (c) 悟玄 <roc9574@sina.com>
 * 
 * This source file is subject to the MIT license that is bundled.
 * with this source code in the file LICENSE.
 */

namespace Tlwl\HttpClient;

use Tlwl\HttpClient\Support\XML;
use Tlwl\HttpClient\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response as GuzzleResponse;


/**
 * Class Response.
 *
 * @author 杨鹏 <yangpeng1@dgg.net>
 */
class Response extends GuzzleResponse
{

    /**
     * 返回请求数据字符串
     * @author 杨鹏 <yangpeng1@dgg.net>
     * @return string
     */
    public function getBodyContents()
    {
        $this->getBody()->rewind();
        $contents = $this->getBody()->getContents();
        $this->getBody()->rewind();

        return $contents;
    }

    /**
     * 返回数据结构
     * @param \Psr\Http\Message\ResponseInterface $response
     * @author 杨鹏 <yangpeng1@dgg.net>
     * @return \Tlwl\HttpClient\Response
     */
    public static function buildFromPsrResponse(ResponseInterface $response)
    {
        return new static(
            $response->getStatusCode(),
            $response->getHeaders(),
            $response->getBody(),
            $response->getProtocolVersion(),
            $response->getReasonPhrase()
        );
    }

    /**
     * Build to json.
     * @author 杨鹏 <yangpeng1@dgg.net>
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Build to array.
     * @author 杨鹏 <yangpeng1@dgg.net>
     * @return array
     */
    public function toArray()
    {
        $content = $this->removeControlCharacters($this->getBodyContents());

        if (false !== stripos($this->getHeaderLine('Content-Type'), 'xml') || 0 === stripos($content, '<xml')) {
            return XML::parse($content);
        }

        // dd($content);

        $array = json_decode($content, true, 512, JSON_BIGINT_AS_STRING);
        if (JSON_ERROR_NONE === json_last_error()) {
            return $array;
        }

        return [];
    }

    /**
     * Get collection data.
     * @author 杨鹏 <yangpeng1@dgg.net>
     * @return \Tlwl\HttpClient\Support\Collection
     */
    public function toCollection()
    {
        return new Collection($this->toArray());
    }

    /**
     * Get object data.
     * @author 杨鹏 <yangpeng1@dgg.net>
     * @return object
     */
    public function toObject()
    {
        return json_decode($this->toJson());
    }

    /**
     * 转换成字符串
     * @author 杨鹏 <yangpeng1@dgg.net>
     * @return bool|string
     */
    public function __toString()
    {
        return $this->getBodyContents();
    }

    /**
     * 去除字符串中特殊字符
     * @param string $content
     * @author 杨鹏 <yangpeng1@dgg.net>
     * @return string
     */
    protected function removeControlCharacters(string $content)
    {
        return \preg_replace('/[\x00-\x1F\x80-\x9F]/u', '', $content);
    }
}
