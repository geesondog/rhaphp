<?php
// +----------------------------------------------------------------------
// | [RhaPHP System] Copyright (c) 2017 http://www.rhaphp.com/
// +----------------------------------------------------------------------
// | [RhaPHP] 并不是自由软件,你可免费使用,未经许可不能去掉RhaPHP相关版权
// +----------------------------------------------------------------------
// | Author: Geeson <qimengkeji@vip.qq.com>
// +----------------------------------------------------------------------

namespace addons\nbforms\controller;


use app\common\controller\Addon;
use think\Db;
use think\facade\Request;

class Subfms extends Addon
{

    public function index()
    {
        $id = input('id');
        $forms = Db::name('forms')->where(['mid' => $this->mid, 'id' => $id])->find();
        $attr = json_decode($forms['attr_value'], true);
        $forms['attr_value'] = [];
        foreach ($attr as $key => $val) {
            $forms['attr_value'][$key] = $val;
            if ($val['type'] == 3 || $val['type'] == 4 || $val['type'] == 5) {
                $valArr = preg_replace("/(\n)|(\s)|(\t)|(\')|(')|(，)/", ',', $val['values']);
                $array = explode(",", $valArr);
                if (isset($array[0]) && empty($array[0])) {
                    $forms['attr_value'][$key]['values'] = [];
                } else {
                    $forms['attr_value'][$key]['values'] = $array;
                }

            }
        }
        if (Request::isAjax()) {
            if (empty($forms)) {
                ajaxMsg(0, '表单不存在');
            }
            $inArr = [];
            $inArr['forms_id'] = $id;
            $inArr['create_time'] = time();
            $inArr['mid'] = $this->mid;
            foreach ($forms['attr_value'] as $key => $val) {
                foreach (input() as $key1 => $val2) {
                    if ($val['name'] == $key1) {
                        $inArr['val'][$key1] = is_array($val2) ? $val2 : htmlspecialchars($val2);
                        if ($val['need'] == 1) {
                            if (isset($val2['0']) && $val2[0] == '0') {
                                ajaxMsg(0, $key1 . '不能为空');
                            }
                            if (empty($val2)) {
                                ajaxMsg(0, $key1 . '不能为空');
                            }
                        }
                        if (!is_array($val2)) {
                            if ($val['regular']) {
                                if (!preg_match("{$val['regular']}", $val2)) {
                                    if ($val['regularMsg']) {
                                        ajaxMsg(0, $key1 . $val['regularMsg']);
                                    } else {
                                        ajaxMsg(0, $key1 . '此字段不匹配的错误提示语为空');
                                    }

                                }
                            }
                        }
                    }
                }
            }
            $inArr['val'] = json_encode($inArr['val']);
            if (Db::name('forms_values')->insert($inArr)) {
                ajaxMsg(1, $forms['success_msg']);
            } else {
                ajaxMsg(0, '保存失败');
            }
        } else {
            if (empty($forms)) {
                $this->error('表单不存在');
            }

            $this->assign('form', $forms);
            $this->fetch('@'.$forms['template'] . '/index');
        }

    }



}