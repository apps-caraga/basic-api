<?php

namespace Tqdev\PhpCrudApi;

use Tqdev\PhpCrudApi\Api;
use Tqdev\PhpCrudApi\Config\Config;
use Tqdev\PhpCrudApi\RequestFactory;
use Tqdev\PhpCrudApi\ResponseUtils;

require_once 'api.include.php';


function is_admin(){
	return (bool) $_SESSION['user']['is_admin'];
}

$config = new Config([
        'driver' => 'sqlite',
        'address' => 'database.sqlite',
		'middlewares'=>'dbAuth,authorization,multiTenancy',
		"dbAuth.registerUser"=>"1",
		"dbAuth.usersTable"=>"users",
		"dbAuth.loginTable"=>"active_users",
		'authorization.tableHandler' => function ($operation, $tableName) {
            $current_role = $_SESSION['user']['role'];
			$admin_tables = ['roles']; //array of tables accessible to admins only

		 	if(is_admin()){
				return true;
			}else{ // not admin, return false if requested table is for admins only
				return (!in_array($tableName,$admin_tables));
			}
		},
		'authorization.columnHandler'=>function($operation, $tableName, $columnName){
			$hide_columns =['password','created_at' ]; //list of columns that will not be returned to frontend
			return !($tableName == 'users' && in_array($columnName,$hide_columns));
		},
		'multiTenancy.handler' => function ($operation, $tableName) {
            $current_role = $_SESSION['user']['role'];
            if(is_admin()){	
                return []; // no filter/show all
            }
            else{ 
                // this attaches "WHERE created_by=`id` " to every requests
                // this can be further customized based on the $operation type as well as the $tableName being accessed
				if($tableName == 'users' || $tableName == 'active_users'){
					// return filtered data based on current user ID; users table has no 'created_by' column, so filter by 'id'
					return ['id' => $_SESSION['user']['id']]; 
				}else{ 
					// return filtered data based on current user ID; assumes all other tables have a 'created_by' column
					// but if it has no 'created_by' column, the filter has no effect
					return ['created_by' => $_SESSION['user']['id']];				}
            }
				
		},
		"cacheType"=>"NoCache",
		"debug"=>true
     ]);
$request = RequestFactory::fromGlobals();
$api = new Api($config);
$response = $api->handle($request);
ResponseUtils::output($response);
//filename: my-api.php