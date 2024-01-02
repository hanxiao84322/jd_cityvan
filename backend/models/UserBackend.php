<?php

namespace backend\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user_backend".
 *
 * @property int $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $email
 * @property string $name
 * @property string $phone
 * @property string $institution_id
 * @property int $type
 * @property string $warehouse_code_list
 * @property string $logistic_id_list
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 */
class UserBackend extends \yii\db\ActiveRecord implements IdentityInterface
{
    public $institution_name;
    public $password;

    const TYPE_SYSTEM = 1;
    const TYPE_CUSTOMER_SERVICE = 2;
    const TYPE_LOGISTIC_CUSTOMER_SERVICE = 3;
    const TYPE_FINANCE = 4;

    public static  $typeList = [
        self::TYPE_SYSTEM => '系统管理人员',
        self::TYPE_CUSTOMER_SERVICE => '系统客服',
        self::TYPE_LOGISTIC_CUSTOMER_SERVICE => '快递公司客服',
        self::TYPE_FINANCE => '财务人员',
    ];
    public static  $staffTypeList = [
        self::TYPE_CUSTOMER_SERVICE => '系统客服',
        self::TYPE_LOGISTIC_CUSTOMER_SERVICE => '快递公司客服',
    ];

    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 2;

    public static  $statusList = [
        self::STATUS_ENABLE => '启用',
        self::STATUS_DISABLE => '禁用',
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_backend';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // 对username的值进行两边去空格过滤
            ['username', 'filter', 'filter' => 'trim'],
            // required表示必须的，也就是说表单提交过来的值必须要有, message 是username不满足required规则时给的提示消息
            ['username', 'required', 'message' => '用户名不可以为空'],
            // unique表示唯一性，targetClass表示的数据模型 这里就是说UserBackend模型对应的数据表字段username必须唯一
            ['username', 'unique', 'targetClass' => '\backend\models\UserBackend', 'message' => '用户名已存在.'],
            // string 字符串，这里我们限定的意思就是username至少包含2个字符，最多255个字符
            ['username', 'string', 'min' => 2, 'max' => 255],
            // 下面的规则基本上都同上，不解释了
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required', 'message' => '邮箱不可以为空'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['name', 'string', 'max' => 255],
            ['phone', 'match', 'pattern' => '/^\d{11}$/', 'message' => '请输入有效的手机号码。'],
            // default 默认在没有数据的时候才会进行赋值
            [['created_at', 'updated_at'], 'default', 'value' => date('Y-m-d H:i:s')],
            ['type', 'integer'],
            ['password', 'string'],
            ['type', 'required', 'message' => '请选择类型'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户名',
            'auth_key' => '登录秘钥',
            'password_hash' => '密码',
            'email' => '邮箱',
            'name' => '姓名',
            'phone' => '联系电话',
            'institution_id' => '组织机构',
            'institution_name' => '组织机构名称',
            'type' => '类型',
            'warehouse_code_list' => '仓库编码',
            'logistic_id_list' => '快递公司 ID',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
    /**
     * @inheritdoc
     * 根据user_backend表的主键（id）获取用户
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * @inheritdoc
     * 根据access_token获取用户，我们暂时先不实现，我们在文章 http://www.manks.top/yii2-restful-api.html 有过实现，如果你感兴趣的话可以先看看
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * @inheritdoc
     * 用以标识 Yii::$app->user->id 的返回值
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     * 获取auth_key
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     * 验证auth_key
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * 为model的password_hash字段生成密码的hash值
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }


    /**
     * 生成 "remember me" 认证key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * 根据user_backend表的username获取用户
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }
    /**
     * 验证密码的准确性
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }
    
    /**
     * Signs user up.
     *
     * @return true|false 添加成功或者添加失败
     */
    public function updatePassword($id)
    {
        $user = self::findOne($id);
        $user->setPassword($this->password);
        $user->generateAuthKey();
        return $user->save(false);
    }

    public static function getTypeName($type)
    {
        return isset(self::$typeList[$type]) ? self::$typeList[$type] : '无';

    }
    public static function getStatusName($status)
    {
        return isset(self::$statusList[$status]) ? self::$statusList[$status] : '无';
    }

}
