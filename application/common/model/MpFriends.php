<?php
// +----------------------------------------------------------------------
// | [RhaPHP System] Copyright (c) 2017 http://www.rhaphp.com/
// +----------------------------------------------------------------------
// | [RhaPHP] 并不是自由软件,你可免费使用,未经许可不能去掉RhaPHP相关版权
// +----------------------------------------------------------------------
// | Author: Geeson <qimengkeji@vip.qq.com>
// +----------------------------------------------------------------------


namespace app\common\model;


use think\Db;
use think\Model;

class MpFriends extends Model
{

    /**
     * 会员列表
     * @param array $where
     * @param string $field
     * @param string $order
     * @param int $page
     * @return \think\Paginator
     */
    public function memberList($where = [], $field = '', $order = '', $page = 20)
    {
        $where['type'] = 1;
        return $this->where($where)->field($field)->order($order)->paginate($page);
    }

    /**
     * @param array $userInfo 授权登录获取的用户信息
     * @param string $mpid
     * @return $this|bool|int|string
     */
    public function register($userInfo = [], $mpid = '')
    {
        if (empty($userInfo) || !$mpid) {
            return false;
        }
        $userInfo['type'] = 1;
        $userInfo['mpid'] = $mpid;
        $userInfo['subscribe_time'] = time();
        $userInfo['nickname'] = isset($userInfo['nickname']) ? $userInfo['nickname'] : '';
        if ($this->where(['openid' => $userInfo['openid'], 'mpid' => $mpid])->find()) {
            return $this->allowField(true)->save($userInfo, ['openid' => $userInfo['openid'], 'mpid' => $mpid]);
        } else {

            return $this->allowField(true)->save($userInfo);
        }

    }

    /**
     * @param array $where
     * @return array|false|\PDOStatement|string|Model
     */
    public function getMemberInfo($where = [])
    {
        return $this->where($where)->find();
    }


    public function updateMember($where = [], $data = [])
    {
        return $this->save($data, $where);
    }

    public function getFriendReport($mid = '')
    {
        $subscribeWhere = [['mpid', '=', $mid], ['subscribe', '=', 1]];
        $today = $this->where($subscribeWhere)->whereTime('subscribe_time', 'today')->count('id');//今天
        $yesterday = $this->where($subscribeWhere)->whereTime('subscribe_time', 'yesterday')->count('id');//昨天
        $week = $this->where($subscribeWhere)->whereTime('subscribe_time', 'week')->count('id');//本周
        $lastweek = $this->where($subscribeWhere)->whereTime('subscribe_time', 'last week')->count('id');//上周
        $month = $this->where($subscribeWhere)->whereTime('subscribe_time', 'month')->count('id');//本月
        $lastmonth = $this->where($subscribeWhere)->whereTime('subscribe_time', 'last month')->count('id');//上月
        $year = $this->where($subscribeWhere)->whereTime('subscribe_time', 'year')->count('id');//今年
        $lastyear = $this->where($subscribeWhere)->whereTime('subscribe_time', 'last year')->count('id');//去年
        $data['subscribe'] = [
            'today' => $today,
            'yesterday' => $yesterday,
            'week' => $week,
            'lastweek' => $lastweek,
            'month' => $month,
            'lastmonth' => $lastmonth,
            'year' => $year,
            'lastyear' => $lastyear,
        ];
        $unsubscribe = [['mpid', '=', $mid], ['subscribe', '=', 0]];
        $today = $this->where($unsubscribe)->whereTime('unsubscribe_time', 'today')->count('id');//今天
        $yesterday = $this->where($unsubscribe)->whereTime('unsubscribe_time', 'yesterday')->count('id');//昨天
        $week = $this->where($unsubscribe)->whereTime('unsubscribe_time', 'week')->count('id');//本周
        $lastweek = $this->where($unsubscribe)->whereTime('unsubscribe_time', 'last week')->count('id');//上周
        $month = $this->where($unsubscribe)->whereTime('unsubscribe_time', 'month')->count('id');//本月
        $lastmonth = $this->where($unsubscribe)->whereTime('unsubscribe_time', 'last month')->count('id');//上月
        $year = $this->where($unsubscribe)->whereTime('unsubscribe_time', 'year')->count('id');//今年
        $lastyear = $this->where($unsubscribe)->whereTime('unsubscribe_time', 'last year')->count('id');//去年
        $data['unsubscribe'] = [
            'today' => $today,
            'yesterday' => $yesterday,
            'week' => $week,
            'lastweek' => $lastweek,
            'month' => $month,
            'lastmonth' => $lastmonth,
            'year' => $year,
            'lastyear' => $lastyear,
        ];
        $total = $this->where('mpid', '=', $mid)->count('id');//累积、关注与取消关注总和
        $subscribe_total = $this->where('mpid', '=', $mid)->where('subscribe', '=', '1')->count('id');//关注总数
        $unsubscribe_total = $this->where('mpid', '=', $mid)->where('subscribe', '=', '0')->count('id');//取消关注总数

        $data['total'] = [
            'total' => $total,
            'subscribe_total' => $subscribe_total,
            'unsubscribe_total' => $unsubscribe_total
        ];
        return $data;

    }

    public function updateLastTime($msgData)
    {
        $this->save(['last_time' => time()], ['openid' => $msgData['FromUserName']]);
    }


}