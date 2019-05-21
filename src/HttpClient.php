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

use GuzzleHttp\Client;
use Tlwl\HttpClient\Support\Logger;
use Tlwl\HttpClient\Exceptions\Exception;
use Tlwl\HttpClient\Traits\ResponseCastable;
use Tlwl\HttpClient\Exceptions\HttpException;
use Tlwl\HttpClient\Exceptions\InvalidArgumentException;
use Tlwl\HttpClient\Support\TArray;

/**
 * 内部发起HTTP请求去请求服务端并获得响应数据
 * @author 悟玄 <roc9574@sina.com>
 * @method \Tlwl\HttpClient\Traits\ResponseCastable get(string $uri,string $format='json',array $data=[])
 * @method \Tlwl\HttpClient\Traits\ResponseCastable put(string $uri,array $data,string $format)
 * @method \Tlwl\HttpClient\Traits\ResponseCastable post(string $uri,array $data,string $format)
 * @method \Tlwl\HttpClient\Traits\ResponseCastable path(string $uri,array $data,string $format)
 * @method \Tlwl\HttpClient\Traits\ResponseCastable delete(string $uri,array $data,string $format)
 * 
 * @see \GuzzleHttp\Client
 * @see \Tlwl\HttpClient\Support\Logger
 * @see \Tlwl\HttpClient\Traits\ResponseCastable
 */
class HttpClient{

    use ResponseCastable;

    /**
     * 签名私钥配置
     * @author 杨鹏 <yangpeng1@dgg.net>
     * @var string
     */
    private static $signKey = "";

    /**
     * 请求客户端实例化
     * @author 杨鹏 <yangpeng1@dgg.net>
     * @var \GuzzleHttp\Client
     */
    protected static $client;

    /**
     * 数据返回格式
     * @author 杨鹏 <yangpeng1@dgg.net>
     * @var \Tlwl\HttpClient\Traits\ResponseCastable array|object|collection|json
     */
    protected static $format;

    /**
     * 实例化参数配置
     * @author 杨鹏 <yangpeng1@dgg.net>
     * @var array
     */
    protected static $guzzle=[
        'base_uri'=>'',
    ];

    /**
     * Undocumented function
     */
    public function __construct(array $guzzle=[],string $format= '')
    {
        static::registerLoggerService();
        static::registerClientService($guzzle);
        static::$format = empty($format)?config('http-client.format'):$format;
        static::$signKey = config('http-client.sign_key');
    }

