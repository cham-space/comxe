# COMX

## 概述
分或合，这是一个问题...
大型业务系统的前端API，面临业务解耦和整合数据输出的两难选择——以一个基于HTTP的RESTful API为例：

```json
// 一个商品的信息关联了商品发布者的信息，如下所示：
// GET https://api.gomeplus.com/item?id={shopId}
// Response:
{
  "message":"",
  "data":{
    "id":123,
    "name": "xx商品",
    "price":128,
    "owner": {
       "id":321,
       "nickname":"老王"
     }
  }
}

// 这里商品 和会员其实分属两个不同的模块，如果不做特殊处理，API势必同时耦合两个不同的模块。
```

COMX 正是针对这样的场景，提供通过简单的配置来组装来自不同模块的数据的服务。简单讲，COMX是RESTful API的胶水层。
目前COMX适配公司[v2的API规范](/projects/meixin/wiki/HTTP_API规范_v2)

以前边提到的商品信息为例，服务开发者可以分别开发两个没有耦合的服务，如下：

```json
// 服务API1. (URL path 第一级表示模块名，第二级表示资源名)
// GET https://api.gomeplus.com/item/item?id=123
// Response:
{
  "message":"",
  "data":{
    "id":123,
    "name":"xx商品",
    "price":128,
    "ownerId":321
  }
}
```

```json
// 服务API2.
// GET https://api.gomeplus.com/user/user?id=321.
// Response:
{
  "message":"",
  "data":{
    "id":321,
    "nickname":"老王"
  }
}
```

```json
// 这两个来自独立模块的API，我们称之为原子API。
// 在comx中我们进行如下配置：
// 在路径 {COMX_HOME}/ext/item/item/目录下放置get.json  (COMX_HOME通过环境变量设置)
// get.json:
{
    "decors":[
        {
            "source":{
                "uri":"/item/item?id={request.url.query.id}&integrity=simple"
            },
            "decors":[
                {
                    "field": "user",
                    "source": {
                        "uri": "/user/user?id={data.ownerId}&integrity=simple"
                    }
                }
            ]
        }
    ]
}
```

```json
// 当我们访问
// GET https://api.gomeplus.com/ext/item/item?id=123  (注意，URL路径中增加了ext)
// 即可获得期望的组合信息：

{
  "message":"",
  "data":{
    "id":123,
    "name": "xx商品",
    "price":128,
    "ownerId": 321,
    "owner": {
       "id":321,
       "nickname":"老王"
     }
  }
}
```

**COMX 支持如下几种使用场景**

1. 扩展模式：在A原子接口的基础上，补充关联的字段或进行其它数据修饰，URL path 通常加上ext前缀，后面跟被扩展的API的资源路径；
1. 组合模式：取多个原子接口的数据，作为不同的字段并列，URL path 通常加上combo前缀。