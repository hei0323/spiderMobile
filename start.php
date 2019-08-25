<?php
require_once(__DIR__ . '/vendor/autoload.php');

use Beanbun\Beanbun;
use DiDom\Document;
$beanbun = new Beanbun;
$beanbun->timeout = 30;
$beanbun->userAgent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:29.0) Gecko/20100101 Firefox/29.0';

$beanbun->seed = [
    'http://www.51hao.cc/city/hebei/qinhuangdao.php',
];

$beanbun->beforeDownloadPage = function($beanbun){
    // 在爬取前设置请求的 headers 从浏览器中复制出来的
    $beanbun->options['headers'] = [
        'Host'=> 'www.51hao.cc',
        'Connection'=> 'keep-alive',
        'Cache-Control'=> 'max-age=0',
        'Upgrade-Insecure-Requests'=> '1',
        'User-Agent'=> 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36',
        'DNT'=> '1',
        'Accept'=> 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
        'Referer'=> 'http://www.51hao.cc/',
        'Accept-Encoding'=> 'gzip, deflate',
        'Accept-Language'=> 'zh-CN,zh;q=0.9,en;q=0.8',
        'Cookie'=> 'UM_distinctid=16cc35beb3dae-02143715d4dcb5-e343166-144000-16cc35beb3e996; CNZZDATA3329425=cnzz_eid%3D1256969324-1566644175-%26ntime%3D1566656491; safedog-flow-item=5C850EF64B7AF44AC1EF12B0EE31D88F',
    ];
};

$beanbun->afterDownloadPage = function($beanbun) {

    //对爬取后的数据进行处理
    file_put_contents(__DIR__ . '/html/' . md5($beanbun->url).'.html', $beanbun->page);
    $html = file_get_contents(__DIR__ . '/html/' . md5($beanbun->url).'.html');
    //实例化DiDom对象
    $document = new Document($html);
    //取出包含号段的所有li元素
    $liData  = $document->find('li');
    $temp = '';
    foreach ($liData as $key=>$value){
        //循环取出每个li元素的子元素a 并得到号段值
        $mobile = $value->child(0)->text();
        $temp .= $mobile."\r\n";
        $tempMobile = '';
        //拼成完整的手机号
        for ($i=0;$i<=1000;$i++){
            if($i<10){
                $tempMobile .= $mobile.'000'.$i."\r\n";
            }elseif($i<100){
                $tempMobile .= $mobile.'00'.$i."\r\n";
            }elseif($i<1000){
                $tempMobile .= $mobile.'0'.$i."\r\n";
            }elseif($i==1000){
                $tempMobile .= $mobile.$i."\r\n";
            }
        }
        file_put_contents(__DIR__ . '/result/qinhuangdao_mobile.txt',$tempMobile,FILE_APPEND);
    }
    file_put_contents(__DIR__ . '/result/qinhuangdao_pre.txt',rtrim($temp,"\r\n"));
};

$beanbun->start();