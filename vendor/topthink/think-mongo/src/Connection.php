<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace think\mongo;

use MongoDB\BSON\ObjectID;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Command;
use MongoDB\Driver\Cursor;
use MongoDB\Driver\Exception\AuthenticationException;
use MongoDB\Driver\Exception\BulkWriteException;
use MongoDB\Driver\Exception\ConnectionException;
use MongoDB\Driver\Exception\InvalidArgumentException;
use MongoDB\Driver\Exception\RuntimeException;
use MongoDB\Driver\Manager;
use MongoDB\Driver\Query as MongoQuery;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;
use think\Collection;
use think\Container;
use think\Db;
use think\Exception;

/**
 * Mongo数据库驱动
 */
class Connection
{
    protected static $instance = [];
    protected $dbName          = ''; // dbName
    /** @var string 当前SQL指令 */
    protected $queryStr = '';
    // 查询数据类型
    protected $typeMap = 'array';
    protected $mongo; // MongoDb Object
    protected $cursor; // MongoCursor Object

    // 监听回调
    protected static $event = [];
    /** @var PDO[] 数据库连接ID 支持多个连接 */
    protected $links = [];
    /** @var PDO 当前连接ID */
    protected $linkID;
    protected $linkRead;
    protected $linkWrite;
    // Builder对象
    protected $builder;
    // 返回或者影响记录数
    protected $numRows = 0;
    // 错误信息
    protected $error = '';
    // 查询参数
    protected $options = [];
    // 数据表信息
    protected static $info = [];
    // 数据库连接参数配置
    protected $config = [
        // 数据库类型
        'type'            => '',
        // 服务器地址
        'hostname'        => '',
        // 数据库名
        'database'        => '',
        // 是否是复制集
        'is_replica_set'  => false,
        // 用户名
        'username'        => '',
        // 密码
        'password'        => '',
        // 端口
        'hostport'        => '',
        // 连接dsn
        'dsn'             => '',
        // 数据库连接参数
        'params'          => [],
        // 数据库编码默认采用utf8
        'charset'         => 'utf8',
        // 主键名
        'pk'              => '_id',
        // 主键类型
        'pk_type'         => 'ObjectID',
        // 数据库表前缀
        'prefix'          => '',
        // 数据库调试模式
        'debug'           => false,
        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        'deploy'          => 0,
        // 数据库读写是否分离 主从式有效
        'rw_separate'     => false,
        // 读写分离后 主服务器数量
        'master_num'      => 1,
        // 指定从服务器序号
        'slave_no'        => '',
        // 是否严格检查字段是否存在
        'fields_strict'   => true,
        // 数据集返回类型
        'resultset_type'  => 'array',
        // 自动写入时间戳字段
        'auto_timestamp'  => false,
        // 时间字段取出后的默认时间格式
        'datetime_format' => 'Y-m-d H:i:s',
        // 是否需要进行SQL性能分析
        'sql_explain'     => false,
        // 是否_id转换为id
        'pk_convert_id'   => false,
        // typeMap
        'type_map'        => ['root' => 'array', 'document' => 'array'],
        // Query对象
        'query'           => '\\think\\mongo\\Query',
    ];

    /**
     * 架构函数 读取数据库配置信息
     * @access public
     * @param array $config 数据库配置数组
     */
    public function __construct(array $config = [])
    {
        if (!class_exists('\MongoDB\Driver\Manager')) {
            throw new Exception('require mongodb > 1.0');
        }

        if (!empty($config)) {
            $this->config = array_merge($this->config, $config);
        }

        $this->builder = new Builder($this);
    }

    /**
     * 取得数据库连接类实例
     * @access public
     * @param mixed         $config 连接配置
     * @param bool|string   $name 连接标识 true 强制重新连接
     * @return Connection
     * @throws Exception
     */
    public static function instance($config = [], $name = false)
    {
        if (false === $name) {
            $name = md5(serialize($config));
        }

        if (true === $name || !isset(self::$instance[$name])) {
            // 解析连接参数 支持数组和字符串
            $options = self::parseConfig($config);

            if (true === $name) {
                $name = md5(serialize($config));
            }
            self::$instance[$name] = new static($options);
        }

        return self::$instance[$name];
    }

