<?php
/**
 * mysql.class操作封装
 * @author michael
 */
class Mysql {
    private static $instance = null;
    public static function getInstance($reLoad = false) {
        if (null === self::$instance || $reLoad == true) {
            self::$instance = new self ();
        }
        return self::$instance;
    }
    protected $conn;
    public function __construct() {
        $this->conn = mysql_connect ( DB_HOST, DB_USER, DB_PASS );
        if ($this->conn) {
            mysql_select_db ( DB_NAME );
            mysql_query ( "set names utf8" );
        } else
            exit ( 'db connect fail!' );
    }
    //常规操作
    public function query($sql, $oneRow = false) {
        Logger::w( LL_EVENT, "execute:{$sql}", "event_sqlExec_" . date ( "Ymd" ) );
        $op = strtolower ( substr ( $sql, 0, 6 ) );
        if (! in_array ( $op, array ('select', 'insert', 'update' ) ))
            exit ( '不支持的SQL语句--' . $sql );
        $re = @mysql_query ( $sql );
        if (! $re) {
            Logger::w ( LL_ERROR, "execErr:" . mysql_error () . ";execute:{$sql}" );
            return false;
        }
        switch ($op) {
            case 'select' :
                $data = array ();
                while ( $row = mysql_fetch_array ( $re, MYSQL_ASSOC ) ) {
                    if ($oneRow) {
                        $data = $row;
                        break;
                    }
                    $data [] = $row;
                }
                return $data;
                break;
            case 'insert' :
                $insertId = mysql_insert_id($this->conn);
            	if($insertId)return $insertId;
            	else return $re;
                break;
            case 'update' :
                return $re;
                break;
            case 'delete' :
                return $re;
                break;
        }
    }
    //添加
    public function insert($table,$data) {
        if (! is_array ( $data ))
            return false;
        $keys = array_keys ( $data );
        $values = array_values ( $data );
        $sql = "insert into {$table}(" . implode ( ",", $keys ) . ") values('" . implode ( "','", $values ) . "')";
        return $this->query ( $sql );
    }
    /**
     * 删除,mixed $arr
     * array('key'=>'value') //删除key=value的记录
     * string $arr //删除条件为where $arr
     */
    public function delete($table,$arr = '') {
        if (! $arr)
            return false;
        if (is_array ( $arr )) {
            foreach ( $arr as $k => $v )
                $wh [] = "{$k}='{$v}'";
            $wh = implode ( " and ", $wh );
        } else
            $wh = $arr;
        $sql = "delete from {$table} where {$wh}";
        return $this->query ( $sql );
    }
    public function update($table,$data, $wh = '') {
        if (! is_array ( $data ))
            return false;
        if ($wh)
            $whs [] = $wh;
        foreach ( $data as $k => $v )
            $values [] = "{$k}='$v'";
        $sql = "update {$table} set " . implode ( ",", $values );
        if (isset ( $whs ))
            $sql .= " where " . implode ( " and ", $whs );
        return $this->query ( $sql );
    }
    public function getRow($table,$cols = '*', $wh = '', $order = '') {
        $sql = "select {$cols} from {$table}";
        if ($wh)
            $sql .= " where {$wh}";
        if ($order)
            $sql .= " order by {$order}";
        $re = $this->query ( $sql, true );
        return $re;
    }
    public function getAll($table,$cols = '*', $wh = '', $order = '', $limit = '') {
        $sql = "select {$cols} from {$table}";
        if ($wh)
            $sql .= " where {$wh}";
        if ($order)
            $sql .= " order by {$order}";
        if ($limit)
            $sql .= " limit {$limit}";
        return $this->query ( $sql );
    }
    public function getCount($table,$wh = '') {
        $sql = "select count(*) as nums from {$table}";
        if ($wh)
            $sql .= " where {$wh}";
        $re = $this->query ( $sql, true );
        return $re ['nums'];
    }
    public function getLastInsertId() {
        return mysql_insert_id ( $this->conn );
    }
    /**
     * 转义字符串,使能正常插入数据库
     */
    public function escape($str) {
        return mysql_escape_string ( $str );
    }
    /**
     * 返回上一SQL操作产生的错误
     */
    public function getLastError() {
        return mysql_error ( $this->conn );
    }

}
?>
