<?php

namespace app\models;

use Yii;

use app\assets\PlaceholderAsset;
use app\base\Cache;
use app\behaviors\JsonBehavior;
use app\widgets\Flashes;
use yii\data\ActiveDataProvider;
use app\modules\rbac\models\AuthItem;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property int $id
 * @property string|null $firstname
 * @property string|null $patronym
 * @property string|null $lastname
 * @property string|null $fullname Строка из полных фамилии, имени, отчества
 * @property string|null $fio Строка из фамилии и инициалов
 * @property string|null $email
 * @property string|null $phone_prefix Префикс телефона (код страны + код города (если есть)
 * @property string|null $phone Префикс телефона (Номер телефона без кода страны и кода города
 * @property string|null $comments Любые комментарии
 * @property float|null $salary Оклад
 * @property int|null $job_id Должность
 * @property int|null $superior_id Непосредственный руководитель
 * @property string|null $hire_date Дата найма в качестве сотрудника (или внешнего контрагента)
 * @property string|null $termination_date Дата прекращения отношений с сотрудником (или внешним контрагентом)
 * @property int $user_status_id Статус именно как пользователя (не человека или сотрудника)
 * @property string|null $password_hash
 * @property string|null $auth_key
 * @property int $is_deleted
 * @property string|null $created
 * @property int|null $creator_id
 * @property string|null $modified
 * @property int|null $modifier_id
 * @property integer $state_id
 * @property boolean $have_account
 *
 * @property AutoListGroup[] $autoListGroups
 * @property AutoListGroup[] $autoListGroups0
 * @property AutoListItem[] $autoListItems
 * @property AutoListItem[] $autoListItems0
 * @property AutoList[] $autoLists
 * @property AutoList[] $autoLists0
 * @property CashFlowTransactionCategory[] $cashFlowTransactionCategories
 * @property CashFlowTransactionCategory[] $cashFlowTransactionCategories0
 * @property User $creator
 * @property Division[] $divisions
 * @property Division[] $divisions0
 * @property EquipmentCategory[] $equipmentCategories
 * @property EquipmentCategory[] $equipmentCategories0
 * @property EquipmentUnit[] $equipmentUnits
 * @property EquipmentUnit[] $equipmentUnits0
 * @property ForTaskPerformerFile[] $forTaskPerformerFiles
 * @property ForTaskPerformerFile[] $forTaskPerformerFiles0
 * @property ForTaskPerformerLink[] $forTaskPerformerLinks
 * @property ForTaskPerformerLink[] $forTaskPerformerLinks0
 * @property FromTaskPerformerFile[] $fromTaskPerformerFiles
 * @property FromTaskPerformerFile[] $fromTaskPerformerFiles0
 * @property FromTaskPerformerLink[] $fromTaskPerformerLinks
 * @property FromTaskPerformerLink[] $fromTaskPerformerLinks0
 * @property Job $job
 * @property Job[] $jobs
 * @property Job[] $jobs0
 * @property MeasureUnit[] $measureUnits
 * @property MeasureUnit[] $measureUnits0
 * @property User $modifier
 * @property MroWorkType[] $mroWorkTypes
 * @property MroWorkType[] $mroWorkTypes0
 * @property PayrollTransactionCategory[] $payrollTransactionCategories
 * @property PayrollTransactionCategory[] $payrollTransactionCategories0
 * @property ProductCategory[] $productCategories
 * @property ProductCategory[] $productCategories0
 * @property ProductType[] $productTypes
 * @property ProductType[] $productTypes0
 * @property ProductionStage[] $productionStages
 * @property ProductionStage[] $productionStages0
 * @property Product[] $products
 * @property Product[] $products0
 * @property StatusScope[] $statusScopes
 * @property StatusScope[] $statusScopes0
 * @property Status[] $statuses
 * @property Status[] $statuses0
 * @property User $superior
 * @property TaskWPerformerLink[] $taskWPerformerLinks
 * @property TaskWPerformerLink[] $taskWPerformerLinks0
 * @property TaskWPerformerLink[] $taskWPerformerLinks1
 * @property Task[] $tasks
 * @property Task[] $tasks0
 * @property Task[] $tasks1
 * @property Task[] $tasks2
 * @property Task[] $tasks3
 * @property UserStatus $userStatus
 * @property User[] $users
 * @property User[] $users0
 * @property User[] $users1
 * @property Warehouse[] $warehouses
 * @property Warehouse[] $warehouses0
 * @property AuthItem|null $role
 * @property string $state
 */
class User extends BaseActiveRecord
{
    const STATE_WORKING = 1;
    const STATE_TERMINATED = 2;
    const STATE_NAMES = [
        self::STATE_WORKING => 'Работает',
        self::STATE_TERMINATED => 'Уволен'
    ];

    const SCENARIO_ACCOUNT = 'account';

    private $_role = null;

	public $search = '';

    // holds the password confirmation word
    public $password_repeat = '';

    // holds new password
    public $password_new = '';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['comments'], 'string'],
            [['salary'], 'number'],
            [['job_id', 'superior_id', 'user_status_id', 'is_deleted', 'creator_id', 'modifier_id', 'state_id'], 'integer'],
            [['hire_date', 'termination_date', 'created', 'modified'], 'safe'],
            [['firstname', 'patronym', 'lastname'], 'string', 'max' => 100],
            [['fullname'], 'string', 'max' => 302],
            [['fio', 'email'], 'string', 'max' => 255],
            [['phone_prefix'], 'string', 'max' => 9],
            [['phone'], 'string', 'max' => 31],
            [['password_hash'], 'string', 'max' => 60],
            [['auth_key'], 'string', 'max' => 32],
            [['roleName'], 'string'],
            [['have_account'], 'boolean'],

			[['email'], 'email', 'on' => [static::SCENARIO_INSERT, static::SCENARIO_UPDATE]],
			[['search'], 'string', 'on' => [static::SCENARIO_SEARCH]],

//			[['password_new', 'password_repeat'], 'required', 'on' => [static::SCENARIO_ACCOUNT]],
            [['password_new', 'password_repeat'], 'string', 'min' => 6, 'max' => 40, 'on' => [static::SCENARIO_ACCOUNT]],
			[['password_new'], 'compare', 'compareAttribute' => 'password_repeat', 'on' => [static::SCENARIO_ACCOUNT]],

            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['creator_id' => 'id']],
            [['job_id'], 'exist', 'skipOnError' => true, 'targetClass' => Job::class, 'targetAttribute' => ['job_id' => 'id']],
            [['modifier_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['modifier_id' => 'id']],
            [['superior_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['superior_id' => 'id']],
            [['user_status_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserStatus::class, 'targetAttribute' => ['user_status_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'firstname' => Yii::t('app', 'Имя'),
            'patronym' => Yii::t('app', 'Отчество'),
            'lastname' => Yii::t('app', 'Фамилия'),
            'fullname' => Yii::t('app', 'Полное имя'),
            'fio' => Yii::t('app', 'ФИО'),
            'email' => Yii::t('app', 'Почта'),
            'phone_prefix' => Yii::t('app', 'Phone Prefix'),
            'phone' => Yii::t('app', 'Телефон'),
            'comments' => Yii::t('app', 'Комментарий'),
            'salary' => Yii::t('app', 'Оклад'),
            'job_id' => Yii::t('app', 'Должность'),
            'superior_id' => Yii::t('app', 'Руководитель'),
            'hire_date' => Yii::t('app', 'Дата приема'),
            'termination_date' => Yii::t('app', 'Дата увольнения'),
            'user_status_id' => Yii::t('app', 'Статус'),
            'password_hash' => Yii::t('app', 'Password Hash'),
            'auth_key' => Yii::t('app', 'Auth Key'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'created' => Yii::t('app', 'Created'),
            'creator_id' => Yii::t('app', 'Creator ID'),
            'modified' => Yii::t('app', 'Modified'),
            'modifier_id' => Yii::t('app', 'Modifier ID'),
            'state_id' => Yii::t('app', 'Статус'),
            'state' => Yii::t('app', 'Статус'),
            'have_account' => Yii::t('app', 'Аккаунт'),

			'password_new' => Yii::t('app', 'Новый пароль'),
            'password_repeat' => Yii::t('app', 'Повтор пароля'),
            'search' => Yii::t('app', 'Поиск'),
            'roleName' => Yii::t('app', 'Роль'),
            'experience' => Yii::t('app', 'Стаж'),
        ];
    }



    /**
     * Gets query for [[AutoListGroups]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAutoListGroups()
    {
        return $this->hasMany(AutoListGroup::class, ['creator_id' => 'id'])->inverseOf('creator');
    }

    /**
     * Gets query for [[AutoListGroups0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAutoListGroups0()
    {
        return $this->hasMany(AutoListGroup::class, ['modifier_id' => 'id'])->inverseOf('modifier');
    }

    /**
     * Gets query for [[AutoListItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAutoListItems()
    {
        return $this->hasMany(AutoListItem::class, ['creator_id' => 'id'])->inverseOf('creator');
    }

    /**
     * Gets query for [[AutoListItems0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAutoListItems0()
    {
        return $this->hasMany(AutoListItem::class, ['modifier_id' => 'id'])->inverseOf('modifier');
    }

    /**
     * Gets query for [[AutoLists]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAutoLists()
    {
        return $this->hasMany(AutoList::class, ['creator_id' => 'id'])->inverseOf('creator');
    }

    /**
     * Gets query for [[AutoLists0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAutoLists0()
    {
        return $this->hasMany(AutoList::class, ['modifier_id' => 'id'])->inverseOf('modifier');
    }

    /**
     * Gets query for [[CashFlowTransactionCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCashFlowTransactionCategories()
    {
        return $this->hasMany(CashFlowTransactionCategory::class, ['creator_id' => 'id'])->inverseOf('creator');
    }

    /**
     * Gets query for [[CashFlowTransactionCategories0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCashFlowTransactionCategories0()
    {
        return $this->hasMany(CashFlowTransactionCategory::class, ['modifier_id' => 'id'])->inverseOf('modifier');
    }

    /**
     * Gets query for [[Creator]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'creator_id'])->inverseOf('users');
    }

    /**
     * Gets query for [[Divisions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDivisions()
    {
        return $this->hasMany(Division::class, ['creator_id' => 'id'])->inverseOf('creator');
    }

    /**
     * Gets query for [[Divisions0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDivisions0()
    {
        return $this->hasMany(Division::class, ['modifier_id' => 'id'])->inverseOf('modifier');
    }

    /**
     * Gets query for [[EquipmentCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEquipmentCategories()
    {
        return $this->hasMany(EquipmentCategory::class, ['creator_id' => 'id'])->inverseOf('creator');
    }

    /**
     * Gets query for [[EquipmentCategories0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEquipmentCategories0()
    {
        return $this->hasMany(EquipmentCategory::class, ['modifier_id' => 'id'])->inverseOf('modifier');
    }

    /**
     * Gets query for [[EquipmentUnits]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEquipmentUnits()
    {
        return $this->hasMany(EquipmentUnit::class, ['creator_id' => 'id'])->inverseOf('creator');
    }

    /**
     * Gets query for [[EquipmentUnits0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEquipmentUnits0()
    {
        return $this->hasMany(EquipmentUnit::class, ['modifier_id' => 'id'])->inverseOf('modifier');
    }

    /**
     * Gets query for [[ForTaskPerformerFiles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getForTaskPerformerFiles()
    {
        return $this->hasMany(ForTaskPerformerFile::class, ['creator_id' => 'id'])->inverseOf('creator');
    }

    /**
     * Gets query for [[ForTaskPerformerFiles0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getForTaskPerformerFiles0()
    {
        return $this->hasMany(ForTaskPerformerFile::class, ['modifier_id' => 'id'])->inverseOf('modifier');
    }

    /**
     * Gets query for [[ForTaskPerformerLinks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getForTaskPerformerLinks()
    {
        return $this->hasMany(ForTaskPerformerLink::class, ['creator_id' => 'id'])->inverseOf('creator');
    }

    /**
     * Gets query for [[ForTaskPerformerLinks0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getForTaskPerformerLinks0()
    {
        return $this->hasMany(ForTaskPerformerLink::class, ['modifier_id' => 'id'])->inverseOf('modifier');
    }

    /**
     * Gets query for [[FromTaskPerformerFiles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFromTaskPerformerFiles()
    {
        return $this->hasMany(FromTaskPerformerFile::class, ['creator_id' => 'id'])->inverseOf('creator');
    }

    /**
     * Gets query for [[FromTaskPerformerFiles0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFromTaskPerformerFiles0()
    {
        return $this->hasMany(FromTaskPerformerFile::class, ['modifier_id' => 'id'])->inverseOf('modifier');
    }

    /**
     * Gets query for [[FromTaskPerformerLinks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFromTaskPerformerLinks()
    {
        return $this->hasMany(FromTaskPerformerLink::class, ['creator_id' => 'id'])->inverseOf('creator');
    }

    /**
     * Gets query for [[FromTaskPerformerLinks0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFromTaskPerformerLinks0()
    {
        return $this->hasMany(FromTaskPerformerLink::class, ['modifier_id' => 'id'])->inverseOf('modifier');
    }

    /**
     * Gets query for [[Job]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getJob()
    {
        return $this->hasOne(Job::class, ['id' => 'job_id'])->inverseOf('users');
    }

    /**
     * Gets query for [[Jobs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getJobs()
    {
        return $this->hasMany(Job::class, ['creator_id' => 'id'])->inverseOf('creator');
    }

    /**
     * Gets query for [[Jobs0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getJobs0()
    {
        return $this->hasMany(Job::class, ['modifier_id' => 'id'])->inverseOf('modifier');
    }

    /**
     * Gets query for [[MeasureUnits]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMeasureUnits()
    {
        return $this->hasMany(MeasureUnit::class, ['creator_id' => 'id'])->inverseOf('creator');
    }

    /**
     * Gets query for [[MeasureUnits0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMeasureUnits0()
    {
        return $this->hasMany(MeasureUnit::class, ['modifier_id' => 'id'])->inverseOf('modifier');
    }

    /**
     * Gets query for [[Modifier]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getModifier()
    {
        return $this->hasOne(User::class, ['id' => 'modifier_id'])->inverseOf('users0');
    }

    /**
     * Gets query for [[MroWorkTypes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMroWorkTypes()
    {
        return $this->hasMany(MroWorkType::class, ['creator_id' => 'id'])->inverseOf('creator');
    }

    /**
     * Gets query for [[MroWorkTypes0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMroWorkTypes0()
    {
        return $this->hasMany(MroWorkType::class, ['modifier_id' => 'id'])->inverseOf('modifier');
    }

    /**
     * Gets query for [[PayrollTransactionCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPayrollTransactionCategories()
    {
        return $this->hasMany(PayrollTransactionCategory::class, ['creator_id' => 'id'])->inverseOf('creator');
    }

    /**
     * Gets query for [[PayrollTransactionCategories0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPayrollTransactionCategories0()
    {
        return $this->hasMany(PayrollTransactionCategory::class, ['modifier_id' => 'id'])->inverseOf('modifier');
    }

    /**
     * Gets query for [[ProductCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductCategories()
    {
        return $this->hasMany(ProductCategory::class, ['creator_id' => 'id'])->inverseOf('creator');
    }

    /**
     * Gets query for [[ProductCategories0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductCategories0()
    {
        return $this->hasMany(ProductCategory::class, ['modifier_id' => 'id'])->inverseOf('modifier');
    }

    /**
     * Gets query for [[ProductTypes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductTypes()
    {
        return $this->hasMany(ProductType::class, ['creator_id' => 'id'])->inverseOf('creator');
    }

    /**
     * Gets query for [[ProductTypes0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductTypes0()
    {
        return $this->hasMany(ProductType::class, ['modifier_id' => 'id'])->inverseOf('modifier');
    }

    /**
     * Gets query for [[ProductionStages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductionStages()
    {
        return $this->hasMany(ProductionStage::class, ['creator_id' => 'id'])->inverseOf('creator');
    }

    /**
     * Gets query for [[ProductionStages0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductionStages0()
    {
        return $this->hasMany(ProductionStage::class, ['modifier_id' => 'id'])->inverseOf('modifier');
    }

    /**
     * Gets query for [[Products]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::class, ['creator_id' => 'id'])->inverseOf('creator');
    }

    /**
     * Gets query for [[Products0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProducts0()
    {
        return $this->hasMany(Product::class, ['modifier_id' => 'id'])->inverseOf('modifier');
    }

    /**
     * Gets query for [[StatusScopes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatusScopes()
    {
        return $this->hasMany(StatusScope::class, ['creator_id' => 'id'])->inverseOf('creator');
    }

    /**
     * Gets query for [[StatusScopes0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatusScopes0()
    {
        return $this->hasMany(StatusScope::class, ['modifier_id' => 'id'])->inverseOf('modifier');
    }

    /**
     * Gets query for [[Statuses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatuses()
    {
        return $this->hasMany(Status::class, ['creator_id' => 'id'])->inverseOf('creator');
    }

    /**
     * Gets query for [[Statuses0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatuses0()
    {
        return $this->hasMany(Status::class, ['modifier_id' => 'id'])->inverseOf('modifier');
    }

    /**
     * Gets query for [[Superior]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSuperior()
    {
        return $this->hasOne(User::class, ['id' => 'superior_id'])->inverseOf('users1');
    }

    /**
     * Gets query for [[TaskWPerformerLinks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskWPerformerLinks()
    {
        return $this->hasMany(TaskWPerformerLink::class, ['creator_id' => 'id'])->inverseOf('creator');
    }

    /**
     * Gets query for [[TaskWPerformerLinks0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskWPerformerLinks0()
    {
        return $this->hasMany(TaskWPerformerLink::class, ['modifier_id' => 'id'])->inverseOf('modifier');
    }

    /**
     * Gets query for [[TaskWPerformerLinks1]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskWPerformerLinks1()
    {
        return $this->hasMany(TaskWPerformerLink::class, ['performer_id' => 'id'])->inverseOf('performer');
    }

    /**
     * Gets query for [[Tasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Task::class, ['creator_id' => 'id'])->inverseOf('creator');
    }

    /**
     * Gets query for [[Tasks0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasks0()
    {
        return $this->hasMany(Task::class, ['modifier_id' => 'id'])->inverseOf('modifier');
    }

    /**
     * Gets query for [[Tasks1]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasks1()
    {
        return $this->hasMany(Task::class, ['responsible_id' => 'id'])->inverseOf('responsible');
    }

    /**
     * Gets query for [[Tasks2]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasks2()
    {
        return $this->hasMany(Task::class, ['setter_id' => 'id'])->inverseOf('setter');
    }

    /**
     * Gets query for [[Tasks3]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasks3()
    {
        return $this->hasMany(Task::class, ['id' => 'task_id'])->via('taskWPerformerLinks1');
    }

    /**
     * Gets query for [[UserStatus]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserStatus()
    {
        return $this->hasOne(UserStatus::class, ['id' => 'user_status_id'])->inverseOf('users');
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['creator_id' => 'id'])->inverseOf('creator');
    }

    /**
     * Gets query for [[Users0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers0()
    {
        return $this->hasMany(User::class, ['modifier_id' => 'id'])->inverseOf('modifier');
    }

    /**
     * Gets query for [[Users1]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers1()
    {
        return $this->hasMany(User::class, ['superior_id' => 'id'])->inverseOf('superior');
    }

    /**
     * Gets query for [[Warehouses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWarehouses()
    {
        return $this->hasMany(Warehouse::class, ['creator_id' => 'id'])->inverseOf('creator');
    }

    /**
     * Gets query for [[Warehouses0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWarehouses0()
    {
        return $this->hasMany(Warehouse::class, ['modifier_id' => 'id'])->inverseOf('modifier');
    }

    public function getRole()
    {
        if($this->_role == null) {
            $auth = Yii::$app->authManager;
            $this->_role = $auth->getRole(array_key_first($auth->getAssignments($this->id)));
        }
        return $this->_role;
    }

    public function getRoleTitle()
    {
        if($this->_role) {
            return AuthItem::findOne(['name' => $this->role->name])->title ?? null;
        }
        return null;
    }

    public function getRoleName()
    {
        return $this->role->name ?? null;
    }

    public function setRoleName($value)
    {
        if(!($this->role == null && $value == null)) {
            $auth = Yii::$app->authManager;
            if($role = $auth->getRole($value)) {
                $this->_role = $role;
            }
        }
    }

    private function saveRoleName()
    {
        $auth = Yii::$app->authManager;
        $previous = array_key_first($auth->getAssignments($this->id));
        if($this->_role == null) {
            $auth->revokeAll($this->id);
        } else {
            if($this->_role->name !== $previous) {
                $auth->revokeAll($this->id);
                $auth->assign($this->_role, $this->id);
            }
        }
    }

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// Стандартные методы User Autentication (из шаблона Yii2 Advanced)
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// End:
	// Стандартные методы User Autentication (из шаблона Yii2 Advanced)
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// Custom Methods
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if (!$this->checkEmail($this->isNewRecord ? 0 : $this->id)) {
                Flashes::setError('Пользователь с таким адресом почты уже существует!');
                return false;
            }

            if (!empty($this->password_new) && !empty($this->password_repeat) && $this->password_new == $this->password_repeat) {
                $this->setPassword($this->password_new);
                $this->auth_key = $this->newAuthKey($this->isNewRecord ? 0 : $this->id); // Cookie-based login
            }

            // Full name
            $tmp = [];
            $tmp[] = trim($this->lastname);
            if ($this->firstname) $tmp[] = trim($this->firstname);
            if ($this->patronym) $tmp[] = trim($this->patronym);
            $this->fullname = implode(' ', $tmp);

            // FIO
            $tmp = [];
            $tmp[] = trim($this->lastname);
            if ($this->firstname) $tmp[] = mb_substr(trim($this->firstname), 0, 1) . '.';
            if ($this->patronym) $tmp[] = mb_substr(trim($this->patronym), 0, 1) . '.';
            $this->fio = implode(' ', $tmp);

            return true;
        }
        return false;
    }

    protected function checkEmail($exceptId)
    {
        if (empty($this->email)) return true;

        $models = static::find()->where("`is_deleted` = 0 AND `email` = '" . $this->email . "' AND `id` != " . $exceptId)->all();
        return (is_array($models) && sizeof($models) == 0);
    }

    public function getAvatar()
    {
        $bundle = PlaceholderAsset::register(Yii::$app->view);
        return $bundle->baseUrl . '/no-profile-picture1.jpg';
    }

    protected function newAuthKey($exceptId)
    {
        for ($i = 0; $i < 10; $i++) {
            $str = \Yii::$app->security->generateRandomString();
            $models = static::find()->where("`auth_key` = '" . $str . "' AND `id` != " . $exceptId)->all();
            if (!is_array($models) || sizeof($models) == 0) return $str;
        }
        return \Yii::$app->security->generateRandomString();
    }

    /**
     * Generates auth key and sets it to the model
     */
    public function setupAuthKey()
    {
        $this->auth_key = $this->newAuthKey($this->isNewRecord ? 0 : $this->id);
    }

    public function __toString()
    {
        return $this->fio;
    }

    /**
     * @inheritDoc
     */
    public function actionAllowed($name)
    {
        // if (in_array($name, ['edit', 'delete'])) {
            // $userType = Yii::$app->user->identity->type;
            // if ($userType == static::USER_TYPE_ADMIN) return true;
            // if ($this->type == static::USER_TYPE_ADMIN) return false;
            // if ($userType == static::USER_TYPE_OWNER) {
                // return in_array($this->type, [static::USER_TYPE_SUPERVISOR]);
            // }

            // return false;
        // }

        // if ($name == 'sign-in') return (Yii::$app->user->id == 1);
        // if ($this->id == 1 && in_array($name, ['delete'])) return false;
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => JsonBehavior::class,
                // 'fields' => [
                    // 'publishing' => true,
                // ]
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function search($params, $recsOnPage = 20)
    {
        if(key_exists('per-page', $params)) {
            $recsOnPage = (int)$params['per-page'];
        }
        $dataProvider = parent::search($params, $recsOnPage);

        $query = $dataProvider->query;

        $jobDivisionId = null;
        if (isset($params['job_division_id'])){
            $query->joinWith('job');
            $jobDivisionId = $params['job_division_id'];
        }

        $this->search = trim($this->search);
        if ($this->search) {
            $or = [
                'or',
                ['like', 'firstname', $this->search],
                ['like', 'lastname', $this->search],
                ['like', 'patronym', $this->search],
                ['like', 'phone', $this->search],
                ['like', 'email', $this->search],
            ];
            if (is_numeric($this->search)) {
                $or[] = ['id' => $this->search];
            }
            $query->andWhere($or);
        }
        $query->andWhere(['!=', 'id', 1]);
        if ($this->job_id) {
            $query->andWhere(['job_id' => $this->job_id]);
        }
        if ($jobDivisionId){
            $query->andWhere(['job.division_id' => $jobDivisionId]);
        }
        // if ($this->type) {
            // $query->andWhere(['type' => $this->type]);
        // }

        // Return data provider
        return $dataProvider;
    }

    public function search0($params, $recsOnPage = 20): ActiveDataProvider
    {
        $this->load($params);
        $query = self::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => $this->sortingEnabled ? ['defaultOrder' => $this->defaultOrder] : false,
            'pagination' => $recsOnPage > 0 ? [
                'defaultPageSize' => $recsOnPage,
            ] : false,
        ]);

        $query = $dataProvider->query;

        if (!empty($params['User']['id'])){
            $query->where(['id' => $params['User']['id']]);
            return $dataProvider;
        }

        $jobDivisionId = null;
        if (isset($params['job_division_id'])){
            $query->joinWith('job');
            $jobDivisionId = $params['job_division_id'];
        }

        $this->search = trim($this->search);
        if ($this->search) {
            $or = [
                'or',
                ['like', 'firstname', $this->search],
                ['like', 'lastname', $this->search],
                ['like', 'patronym', $this->search],
                ['like', 'phone', $this->search],
                ['like', 'email', $this->search],
            ];
            if (is_numeric($this->search)) {
                $or[] = ['id' => $this->search];
            }
            $query->andWhere($or);
        }
        $query->andWhere(['!=', 'user.id', 1]);
        if ($this->job_id) {
            $query->andWhere(['job_id' => $this->job_id]);
        }
        if ($jobDivisionId){
            $query->andWhere(['job.division_id' => $jobDivisionId]);
        }
        $query->andWhere(['user.is_deleted' => 0]);
        // if ($this->type) {
        // $query->andWhere(['type' => $this->type]);
        // }

        // Return data provider
        return $dataProvider;
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->saveRoleName();
        Cache::dropCache(static::tableName());
        return parent::afterSave($insert, $changedAttributes);
    }


    public function afterDelete()
    {
        Cache::dropCache(static::tableName());
        return parent::afterDelete();
    }

    public static function getList($full = false)
    {
        return Cache::getOrSetCache(static::tableName(), $full ? 'list_full' : 'list', function () use ($full) {
            $qry = $full ? static::find() : static::findActive();
            $tmp = [];
            foreach ($qry->each(1) as $model) {
                $tmp[$model->id] = (string)$model;
            }
            asort($tmp);
            return $tmp;
        }, 0, true);
    }

    public function getState()
    {
        return static::STATE_NAMES[$this->state_id] ?? '';
    }

    /**
     * @inheritDoc
     *
     * @return UserQuery
     */
    public static function customFind(): UserQuery
    {
        return new UserQuery(get_called_class());
    }

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// End:
	// Custom Methods
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
