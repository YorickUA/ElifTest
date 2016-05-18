<?php
/**
 * Created by PhpStorm.
 * User: Pavlo
 * Date: 14.05.2016
 * Time: 12:14
 */
require_once 'DBManager.php';

class API
{
    private function __construct()
    {
    }

    private static $_inst;

    public static function  instance()
    {
        if (!isset($_inst))
        {
            $_inst = new API();
        }
        return $_inst;
    }
    public function createCompany($data)
    {
        $statatement= DBManager::instance()->createCompany($data);
        echo (int)$statatement;
    }
    
    public function deleteCompany($companyName){
        $statatement= DBManager::instance()->deleteCompany($companyName);
       // file_put_contents('C:/test.txt', print_r($statatement,true), FILE_APPEND);
        echo (int)$statatement;
    }

    public function showCompany($companyName){
        $companyObj= DBManager::instance()->showCompany($companyName);
        echo json_encode($companyObj);
    }

    public function editCompany($data){
        $statatement= DBManager::instance()->editCompany($data);
        echo (int)$statatement;
    }
    
    public function getTree()
    {
         $companies= DBManager::instance()->getAllCompanies();
         echo json_encode($companies);
    }
    
}