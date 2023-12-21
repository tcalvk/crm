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

status = updated 

4/7/23
alter table Property
add Name varchar(64) after PropertyId 

status = updated 

4/7/23
UPDATE `Property` SET `Name` = 'The UPS Store #6054', `Address2` = '' WHERE `Property`.`PropertyId` = 2

status = updated 

4/7/23
UPDATE `Property` SET `Name` = 'The UPS Store #5517', `Address2` = '' WHERE `Property`.`PropertyId` = 3

status = updated 

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

status = updated 

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

status = updated 

4/8/23
alter table Statements 
add PaidDate datetime after CreatedDate

status = updated 

4/8/23
create table users (
    userId int AUTO_INCREMENT,
    name varchar(128),
    email varchar(128),
    password varchar(256),
    primary key (userId)
)

status = updated 

5/22/23
alter table Contract 
add TestContract bit 

status = updated 

5/25/23
alter table Statements 
rename to LogStatements

status = updated 

5/28/23
alter table users 
drop column name;

alter table users
add firstname varchar(128) after userId;

alter table users
add lastname varchar(128) after firstname;

status = updated 

7/4/23 
alter table Customer 
add userId int after Email 

alter table Customer 
add foreign key (userId) references users(userId)

alter table users 
add superuser bit after password 

INSERT INTO `users` (`userId`, `firstname`, `lastname`, `email`, `password`, `superuser`) VALUES (NULL, 'Todd', 'Klein', 'toddcalvinklein@gmail.com', 'password', NULL);

status = updated 

7/7/23
create table Contact ( ContactId int AUTO_INCREMENT, FirstName varchar(64), LastName varchar(64), Address1 varchar(256), Address2 varchar(256), City varchar(128), StateId varchar(12), Zip varchar(25), Phone varchar(20), Email varchar(256), CustomerId int, primary key (ContactId) );

alter table Contact add foreign key (CustomerId) REFERENCES Customer(CustomerId);

alter table Contact add ReceiveStatements bit after Email;


insert into Property (Address1, City, StateId, Zip, OwnedBy) 
values ('210 E Crossroads Blvd', 'Saratoga Springs', 'UT', 84045, 3)

insert into Customer (Name, Address1, City, StateId, Zip, Phone) 
values ('Franchise Management', '7650 S Redwood Rd', 'West Jordan', 'UT', '84084', '8018704177');

insert into Contact (FirstName, LastName, Email, CustomerId) values ('AP', ' ', 'ap@frmwj.com', 5);
insert into Contact (FirstName, LastName, Email, CustomerId) values ('Jack', ' ', 'jack@frmwj.com', 5);
insert into Contact (FirstName, LastName, Email, CustomerId) values ('Robert', 'Anderson', 'robert.anderson@frmwj.com', 5);

status = updated 

8/25/23

alter table LogStatements
drop column PaidDate;

alter table LogStatements
add column PaidDate date after CreatedDate;

update LogStatements
set PaidDate = cast(CreatedDate as date);

alter table LogStatements
add column WrittenOff boolean;

update LogStatements 
set WrittenOff = 0;

status = updated 

9/14/23

alter table LogFixedPayments
add column StatementNumber int after Id;

alter table LogFixedPayments
add foreign key (StatementNumber) references LogStatements(StatementNumber);

status = updated 

9/15/23

alter table LogStatements
add column PaymentAmount decimal(13,2); 

alter table LogStatements
modify column TotalAmt decimal(13,2);

status = updated 

9/18/23

alter table Contract
add column Name varchar(256) after ContractId;

status = updated 

9/26/23

create table CustomStatement (
    CustomStatementId int AUTO_INCREMENT,
    CustomerId int,
    Name varchar(256),
    CreatedDate date,
    Primary Key (CustomStatementId)
)

alter table CustomStatement
add foreign key (CustomerId) references Customer(CustomerId)

status = updated 

create table CustomStatementLine (
    CustomStatementLineId int AUTO_INCREMENT,
    CustomStatementId int,
    Description varchar(256),
    Qty decimal (19,2),
    UnitPrice decimal(19,2),
    UnitDiscount decimal (19,2),
    Primary Key (CustomStatementLineId)
)

alter table CustomStatementLine
add foreign key (CustomStatementId) references CustomStatement(CustomStatementId)

status = updated 

alter table LogStatements 
add column CustomStatementId int;

alter table LogStatements
add foreign key (CustomStatementId) references CustomStatement(CustomStatementId);

status = updated 

12/20/2023

-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 21, 2023 at 02:16 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `crm54`
--

-- --------------------------------------------------------

--
-- Table structure for table `ContractTerm`
--

CREATE TABLE `ContractTerm` (
  `ContractTermId` int(11) NOT NULL,
  `ContractId` int(11) DEFAULT NULL,
  `TermStartDate` date DEFAULT NULL,
  `TermEndDate` date DEFAULT NULL,
  `BaseAmt` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ContractTerm`
--

INSERT INTO `ContractTerm` (`ContractTermId`, `ContractId`, `TermStartDate`, `TermEndDate`, `BaseAmt`) VALUES
(1, 8, '2023-01-01', '2023-12-31', '100'),
(2, 8, '2024-01-01', '2024-12-31', '125');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ContractTerm`
--
ALTER TABLE `ContractTerm`
  ADD PRIMARY KEY (`ContractTermId`),
  ADD KEY `ContractId` (`ContractId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ContractTerm`
--
ALTER TABLE `ContractTerm`
  MODIFY `ContractTermId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ContractTerm`
--
ALTER TABLE `ContractTerm`
  ADD CONSTRAINT `contractterm_ibfk_1` FOREIGN KEY (`ContractId`) REFERENCES `Contract` (`ContractId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

status = 'not updated'