    /**
     * 发起GET请求 从服务器取出资源（一项或多项）
     * @author 悟玄 <roc9574@sina.com>
     * @param string $uri   资源请求地址
     * @param array $data   请求资源所需参数['id'=>1,'page'=>0,'limit'=>10]
     * @param string $mode  请求方式 同步 || 异步 默认 同步 ['sync','async']
     * @return void
     */
    public static function get(string $uri,string $format='json',array $data=[],string $mode='sync')
    {

        if (!\in_array(\strtolower($mode),['sync','async']))
        {
            Logger::error("Invalid request mode:",[$mode]);
            throw new InvalidArgumentException("Invalid request mode:{$mode}");
        }

        if (!\is_array($data))
        {
            Logger::error("Invalid request data:",$data);
            throw new InvalidArgumentException("Invalid request data:{$data}");
        }

        if (!\in_array(\strtolower($format), ['object','array', 'json'])) {
            Logger::error("Invalid request data:",[$format]);
            throw new InvalidArgumentException('Invalid response format: '.$format);
        }

        try {
            $data['sign'] = generate_sign($data,static::$signKey);
            Logger::debug("http client request method:GET uri:{$uri} data:",$data);
            $response=self::$client->get($uri,['query'=>$data]);

            return self::castResponseToType($response,$format);
        }catch (Exception $e) {
            Logger::error("http client request code:{$e->getCode()}",[$e->getMessage()]);
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * 发起PUT请求 在服务器更新资源（客户端提供改变后的完整资源）
     * @author 杨鹏 <yangpeng1@dgg.net>
     * @param string $uri   资源请求地址
     * @param array $data   请求资源所需参数['id'=>1,'nickname'=>'张三','sex'=>1,'avater'=>'http://images.baidu.com/image/pic/item/ac4bd11373f08202fee6996a45fbfbedab641b6a.jpg']
     * @param string $mode  请求方式 同步 || 异步 默认 同步 ['sync','async']
     * @return void
     */
    public static function put(string $uri,array $data,string $format = 'json',string $mode = "sync")
    {

        if (!\in_array(\strtolower($mode),['sync','async']))
        {
            Logger::error("Invalid request mode:",[$mode]);
            throw new InvalidArgumentException("Invalid request mode:{$mode}");
        }

        if (!\is_array($data))
        {
            Logger::error("Invalid request data:",$data);
            throw new InvalidArgumentException("Invalid request data:{$data}");
        }

        if (!\in_array(\strtolower($format), ['object','array', 'json'])) {
            Logger::error("Invalid request data:",[$format]);
            throw new InvalidArgumentException('Invalid response format: '.$format);
        }

        $data['sign'] = generate_sign($data,static::$signKey);

        $body['body'] = urldecode(http_build_query($data));

        try {
            Logger::debug("http client request method:PUT uri:{$uri} data:",$data);
            $response = self::$client->put($uri,$body);
            return self::castResponseToType($response,$format);
        }catch (Exception $e) {
            Logger::error("http client request code:{$e->getCode()}",[$e->getMessage()]);
            throw new HttpException($e->getMessage(),$e->getCode(),$e);
        }
    }

    /**
     * 发起POST请求 在服务器新建一个资源（客户端提供需要创建的完整资源）
     * @author 杨鹏 <yangpeng1@dgg.net>
     * @param string $uri       资源请求地址
     * @param array $data       请求资源所需参数['id'=>1,'page'=>0,'limit'=>10]
     * @param string $format    获取远端返回数据格式 支持[object,array,json]
     * @param string $mode      请求方式 同步 || 异步 默认 同步 ['sync','async']
     * @return void
     */
    public static function post(string $uri,array $data,string $format='json',string $mode = "sync")
    {
        if (!\in_array(\strtolower($mode),['sync','async']))
        {
            Logger::error("Invalid request mode:",[$mode]);
            throw new InvalidArgumentException("Invalid request mode:{$mode}");
        }

        if (!\is_array($data))
        {
            Logger::error("Invalid request data:",$data);
            throw new InvalidArgumentException("Invalid request data:{$data}");
        }

        if (!\in_array(\strtolower($format), ['object','array', 'json'])) {
            Logger::error("Invalid request data:",[$format]);
            throw new InvalidArgumentException('Invalid response format: '.$format);
        }


        try {
            $data = TArray::depthFormat($data);
            $data['sign'] = generate_sign($data,static::$signKey);
            Logger::debug("http client request method:POST uri:{$uri} data:",$data);
            $response = self::$client->post($uri,['form_params'=>$data]);
            return self::castResponseToType($response,$format);
        }catch (Exception $e) {
            Logger::error("http client request code:{$e->getCode()}",[$e->getMessage()]);
            throw new HttpException($e->getMessage(),$e->getCode(),$e);
        }
    }

    /**
     * 发起PATH请求 在服务器更新资源（客户端提供改变的属性）
     * @author 杨鹏 <yangpeng1@dgg.net>
     * @param string $uri       资源请求地址
     * @param array $data       更新资源参数['id'=>1,'status'=>1]
     * @param string $format    获取远端返回数据格式 支持[object,array,json]
     * @param string $mode      请求方式 同步 || 异步 默认 同步 ['sync','async']
     * @return void
     */
    public static function patch(string $uri,array $data,string $format = 'json',string $mode = "sync")
    {
        if (!\in_array(\strtolower($mode),['sync','async']))
        {
            Logger::error("Invalid request mode:",[$mode]);
            throw new InvalidArgumentException("Invalid request mode:{$mode}");
        }

        if (!\is_array($data))
        {
            Logger::error("Invalid request data:",$data);
            throw new InvalidArgumentException("Invalid request data:{$data}");
        }

        if (!\in_array(\strtolower($format), ['object','array', 'json'])) {
            Logger::error("Invalid request data:",[$format]);
            throw new InvalidArgumentException('Invalid response format: '.$format);
        }

        $query=['body'=>http_build_query($data)];
        try {
            $data['sign'] = generate_sign($data,static::$signKey);
            Logger::debug("http client request method:PATCH uri:{$uri} data:",$query);
            $response = self::$client->patch($uri,$query);
            return self::castResponseToType($response,$format);
        }catch (Exception $e) {
            Logger::error("http client request code:{$e->getCode()}",[$e->getMessage()]);
            throw new HttpException($e->getMessage(),$e->getCode(),$e);
        }

    }

    /**
     * 发起DELETE请求 从服务器删除资源。
     * @author 杨鹏 <yangpeng1@dgg.net>
     * @param string $uri       资源请求地址：http://api.example.com/api/v1/user
     * @param array  $data      删除资源参数['id'=>1]
     * @param string $format    获取远端返回数据格式 支持[object,array,json]
     * @param string $mode      请求方式 同步 || 异步 默认 同步 ['sync','async']
     * @return void
     */
    public static function delete(string $uri,array $data,string $format='json',string $mode = "sync")
    {

        if (!\in_array(\strtolower($mode),['sync','async']))
        {
            Logger::error("Invalid request mode:",[$mode]);
            throw new InvalidArgumentException("Invalid request mode:{$mode}");
        }

        if (!\is_array($data))
        {
            Logger::error("Invalid request data:",$data);
            throw new InvalidArgumentException("Invalid request data:{$data}");
        }

        if (!\in_array(\strtolower($format), ['object','array', 'json'])) {
            Logger::error("Invalid request data:",[$format]);
            throw new InvalidArgumentException('Invalid response format: '.$format);
        }

        try {
            $data['sign'] = generate_sign($data,static::$signKey);
            Logger::debug("http client request method:PATCH uri:{$uri} data:",$data);
            $response =  self::$client->delete($uri,['form_params'=>$data]);
            return self::castResponseToType($response,$format);
        }catch (Exception $e) {
            Logger::error("http client request code:{$e->getCode()}",[$e->getMessage()]);
            throw new HttpException($e->getMessage(),$e->getCode(),$e);
        }

    }

    /**
     * Register logger service.
     *
     * @author 杨鹏 <yangpeng1@dgg.net>
     *
     * @throws Exception
     */
    private function registerLoggerService()
    {
        Logger::setLogger(Logger::createLogger(
            config('http-client.log.file','http-client.log'),
            'tlwl.http_client',
            config('http-client.log.level', 'warning'),
            config('http-client.log.log', 'daily'),
            config('http-client.log.max_file', 30)
        ));
    }

    /**
     * Register client service.
     * @author 杨鹏 <yangpeng1@dgg.net>
     * @return \GuzzleHttp\Client;
     */
    private static function registerClientService(array $guzzle){
        $config_guzzle = [
            'cert'=>config('http-client.cert'),
            'auth'=>config('http-client.auth'),
            'debug'=>config('http-client.debug'),
            'verify'=>config('http-client.verify'),
            'headers'=>config('http-client.headers'),
            'tiemout'=>config('http-client.timeout'),
            'connect_timeout'=>config('http-client.connect_timeout'),
            'allow_redirects'=>config('http-client.allow_redirects')
        ];
        self::$client = new Client(array_replace_recursive(self::$guzzle,$config_guzzle,$guzzle));
    }
}