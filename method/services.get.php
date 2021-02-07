<?php

//pcbid不对
header('HTTP/1.0 403 Forbidden');
exit();

//添加services 并设置属性
	$xmldata->addChild('services');
	$xmldata->services->addAttribute('expire','10800');
	$xmldata->services->addAttribute('method','get');
	$xmldata->services->addAttribute('mode','operation');
	$xmldata->services->addAttribute('status','0');
		
	
	if(strpos($url,':') !== false){
		$it = explode(':',$_SERVER['HTTP_HOST']);
		$ip = $it[0];
		unset($it);
	}else{
		$ip = $_SERVER['HTTP_HOST'];
	}
	
	$array_t = array(
		array(
			'name' => 'ntp',
			'url' => 'ntp://pool.ntp.org/'
		),
		array(
			'name' => 'keepalive',
			'url' => 'http://'.$ip.'/keepalive?pa='.$ip.'&ia='.$ip.'&ga='.$ip.'&ma='.$ip.'&t1=2&t2=10'
		),
		'cardmng',
		'facility',
		'message',
		'numbering',
		'package',
		'pcbevent',
		'pcbtracker',
		'pkglist',
		'posevent',
		'userdata',
		'userid',
		'eacoin',
		'local',
		'local2',
		'lobby',
		'lobby2',
		'dlstatus',
		'netlog',
		'sidmgr',
		'globby'
	);
	
	for($i = 1;$i <= count($array_t);$i++){
		
		$xmldata->services->addChild('item');
		
		if(is_array($array_t[$i - 1])){
			
			$xmldata->services->item[$i - 1]->addAttribute('name',$array_t[$i - 1]['name']);
			$xmldata->services->item[$i - 1]->addAttribute('url',$array_t[$i - 1]['url']);
			
		}else{
			
			$xmldata->services->item[$i - 1]->addAttribute('name',$array_t[$i - 1]);
			$xmldata->services->item[$i - 1]->addAttribute('url',$url);
			
		}
		
	}