create table operadora_celular (
id int identity primary key not null,
descricao varchar(30) not null,
status_id char(1) not null
)
/*
 insert into operadora_celular values ('VIVO', 'a')
 insert into operadora_celular values ('CLARO', 'a')
 insert into operadora_celular values ('OI', 'a')
 insert into operadora_celular values ('TIM', 'a')
 insert into operadora_celular values ('NEXTEL', 'a')
 insert into operadora_celular values ('VIVO - 3G', 'a')
*/

create table celular_covre (
id int identity primary key not null,
num_celular varchar(11) not null,
operadora_id int not null,
imei varchar(20) null,
observacao varchar(30) null,
data_gravacao datetime not null,
user_gravacao int not null,
data_inativacao datetime null,
user_inativacao int null,
status_id char(1) not null
)
--drop table celular_covre
/*
insert into celular_covre values ('11111111111', 1, NULL, NULL, GETDATE(), 79, NULL, NULL, 'a')
insert into celular_covre values ('22222222222', 1, NULL, NULL, GETDATE(), 79, NULL, NULL, 'a')
insert into celular_covre values ('33333333333', 1, NULL, NULL, GETDATE(), 79, NULL, NULL, 'a')
*/

create table celular_funcionario (
id int identity primary key not null,
pessoa_id int not null,
celular_covre_id varchar(11) not null,
data_inicio date not null,
data_fim date null,
cod_ccusto int null,
observacao varchar(200) null,
data_gravacao datetime not null,
user_gravacao int not null,
data_inativacao datetime null,
user_inativacao int null,
status_id char(1) not null
)
--drop table celular_funcionario
--truncate table celular_agregado


create table parametro_celular_agregado (
id int identity primary key not null,
valor numeric(15,2) not null,
dia_ini int not null,
dia_fim int not null
)
--drop table parametro_celular_agregado

--insert into parametro_celular_agregado values ('31.00', 1, 20)


--insert into celular_covre (num_celular, operadora_id, imei, observacao, data_gravacao, user_gravacao, data_inativacao, user_inativacao, status_id)
select CAST(CAST(DDD AS INT)AS VARCHAR)+CAST(CAST(NUMERO AS INT)AS VARCHAR), OPERADORA.ID, CHIP IMEI, NULL, GETDATE(), 79, NULL, NULL, 'a'
from Plan1$
join operadora_celular operadora with (nolock) on
	operadora.descricao = Plan1$.CONTA