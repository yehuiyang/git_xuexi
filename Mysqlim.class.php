<?php

/**
 * 数据库连接类
 */
class Mysqlim
{
    /**
     * [$link 数据库连接私有属性]
     * @var [type]
     */
    private $link;

    /**
     * [$stmt stmt内置对象]
     * @var [type]
     */
    private $stmt;

    /**
     * [__construct 构建函数]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2016-08-16T18:56:25+0800
     * @copyright (c)                      ZiShang520  All           Rights Reserved
     * @param     [type]                   $db_host    [description]
     * @param     [type]                   $db_user    [description]
     * @param     [type]                   $db_pass    [description]
     * @param     [type]                   $db_name    [description]
     * @param     [type]                   $db_port    [description]
     * @param     string                   $db_charset [description]
     */
    public function __construct($db_host, $db_user, $db_pass, $db_name, $db_port, $db_charset = 'utf8')
    {
        //判断数据库；连接类是否存在
        class_exists('Mysqli', false) || exit('PHP不支持Mysqli类');

        //实例化连接数据库类
        $this->link = @new Mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);

        //判断是否右错误信息 无措错errno返回0
        if ($this->link->connect_errno) {
            exit($this->error($this->link->connect_errno));
        }

        //设置数据库通道编码
        $this->link->set_charset($db_charset);
    }

    /**
     * [bind_query 绑定执行sql]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2016-08-16T18:44:14+0800
     * @copyright (c)                      ZiShang520   All           Rights Reserved
     * @param     string                   $querystring [sql语句]
     * @param     array                    $params      [绑定参数]
     * @return    [type]                                [状态 布尔]
     */
    public function bind_query($querystring = '', $params = array())
    {
        //判断是否存在对应得class
        class_exists('Mysqli_stmt', false) || exit('Mysqli不支持Mysqli_stmt类');
        //实例化stmt
        $this->stmt = new Mysqli_stmt($this->link);
        // method_exists($this->link, 'stmt_init') || exit('Mysqli不支持stmt_init方法');
        // $this->stmt = $this->link->stmt_init();
        //定义参数地图
        $map = array(
            '%d' => 'i', //integer
            '%f' => 'd', //float
            '%s' => 's', //string
            '%b' => 'b', //blob
        );
        // /(%d|%f|%s|%b)/
        $expr = '/(' . implode('|', array_keys($map)) . ')/';
        if (preg_match_all($expr, $querystring, $matches)) {
            // 检测参数数量 占位符是否和传参数相等
            count($matches[0]) !== count($params) && exit('参数不符合');
            // 合并类型字符串
            $types = implode('', $matches[0]);
            // 转换参数
            $types = strtr($types, $map);
            // 执行正则表达式替换
            $query = preg_replace($expr, '?', $querystring);

            // 预处理sql
            if (!$this->stmt->prepare($query)) {
                return false;
            }
            // 把绑定类型放置到第一个下标
            array_unshift($params, $types);
            // 解决引用问题
            $param = array();
            foreach ($params as $key => $value) {
                $param[] = &$params[$key];
            }
            // 使用回掉函数动态传参
            if (!call_user_func_array(array($this->stmt, 'bind_param'), $param)) {
                return false;
            }
        } else {
            //不需要绑定参数执行
            // 预处理sql
            if (!$this->stmt->prepare($querystring)) {
                return false;
            }
        }
        // 执行sql
        return $this->stmt->execute();
    }
    /**
     * [fetch_array 取出数据]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2016-08-16T19:37:20+0800
     * @copyright (c)                      ZiShang520 All           Rights Reserved
     * @param     boolean                  $status    [是否只显示一条]
     * @return    [type]                              [description]
     */
    public function fetch_array($status = false)
    {
        // 取出获取到的数据放置到内存中
        if (!$this->stmt->store_result()) {
            return false;
        }
        // 如果没有数据就直接返回空
        if ($this->stmt->num_rows == 0) {
            return null;
        }
        // 定义空数组
        $result = array();
        // 获取字段相关信息
        $md = $this->stmt->result_metadata();
        // 定义空数组
        $params = array();
        // 取出查询字段
        while ($field = $md->fetch_field()) {
            // 解决引用字段名问题
            $params[] = &$result[$field->name];
        }
        // 把查询结果字段绑定到指定变量名（引用传参）
        if (!call_user_func_array(array($this->stmt, 'bind_result'), $result)) {
            return false;
        }
        // 判断是否只需要一条数据
        if ($status) {
            // 只执行一次
            $this->stmt->fetch();
            // 直接返回一条数据
            $data = $result;
        } else {
            // 取出内存中的数据赋值到变量中
            $data = array();
            // 循环去搓数据库查询到得数据
            while ($status = $this->stmt->fetch()) {
                // 处理由于引用带来得问题
                $arr = array();
                // 构建新的数组
                foreach ($result as $key => $value) {
                    $arr[$key] = $value;
                }
                $data[] = $arr;
            }
        }
        $this->stmt->free_result();
        return $data;
    }

    /**
     * [error 自定义错误信息]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2016-08-16T14:11:55+0800
     * @copyright (c)                      ZiShang520 All           Rights Reserved
     * @param     [type]                   $errno     [description]
     * @return    [type]                              [description]
     */
    private function error($errno)
    {
        switch ($errno) {
            case 1045:
            case 1046:
                return '连接数据库失败，用户名或者密码错误';
                break;
        }
    }
    /**
     * [get_stmt_error 获取错误信息]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2016-08-16T18:59:09+0800
     * @copyright (c)                      ZiShang520    All Rights Reserved
     * @return    [type]                   [description]
     */
    public function get_stmt_error()
    {
        return $this->stmt->error;
    }
}

$a = new Mysqlim('127.0.0.1', 'root', 'root', 'blog_log', 3306);
if (!$a->bind_query('SELECT * FROM `user` WHERE `username`=%s', array('admin'))) {
    exit($a->get_stmt_error());
}
if (($rows = $a->fetch_array()) === false) {
    exit($a->get_stmt_error());
}
var_dump($rows);
// $a->stmt_close();
// $a->close();