    /**
     * 连接数据库方法
     * @access public
     * @param array         $config 连接参数
     * @param integer       $linkNum 连接序号
     * @return Manager
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function connect(array $config = [], $linkNum = 0)
    {
        if (!isset($this->links[$linkNum])) {
            if (empty($config)) {
                $config = $this->config;
            } else {
                $config = array_merge($this->config, $config);
            }

            $this->dbName  = $config['database'];
            $this->typeMap = $config['type_map'];

            if ($config['pk_convert_id'] && '_id' == $config['pk']) {
                $this->config['pk'] = 'id';
            }

            $host = 'mongodb://' . ($config['username'] ? "{$config['username']}" : '') . ($config['password'] ? ":{$config['password']}@" : '') . $config['hostname'] . ($config['hostport'] ? ":{$config['hostport']}" : '');

            if ($config['debug']) {
                $startTime = microtime(true);
            }

            $this->links[$linkNum] = new Manager($host, $this->config['params']);

            if ($config['debug']) {
                // 记录数据库连接信息
                $this->logger('[ MongoDb ] CONNECT :[ UseTime:' . number_format(microtime(true) - $startTime, 6) . 's ] ' . $config['dsn']);
            }
        }

        return $this->links[$linkNum];
    }

    /**
     * 获取数据库的配置参数
     * @access public
     * @param string $config 配置名称
     * @return mixed
     */
    public function getConfig($config = '')
    {
        return $config ? $this->config[$config] : $this->config;
    }

    /**
     * 设置数据库的配置参数
     * @access public
     * @param string    $config 配置名称
     * @param mixed     $value 配置值
     * @return void
     */
    public function setConfig($config, $value)
    {
        $this->config[$config] = $value;
    }

    /**
     * 获取Mongo Manager对象
     * @access public
     * @return Manager|null
     */
    public function getMongo()
    {
        if (!$this->mongo) {
            return;
        } else {
            return $this->mongo;
        }
    }

    /**
     * 设置/获取当前操作的database
     * @access public
     * @param string  $db db
     * @throws Exception
     */
    public function db($db = null)
    {
        if (is_null($db)) {
            return $this->dbName;
        } else {
            $this->dbName = $db;
        }
    }

    /**
     * 将SQL语句中的__TABLE_NAME__字符串替换成带前缀的表名（小写）
     * @access public
     * @param string $sql sql语句
     * @return string
     */
    public function parseSqlTable($sql)
    {
        if (false !== strpos($sql, '__')) {
            $prefix = $this->getConfig('prefix');

            $sql = preg_replace_callback("/__([A-Z0-9_-]+)__/sU", function ($match) use ($prefix) {
                return $prefix . strtolower($match[1]);
            }, $sql);
        }

        return $sql;
    }

    /**
     * 执行查询
     * @access public
     * @param string            $namespace 当前查询的collection
     * @param MongoQuery        $query 查询对象
     * @param ReadPreference    $readPreference readPreference
     * @param string|bool       $class 返回的数据集类型
     * @param string|array      $typeMap 指定返回的typeMap
     * @return mixed
     * @throws AuthenticationException
     * @throws InvalidArgumentException
     * @throws ConnectionException
     * @throws RuntimeException
     */
    public function query($namespace, MongoQuery $query, ReadPreference $readPreference = null, $class = false, $typeMap = null)
    {
        $this->initConnect(false);
        Db::$queryTimes++;

        if (false === strpos($namespace, '.')) {
            $namespace = $this->dbName . '.' . $namespace;
        }

        if ($this->config['debug'] && !empty($this->queryStr)) {
            // 记录执行指令
            $this->queryStr = 'db' . strstr($namespace, '.') . '.' . $this->queryStr;
        }

        $this->debug(true);

        $this->cursor = $this->mongo->executeQuery($namespace, $query, $readPreference);

        $this->debug(false);

        return $this->getResult($class, $typeMap);
    }

    /**
     * 执行指令
     * @access public
     * @param Command           $command 指令
     * @param string            $dbName 当前数据库名
     * @param ReadPreference    $readPreference readPreference
     * @param string|bool       $class 返回的数据集类型
     * @param string|array      $typeMap 指定返回的typeMap
     * @return mixed
     * @throws AuthenticationException
     * @throws InvalidArgumentException
     * @throws ConnectionException
     * @throws RuntimeException
     */
    public function command(Command $command, $dbName = '', ReadPreference $readPreference = null, $class = false, $typeMap = null)
    {
        $this->initConnect(false);
        Db::$queryTimes++;

        $this->debug(true);

        $dbName = $dbName ?: $this->dbName;

        if ($this->config['debug'] && !empty($this->queryStr)) {
            $this->queryStr = 'db.' . $this->queryStr;
        }

        $this->cursor = $this->mongo->executeCommand($dbName, $command, $readPreference);

        $this->debug(false);

        return $this->getResult($class, $typeMap);

    }

    /**
     * 获得数据集
     * @access protected
     * @param bool|string       $class true 返回Mongo cursor对象 字符串用于指定返回的类名
     * @param string|array      $typeMap 指定返回的typeMap
     * @return mixed
     */
    protected function getResult($class = '', $typeMap = null)
    {
        if (true === $class) {
            return $this->cursor;
        }

        // 设置结果数据类型
        if (is_null($typeMap)) {
            $typeMap = $this->typeMap;
        }

        $typeMap = is_string($typeMap) ? ['root' => $typeMap] : $typeMap;

        $this->cursor->setTypeMap($typeMap);

        // 获取数据集
        $result = $this->cursor->toArray();

        if ($this->getConfig('pk_convert_id')) {
            // 转换ObjectID 字段
            foreach ($result as &$data) {
                $this->convertObjectID($data);
            }
        }

        $this->numRows = count($result);

        return $result;
    }

