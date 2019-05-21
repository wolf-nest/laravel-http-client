<?php

/*
 * This file is part of the tlwl/http-client.
 * 
 * (c) 悟玄 <roc9574@sina.com>
 * 
 * This source file is subject to the MIT license that is bundled.
 * with this source code in the file LICENSE.
 */

return [

    /**
     * sign key.
     */
   'sign_key'=>'CD77ECDD9C9E4C67B41222E1269787D4',

    /**
     * response format.
     */
    'format'=>'json',

    /**
     * 日志信息配置
     */

    'log' => [
        'log'   => env('APP_LOG','single'), 
        'level' => env('HTTP_CLIENT_LOG_LEVEL', 'debug'),
        'file' => env('HTTP_CLIENT_LOG_FILE', storage_path('logs/http-client.log')),
    ],

    /**
     * 设置成指定PEM格式认证文件的路径的字符串，
     * 如果需要密码，需要设置成一个数组，其中PEM文件在第一个元素，
     * 密码在第二个元素。
     * 类型:string,array
     * 默认值: None
     * '/path/server.pem' | ['cert' => ['/path/server.pem', 'password']]
     * 
     */
    'cert'=>null,

    /**
     * 传入HTTP认证参数的数组来使用请求，
     * 该数组索引[0]为用户名、索引[1]为密码，
     * 索引[2]为可选的内置认证类型。
     * 传入 null 进入请求认证。
     * 类型: array,string,null
     * 默认值: None
     * [username,password] || [username,password,digest]
     */
    'auth'=>null,

    /**
     * 设置成 true 或设置成一个 fopen() 返回的流来启用调试输出发送请求的处理器， 
     * 比如，当使用cURL传输请求，cURL的 CURLOPT_VERBOSE 的冗长将会发出， 
     * 当使用PHP流，流处理的提示将会发生。 如果设置为true，输出到PHP标准输出文件，
     * 如果提供了PHP流，将会输出到流。
     * 类型: bool | fopen() resource
     * 默认值:None
     * fase|true|fopen(storage_path('logs/http_client_request.log'),'w+')
     */
    'debug'=>fopen(storage_path('logs/http_client_request.log'),'aw+'),

    /**
     * 请求时验证SSL证书行为。
     * 设置成 true 启用SSL证书验证，默认使用操作系统提供的CA包。
     * 设置成 false 禁用证书验证(这是不安全的！)。
     * 设置成字符串启用验证，并使用该字符串作为自定义证书CA包的路径。
     * 类型: bool | string
     * 默认: true
     */
    'verify'=>false,

    /**
     * 请求超时的秒数。使用 0 无限期的等待(默认行为)。
     * 类型: float
     * 默认: 0
     */
    'timeout'=>3.14,

    /**
     * 表示等待服务器响应超时的最大值，使用 0 将无限等待 (默认行为).
     * 类型: float
     * 默认: 0
     */
    'connect_timeout'=>3.14,

    /**
     * 是否描述请求的重定向行为
     * 类型
     * bool = false
     * array=[
     *  'max'             => 5,
     *  'strict'          => false,
     *  'referer'         => true,
     *  'protocols'       => ['http', 'https'],
     *  'track_redirects' => false
     * ]
     * 
     */

    'allow_redirects' => [
        'max'             => 5,
        'strict'          => false,
        'referer'         => true,
        'protocols'       => ['http', 'https'],
        'track_redirects' => false
    ],

    /**
     * 要添加到请求的报文头的关联数组，每个键名是header的名称，
     * 每个键值是一个字符串或包含代表头字段字符串的数组。
     */
    'headers' => [
        'Accept'=>'application/json',
        'User-Agent'=>'php/client',
        'Accept-Encoding'=>'gzip, deflate',
        'Accept-Language'=>'zh-CN,zh;q=0.9,en;q=0.8',
        'Cache-Control'=>'no-cache',
        'Content-Type'=>'application/x-www-form-urlencoded'
    ],

    /**
     * 声明是否自动解码 Content-Encoding 响应 (gzip, deflate等) 。
     * 类型:string,bool
     * 默认值:true
     */
    'decode_content'=>false,

    /**
     * 设置链接超时处理
     */
    'connect_timeout'=>3.14,

    /**
     * 是否禁用HTTP协议抛出的异常(如 4xx 和 5xx 响应) 默认情况下HTPP协议出错时会抛出异常
     * true 抛出
     * false 禁止抛出
     */
    'http_errors' => true,

];