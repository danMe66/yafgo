<?php
/**
 * Created by PhpStorm.
 * User: pengjun
 * Date: 2020/08/17
 * Time: 12:11
 */

// 命令示例  php Command.php make:library Rpc_Aps_ApsAir  aps/aps_air.thrift
//Command.php 是脚本
//make:library 是命令
//test_testController 是你要生成的controller的名字  注意仅支持一个_ 代表一个目录如admin_aaaController，目录太深不好管理
//pam-end.thrift   需要编译的 thrift文件名  默认存放在application/library/thrift/目录下,请将需要编译的存在那个目录下
$len = count($argv);
if ($len<=1){
    exit('arg is empty');
}
//定义常量
define('APP_PATH','./application/');
//获取执行的命令
$command = $argv[1];

if ($command == 'make:library'){
    //获取要生成的控制器名
    $controllerName = $argv[2];

    $arr = explode('_',$controllerName);
    if (!is_array($arr)){
        exit('controller有问题');
    }
    //print_r($arr);
    //捕获文件名
    preg_match('/([a-zA-z]+)/',$arr[count($arr)-1],$fileName);
    //print_r($fileName);

    if (empty($fileName[1])){
        exit('Did not match the file name');
    }

    //目录
    //$dirPath = APP_PATH.'controllers/'.ucfirst($arr[0]);
    $librayPath = $className ="";
    foreach ($arr as $key => $value){
        $className .= "_".$value;
        if ((count($arr)-1) <= $key){
            break ;
        }else{
            $librayPath .= "/".ucfirst($value);
        }
    }
    $dirPath = APP_PATH.'library'.$librayPath;

    if (!is_dir($dirPath)){
        mkdir($dirPath);
    }

    if (empty($argv[3])){
        exit('Missing thrift that needs to be compiled');
    }
    $consulName = substr($argv[3],0,3);
    $res = file_get_contents('./application/library/thrift/'.$argv[3]);
    //匹配获取到服务名
    preg_match('/service ([a-zA-Z]+)/',$res,$match);
    if (empty($match[1])){
        exit('Did not match the service name');
    }
    $serviceName = $match[1];
    //匹配所有的方法
    preg_match_all('/ ([a-zA-Z]+)\(+/',$res,$function);

    //$dir = ucfirst($arr[0]);
    //$controller = ucfirst($arr[1]);
    //$controllerName = $dir.'_'.$controller;

    $className = substr($className , 1);
    //print_r($className);
    $time = Date("Y-m-d H:i:s" , time());
    $str = <<<heredoc
<?php
/**
 * Created by PhpStorm.
 * User: Rpc 
 * Date: {$time}
 * Desc: 自动编译生成，禁止手动更改
 */
 
class  $className{
        
    protected static $$consulName      = '$consulName';
    protected static $$serviceName   = '$serviceName';

heredoc;
    foreach ($function[1] as $functionName){
        $exception = '$exception';
        $exceptionCode = '$exception->getCode()';
        $exceptionMsg = '$exception->msg';
        $e1 = 'Common::EchoResult($exception->getCode(),$exception->msg)';
        $e2 = 'Common::EchoResult($exception->getCode(),$exception->getMessage())';
        $params = '$params';
        //$functionName.='Action';
        $str .= <<<heredoc
        
    public static function $functionName($params){
        try{
            //code
            return ClientFactory::getArrayService(
                self::$$consulName,
                self::$$serviceName,
                false,
                '$functionName',
                $params
            );
        }catch(\MicroserviceException $exception){
            Source_Notice::sendMicroserviceErrorNotice(self::$$consulName ,self::$$serviceName , '$functionName' , $exceptionCode, $exceptionMsg);
            $e1;
        }catch(\Exception $exception){
            Source_Notice::sendMicroserviceErrorNotice(self::$$consulName ,self::$$serviceName , '$functionName');
            $e2;
        }
    }

heredoc;
    }

    $str .= <<<heredoc
}
heredoc;

    $path = $dirPath.'/'.ucfirst($fileName[1]).'.php';
    if (!file_exists($path)){
        file_put_contents($path,$str);
        echo "Generate controller successfully\n";
    }else{
        echo "The generated library is unsuccessful, the file already exists, and the overlay code is not generated repeatedly. Please add the method manually. \n";
    }
    shell_exec('thrift.exe -out ./application/library/lib -gen php:oop ./application/library/thrift/'.$argv[3]);
    echo "Already will".$argv[3].'Compile successfully, in the application/library/lib folder'.PHP_EOL;
}