    /**
     * ObjectID处理
     * @access public
     * @param array     $data
     * @return void
     */
    private function convertObjectID(&$data)
    {
        if (isset($data['_id'])) {
            $data['id'] = $data['_id']->__toString();
            unset($data['_id']);
        }
    }

    /**
     * 执行写操作
     * @access public
     * @param string        $namespace
     * @param BulkWrite     $bulk
     * @param WriteConcern  $writeConcern
     *
     * @return WriteResult
     * @throws AuthenticationException
     * @throws InvalidArgumentException
     * @throws ConnectionException
     * @throws RuntimeException
     * @throws BulkWriteException
     */
    public function execute($namespace, BulkWrite $bulk, WriteConcern $writeConcern = null)
    {
        $this->initConnect(true);
        Db::$executeTimes++;

        if (false === strpos($namespace, '.')) {
            $namespace = $this->dbName . '.' . $namespace;
        }

        if ($this->config['debug'] && !empty($this->queryStr)) {
            // 记录执行指令
            $this->queryStr = 'db' . strstr($namespace, '.') . '.' . $this->queryStr;
        }

        $this->debug(true);

        $writeResult = $this->mongo->executeBulkWrite($namespace, $bulk, $writeConcern);

        $this->debug(false);

        $this->numRows = $writeResult->getMatchedCount();

        return $writeResult;
    }

    /**
     * 数据库日志记录（仅供参考）
     * @access public
     * @param string $type 类型
     * @param mixed  $data 数据
     * @param array  $options 参数
     * @return void
     */
    public function log($type, $data, $options = [])
    {
        if (!$this->config['debug']) {
            return;
        }

        if (is_array($data)) {
            array_walk_recursive($data, function (&$value) {
                if ($value instanceof ObjectID) {
                    $value = $value->__toString();
                }
            });
        }

        switch (strtolower($type)) {
            case 'aggregate':
                $this->queryStr = 'runCommand(' . ($data ? json_encode($data) : '') . ');';
                break;
            case 'find':
                $this->queryStr = $type . '(' . ($data ? json_encode($data) : '') . ')';

                if (isset($options['sort'])) {
                    $this->queryStr .= '.sort(' . json_encode($options['sort']) . ')';
                }

                if (isset($options['limit'])) {
                    $this->queryStr .= '.limit(' . $options['limit'] . ')';
                }

                $this->queryStr .= ';';
                break;
            case 'insert':
            case 'remove':
                $this->queryStr = $type . '(' . ($data ? json_encode($data) : '') . ');';
                break;
            case 'update':
                $this->queryStr = $type . '(' . json_encode($options) . ',' . json_encode($data) . ');';
                break;
            case 'cmd':
                $this->queryStr = $data . '(' . json_encode($options) . ');';
                break;
        }

        $this->options = $options;
    }

    /**
     * 获取最近执行的指令
     * @access public
     * @return string
     */
    public function getLastSql()
    {
        return $this->queryStr;
    }

    /**
     * 监听SQL执行
     * @access public
     * @param callable $callback 回调方法
     * @return void
     */
    public function listen($callback)
    {
        self::$event[] = $callback;
    }

    /**
     * 触发SQL事件
     * @access protected
     * @param string    $sql SQL语句
     * @param float     $runtime SQL运行时间
     * @param mixed     $options 参数
     * @return bool
     */
    protected function triggerSql($sql, $runtime, $options = [])
    {
        if (!empty(self::$event)) {
            foreach (self::$event as $callback) {
                if (is_callable($callback)) {
                    call_user_func_array($callback, [$sql, $runtime, $options]);
                }
            }
        } else {
            // 未注册监听则记录到日志中
            $this->logger('[ SQL ] ' . $sql . ' [ RunTime:' . $runtime . 's ]');
        }
    }

    public function logger($log, $type = 'sql')
    {
        $this->config['debug'] && Container::get('log')->record($log, $type);
    }

    /**
     * 数据库调试 记录当前SQL及分析性能
     * @access protected
     * @param boolean $start 调试开始标记 true 开始 false 结束
     * @param string  $sql 执行的SQL语句 留空自动获取
     * @return void
     */
    protected function debug($start, $sql = '')
    {
        if (!empty($this->config['debug'])) {
            // 开启数据库调试模式
            $debug = Container::get('debug');
            if ($start) {
                $debug->remark('queryStartTime', 'time');
            } else {
                // 记录操作结束时间
                $debug->remark('queryEndTime', 'time');

                $runtime = $debug->getRangeTime('queryStartTime', 'queryEndTime');

                $sql = $sql ?: $this->queryStr;

                // SQL监听
                $this->triggerSql($sql, $runtime, $this->options);
            }
        }
    }

