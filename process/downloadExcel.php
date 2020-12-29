<?php
/**
 * 이 파일은 iModule 관리자모듈의 일부입니다. (https://www.imodules.io)
 * 
 * 관리자모듈에 의해 생성된 엑셀파일로 변환한다.
 *
 * @file /modules/admin/process/downloadExcel.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.1.0
 * @modified 2018. 3. 18.
 */
if (defined('__IM__') == false) exit;

$hash = Request('hash');
$title = str_replace(array('&lt;','&gt;'),array('<','>'),urldecode(Request('title')));
$mime = Request('mime') ? intval(Request('mime')) : 1;
$extension = $mime === 1 ? 'xlsx' : 'zip';
$this->IM->getModule('attachment')->tempFileDownload($hash,true,$title.'.'.$extension);
exit;
?>