<?php

namespace Core\View;

/**
 * 模版引擎接口
 *
 * @author lisijie <lsj86@qq.com>
 * @package Core\View
 */
interface ViewInterface
{

    /**
     * 获取设置项的值
     *
     * @param string $name
     * @return mixed
     */
    public function getOption($name);

    /**
     * 设置配置项的值
     *
     * @param $name
     * @param $value
     * @return mixed
     */
    public function setOption($name, $value);

    /**
     * 渲染模板
     *
     * @param $filename
     * @param $data
     * @return string
     */
    public function render($filename, array $data);

    /**
     * 返回模板文件路径
     *
     * @param string $filename
     * @return mixed
     */
    public function getViewFile($filename);

    /**
     * 设置布局文件
     * @param $filename
     * @return mixed
     */
    public function setLayout($filename);

    /**
     * 设置子布局文件
     * @param $name
     * @param $filename
     * @return mixed
     */
    public function setLayoutSection($name, $filename);

    /**
     * 重置
     * @return mixed
     */
    public function reset();

    /**
     * 注册CSS文件（在控制器中使用）
     *
     * @param $url
     * @param array $options
     * @return mixed
     */
    public function registerCssFile($url, $options = []);

    /**
     * 注册JS文件（在控制器中使用）
     *
     * @param $url
     * @param array $options
     * @param bool $head
     * @return mixed
     */
    public function registerJsFile($url, $options = [], $head = true);

    /**
     * 引入CSS文件（在模板中使用）
     *
     * @param $url
     * @param array $options
     * @return mixed
     */
    public function requireCssFile($url, $options = []);

    /**
     * 引入JS文件（在模板中使用）
     *
     * @param $url
     * @param array $options
     * @return mixed
     */
    public function requireJsFile($url, $options = []);

    /**
     * 返回资源的实际URL
     *
     * @param $url
     * @return mixed
     */
    public function getAssetUrl($url);
}