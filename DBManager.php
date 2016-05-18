<?php
/**
 * Created by PhpStorm.
 * User: Pavlo
 * Date: 14.05.2016
 * Time: 13:53
 */

class DBManager
{
    private function __construct()
    {
        $dbInfo="mysql:host=mysql.zzz.com.ua; username=livchak11; port=3306; dbname=eliflivchak_zzz_com_ua; ";
        $dbUser="livchak11";
        $dbPassword="laas-cnrs";
        $this->db=new PDO($dbInfo, $dbUser, $dbPassword);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    private static $_inst;

    private $db;

    public static function instance()
    {
        if (!isset($_inst)){
            $_inst = new DBManager();
        }
        return $_inst;
    }

    public function createCompany($data)
    {
        if (!ctype_digit($data['earnings'])){
            return false;
         }
        if($data['parent']) {
            $entrySQL="SELECT * FROM company_poll WHERE company_name=?";
            $formData=array($data['parent']);
            $parent=$this->makeStatement($entrySQL,$formData);
            if($parent){
                $parentData=$parent->fetchObject();
                if (empty($parentData)) {
                    return false;
                }
            }
        }
        $parentId =!empty($parentData)? $parentData->company_id :0;
        $formData=array($data['name'],$data['earnings'],$parentId);
        $entrySQL="INSERT INTO company_poll (company_name, company_earnings, company_parent)
                    VALUES (?, ?, ?)";
        $entryStatement=$this->makeStatement($entrySQL,$formData);
               return (bool)$entryStatement;
    }

    public function deleteCompany($companyName)
    {
        $entrySQL="SELECT * FROM company_poll WHERE company_name=?";
        $formData=array($companyName);
        $returnValue=false;
        $company=$this->makeStatement($entrySQL,$formData);
        if($company) {
            $companyData=$company->fetchObject();
            if ($companyData) {
                $entrySQL="DELETE  FROM company_poll WHERE company_name=?";
                $formData=array($companyName);
                $company=$this->makeStatement($entrySQL,$formData);
                //file_put_contents('C:/test.txt', print_r($company, true), FILE_APPEND);
                $returnValue=(bool)$company;
                $updateSQL="UPDATE company_poll SET company_parent=? WHERE company_parent=?";
                $formData=array($companyData->company_parent,$companyData->company_id);
                $company=$this->makeStatement($updateSQL,$formData);
            }
        }
        return $returnValue;
    }

    public function getAllCompanies()
    {
        $entrySQL="SELECT * FROM company_poll";
        $entryStatement=$this->makeStatement($entrySQL);
        return ($entryStatement ? $entryStatement->fetchAll() : array());
    }

    public function showCompany($companyName)
    {
        $entrySQL="SELECT * FROM company_poll WHERE company_name=?";
        $formData=array($companyName);
        $companyStatement=$this->makeStatement($entrySQL,$formData);
        $company=$companyStatement->fetchObject();
        if($company) {
            $entrySQL = "SELECT * FROM company_poll WHERE company_parent=?";
            $formData = array($company->company_id);
            $childStatement=$this->makeStatement($entrySQL,$formData);
            $company->child_companies=$childStatement->fetchAll();
        }
        return $company;
    }

    public function editCompany($data)
    {
        $entrySQL="SELECT * FROM company_poll WHERE company_name=?";
        $formData=array($data['name']);
        $checkStatement=$this->makeStatement($entrySQL,$formData);
        if ($checkStatement->fetchObject()) {
            return false;
        }
        $entrySQL="SELECT * FROM company_poll WHERE company_name=?";
        $formData=array($data['oldName']);
        $searchStatement=$this->makeStatement($entrySQL,$formData);
        $company=$searchStatement->fetchObject();
       
        if ($company){
            if($data['earnings']&&$data['name']) {
                 if (!ctype_digit($data['earnings'])){
                      return false;
                 }
                 $entrySQL="UPDATE company_poll SET company_name=?, company_earnings=? WHERE company_name=?";
                 $formData=array($data['name'],$data['earnings'],$data['oldName']);
               // file_put_contents('C:/test.txt', print_r('1',true), FILE_APPEND);
                $statement=$this->makeStatement($entrySQL,$formData);
                return true;
            }
            else if(!$data['earnings']&&$data['name']) {
                $entrySQL="UPDATE company_poll SET company_name=?  WHERE company_name=?";
                $formData=array($data['name'],$data['oldName']);
               // file_put_contents('C:/test.txt', print_r('2',true), FILE_APPEND);
                $statement=$this->makeStatement($entrySQL,$formData);
                return true;
            }
            else if ($data['earnings']&&!$data['name']) {
                if (!ctype_digit($data['earnings'])){
                    return false;
                }
                $entrySQL="UPDATE company_poll SET company_earnings=?  WHERE company_name=?";
                $formData=array($data['earnings'],$data['oldName']);
               // file_put_contents('C:/test.txt', print_r('3',true), FILE_APPEND);
                $statement=$this->makeStatement($entrySQL,$formData);
                return true;
            }
            else {return true;}
        }
        return false;

    }
    
    private function makeStatement($sql, $data=NULL)
    {
        $statement=$this->db->prepare($sql);

            $statement->execute($data);
               return $statement->errorCode() === '00000' ? $statement : false;

    }
}		