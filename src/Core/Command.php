<?php

namespace Core;

use Core\Http\Response;
use Core\Exception\AppException;
use Core\Lib\Console;

/**
 * 命令行控制器基类
 *
 * @author lisijie <lsj86@qq.com>
 * @package Core
 */
abstract class Command
{
    /**
     * 默认动作
     * @var string
     */
    protected $defaultAction = 'index';

    public final function __construct()
    {
        $this->init();
    }

    public function init()
    {

    }

    /**
     * 提示消息
     *
     * @param string $message 提示消息
     * @param int $code 消息号
     */
    public function message($message, $code = MSG_ERR)
    {
        if ($code == MSG_OK) {
            echo $message . "\n";
        } else {
            echo Console::ansiFormat("错误提示：", Console::FG_RED) . $message . "\n";
            exit(1);
        }
    }


    /**
     * 执行控制器方法
     *
     * @param string $actionName 方法名
     * @param array $params 参数列表
     * @return Response|mixed
     * @throws AppException
     */
    public function execute($actionName, $params = [])
    {
        if (empty($actionName)) {
            $actionName = $this->defaultAction;
        }
        $actionName .= 'Action';
        if (!method_exists($this, $actionName)) {
            throw new \BadMethodCallException("方法不存在: " . get_class($this) . "::{$actionName}");
        }

        $method = new \ReflectionMethod($this, $actionName);
        if (!$method->isPublic()) {
            throw new \BadMethodCallException("调用非公有方法: " . get_class($this) . "::{$actionName}");
        }

        $args = [];
        $methodParams = $method->getParameters();
        if (!empty($methodParams)) {
            foreach ($methodParams as $k => $p) {
                $default = $p->isOptional() ? $p->getDefaultValue() : null;
                $value = array_key_exists($k, $params) ? $params[$k] : $default;
                if (null === $value && !$p->isOptional()) {
                    throw new AppException('缺少请求参数:' . $p->getName());
                }
                $args[] = $value;
            }
        }
        return $method->invokeArgs($this, $args);
    }
}