    /**
     * 释放查询结果
     * @access public
     */
    public function free()
    {
        $this->cursor = null;
    }

    /**
     * 关闭数据库
     * @access public
     */
    public function close()
    {
        $this->mongo     = null;
        $this->cursor    = null;
        $this->linkRead  = null;
        $this->linkWrite = null;
        $this->links     = [];
    }

    /**
     * 初始化数据库连接
     * @access protected
     * @param boolean $master 是否主服务器
     * @return void
     */
    protected function initConnect($master = true)
    {
        if (!empty($this->config['deploy'])) {
            // 采用分布式数据库
            if ($master) {
                if (!$this->linkWrite) {
                    $this->linkWrite = $this->multiConnect(true);
                }

                $this->mongo = $this->linkWrite;
            } else {
                if (!$this->linkRead) {
                    $this->linkRead = $this->multiConnect(false);
                }

                $this->mongo = $this->linkRead;
            }
        } elseif (!$this->mongo) {
            // 默认单数据库
            $this->mongo = $this->connect();
        }
    }

    /**
     * 连接分布式服务器
     * @access protected
     * @param boolean $master 主服务器
     * @return Manager
     */
    protected function multiConnect($master = false)
    {
        $config = [];
        // 分布式数据库配置解析
        foreach (['username', 'password', 'hostname', 'hostport', 'database', 'dsn'] as $name) {
            $config[$name] = explode(',', $this->config[$name]);
        }

        // 主服务器序号
        $m = floor(mt_rand(0, $this->config['master_num'] - 1));

        if ($this->config['rw_separate']) {
            // 主从式采用读写分离
            if ($master) // 主服务器写入
            {
                if ($this->config['is_replica_set']) {
                    return $this->replicaSetConnect();
                } else {
                    $r = $m;
                }
            } elseif (is_numeric($this->config['slave_no'])) {
                // 指定服务器读
                $r = $this->config['slave_no'];
            } else {
                // 读操作连接从服务器 每次随机连接的数据库
                $r = floor(mt_rand($this->config['master_num'], count($config['hostname']) - 1));
            }
        } else {
            // 读写操作不区分服务器 每次随机连接的数据库
            $r = floor(mt_rand(0, count($config['hostname']) - 1));
        }

        $dbConfig = [];

        foreach (['username', 'password', 'hostname', 'hostport', 'database', 'dsn'] as $name) {
            $dbConfig[$name] = isset($config[$name][$r]) ? $config[$name][$r] : $config[$name][0];
        }

        return $this->connect($dbConfig, $r);
    }

    /**
     * 创建基于复制集的连接
     * @return Manager
     */
    public function replicaSetConnect()
    {
        $this->dbName  = $this->config['database'];
        $this->typeMap = $this->config['type_map'];

        if ($this->config['debug']) {
            $startTime = microtime(true);
        }

        $this->config['params']['replicaSet'] = $this->config['database'];

        $manager = new Manager($this->buildUrl(), $this->config['params']);

        if ($this->config['debug']) {
            // 记录数据库连接信息
            $this->logger('[ MongoDB ] ReplicaSet CONNECT:[ UseTime:' . number_format(microtime(true) - $startTime, 6) . 's ] ' . $this->config['dsn']);
        }

        return $manager;
    }

    /**
     * 根据配置信息 生成适用于连接复制集的 URL
     * @return string
     */
    private function buildUrl()
    {
        $url = 'mongodb://' . ($this->config['username'] ? "{$this->config['username']}" : '') . ($this->config['password'] ? ":{$this->config['password']}@" : '');

        $hostList = explode(',', $this->config['hostname']);
        $portList = explode(',', $this->config['hostport']);

        for ($i = 0; $i < count($hostList); $i++) {
            $url = $url . $hostList[$i] . ':' . $portList[0] . ',';
        }

        return rtrim($url, ",") . '/';
    }

    /**
     * 插入记录
     * @access public
     * @param Query     $query 查询对象
     * @param boolean   $replace      是否replace（目前无效）
     * @param boolean   $getLastInsID 返回自增主键
     * @return WriteResult
     * @throws AuthenticationException
     * @throws InvalidArgumentException
     * @throws ConnectionException
     * @throws RuntimeException
     * @throws BulkWriteException
     */
    public function insert(Query $query, $replace = null, $getLastInsID = false)
    {
        // 分析查询表达式
        $options = $query->getOptions();

        if (empty($options['data'])) {
            throw new Exception('miss data to insert');
        }

        // 生成bulk对象
        $bulk         = $this->builder->insert($query, $replace);
        $writeConcern = isset($options['writeConcern']) ? $options['writeConcern'] : null;
        $writeResult  = $this->execute($options['table'], $bulk, $writeConcern);
        $result       = $writeResult->getInsertedCount();

        if ($result) {
            $data      = $options['data'];
            $lastInsId = $this->getLastInsID();

            if ($lastInsId) {
                $pk        = $query->getPk($options);
                $data[$pk] = $lastInsId;
            }

            $query->setOption('data', $data);

            $query->trigger('after_insert');

            if ($getLastInsID) {
                return $lastInsId;
            }
        }
        return $result;
    }

