--Use this file to track database changes--
--Example
--4/3/23
--create table example table (
--  PrimaryId int auto-increment, 
--  Name varchar(25)
--)
--Status = updated

4/6/23
update Customer set email = 'tannerklein5@icloud.com'
status = updated 

4/6/23
UPDATE `Customer` SET `City` = 'American Fork' WHERE `Customer`.`CustomerId` = 1
status = updated 

4/8/23 
update Customer 
set Email = 'anthony@mortgageeducators.com'
where CustomerId = 1

update customer 
set email = 'j.tyler.gray@gmail.com'
where customerid in (2,3)

status = updated 

4/7/23 
alter table Contract
add TotalPaymentsDue int after NumPaymentsDue

4/7/23
alter table Property
add Name varchar(64) after PropertyId 

4/7/23
UPDATE `Property` SET `Name` = 'The UPS Store #6054', `Address2` = '' WHERE `Property`.`PropertyId` = 2

4/7/23
UPDATE `Property` SET `Name` = 'The UPS Store #5517', `Address2` = '' WHERE `Property`.`PropertyId` = 3

4/7/23
create table LogFixedPayments (
    Id int AUTO_INCREMENT,
    CompletedDate datetime, 
    CurrentPaymentsDue int, 
    ChangeAmt int, 
    NewPaymentsDue int, 
    ContractId int,  
    primary key (Id), 
    foreign key (ContractId) references Contract(ContractId)
)

4/8/23
create table Statements (
    StatementNumber int AUTO_INCREMENT,
    CreatedDate datetime, 
    TotalAmt decimal,
    PaymentNumber int, 
    ContractId int,
    primary key (StatementNumber),
    foreign key (ContractId) references Contract(ContractId)
)

4/8/23
alter table Statements 
add PaidDate datetime after CreatedDate

4/8/23
create table users (
    userId int AUTO_INCREMENT,
    name varchar(128),
    email varchar(128),
    password varchar(256),
    primary key (userId)
)