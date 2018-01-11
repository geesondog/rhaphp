<?php
namespace addons\tuLing\controller;
use think\facade\Cache;
/**
 * 聊天机器人响应控制器
 * http://www.msr98.com
 * @author 邓继松
 */

class api{
	
	public function message($msg = [], $param = [])
    {
        $info = getAddonInfo('tuLing');
        $info = $info['mp_config'];
        if(!$info) return replyText('机器人聊天接口未填写，暂时不能使用此功能');
        if($info['enter_tip']=="")$info['enter_tip'] = '你想聊点什么呢';
        $info['keep_time'] || $info['keep_time'] = 300;
        $info['exit_keyword'] || $info['exit_keyword'] = '退出';
        $info['exit_tip'] || $info['exit_tip'] = '下次无聊的时候可以再找我聊天哦';
        if (!$info['api_url'] || !$info['api_key']) {
            replyText('机器人聊天接口未填写，暂时不能使用此功能');
            exit();
        }
        if(($msg['MsgType'] != 'text') && ($msg['MsgType'] != 'event' )){
            replyText('谢谢你的关注，你的回复我都保存了。智能客服智商偏低，视频语音等都暂时不能自动回复。');
            exit();
        }
        if($msg['MsgType'] == 'event')$msg['Content'] = $msg['EventKey'];    //点击菜单事件也继续上下文触发
        $content = $msg['Content'];	
        $tulin_userid  = Cache::get('tulin_userid');     //没有这个缓存相当于就是第一次调用
        if(empty($tulin_userid)){
            $openid = getOrSetOpenid();
            if($openid){
                $tulin_userid = str_replace("-","",$openid);
                $tulin_userid = substr($tulin_userid,1,15); 
                Cache::set('tulin_userid',$tulin_userid,$info['keep_time'] );
            }
             return  replyText($info['enter_tip']);
        }
        if(preg_match("/".$info['exit_keyword']."/",$content)){
            Cache::rm('tulin_userid');
            return replyText($info['exit_tip']);
        }
        $reply = $this->tuLingAPI($content);
        return (is_array($reply)) ? replyNews($reply): replyText($reply);
    }
	
	
	
	/**
	 * 发送post请求
	 * @param string $url 请求地址
	 * @param array $post_data post键值对数据
	 * @return string
	 */
	protected function send_post($url, $post_data) { 
	  $postdata = json_encode($post_data);
	  $options = array(
	    'http' => array(
	      'method' => 'POST',
	      'header' => 'Content-type:application/json; charset=utf-8',
	      'content' => $postdata,
	      'timeout' => 15 * 60 // 超时时间（单位:s）
	    )
	  );
	  $context = stream_context_create($options);
	  $result = file_get_contents($url, false, $context);
	  return $result;
	}
	
	
	
