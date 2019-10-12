<?php
/**
 * 이 파일은 iModule 관리자모듈의 일부입니다. (https://www.imodules.io)
 * 
 * 서버에 존재하는 템플릿을 불러온다.
 *
 * @file /modules/admin/process/@getTemplets.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.1.0
 * @modified 2018. 3. 18.
 */
if (defined('__IM__') == false) exit;

$type = Request('type');
$target = Request('target');
$use_default = Request('use_default');

$lists = array();

if ($type == 'core') {
	if ($target == 'site') {
		$templets = $this->IM->getTemplets($this->IM);

		for ($i=0, $loop=count($templets);$i<$loop;$i++) {
			$lists[] = array('title'=>$templets[$i]->getTitle().' ('.$templets[$i]->getDir().')','templet'=>$templets[$i]->getName(),'sort'=>1);
		}
	}
}

if ($type == 'module') {
	$templets = $this->IM->getModule($target,true)->getModule()->getTemplets();
	
	for ($i=0, $loop=count($templets);$i<$loop;$i++) {
		$lists[] = array('title'=>$templets[$i]->getTitle().' ('.$templets[$i]->getDir().')','templet'=>$templets[$i]->getName(),'sort'=>1);
	}
	
	if ($use_default !== 'false') {
		$lists[] = array('title'=>$use_default === 'true' ? '기본설정사용' : $use_default,'templet'=>'#','sort'=>0);
	}
}

if ($type == 'plugin') {
	$templets = $this->IM->getPlugin($target,true)->getTemplets();
	
	for ($i=0, $loop=count($templets);$i<$loop;$i++) {
		$lists[] = array('title'=>$templets[$i]->getTitle().' ('.$templets[$i]->getDir().')','templet'=>$templets[$i]->getName(),'sort'=>1);
	}
	
	if ($use_default !== 'false') {
		$lists[] = array('title'=>$use_default === 'true' ? '기본설정사용' : $use_default,'templet'=>'#','sort'=>0);
	}
}

$results->success = true;
$results->lists = $lists;
$results->total = count($lists);
?>