-- ivanoa 数据库表

create  database ivanoa;
use ivanoa;

-- user表
drop table if exists user;

create table user(

	id int unsigned auto_increment primary key not null comment '用户编号',
	username varchar(100) not null default '' comment '用户名称',
	password varchar(100) not null default '' comment '用户密码',
	salt varchar(100) not null default '' comment '加密字段',
	nickname varchar(100) not null default '' comment '用户昵称',
	sex tinyint unsigned not null default 0 comment '用户性别 1男 2女',
	name_update_count tinyint unsigned not null default 0 comment '用户名修改次数',
	birthday int unsigned not null default 0 comment '用户生日',
	avatar varchar(255)  not null default '' comment '头像',
	avatar_attachement_id int unsigned not null default 0 comment '头像附件关联id',
	deptid int unsigned not null default 0 comment '部门管理id',
	positid int unsigned not null default 0 comment '职位id',
	offiec_tel varchar(30) not null default '' comment '所在办公室电话',
	mobile varchar(30) not null default '' comment '个人移动电话',
	email varchar(30) not null default '' comment '电子邮件',
	duty varchar(2000) not null default '' comment '员工职责',
	last_login_ip varchar(40) not null default '' comment '最后登陆ip',
	createtime int not null default 0 comment '创建时间',
	updatetime int not null default 0 comment '更新时间',
	status tinyint not null default 0 comment '用户状态 1 代表启用 0 代表禁用',
	is_delete tinyint not null default 0 comment '是否删除记录 1 代表删除 '


) charset utf8 comment="用户表";


-- 事务日程
drop table if exists user_schedule;

create table user_schedule (

	id int unsigned not null auto_increment primary key  comment '事务id',
	user_id int unsigned not null default 0 comment '用户关联id',
	title varchar(255) not null default '' comment '事务标题',
	start_time int unsigned not null default 0 comment '开始时间',
	end_time int unsigned not null default 0 comment '结束时间',
	address varchar(255) not null default '' comment '地点',
	level tinyint not null default 0 comment '事务执行的优先级 1不急  2不急  3一般 4比较急 5很急',
	content text comment '事务内容',
	is_delete tinyint unsigned default 0 ,
	createtime int unsigned not null default 0 comment '创建时间',
	updatetime int unsigned not null default 0 comment '更新时间'

) charset utf8 comment = "事务日程表";

-- 日程附件表
drop table if exists user_schedule_attachement;
create table user_schedule_attachement (
	id int unsigned not null auto_increment primary key comment '主键id',
	sid int unsigned not null default 0 comment '日程id',
	attachement_id int unsigned not null default 0 comment '附件id'
) charset utf8 comment ="日程附件表";

-- 日程参与人员表
drop table if exists user_schedule_participant;
create table user_schedule_participant (
	id int unsigned not null auto_increment primary key comment '主键id',
	sid int unsigned not null default 0 comment '日程id',
	participant int unsigned not null default 0 comment '参与人员id'
) charset utf8 comment = "日程参与人员表";


-- 待办事项
drop table if exists user_todo;

create table user_todo(

	id int unsigned not null auto_increment primary key,
	user_id int unsigned not null default 0 comment '用户关联id',
	title varchar(255) not null default '' comment '事项标题',
	end_time int unsigned not null default 0 comment '结束时间',
	level tinyint not null default 0 comment '事务执行的优先级 1不急  2不急  3一般 4比较急 5很急',
	item_attachement_id int unsigned not null default 0 comment '事项附件id',
	content text comment '事务内容',
	status tinyint unsigned not null default 0 comment '待办事项状态 0 尚未进行 1完成 2 正在进行',
	is_delete tinyint unsigned default 0 ,
	createtime int unsigned not null default 0 comment '创建时间',
	updatetime int unsigned not null default 0 comment '更新时间'

) charset utf8 comment = "待办事项";


-- 用户信息
drop table if exists user_message;