    /**
     * 获取最近插入的ID
     * @access public
     * @return mixed
     */
    public function getLastInsID($sequence = null)
    {
        $id = $this->builder->getLastInsID();

        if (is_array($id)) {
            array_walk($id, function (&$item, $key) {
                if ($item instanceof ObjectID) {
                    $item = $item->__toString();
                }
            });
        } elseif ($id instanceof ObjectID) {
            $id = $id->__toString();
        }

        return $id;
    }

    /**
     * 批量插入记录
     * @access public
     * @param Query     $query 查询对象
     * @param mixed     $dataSet 数据集
     * @return integer
     * @throws AuthenticationException
     * @throws InvalidArgumentException
     * @throws ConnectionException
     * @throws RuntimeException
     * @throws BulkWriteException
     */
    public function insertAll(Query $query, array $dataSet)
    {
        // 分析查询表达式
        $options = $query->getOptions();

        if (!is_array(reset($dataSet))) {
            return false;
        }

        // 生成bulkWrite对象
        $bulk         = $this->builder->insertAll($query, $dataSet);
        $writeConcern = isset($options['writeConcern']) ? $options['writeConcern'] : null;
        $writeResult  = $this->execute($options['table'], $bulk, $writeConcern);

        return $writeResult->getInsertedCount();
    }

    /**
     * 更新记录
     * @access public
     * @param Query     $query 查询对象
     * @return int
     * @throws Exception
     * @throws AuthenticationException
     * @throws InvalidArgumentException
     * @throws ConnectionException
     * @throws RuntimeException
     * @throws BulkWriteException
     */
    public function update(Query $query)
    {
        $options = $query->getOptions();
        $data    = $options['data'];

        if (isset($options['cache']) && is_string($options['cache']['key'])) {
            $key = $options['cache']['key'];
        }

        $pk = $query->getPk($options);

        if (empty($options['where'])) {
            // 如果存在主键数据 则自动作为更新条件
            if (is_string($pk) && isset($data[$pk])) {
                $where[$pk] = $data[$pk];
                $key        = 'mongo:' . $options['table'] . '|' . $data[$pk];
                unset($data[$pk]);
            } elseif (is_array($pk)) {
                // 增加复合主键支持
                foreach ($pk as $field) {
                    if (isset($data[$field])) {
                        $where[$field] = $data[$field];
                    } else {
                        // 如果缺少复合主键数据则不执行
                        throw new Exception('miss complex primary data');
                    }

                    unset($data[$field]);
                }
            }
            if (!isset($where)) {
                // 如果没有任何更新条件则不执行
                throw new Exception('miss update condition');
            } else {
                $options['where']['$and'] = $where;
            }
        } elseif (!isset($key) && is_string($pk) && isset($options['where']['$and'][$pk])) {
            $key = $this->getCacheKey($options['where']['$and'][$pk], $options);
        }

        // 生成bulkWrite对象
        $bulk         = $this->builder->update($query);
        $writeConcern = isset($options['writeConcern']) ? $options['writeConcern'] : null;
        $writeResult  = $this->execute($options['table'], $bulk, $writeConcern);

        // 检测缓存
        if (isset($key) && Container::get('cache')->get($key)) {
            // 删除缓存
            Container::get('cache')->rm($key);
        }

        $result = $writeResult->getModifiedCount();

        if ($result) {
            if (isset($where[$pk])) {
                $data[$pk] = $where[$pk];
            } elseif (is_string($pk) && isset($key) && strpos($key, '|')) {
                list($a, $val) = explode('|', $key);
                $data[$pk]     = $val;
            }

            $query->setOption('data', $data);

            $query->trigger('after_update');
        }

        return $result;
    }

