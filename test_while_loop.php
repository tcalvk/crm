<?php 
require 'model/database.php';
require 'model/log_statements_db.php';
$log_statements_db = new LogStatementsDB; 
echo 'hello world';
$display_text = $log_statements_db->generate_statement_number();
echo $display_text;
?>