create table user_message (
	id int unsigned not null auto_increment primary key,
	user_id int unsigned not null default 0 comment '用户关联id',
	title varchar(255) not null default 0 comment '消息标题',
	type tinyint unsigned not null default 0 comment '消息来源',
	msg varchar(255) not null default '' comment '留言内容',
	is_read tinyint unsigned  default 0 comment '是否已读。 0 未读 1 已读',
	is_delete tinyint unsigned default 0 ,
	createtime int unsigned not null default 0 comment '创建时间',
	updatetime int unsigned not null default 0 comment '更新时间'

) charset utf8 comment ="用户信息";


-- 登陆失败表
drop table if exists common_fail_logs;

create table common_fail_logs(
	id int unsigned not null auto_increment primary key,
	username varchar(30) not null default '' comment '用户名',
	ip varchar(30) not null default '' comment 'ip地址',
	type tinyint unsigned not null default 0 comment '0 登陆失败',
	count tinyint unsigned not null default 0 comment '登陆失败次数',
	is_delete tinyint not null default 0,
	createtime int unsigned not null default 0 comment '创建时间',
	updatetime int unsigned not null default 0 comment '更新时间'

) charset utf8 comment ="登陆失败信息表";


-- 存储所有的验证码

drop table if exists comment_code_logs;
create table comment_code_logs(

	id int unsigned not null auto_increment primary key,
	user_id int unsigned not null default 0 comment '用户id',
	captach_code varchar(6) not null default '' comment '验证码',
	username varchar(100) not null default '' comment '使用的账号',
	type tinyint unsigned not null default 0 comment '0 登陆',
	send_type tinyint unsigned not null default 0 comment '0 无 1手机 2邮箱',
	status tinyint unsigned not null default 0 comment '0没使用过 1已使用 2过期',
	ip varchar(100) not null default '' comment 'ip地址',
	is_delete tinyint unsigned not null default 0,
	createtime int unsigned not null default 0 comment '创建时间',
	updatetime int unsigned not null default 0 comment '更新时间'

) charset utf8 comment ="验证码一览表";


-- 用户角色关联表
drop table if exists user_role_relation;
create table user_role_relation (
	id int unsigned not null auto_increment primary key,
	user_id int unsigned not null default 0 comment '用户id',
	role_id int unsigned not null default 0 comment '角色id',
	status 	tinyint unsigned not null default 1 comment '记录状态',
	is_delete tinyint unsigned not null default 0 comment '伪删除',
	createtime int unsigned not null default 0 comment '创建时间',
	updatetime int unsigned not null default 0 comment '更新时间'

) charset utf8 comment ="用户角色关联表";

-- 角色表
drop table if exists user_role;
create table user_role(

	id int unsigned not null auto_increment primary key,
	name varchar(100) not null default '' comment '角色名称',
	pid  tinyint unsigned not null default 0 comment '父级id',
	status tinyint unsigned not null default 0 comment '角色状态 0未启用 1启用',
	sort tinyint unsigned not null default 0 comment '排序',
	is_delete tinyint not null default 0,
	remark text,
	createtime int unsigned not null default 0 comment '创建时间',
	updatetime int unsigned not null default 0 comment '更新时间'

) charset utf8 comment="用户角色表";

-- 虚拟数据
insert into user_role values(default,'公司管理员',0,1,10,0,'公司管理员',unix_timestamp(),unix_timestamp());
insert into user_role values(default,'基本权限',0,1,10,0,'基本权限',unix_timestamp(),unix_timestamp());
insert into user_role values(default,'领导',0,1,10,0,'领导',unix_timestamp(),unix_timestamp());

-- 角色权限关联表
drop table if exists user_role_auth;
create table user_role_auth(
	id int unsigned not null auto_increment primary key,
	role_id int unsigned not null default 0 comment '用户角色',
	auth_id int unsigned not null default 0 comment '权限id',
	is_admin  tinyint unsigned not null default 0 comment '是否可以管理 增删改 节点 0 不可以 1 可以',
	is_write tinyint unsigned not null default 1 comment '是否可以访问 改节点 0 不可以 1 可以',
	is_add tinyint unsigned not null default 1 comment '是否可以访问 增节点 0 不可以 1 可以',
	is_del tinyint unsigned not null default 1 comment '是否可以访问 删节点 0 不可以 1 可以',
	is_delete tinyint unsigned not null default 0 comment '伪删除',
	status tinyint unsigned not null default 1 comment '节点状态',
	createtime int unsigned not null default 0 comment '创建时间',
	updatetime int unsigned not null default 0 comment '更新时间'
) charset utf8 comment ="角色权限关联表";