    /**
     * 删除记录
     * @access public
     * @param Query     $query 查询对象
     * @return int
     * @throws Exception
     * @throws AuthenticationException
     * @throws InvalidArgumentException
     * @throws ConnectionException
     * @throws RuntimeException
     * @throws BulkWriteException
     */
    public function delete(Query $query)
    {
        // 分析查询表达式
        $options = $query->getOptions();
        $pk      = $query->getPk($options);
        $data    = $options['data'];

        if (!is_null($data) && true !== $data) {
            if (!is_array($data)) {
                // 缓存标识
                $key = 'mongo:' . $options['table'] . '|' . $data;
            }

            // AR模式分析主键条件
            $query->parsePkWhere($data);
        } elseif (!isset($key) && is_string($pk) && isset($options['where']['$and'][$pk])) {
            $key = $this->getCacheKey($options['where']['$and'][$pk], $options);
        }

        if (true !== $data && empty($options['where'])) {
            // 如果不是强制删除且条件为空 不进行删除操作
            throw new Exception('delete without condition');
        }

        // 生成bulkWrite对象
        $bulk = $this->builder->delete($query);

        $writeConcern = isset($options['writeConcern']) ? $options['writeConcern'] : null;

        // 执行操作
        $writeResult = $this->execute($options['table'], $bulk, $writeConcern);

        // 检测缓存
        if (isset($key) && Container::get('cache')->get($key)) {
            // 删除缓存
            Container::get('cache')->rm($key);
        }

        $result = $writeResult->getDeletedCount();

        if ($result) {
            if (!is_array($data) && is_string($pk) && isset($key) && strpos($key, '|')) {
                list($a, $val) = explode('|', $key);

                $item[$pk] = $val;
                $data      = $item;
            }

            $query->setOption('data', $data);
            $query->trigger('after_delete');
        }
        return $result;
    }

    /**
     * 执行查询但只返回Cursor对象
     * @access public
     * @param Query     $query 查询对象
     * @return Cursor
     */
    public function getCursor(Query $query)
    {
        // 分析查询表达式
        $options = $query->getOptions();

        // 生成MongoQuery对象
        $mongoQuery = $this->builder->select($query);

        // 执行查询操作
        $readPreference = isset($options['readPreference']) ? $options['readPreference'] : null;

        return $this->query($options['table'], $mongoQuery, $readPreference, true, $options['typeMap']);
    }

    /**
     * 查找记录
     * @access public
     * @param Query     $query 查询对象
     * @return Collection|false|Cursor|string
     * @throws ModelNotFoundException
     * @throws DataNotFoundException
     * @throws AuthenticationException
     * @throws InvalidArgumentException
     * @throws ConnectionException
     * @throws RuntimeException
     */
    public function select(Query $query)
    {
        $options = $query->getOptions();

        $resultSet = false;
        if (!empty($options['cache'])) {
            // 判断查询缓存
            $cache     = $options['cache'];
            $key       = is_string($cache['key']) ? $cache['key'] : md5(serialize($options));
            $resultSet = Container::get('cache')->get($key);
        }

        if (!$resultSet) {
            // 生成MongoQuery对象
            $mongoQuery = $this->builder->select($query);

            if ($resultSet = $query->trigger('before_select')) {
            } else {
                // 执行查询操作
                $readPreference = isset($options['readPreference']) ? $options['readPreference'] : null;

                $resultSet = $this->query($options['table'], $mongoQuery, $readPreference, $options['fetch_cursor'], $options['typeMap']);

                if ($resultSet instanceof Cursor) {
                    // 返回MongoDB\Driver\Cursor对象
                    return $resultSet;
                }
            }

            if (isset($cache)) {
                // 缓存数据集
                $this->cacheData($key, $resultSet, $cache);
            }
        }

        return $resultSet;
    }

    /**
     * 查找单条记录
     * @access public
     * @param Query     $query 查询对象
     * @return array|null|Cursor|string|Model
     * @throws ModelNotFoundException
     * @throws DataNotFoundException
     * @throws AuthenticationException
     * @throws InvalidArgumentException
     * @throws ConnectionException
     * @throws RuntimeException
     */
    public function find(Query $query)
    {
        // 分析查询表达式
        $options = $query->getOptions();
        $pk      = $query->getPk($options);
        $data    = $options['data'];

        if (!empty($options['cache']) && true === $options['cache']['key'] && is_string($pk) && isset($options['where']['$and'][$pk])) {
            $key = $this->getCacheKey($options['where']['$and'][$pk], $options);
        }

        $result = false;
        if (!empty($options['cache'])) {
            // 判断查询缓存
            $cache = $options['cache'];
            if (true === $cache['key'] && !is_null($data) && !is_array($data)) {
                $key = 'mongo:' . $options['table'] . '|' . $data;
            } elseif (!isset($key)) {
                $key = is_string($cache['key']) ? $cache['key'] : md5(serialize($options));
            }
            $result = Container::get('cache')->get($key);
        }

        if (false === $result) {

            if (is_string($pk)) {
                if (!is_array($data)) {
                    if (isset($key) && strpos($key, '|')) {
                        list($a, $val) = explode('|', $key);
                        $item[$pk]     = $val;
                    } else {
                        $item[$pk] = $data;
                    }
                    $data = $item;
                }
            }

            $query->setOption('data', $data);
            $query->setOption('limit', 1);

            // 生成查询对象
            $mongoQuery = $this->builder->select($query);

            // 事件回调
            if ($result = $query->trigger('before_find')) {
            } else {
                // 执行查询
                $readPreference = isset($options['readPreference']) ? $options['readPreference'] : null;
                $resultSet      = $this->query($options['table'], $mongoQuery, $readPreference, $options['fetch_cursor'], $options['typeMap']);

                if ($resultSet instanceof Cursor) {
                    // 返回MongoDB\Driver\Cursor对象
                    return $resultSet;
                }

                $result = isset($resultSet[0]) ? $resultSet[0] : null;
            }

            if (isset($cache)) {
                // 缓存数据
                $this->cacheData($key, $result, $cache);
            }
        }

        return $result;
    }

