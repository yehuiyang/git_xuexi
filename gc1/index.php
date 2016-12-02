<?php
$p=isset($_GET['p'])&&!empty($_GET['p'])?$_GET['p']:'home';
$c=isset($_GET['c'])&&!empty($_GET['c'])?$_GET['c']:'User';
$a=isset($_GET['act'])&&!empty($_GET['act'])?$_GET['act']:'index';

define('DS', DIRECTORY_SEPARATOR);//获取系统分隔符
define('FRA', 'framework'.DS);//获取核心文件路径
define('APP', 'application'.DS);//获取应用文件路径
define('PLAT', APP.$p.DS);//获取平台路径文件路径
define('CF', APP.'cofg'.DS);//获取配置
define('M', PLAT."model".DS);//获取模型路径
define('V', PLAT.'view'.DS);//获取视图路路径
define('C', PLAT.'controller'.DS);//获取控制器路径
define('UP', FRA.'tool'.DS);//获取工具类路径
define('CMN', APP.'commen'.DS);//获取通用函数路径
//自动加载
spl_autoload_register(
	function ($class){
		$framework['Controller']=FRA.'Controller.class.php';
		$framework['MysqliDb']=FRA.'MysqliDb.class.php';
		$framework['Model']=FRA.'Model.class.php';
		$framework['upload']=UP.'upload.class.php';
		if (array_key_exists($class, $framework)) {
			require $framework[$class];
		}elseif (substr($class, -5)=='Model') {
			require M.$class.'.class.php';
		}elseif (substr($class, -10)=='Controller') {
			require C.$class.'.class.php';
		}
	}
);
$c.="Controller";
$C=new $c();
// $a=isset($_GET['act'])&&!empty($_GET['act'])&&method_exists($c, $_GET['act'])?$_GET['act']:'index';
$C->$a();