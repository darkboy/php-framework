<?php

namespace Core\Logger;

use Core\Logger\Handler\HandlerInterface;
use Core\Logger\Handler\NullHandler;

/**
 * 日志处理类
 *
 * @author lisijie <lsj86@qq.com>
 * @package Core\Logger
 */
class Logger implements LoggerInterface
{
    const FATAL = 5;
    const ERROR = 4;
    const WARN = 3;
    const INFO = 2;
    const DEBUG = 1;

    /**
     * 日志等级对应名称映射
     *
     * @var array
     */
    protected $levels = [
        self::FATAL => 'FATAL',
        self::ERROR => 'ERROR',
        self::WARN => 'WARN',
        self::INFO => 'INFO',
        self::DEBUG => 'DEBUG',
    ];

    /**
     * 日志通道名称
     * @var string
     */
    protected $channel;

    /**
     * 时区
     * @var \DateTimeZone
     */
    protected $timeZone;

    /**
     * 日志处理器
     * @var array
     */
    protected $handlers = [];

    public function __construct($channel)
    {
        $this->channel = $channel;
    }

    /**
     * 获取名称
     *
     * @return string
     */
    public function getName()
    {
        return $this->channel;
    }

    /**
     * 设置日志处理器
     *
     * @param HandlerInterface $handler
     */
    public function addHandler(HandlerInterface $handler)
    {
        $this->handlers[] = $handler;
    }

    /**
     * 设置时区
     *
     * @param \DateTimeZone $timeZone
     */
    public function setTimeZone(\DateTimeZone $timeZone)
    {
        $this->timeZone = $timeZone;
    }

    /**
     * 发生危险错误
     *
     * 例如: 应用程序组件不可用,意想不到的异常。
     *
     * @param string $message
     * @param array $context
     * @return null|void
     */
    public function fatal($message, array $context = [])
    {
        $this->log(self::FATAL, $message, $context);
    }

    /**
     * 运行时错误
     *
     * 例如: 用户非法操作,一般不需要立即采取行动,但需要记录和监控
     *
     * @param string $message
     * @param array $context
     * @return null|void
     */
    public function error($message, array $context = [])
    {
        $this->log(self::ERROR, $message, $context);
    }

    /**
     * 表明会出现潜在错误的情形
     *
     * 例如: 使用一个已经废弃的API,虽然没有错误,但应该提醒用户修正
     *
     * @param string $message
     * @param array $context
     * @return null|void
     */
    public function warn($message, array $context = [])
    {
        $this->log(self::WARN, $message, $context);
    }

    /**
     * 记录程序运行时的相关信息
     *
     * 例如: 用户登录,SQL记录
     *
     * @param string $message
     * @param array $context
     * @return null|void
     */
    public function info($message, array $context = [])
    {
        $this->log(self::INFO, $message, $context);
    }

    /**
     * 调试信息
     *
     * 主要用于开发期间记录调试信息，线上一般不开启
     *
     * @param string $message
     * @param array $context
     * @return null|void
     */
    public function debug($message, array $context = [])
    {
        $this->log(self::DEBUG, $message, $context);
    }

    /**
     * 记录日志
     *
     * @param int $level 日志级别
     * @param string $message 内容
     * @param array $context 上下文
     * @return null|void
     * @throws InvalidArgumentException
     */
    private function log($level, $message, array $context = [])
    {
        if (!isset($this->levels[$level])) {
            throw new InvalidArgumentException('日志级别无效:' . $level);
        }

        if (empty($this->handlers)) {
            $this->addHandler(new NullHandler());
        }

        if (!$this->timeZone) {
            $this->timeZone = new \DateTimeZone(date_default_timezone_get() ?: 'UTC');
        }
        if (is_array($message)) {
            $message = json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
        $file = '???';
        $line = 0;
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        if (isset($backtrace[1]) && isset($backtrace[1]['file'])) {
            $file = basename($backtrace[1]['file']);
            $line = $backtrace[1]['line'];
        }
        $record = [
            'message' => (string)$message,
            'context' => $context,
            'level' => $level,
            'level_name' => $this->levels[$level],
            'channel' => $this->channel,
            'datetime' => \DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true)), $this->timeZone)->setTimezone($this->timeZone),
            'extra' => [],
            'file' => $file,
            'line' => $line,
        ];

        foreach ($this->handlers as $handler) {
            if ($handler->handle($record)) {
                break;
            }
        }
    }
}