-- 用户权限节点表
drop table if exists user_auth;
create table user_auth(
	id int unsigned not null auto_increment primary key,
	name varchar(100) not null default '' comment '节点名称',
	title varchar(255) not null default '' comment '节点',
	status tinyint not null default 0 comment '节点状态 0 未启用 1已启用',
	pid int unsigned not null default 0 comment '父级id',
	sort tinyint unsigned not null default 0 comment '排序',
	auth_c varchar(100) not null default '' comment '控制器名字',
	auth_a varchar(100) not null default '' comment '操作函数',
	level tinyint unsigned not null default 0 comment '1 项目 2控制 3操作',
	icon varchar(100) not null default '' comment '图标名称',
	link varchar(255) not null default '' comment '标题链接',
	html_option varchar(255) not null default '' comment '标题样式 class="xxx",type="bbb"',
	li_html_option varchar(255) not null default '' comment 'li样式 class="xxx",type="bbb"',
	is_delete tinyint unsigned not null default 0,
	createtime int unsigned not null default 0 comment '创建时间',
	updatetime int unsigned not null default 0 comment '更新时间'

) charset utf8 comment ="用户权限节点表";


-- 公司部门表
drop table if exists company_dept;
create table company_dept(
	id int unsigned not null auto_increment primary key,
	name varchar(100) not null default '' comment '部门名称',
	pid int unsigned not null default 0 comment '部们父级id',
	sort tinyint unsigned not null default 0 comment '排序',
	remark text,
	status tinyint unsigned not null default 0 comment '部门状态',
	createtime int unsigned not null default 0 comment '创建时间',
	updatetime int unsigned not null default 0 comment '更新时间'
) charset utf8 comment="公司部门表";

-- 虚拟数据
insert into company_dept values(default,'xxx集团',0,10,'',1,0,unix_timestamp(),unix_timestamp());
insert into company_dept values(default,'董事长',1,10,'',1,0,unix_timestamp(),unix_timestamp());
insert into company_dept values(default,'总经理',2,10,'',1,0,unix_timestamp(),unix_timestamp());
insert into company_dept values(default,'部门总监',3,10,'',1,0,unix_timestamp(),unix_timestamp());

-- 公司职位表
drop table if exists company_post;
create table company_post(

	id int unsigned not null auto_increment primary key,
	name varchar(100) not null default '' comment '职位名称',
	sort tinyint unsigned not null default 0 comment '排序',
	status tinyint unsigned not null default 0 comment '状态',
	remark text,
	createtime int unsigned not null default 0 comment '创建时间',
	updatetime int unsigned not null default 0 comment '更新时间'
) charset utf8 comment ="公司职位表";

-- 虚拟数据
insert into company_post values(default,'董事长',1,1,'董事长',0,unix_timestamp(),unix_timestamp());
insert into company_post values(default,'总经理',1,1,'总经理',0,unix_timestamp(),unix_timestamp());
insert into company_post values(default,'部门总监',1,1,'部门总监',0,unix_timestamp(),unix_timestamp());
insert into company_post values(default,'职员',1,1,'职员',0,unix_timestamp(),unix_timestamp());

-- 上传附件表
drop table if exists common_attchments;
create table common_attchments(
	id int unsigned not null auto_increment primary key,
	user_id int unsigned not null default 0 comment '用户id',
	file_path varchar(255) not null default '' comment '文件路径',
	file_name varchar(255)	not null default '' comment '文件名称',
	file_rename varchar(255) not null default '' comment '文件旧名',
	file_suffix varchar(255) not null default '' comment '文件后缀',
	file_size int unsigned not null default 0 comment '文件大小',
	is_delete tinyint unsigned not null default 0,
	createtime int unsigned not null default 0,
	updatetime int unsigned not null default 0
) charset utf8 comment="上传附件表";
