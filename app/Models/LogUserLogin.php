<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $uid
 * @property string $username
 * @property boolean $status
 * @property string $ip
 * @property int $addtime
 * @property string $last_update_time
 */
class LogUserLogin extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_login';

    /**
     * @var array
     */
    protected $fillable = ['uid', 'username', 'status', 'ip', 'addtime', 'last_update_time'];

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'mysql_log';
    //取消时间戳
    public $timestamps = false;

}
