<?php

/*
 * This file is part of the tlwl/http-client.
 * 
 * (c) 悟玄 <roc9574@sina.com>
 * 
 * This source file is subject to the MIT license that is bundled.
 * with this source code in the file LICENSE.
 */

namespace Tlwl\HttpClient\Facade;

use Tlwl\HttpClient\HttpClient;
use Illuminate\Support\Facades\Facade;

class HttpClientFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return HttpClient::class;
    }
}
