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