    /**
     * 缓存数据
     * @access public
     * @param string    $key    缓存标识
     * @param mixed     $data   缓存数据
     * @param array     $config 缓存参数
     */
    protected function cacheData($key, $data, $config = [])
    {
        $cache = Container::get('cache');

        if (isset($config['tag'])) {
            $cache->tag($config['tag'])->set($key, $data, $config['expire']);
        } else {
            $cache->set($key, $data, $config['expire']);
        }
    }

    /**
     * 生成缓存标识
     * @access public
     * @param mixed     $value   缓存数据
     * @param array     $options 缓存参数
     */
    protected function getCacheKey($value, $options)
    {
        if (is_scalar($value)) {
            $data = $value;
        } elseif (is_array($value) && 'eq' == strtolower($value[0])) {
            $data = $value[1];
        }

        if (isset($data)) {
            return 'mongo:' . $options['table'] . '|' . $data;
        } else {
            return md5(serialize($options));
        }
    }

    /**
     * 获取数据表信息
     * @access public
     * @param string $tableName 数据表名 留空自动获取
     * @param string $fetch 获取信息类型 包括 fields type pk
     * @return mixed
     */
    public function getTableInfo($tableName, $fetch = '')
    {
        if (is_array($tableName)) {
            $tableName = key($tableName) ?: current($tableName);
        }

        if (strpos($tableName, ',')) {
            // 多表不获取字段信息
            return false;
        } else {
            $tableName = $this->parseSqlTable($tableName);
        }

        $guid = md5($tableName);
        if (!isset(self::$info[$guid])) {
            $mongoQuery = new MongoQuery([], ['limit' => 1]);

            $cursor = $this->query($tableName, $mongoQuery, null, true, ['root' => 'array', 'document' => 'array']);

            $resultSet = $cursor->toArray();
            $result    = isset($resultSet[0]) ? (array) $resultSet[0] : [];
            $fields    = array_keys($result);
            $type      = [];

            foreach ($result as $key => $val) {
                // 记录字段类型
                $type[$key] = getType($val);
                if ('_id' == $key) {
                    $pk = $key;
                }
            }

            if (!isset($pk)) {
                // 设置主键
                $pk = null;
            }

            $result = ['fields' => $fields, 'type' => $type, 'pk' => $pk];

            self::$info[$guid] = $result;
        }

        return $fetch ? self::$info[$guid][$fetch] : self::$info[$guid];
    }

    /**
     * 得到某个字段的值
     * @access public
     * @param string    $field 字段名
     * @param mixed     $default 默认值
     * @return mixed
     */
    public function value(Query $query, $field, $default = null)
    {
        $options = $query->getOptions();

        $result = null;
        if (!empty($options['cache'])) {
            // 判断查询缓存
            $cache  = $options['cache'];
            $key    = is_string($cache['key']) ? $cache['key'] : md5($field . serialize($options));
            $result = Container::get('cache')->get($key);
        }

        if (!$result) {
            if (isset($options['field'])) {
                $query->removeOption('field');
            }

            $query->setOption('field', $field);
            $query->setOption('limit', 1);

            $mongoQuery = $this->builder->select($query);

            // 执行查询操作
            $readPreference = isset($options['readPreference']) ? $options['readPreference'] : null;
            $cursor         = $this->query($options['table'], $mongoQuery, $readPreference, true, ['root' => 'array']);
            $resultSet      = $cursor->toArray();

            if (!empty($resultSet)) {
                $data = (array) array_shift($resultSet);
                if ($this->getConfig('pk_convert_id')) {
                    // 转换ObjectID 字段
                    $data['id'] = $data['_id']->__toString();
                }

                $result = $data[$field];
            } else {
                $result = null;
            }

            if (isset($cache)) {
                // 缓存数据
                $this->cacheData($key, $result, $cache);
            }
        }

        return !is_null($result) ? $result : $default;
    }

