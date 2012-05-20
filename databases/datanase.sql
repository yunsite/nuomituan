/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     2012/3/30 下午 10:20:05                        */
/*==============================================================*/


drop table if exists nm_admin;

drop table if exists nm_admin_auth;

drop table if exists nm_admin_log;

drop table if exists nm_admin_permission;

drop table if exists nm_admin_role;

drop table if exists nm_article;

drop table if exists nm_article_column;

drop table if exists nm_city;

drop table if exists nm_comment;

drop table if exists nm_coupon;

drop table if exists nm_deal;

drop table if exists nm_deal_order;

drop table if exists nm_deal_sort;

drop table if exists nm_income;

drop table if exists nm_massage;

drop table if exists nm_password_return;

drop table if exists nm_subscription;

drop table if exists nm_supplier_pay;

drop table if exists nm_sys_cfg;

drop table if exists nm_user;

drop table if exists nm_user_address;

/*==============================================================*/
/* Table: nm_admin                                              */
/*==============================================================*/
create table nm_admin
(
   id                   int unsigned not null auto_increment,
   user_name            varchar(32) not null,
   role                 int not null,
   pass_word            varchar(64) not null,
   salt                 varbinary(64) not null,
   ctime                int not null,
   mtime                int,
   login_time           int,
   login_ip             varchar(32),
   primary key (id)
);

/*==============================================================*/
/* Table: nm_admin_auth                                         */
/*==============================================================*/
create table nm_admin_auth
(
   role_id              int not null,
   per_id               int not null,
   ctime                int not null,
   admin                int not null,
   primary key (role_id, per_id)
);

/*==============================================================*/
/* Table: nm_admin_log                                          */
/*==============================================================*/
create table nm_admin_log
(
   id                   int unsigned not null,
   ctime                int not null,
   admin                int not null,
   action               varchar(64) not null,
   massage              varchar(255) not null,
   primary key (id)
);

/*==============================================================*/
/* Table: nm_admin_permission                                   */
/*==============================================================*/
create table nm_admin_permission
(
   id                   int unsigned not null,
   name                 varchar(128) not null,
   description          varchar(255),
   primary key (id)
);

/*==============================================================*/
/* Table: nm_admin_role                                         */
/*==============================================================*/
create table nm_admin_role
(
   id                   int unsigned not null auto_increment,
   role_name            varchar(64) not null,
   role_c_name          varchar(64) not null,
   description          varchar(255) not null,
   primary key (id)
);

/*==============================================================*/
/* Table: nm_article                                            */
/*==============================================================*/
create table nm_article
(
   id                   int unsigned not null auto_increment,
   title                varchar(255) not null,
   cid                  int not null,
   keywords             varchar(255),
   description          varchar(255),
   content              text not null,
   writer               int not null,
   ctime                int not null,
   mtime                int,
   primary key (id)
);

/*==============================================================*/
/* Table: nm_article_column                                     */
/*==============================================================*/
create table nm_article_column
(
   id                   int unsigned not null auto_increment,
   name                 varchar(64) not null,
   fid                  int not null default 0,
   keywords             varchar(255),
   description          varchar(255),
   content              text,
   primary key (id)
);

/*==============================================================*/
/* Table: nm_city                                               */
/*==============================================================*/
create table nm_city
(
   id                   int unsigned not null auto_increment,
   city_py              varchar(32) not null,
   city                 varchar(32) not null,
   admin                int,
   content              text,
   keywords             char(255),
   description          char(255),
   primary key (id)
);

/*==============================================================*/
/* Table: nm_comment                                            */
/*==============================================================*/
create table nm_comment
(
   id                   int unsigned not null auto_increment,
   uid                  int not null,
   did                  int not null,
   score                char(1) not null,
   content              text,
   is_effect            char(1) not null,
   primary key (id)
);

/*==============================================================*/
/* Table: nm_coupon                                             */
/*==============================================================*/
create table nm_coupon
(
   id                   int unsigned not null auto_increment,
   order_no             int not null,
   coupon_no            varchar(32) not null,
   coupon_code          varchar(32) not null,
   use_time             int,
   status               char(1) not null,
   primary key (id)
);

/*==============================================================*/
/* Table: nm_deal                                               */
/*==============================================================*/
create table nm_deal
(
   ID                   int unsigned not null auto_increment,
   title                varchar(255) not null,
   price                float not null,
   sid                  int not null,
   keyword              varbinary(255),
   description          varchar(255),
   content              text not null,
   sort                 int not null,
   city                 int not null,
   admin                int not null,
   status               char(1) not null,
   start_time           int not null,
   end_time             int,
   ctime                int not null,
   mtime                int,
   income               float not null,
   primary key (ID)
);

/*==============================================================*/
/* Table: nm_deal_order                                         */
/*==============================================================*/
create table nm_deal_order
(
   id                   int unsigned not null,
   order_no             int not null,
   did                  int not null,
   uid                  int not null,
   count                tinyint not null,
   money                float not null,
   final_money          float not null,
   status               char(1) not null,
   ctime                int not null,
   pay_time             int,
   dispatch_time        int,
   finish_time          int,
   address              int,
   exp_no               varchar(32),
   exp_name             varchar(64),
   is_effect            char(1),
   primary key (id)
);

/*==============================================================*/
/* Table: nm_deal_sort                                          */
/*==============================================================*/
create table nm_deal_sort
(
   ID                   int unsigned not null auto_increment,
   name                 varchar(64) not null,
   fid                  int not null,
   keywords             varchar(256),
   description          varchar(256),
   content              text,
   url                  varchar(128),
   primary key (ID)
);

