<?php
/**
 * @Desc:用户操作日志
 * @author: hbh
 * @Time: 2020/4/9   17:05
 */
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class LogUserOperate extends Model
{
    protected $connection='mysql_log';

    protected $table = 'user_operate';
}
