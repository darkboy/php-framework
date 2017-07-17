<?php

namespace Core\Router;

/**
 * PATH_INFO理由解析
 * 对$_SERVER['PATH_INFO']进行理由解析，需要WEB服务器支持
 *
 * @author lisijie <lsj86@qq.com>
 * @package Core\Router
 */
class Pathinfo extends Router
{

    protected function parse()
    {
        if (null !== ($pathInfo = $this->request->getPathInfo())) {
            $this->parseRoute($pathInfo);
        }
    }

    public function makeUrl($route, $params = [])
    {
        $result = $this->makeUrlPath($route, $params);
        return $this->request->getBaseUrl() . '/' . $result['path'] . (empty($result['params']) ? '' : '&' . http_build_query($result['params']));
    }

}
