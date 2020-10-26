<?php
/**
 * Created by PhpStorm.
 * User: pengjun
 * Date: 2020/08/17
 * Time: 12:11
 */

// 命令示例 php CommandRpc.php make:library Rpc_Aps_ApsAir  aps/aps_air.thrift
//Command.php 是脚本
//make:library 是命令
// Rpc_Aps_ApsAir 是你要生成的类库的路径和名字  Rpc/Aps 目录下面 文件ApsAir.php 中的类名为 Rpc_Aps_ApsAir
// aps/pam.thrift   THRIFT_PATH ：
// return  生成标准的类库，生成thrift文件并自动进行替换。
$len = count($argv);
if ($len<=1){
    exit('arg is empty');
}
//定义常量
define('APP_PATH','');
define('THRIFT_PATH','D:/java_work/microservice_api/src/main/thrift/');
//获取执行的命令
$command = $argv[1];

if ($command == 'make:library'){
    //获取要生成的路径
    $libraryName = $argv[2];

    $arr = explode('_',$libraryName);
    if (!is_array($arr)){
        exit('library路径有问题');
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
    $dirPath = APP_PATH.'../application/library'.$librayPath;

    if (!is_dir($dirPath)){
        mkdir($dirPath);
    }

    if (empty($argv[3])){
        exit('Missing thrift that needs to be compiled');
    }
    $thriftArr = explode('/',$argv[3]);
    $consulName = $thriftArr[0];

    $res = file_get_contents(THRIFT_PATH.$argv[3]);
    //匹配获取到服务名
    preg_match('/service ([a-zA-Z]+)/',$res,$match);
    if (empty($match[1])){
        exit('Did not match the service name');
    }
    $serviceName = $match[1];
    //匹配所有的方法
    preg_match_all('/ ([a-zA-Z]+)\(+/',$res,$function);

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
        $str .= <<<heredoc
        
    public static function $functionName($params = array()){
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
            self::sendErrorMsg(self::$$consulName ,self::$$serviceName , '$functionName' , json_encode($params , JSON_UNESCAPED_UNICODE), $exceptionMsg);
            $e1;
        }catch(\Exception $exception){
            self::sendErrorMsg(self::$$consulName ,self::$$serviceName , '$functionName' , '' , json_encode($params , JSON_UNESCAPED_UNICODE));
            $e2;
        }
    }

heredoc;
    }
    $errConsulName = '$errConsulName';
    $errServiceName = '$errServiceName';
    $errFunctionName = '$functionName';
    $code = '$code';
    $msg = '$msg';
    $str .= <<<heredoc
    
    /**
    * 通用的发送群报警消息
    */
    private static function sendErrorMsg($errConsulName = "" ,$errServiceName = "" , $errFunctionName = "" , $code = "" , $msg = ""){
        Source_Notice::sendMicroserviceErrorNotice($errConsulName ,$errServiceName , $errFunctionName , $code, $msg);
    }
}
heredoc;
    // 以上文件结束，等待生成

    $path = $dirPath.'/'.ucfirst($fileName[1]).'.php';
    // 先删除之前编译的基本类库，然后将新生成的类库进行编译
    shell_exec("cd {$dirPath} && del {$fileName[1]}.php");

    if (!file_exists($path)){
        file_put_contents($path,$str);
        echo "生成RPC类库成功 {$fileName[1]}.php \n";
    }else{
        echo "生成的库不成功，文件已存在,请删除源文件再进行编译 \n";
    }
    // 执行编译thrift文件
    //shell_exec('thrift.exe -out ./application/library/lib -gen php:oop ./application/library/thrift/'.$argv[3]);
    shell_exec("cd ../public/thrift/thriftLib &&  rd /s /q com"); // 删除编译的thrift文件[删除 public/thrift/thriftLib 下面的 com 文件]
    shell_exec('thrift.exe -out ../public/thrift/thriftLib -gen php:oop '.THRIFT_PATH . $argv[3]); // 重新生成thrift文件
    shell_exec('XCOPY "../public/thrift/thriftLib" "../application/library/lib" /E /Y '); //将生成的文件移动到项目文件中
    //shell_exec('move /y  ./public/thrift/rpcLib  ./public'); // windows 下面使用命令移动，显示拒绝访问
    echo "生成标准类库成功 ".$argv[3].' in the application/library/Rpc folder'.PHP_EOL;
    echo "生成编译的thrift文件成功 in the /public/thrift/thriftLib/com folder ， thrift编译文件已经自动同步到 application/library/lib 下面的文件夹中了".PHP_EOL;
}