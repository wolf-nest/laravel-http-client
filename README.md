# tlwl/http-client
http client for the request internal service.

## 运行环境

- php >= 7.0
- composer
- laravel || lumen >= 5.1

## 如何安装

```Shell
$ composer require tlwl/http-client
```

### 添加 service provider（optional. if laravel < 5.5 || lumen）

```PHP
// laravel < 5.5
Tlwl\HttpClient\HttpClientServiceProvider::class


// lumen
$app->register(Tlwl\HttpClient\HttpClientServiceProvider::class);
```

### 添加 alias（optional. if laravel < 5.5）

```PHP
'HttpClient'=>Tianwolf\HttpClient\Facades\HttpClient::class,
```

### 配置文件&数据表生成

```Shell
$ php artisan vendor:publish --provider="Tlwl\HttpClient\HttpClientServiceProvider" 
```

**lumen 用户请手动复制**

随后，请在 `config` 文件夹中完善配置信息。

具体使用说明请传送至 [https://github.com/tian-wolf/laravel-http-client](https://github.com/tian-wolf/laravel-http-client)

## LICENSE [MIT](https://github.com/tian-wolf/laravel-http-client/blob/master/LICENSE)
