<?php

namespace Tqdev\PhpCrudApi;

use Tqdev\PhpCrudApi\Api;
use Tqdev\PhpCrudApi\Config\Config;
use Tqdev\PhpCrudApi\RequestFactory;
use Tqdev\PhpCrudApi\ResponseUtils;

require_once 'api.include.php';
 
 
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
		 	if($current_role =='ADMIN'){
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
            if($current_role ==='ADMIN'){
                return [];
            }
            else{ 
                // this attaches "WHERE created_by= < user id" to every requests
                // this can be further customized based on the $operation type as well as the $tableName being accessed
                return ['created_by' => $_SESSION['user']['id']];
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