/*==============================================================*/
/* Table: nm_income                                             */
/*==============================================================*/
create table nm_income
(
   id                   int not null,
   did                  int not null,
   count                int not null,
   salse_money          float not null,
   supplier_money       float not null,
   income               float not null,
   ctime                int not null,
   primary key (id)
);

/*==============================================================*/
/* Table: nm_massage                                            */
/*==============================================================*/
create table nm_massage
(
   id                   int unsigned not null auto_increment,
   send_id              int not null,
   receive_id           int not null,
   send_sort            enum("S","U") not null comment 'U：普通用户
            S：系统',
   title                varchar(64) not null,
   content              text not null,
   primary key (id)
);

/*==============================================================*/
/* Table: nm_password_return                                    */
/*==============================================================*/
create table nm_password_return
(
   ID                   int unsigned not null auto_increment,
   uid                  int not null,
   ctime                int not null,
   mtime                int,
   method               enum("phone","email","question") not null,
   captcha              varchar(64) not null,
   is_effect            char(1) not null,
   primary key (ID)
);

/*==============================================================*/
/* Table: nm_subscription                                       */
/*==============================================================*/
create table nm_subscription
(
   id                   int unsigned not null auto_increment,
   is_effect            char(1) not null,
   ctime                int not null,
   Email                varchar(128) not null,
   mtime                int,
   primary key (id)
);

/*==============================================================*/
/* Table: nm_supplier_pay                                       */
/*==============================================================*/
create table nm_supplier_pay
(
   id                   int unsigned not null auto_increment,
   sid                  int not null,
   pay_time             int not null,
   money                float not null,
   pay_method           enum("cash","transfer") not null,
   operator             varchar(64) not null,
   primary key (id)
);

/*==============================================================*/
/* Table: nm_sys_cfg                                            */
/*==============================================================*/
create table nm_sys_cfg
(
   id                   int unsigned not null auto_increment,
   var_name             varchar(64) not null,
   var_value            varchar(256) not null,
   massage              varchar(256) not null,
   sort                 varchar(32),
   primary key (id)
);

/*==============================================================*/
/* Table: nm_user                                               */
/*==============================================================*/
create table nm_user
(
   id                   int unsigned not null auto_increment,
   user_name            varchar(128) not null,
   pass_word            varchar(128) not null,
   salt                 varchar(128) not null,
   status               char(1) not null,
   nike_name            varchar(16),
   email                varchar(128),
   phone                varchar(16),
   balance              float not null default 0,
   user_sort            char(1) not null,
   primary key (id)
);

/*==============================================================*/
/* Table: nm_user_address                                       */
/*==============================================================*/
create table nm_user_address
(
   ID                   int unsigned not null auto_increment,
   uid                  int not null,
   name                 varchar(32) not null,
   phone                varchar(16) not null,
   post_code            varchar(8),
   is_effect            char(1) not null,
   primary key (ID)
);

alter table nm_admin add constraint FK_admin_role_on_amdin_role_id foreign key (role)
      references nm_admin_role (id) on delete restrict on update restrict;

alter table nm_admin_auth add constraint FK_admin_auth_pre_id_on_admin_permission foreign key (per_id)
      references nm_admin_permission (id) on delete restrict on update restrict;

alter table nm_admin_auth add constraint FK_admin_auth_role_id_on_admin_role_id foreign key (role_id)
      references nm_admin_role (id) on delete restrict on update restrict;

alter table nm_admin_log add constraint FK_admin_log_on_admin_id foreign key (id)
      references nm_admin (id) on delete restrict on update restrict;

alter table nm_article add constraint FK_article_admin_on_admin_id foreign key (writer)
      references nm_admin (id) on delete restrict on update restrict;

alter table nm_article add constraint FK_article_cid_on_column_id foreign key (cid)
      references nm_article_column (id) on delete restrict on update restrict;

alter table nm_comment add constraint FK_comment_did_on_deal_id foreign key (did)
      references nm_deal (ID) on delete restrict on update restrict;

alter table nm_comment add constraint FK_comment_uid_on_user_id foreign key (uid)
      references nm_user (id) on delete restrict on update restrict;

alter table nm_coupon add constraint FK_coupon_order_no_on_dear_order_no foreign key (order_no)
      references nm_deal_order (id) on delete restrict on update restrict;

alter table nm_deal add constraint FK_deal_city_on_city_id foreign key (city)
      references nm_city (id) on delete restrict on update restrict;

alter table nm_deal add constraint FK_deal_sid_on_user_id foreign key (sid)
      references nm_user (id) on delete restrict on update restrict;

alter table nm_deal add constraint FK_deal_sort_on_deal_sort_id foreign key (sort)
      references nm_deal_sort (ID) on delete restrict on update restrict;

alter table nm_deal_order add constraint FK_deal_order_address_on_user_address_id foreign key (address)
      references nm_user_address (ID) on delete restrict on update restrict;

alter table nm_deal_order add constraint FK_deal_order_uid_on_user_id foreign key (uid)
      references nm_user (id) on delete restrict on update restrict;

alter table nm_deal_order add constraint FK_user_order_did_on_deal_id foreign key (did)
      references nm_deal (ID) on delete restrict on update restrict;

alter table nm_income add constraint FK_income_did_on_deal_id foreign key (did)
      references nm_deal (ID) on delete restrict on update restrict;

alter table nm_password_return add constraint FK_password_return_uid_on_user foreign key (uid)
      references nm_user (id) on delete restrict on update restrict;

alter table nm_supplier_pay add constraint FK_supplier_uid_on_user_id foreign key (sid)
      references nm_user (id) on delete restrict on update restrict;

alter table nm_user_address add constraint FK_user_address_uid_on_user_id foreign key (uid)
      references nm_user (id) on delete restrict on update restrict;

