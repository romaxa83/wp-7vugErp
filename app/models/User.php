<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use developeruz\db_rbac\interfaces\UserRbacInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property string $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface, UserRbacInterface
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['store_id' ,'checkManagerRole', 'skipOnEmpty' => false],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE]],
            [['username', 'password', 'email', 'role'], 'required'],
            [['email'],'email'],
            [['created_at', 'updated_at', 'status'], 'integer'],
            [['username', 'password', 'password_reset_token', 'email', 'role'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
            ['auth_key', 'default', 'value' => Yii::$app->security->generateRandomString()],
            [['store_id','role'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Имя пользователя',
            'role' => 'Роль',
            'store_id' => 'Магазин',
            'auth_key' => 'Auth Key',
            'password' => 'Пароль',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'E-mail',
            'status' => 'Статус',
            'created_at' => 'Добавлен',
            'updated_at' => 'Updated At',
        ];
    }
    
    public function checkManagerRole(){
        if($this->role == 'manager'){
            if(empty($this->store_id)){
                $this->addError('store_id','Менеджер должен быть привязан к магазину');
            }
        }else{
            if(!empty($this->store_id)){
                $this->addError('store_id','Только менеджер может быть привязан к магазину');
            }
        }
    }
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }
 
    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }
    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }
    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }
    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }
    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * getPassword
     */
    public function getPassword()
    {
        return $this->password;
    }
    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * getUserName
     */
    public function getUserName()
    {
       return $this->username;
    }

    /**
     * Переопределение beforeSave для сохранения password_hash при новой записи или изменения пароля
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert))
        {
            if ($this->isAttributeChanged('password') || $this->isNewRecord){
                $this->setPassword($this->password);
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Переопределение afterSave для установки доступов новому или измененному пользователю
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if(!$insert && isset($changedAttributes['role'])) {
            Yii::$app->authManager->revokeAll($this->id);
        }
        if($insert || isset($changedAttributes['role'])) {
            $new_role = Yii::$app->authManager->getRole($this->role);
            Yii::$app->authManager->assign($new_role, $this->id);
        }
    }

    public function isRoleManager()
    {
        return $this->role == 'manager';
    }

    /**
     * Переопределение afterDelete для сброса доступов
     */
    public function afterDelete()
    {
        parent::afterDelete();
        Yii::$app->authManager->revokeAll($this->id);
    }
}