    /**
     * 得到某个列的数组
     * @access public
     * @param string $field 字段名 多个字段用逗号分隔
     * @param string $key 索引
     * @return array
     */
    public function column(Query $query, $field, $key = '')
    {
        $options = $query->getOptions();

        $result = false;
        if (!empty($options['cache'])) {
            // 判断查询缓存
            $cache  = $options['cache'];
            $guid   = is_string($cache['key']) ? $cache['key'] : md5($field . serialize($options));
            $result = Container::get('cache')->get($guid);
        }

        if (!$result) {
            if (isset($options['projection'])) {
                $query->removeOption('projection');
            }

            if ($key && '*' != $field) {
                $field = $key . ',' . $field;
            }

            if (is_string($field)) {
                $field = array_map('trim', explode(',', $field));
            }

            $query->field($field);

            $mongoQuery = $this->builder->select($query);
            // 执行查询操作
            $readPreference = isset($options['readPreference']) ? $options['readPreference'] : null;
            $cursor         = $this->query($options['table'], $mongoQuery, $readPreference, true, ['root' => 'array']);
            $resultSet      = $cursor->toArray();

            if ($resultSet) {
                $fields = array_keys(get_object_vars($resultSet[0]));
                $count  = count($fields);
                $key1   = array_shift($fields);
                $key2   = $fields ? array_shift($fields) : '';
                $key    = $key ?: $key1;

                foreach ($resultSet as $val) {
                    $val = (array) $val;
                    if ($this->getConfig('pk_convert_id')) {
                        // 转换ObjectID 字段
                        $val['id'] = $val['_id']->__toString();
                        unset($val['_id']);
                    }
                    $name = $val[$key];
                    if ($name instanceof ObjectID) {
                        $name = $name->__toString();
                    }
                    if (2 == $count) {
                        $result[$name] = $val[$key2];
                    } elseif (1 == $count) {
                        $result[$name] = $val[$key1];
                    } else {
                        $result[$name] = $val;
                    }
                }
            } else {
                $result = [];
            }

            if (isset($cache) && isset($guid)) {
                // 缓存数据
                $this->cacheData($guid, $result, $cache);
            }
        }

        return $result;
    }

    /**
     * 执行command
     * @access public
     * @param Query                 $query      查询对象
     * @param string|array|object   $command 指令
     * @param mixed                 $extra 额外参数
     * @param string                $db 数据库名
     * @return array
     */
    public function cmd(Query $query, $command, $extra = null, $db = null)
    {
        if (is_array($command) || is_object($command)) {
            if ($this->getConfig('debug')) {
                $this->log('cmd', 'cmd', $command);
            }

            // 直接创建Command对象
            $command = new Command($command);
        } else {
            // 调用Builder封装的Command对象
            $command = $this->builder->$command($query, $extra);
        }

        return $this->command($command, $db);
    }

    /**
     * 数据库连接参数解析
     * @access private
     * @param mixed $config
     * @return array
     */
    private static function parseConfig($config)
    {
        if (empty($config)) {
            $config = Container::get('config')->pull('database');
        } elseif (is_string($config) && false === strpos($config, '/')) {
            // 支持读取配置参数
            $config = Container::get('config')->get('database.' . $config);
        }

        if (is_string($config)) {
            return self::parseDsnConfig($config);
        } else {
            return $config;
        }
    }

    /**
     * DSN解析
     * 格式： mysql://username:passwd@localhost:3306/DbName?param1=val1&param2=val2#utf8
     * @access private
     * @param string $dsnStr
     * @return array
     */
    private static function parseDsnConfig($dsnStr)
    {
        $info = parse_url($dsnStr);

        if (!$info) {
            return [];
        }

        $dsn = [
            'type'     => $info['scheme'],
            'username' => isset($info['user']) ? $info['user'] : '',
            'password' => isset($info['pass']) ? $info['pass'] : '',
            'hostname' => isset($info['host']) ? $info['host'] : '',
            'hostport' => isset($info['port']) ? $info['port'] : '',
            'database' => !empty($info['path']) ? ltrim($info['path'], '/') : '',
            'charset'  => isset($info['fragment']) ? $info['fragment'] : 'utf8',
        ];

        if (isset($info['query'])) {
            parse_str($info['query'], $dsn['params']);
        } else {
            $dsn['params'] = [];
        }

        return $dsn;
    }

    /**
     * 获取数据表的主键
     * @access public
     * @param string $tableName 数据表名
     * @return string|array
     */
    public function getPk($tableName)
    {
        return $this->getTableInfo($tableName, 'pk');
    }

    // 获取当前数据表字段信息
    public function getTableFields($tableName)
    {
        return $this->getTableInfo($tableName, 'fields');
    }

    // 获取当前数据表字段类型
    public function getFieldsType($tableName)
    {
        return $this->getTableInfo($tableName, 'type');
    }

    /**
     * 启动事务
     * @access public
     * @return void
     * @throws \PDOException
     * @throws \Exception
     */
    public function startTrans()
    {}

    /**
     * 用于非自动提交状态下面的查询提交
     * @access public
     * @return void
     * @throws PDOException
     */
    public function commit()
    {}

    /**
     * 事务回滚
     * @access public
     * @return void
     * @throws PDOException
     */
    public function rollback()
    {}

    /**
     * 析构方法
     * @access public
     */
    public function __destruct()
    {
        // 释放查询
        $this->free();

        // 关闭连接
        $this->close();
    }
}
