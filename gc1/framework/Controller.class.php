<?php
class Controller{
	public function __construct(){
		header("content-type:text/html;charset=utf-8");
	}
	public function gotourl($message,$url,$time){
		echo $message."<br>";
		echo "请稍等".$time."秒后返回<br>";
		header("refresh:$time;url=$url");
		echo "<a href='$url'>直接返回</a>";
		die;
	}
}
