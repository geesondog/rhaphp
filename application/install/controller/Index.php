<?php
namespace app\install\controller;

use think\facade\Env;
use think\facade\Request;
use think\facade\View;

class Index extends \think\Controller {

	protected $status;

	public function initialize() {
		$this->status = array(
			'index'    => 'info',
			'check'    => 'info',
			'config'   => 'info',
			'sql'      => 'info',
			'complete' => 'info',
		);
		if (request()->action() != 'complete' && is_file(APP_PATH . '/install.lock')) {
			return $this->redirect('admin/index/index');
		}
		View::config('view_path',APP_PATH.Request::module().'/view/');
		$this->assign('stlUrl',Request::domain().'/index.php');
	}

	public function index() {
		$this->status['index'] = 'primary';
		$this->assign('status', $this->status);
		return $this->fetch();
	}

	public function check() {
		session('error', false);
		//环境检测
		$env = check_env();
		//目录文件读写检测
		if (IS_WRITE) {
			$dirfile = check_dirfile();
			$this->assign('dirfile', $dirfile);
		}
		//函数检测
		$func = check_func();
		session('step', 1);
		$this->assign('env', $env);
		$this->assign('func', $func);
		$this->status['index'] = 'success';
		$this->status['check'] = 'primary';
		$this->assign('status', $this->status);
		return view();
	}

	public function config($db = null, $admin = null) {
		if (request()->IsPost()) {
			//检测管理员信息
			if (!is_array($admin) || empty($admin[0]) || empty($admin[1]) || empty($admin[3])) {
				return $this->error('请填写完整管理员信息');
			} else if ($admin[1] != $admin[2]) {
				return $this->error('确认密码和密码不一致');
			} else {
				$info = array();
				list($info['username'], $info['password'], $info['repassword'], $info['email'])
				= $admin;
				//缓存管理员信息
				session('admin_info', $info);
			}

			//检测数据库配置
			if (!is_array($db) || empty($db[0]) || empty($db[1]) || empty($db[2]) || empty($db[3])) {
				return $this->error('请填写完整的数据库配置');
			} else {
				$DB = array();
				list($DB['type'], $DB['hostname'], $DB['database'], $DB['username'], $DB['password'],
					$DB['hostport'], $DB['prefix']) = $db;
				//缓存数据库配置
				session('db_config', $DB);

				//创建数据库
				$dbname = $DB['database'];
				unset($DB['database']);
				$db  = \think\Db::connect($DB);
				$sql = "CREATE DATABASE IF NOT EXISTS `{$dbname}` DEFAULT CHARACTER SET utf8";
				if (!$db->execute($sql)) {
					return $this->error('创建数据库失败');
				} else {
					return $this->redirect('install/index/sql');
				}
			}
		} else {
			$this->status['index']  = 'success';
			$this->status['check']  = 'success';
			$this->status['config'] = 'primary';
			$this->assign('status', $this->status);
			return $this->fetch();
		}
	}

	public function sql() {
        set_time_limit(0);
		session('error', false);
		$this->status['index']  = 'success';
		$this->status['check']  = 'success';
		$this->status['config'] = 'success';
		$this->status['sql']    = 'primary';
		$this->assign('status', $this->status);
		echo $this->fetch();
		if (session('update')) {

		} else {
			//连接数据库
			$dbconfig = session('db_config');
			$db       = \think\Db::connect($dbconfig);
			//创建数据表
			create_tables($db, $dbconfig['prefix']);
			//注册创始人帐号
			$admin = session('admin_info');
			register_administrator($db, $dbconfig['prefix'], $admin);

			//创建配置文件
			$conf = write_config($dbconfig);
			session('config_file', $conf);
		}

		if (session('error')) {
			show_msg('失败');
		} else {
			echo '<script type="text/javascript">location.href = "'.url('install/index/complete').'";</script>';
		}
	}

	public function complete() {
		$this->status['index']    = 'success';
		$this->status['check']    = 'success';
		$this->status['config']   = 'success';
		$this->status['sql']      = 'success';
		$this->status['complete'] = 'primary';
        $data=[];
        $app=[];
        $result=getAppAndWindvaneByApi();
        if($result != false){
            if(isset($result['status']) && isset($result['data'])){
                if($result['status']==1){
                    $data=isset($result['data'])?$result['data']:[];
                    $app=isset($result['app'])?$result['app']:[];
                }
            }
        }
        $this->assign('app_by_api',$app);
        $this->assign('data_by_api',$data);
		$this->assign('status', $this->status);
		$this->assign('status', $this->status);
		return $this->fetch();
	}
}