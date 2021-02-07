<?php
	//抑制报错
	error_reporting(0);
	//判断是否启用ssl
	if(!empty($_SERVER['HTTPS'])){
		$url = 'https://';
	}else{
		$url = 'http://';
	}
	//拼接url
	$url = $url.$_SERVER['HTTP_HOST'];
	
	//获取model
	$model = $_GET['model'];
	//获取method
	$raw_f = $_GET['f'];
	//取客户端发送的header
	$header = getallheaders();
	
	//初始化值
	$noenc = false;
	$isxml = false;
	$docomp = false;
	
	//判定是否需要读取文件夹
	$need_to_dir = array('game','cardmng');
	$tf = explode('.',$raw_f);
	if(array_search($tf[0],$need_to_dir) !== false){
		$f = $tf[0].'/'.$tf[1];
	}else{
		$f = $raw_f;
	}
	unset($tf);
	//兄弟,
	//加密么?
	if(file_exists('method/'.$f.'.noenc')){
		$noenc = true;
	}
	//压缩么?
	if(file_exists('method/'.$f.'.docomp')){
		$docomp = true;
	}
	//调用method
	if(!file_exists('method/'.$f.'.php')){
		//固定值直接用xml好了
		if(file_exists('method/'.$f.'.xml')){
			//既然是个写好的xml直接读入啊
			$isxml = true;
		}else{
			//xml都没有?你请求了个鬼
			header('HTTP/1.0 404 Not Found');
			exit('Not Found');
		}
	}else{
		//创建一个xml模板
		$xmldata = new SimpleXMLElement('<?xml version="1.0" encoding="SHIFT_JIS"?><response></response>');
		//引入php
		include 'method/'.$f.'.php';
	}
	
	//生成缓存文件
	$filename = 'tmp/'.md5(microtime());
	$filename = 'tmp/'.$raw_f;
	file_put_contents($filename.'.bin',file_get_contents('php://input'));
	
	sleep(0.1);
	
	file_put_contents($filename.'.txt',json_encode($header));
	
	//需不需要解密
	if(!isset($header['X-Eamuse-Info'])){
		kbinxml($filename.'bin',$filename.'xml','none',$header['X-Compress'],0);
	}else{
		kbinxml($filename.'bin',$filename.'xml',$header['X-Eamuse-Info'],$header['X-Compress'],0);
	}
	
	
	if($isxml){
		copy('method/'.$f.'.xml',$filename.'-2.xml');
	}else{
		//把xml生成xml文件
		$xmldata->asxml($filename.'-2.xml');
	}
	
	//生成X-Eamuse-Info秘钥
	$eamuse_info = '1-'.substr(md5(microtime()),0,8).'-'.substr(md5(microtime()),0,4);
	
	//加密么?压缩么?
	if($noenc){
		$t_eamuse_info = 'none';
	}else{
		$t_eamuse_info = $eamuse_info;
	}
	if($docomp){
		$t_cop = 'lz77';
	}else{
		$t_cop = 'none';
	}
	
	//编码啥的
	kbinxml($filename.'-2.xml',$filename.'-2.bin',$t_eamuse_info,$t_cop,1);
	
	unset($t_eamuse_info);
	
	unlink($filename.'-2.xml');
	sleep(0.1);
	//取加密后的发送出去
	$file = file_get_contents($filename.'-2.bin');
	unlink($filename.'-2.bin');
	
	header('X-Powered-By: IaSoC');
	
	if(!$noenc){
		header('X-Eamuse-Info: '.$eamuse_info);
	}
	
	header('X-Compress: '.$t_cop);
	unset($t_cop);
	header('Content-type: application/octet-stream');
    header('Content-Length: '.strlen($file));
	echo $file;
	
	
	function kbinxml($in,$ot,$xeamuseinfo,$comp,$en_de){
		//<inputfile> <outputfile> <arc4> <lz77> <en-1/de-0>
		
		if($en_de == '1' or $en_de == '0'){
			$result = exec('"./kbinxml/kbinxml.exe" '.$in.' '.$ot.' '.$xeamuseinfo.' '.$comp.' '.$en_de);
			if($result == 'ok'){
				return 0;
			}else{
				return $result;
			}
		}else{
			return -1;
		}
	}