<?php
/**
 * 이 파일은 iModule 관리자모듈의 일부입니다. (https://www.imodules.io)
 * 
 * 사이트에 생성되어 있는 1차메뉴 또는 2차메뉴 목록을 가져온다.
 *
 * @file /modules/admin/process/@getSitemap.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.1.0
 * @modified 2019. 4. 20.
 */
if (defined('__IM__') == false) exit;

$domain = Request('domain');
$language = Request('language');
$menu = Request('menu');
$mode = Request('mode');

/**
 * 사이트정보를 가져온다.
 */
$this->IM->initSites();
$site = $this->IM->getSites($domain,$language);

if (strpos($site->templet,'#') === 0 && $this->IM->getModule()->isSitemap(substr($site->templet,1)) == true) {
	$results->success = false;
	$results->message = str_replace('{MODULE}',$this->IM->getModule()->getName(substr($site->templet,1)),$this->getErrorText('SITEMAP_FROM_MODULE'));
} else {
	$lists = $this->IM->db()->select($this->IM->getTable('sitemap'))->where('domain',$domain)->where('language',$language);
	if ($menu == null) $lists->where('page','');
	else $lists->where('menu',$menu)->where('page','','!=');
	if ($mode == 'subpage') $lists->where('type','GROUPSTART','!=')->where('type','GROUPEND','!=');
	$lists = $lists->orderBy('sort','asc')->get();
	
	$is_grouping = false;
	for ($i=0, $loop=count($lists);$i<$loop;$i++) {
		if ($lists[$i]->type == 'GROUPSTART') $is_grouping = true;
		$lists[$i]->url = ($site->is_ssl == 'TRUE' ? 'https://' : 'http://').$site->domain.__IM_DIR__.'/'.$site->language.'/';
		if ($lists[$i]->page) $lists[$i]->url.= $lists[$i]->menu.'/';
		
		$lists[$i]->icon = $this->IM->parseIconString($lists[$i]->icon);
		$lists[$i]->is_hide = $lists[$i]->is_hide == 'TRUE';
		$lists[$i]->is_footer = $lists[$i]->is_footer == 'TRUE';
		
		$context = json_decode($lists[$i]->context);
		if ($lists[$i]->type == 'EXTERNAL') {
			$lists[$i]->context = $context->external;
		} elseif ($lists[$i]->type == 'MODULE') {
			$lists[$i]->context = $this->Module->getTitle($context->module).' - '.$this->Module->getContextTitle($context->context,$context->module);
		} elseif ($lists[$i]->type == 'PAGE') {
			$lists[$i]->context = $this->IM->getPages($lists[$i]->menu,$context->page,$lists[$i]->domain,$lists[$i]->language)->title.'('.$context->page.')';
		} elseif ($lists[$i]->type == 'LINK') {
			$lists[$i]->context = $context->link;
		} elseif ($lists[$i]->type == 'HTML') {
			$lists[$i]->context = $context != null && isset($context->html) == true && isset($context->css) == true ? '본문 : '.GetFileSize(strlen($context->html)).' / 스타일시트 : '.GetFileSize(strlen($context->css)) : '내용없음';
		} else {
			$lists[$i]->context = '';
		}
		
		$header = json_decode($lists[$i]->header);
		$footer = json_decode($lists[$i]->footer);
		if ($header == null || $footer == null) {
			$header = new stdClass();
			$header->type = 'NONE';
			$footer = new stdClass();
			$footer->type = 'NONE';
			
			$this->db()->update($this->IM->getTable('sitemap'),array('header'=>json_encode($header),'footer'=>json_encode($footer)))->where('domain',$domain)->where('language',$language)->where('menu',$lists[$i]->menu)->where('page',$lists[$i]->page)->execute();
		}
		
		if ($mode != 'subpage' && $lists[$i]->sort != $i) {
			$this->IM->db()->update($this->IM->getTable('sitemap'),array('sort'=>$i))->where('domain',$domain)->where('language',$language)->where('menu',$lists[$i]->menu)->where('page',$lists[$i]->page)->execute();
			$lists[$i]->sort = $i;
		}
		
		$lists[$i]->is_grouping = $is_grouping;
		if ($lists[$i]->type == 'GROUPEND') $is_grouping = false;
	}
	
	$results->success = true;
	$results->lists = $lists;
	$results->total = count($lists);
}
?>