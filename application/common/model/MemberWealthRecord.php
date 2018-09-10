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

class MemberWealthRecord extends Model
{
    /**
     * 增加积分
     * @param string $member_id 会员 ID
     * @param string $mpid
     * @param int $score
     * @param string $remarks
     * @return false|int
     */
    public function addScore($member_id = '', $mpid = '', $score = 0, $remarks = '')
    {
        $data = ['score' => $score, 'type' => '1', 'remark' => $remarks, 'member_id' => $member_id, 'mpid' => $mpid, 'time' => time()];
        Db::startTrans();
        try {
            if (Db::name('member_wealth_record')->insert($data)) {
                if (!Db::name('mp_friends')->where(['id' => $member_id, 'mpid' => $mpid])->setInc('score', $score)) {
                    Db::rollback();
                    return false;
                }
                Db::commit();
                return true;
            } else {
                return false;
            }
        } catch (\Exception $exception) {
            Db::rollback();
            return false;
        }
    }

    /**
     * 增加金钱
     * @param string $member_id 会员 ID
     * @param string $mpid
     * @param int $money
     * @param string $remarks
     * @return false|int
     */
    public function addMoney($member_id = '', $mpid = '', $money = 0, $remarks = '', $order_number = '')
    {
        $data = ['money' => $money, 'type' => '2', 'remark' => $remarks, 'member_id' => $member_id, 'mpid' => $mpid, 'time' => time()];
        Db::startTrans();
        try {
            if (Db::name('member_wealth_record')->insert($data)) {
                if (Db::name('mp_friends')->where(['id' => $member_id, 'mpid' => $mpid])->setInc('money', $money)) {
                    if ($order_number) {
                        if (!Db::name('payment')->where(['mpid' => $mpid, 'order_number' => $order_number])->update(['status' => 1])) {
                            Db::rollback();
                            return false;
                        }
                    }
                } else {
                    Db::rollback();
                    return false;
                }
                Db::commit();
                return true;
            } else {
                return false;
            }
        } catch (\Exception $exception) {
            Db::rollback();
            return false;
        }
    }

    /**
     * 减积分
     * @param string $member_id 会员 ID
     * @param string $mpid
     * @param int $score
     * @param string $remarks
     * @return false|int
     */
    public function subtractScore($member_id = '', $mpid = '', $score = 0, $remarks = '')
    {
        $data = ['score' => '-' . $score, 'type' => '1', 'remark' => $remarks, 'member_id' => $member_id, 'mpid' => $mpid, 'time' => time()];
        Db::startTrans();
        try {
            if (Db::name('member_wealth_record')->insert($data)) {
                if (!Db::name('mp_friends')->where(['id' => $member_id, 'mpid' => $mpid])->setDec('score', $score)) {
                    Db::rollback();
                    return false;
                }
                Db::commit();
                return true;
            } else {
                return false;
            }
        } catch (\Exception $exception) {
            Db::rollback();
            return false;
        }
    }

    /**
     * 减金钱
     * @param string $member_id 会员 ID
     * @param string $mpid
     * @param int $money
     * @param string $remarks
     * @return false|int
     */
    public function subtractMoney($member_id = '', $mpid = '', $money = 0, $remarks = '')
    {
        $data = ['money' => '-' . $money, 'type' => '2', 'remark' => $remarks, 'member_id' => $member_id, 'mpid' => $mpid, 'time' => time()];
        Db::startTrans();
        try {
            if (Db::name('member_wealth_record')->insert($data)) {
                if (!Db::name('mp_friends')->where(['id' => $member_id, 'mpid' => $mpid])->setDec('money', $money)) {
                    Db::rollback();
                    return false;
                }
                Db::commit();
                return true;
            } else {
                return false;
            }
        } catch (\Exception $exception) {
            Db::rollback();
            return false;
        }
    }

    public function getMemberScoreBySum($id = '', $mpid = '')
    {
        return $this->where(['member_id' => $id, 'mpid' => $mpid, 'type' => 1])->where('score', '>', '0')->sum('score');
    }

    public function getMemberMoneyBySum($id = '', $mpid = '')
    {
        return $this->where(['member_id' => $id, 'mpid' => $mpid, 'type' => 2])->where('money', '>', '0')->sum('money');
    }

}