	//图灵机器人接口
	private function tuLingAPI($keyword) {
		$info = getAddonInfo();
		$url = $info['mp_config']['api_url'];
		$key = $info['mp_config']['api_key'];
                //用于上下文
                $tulin_userid  = Cache::get('tulin_userid');
                if(!$tulin_userid){
                        $openid = getOrSetOpenid();
                        if($openid){
                            $tulin_userid = str_replace("-","",$openid);
                            $tulin_userid = substr($tulin_userid,1,15); 
                            Cache::set('tulin_userid',$tulin_userid,$info['mp_config']['keep_time']);
                        }
                }
                Cache::set('tulin_userid',$tulin_userid,$info['mp_config']['keep_time']);     //在保持时间内。就让他继续保持
		$postdata = [
			'key'=> $key,
			'info' => $keyword,
			'tulin_userid' => $tulin_userid
		];
		$result = $this ->send_post($url, $postdata);
                $result = json_decode ( $result, true );
                if ($result ['code'] > 40000 && $result['code'] < 40008) {
			if ($result ['code'] < 40008 && ! empty ( $result ['text'] )) {
				return '图灵机器人请你注意：' . $result ['text'];
			} else {
                                $code = $result ['code'];
                                $msg = [];
                                $msg[40001]='参数key错误';
                                $msg[40002]='请求内容info为空';
                                $msg[40004]='当天请求次数已使用完';
                                $msg[40007]='数据格式异常';
				return  $msg;
			}
		}
		switch ($result ['code']) {
			case '100000' :
				return $result['text'];
				break;
			case '200000' :
				$text = $result ['text'] . ',<a href="' . $result ['url'] . '">点击进入</a>';
				return $text;
				break;
			case '301000' :
				foreach ( $result ['list'] as $info ) {
					$articles [] = array (
							'Title' => $info ['name'],
							'Description' => $info ['author'],
							'PicUrl' => $info ['icon'],
							'Url' => $info ['detailurl'] 
					);
				}
				return $articles;
				break;
			case '302000' :
				foreach ( $result ['list'] as $info ) {
					$articles [] = array (
							'Title' => $info ['article'],
							'Description' => $info ['source'],
							'PicUrl' => $info ['icon'],
							'Url' => $info ['detailurl'] 
					);
				}
				return $articles;
				break;
			case '304000' :
				foreach ( $result ['list'] as $info ) {
					$articles [] = array (
							'Title' => $info ['name'],
							'Description' => $info ['count'],
							'PicUrl' => $info ['icon'],
							'Url' => $info ['detailurl'] 
					);
				}
				return $articles;
				break;
			case '305000' :
				foreach ( $result ['list'] as $info ) {
					$articles [] = array (
							'Title' => $info ['start'] . '--' . $info ['terminal'],
							'Description' => $info ['starttime'] . '--' . $info ['endtime'],
							'PicUrl' => $info ['icon'],
							'Url' => $info ['detailurl'] 
					);
				}
				return $articles;
				break;
			case '306000' :
				foreach ( $result ['list'] as $info ) {
					$articles [] = array (
							'Title' => $info ['flight'] . '--' . $info ['route'],
							'Description' => $info ['starttime'] . '--' . $info ['endtime'],
							'PicUrl' => $info ['icon'],
							'Url' => $info ['detailurl'] 
					);
				}
				return $articles;
				break;
			case '307000' :
				foreach ( $result ['list'] as $info ) {
					$articles [] = array (
							'Title' => $info ['name'],
							'Description' => $info ['info'],
							'PicUrl' => $info ['icon'],
							'Url' => $info ['detailurl'] 
					);
				}
				return $articles;
				break;
			case '308000' :
				foreach ( $result ['list'] as $info ) {
					$articles [] = array (
							'Title' => $info ['name'],
							'Description' => $info ['info'],
							'PicUrl' => $info ['icon'],
							'Url' => $info ['detailurl'] 
					);
				}
				return $articles;
				break;
			case '309000' :
				foreach ( $result ['list'] as $info ) {
					$articles [] = array (
							'Title' => $info ['name'],
							'Description' => '价格 : ' . $info ['price'] . ' 满意度 : ' . $info ['satisfaction'],
							'PicUrl' => $info ['icon'],
							'Url' => $info ['detailurl'] 
					);
				}
				return $articles;
				break;
			case '310000' :
				foreach ( $result ['list'] as $info ) {
					$articles [] = array (
							'Title' => $info ['number'],
							'Description' => $info ['info'],
							'PicUrl' => $info ['icon'],
							'Url' => $info ['detailurl'] 
					);
				}
				return $articles;
				break;
			case '311000' :
				foreach ( $result ['list'] as $info ) {
					$articles [] = array (
							'Title' => $info ['name'],
							'Description' => '价格 : ' . $info ['price'],
							'PicUrl' => $info ['icon'],
							'Url' => $info ['detailurl'] 
					);
				}
				return $articles;
				break;
			case '312000' :
				foreach ( $result ['list'] as $info ) {
					$articles [] = array (
							'Title' => $info ['name'],
							'Description' => '价格 : ' . $info ['price'],
							'PicUrl' => $info ['icon'],
							'Url' => $info ['detailurl'] 
					);
				}
				return $articles;
				break;
			default :
				if (empty ( $result ['text'] )) {
					return false;
				} else {
					return $result ['text'];
				}
                         
                     }
                return true;
		
	}
	

	
	
}

?>