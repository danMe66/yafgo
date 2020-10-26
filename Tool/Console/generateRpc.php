<?php
/**
 * Mac、linux系统可使用此命令！
 * 前置条件：ald_sgw项目和microservice-api项目必须在同一目录下，否则会找不到thrift文件
 * 使用示例：首选 cd 到项目根目录，然后执行下边命令即可即可
 * 针对新的thrift文件，每个类中对应一个service方法，执行命令：php console/generateRpc.php make:library Rpc_Aps_ApsAir aps/aps_air.thrift
 * 针对旧的的thrift文件，每个类中有多个service方法，执行命令：php console/generateRpc.php make:library Rpc_Djm_DeliveryStatus djm/djm_api.thrift DJMDeliveryStatusService
 */
define("APP_PATH", realpath(dirname(__FILE__) . '/../application/')); /* 指向public的上一级 */
define('THRIFT_PATH', realpath(dirname(__FILE__) . '/../../microservice-api/src/main/thrift/'));
$len = count($argv);
if ($len <= 1) {
    exit('脚本参数不规范！');
}
//获取执行的命令
$console = $argv[1];
if ($console == 'make:library') {
    //获取要生成的路径
    $library_name = $argv[2];
    $arr = explode('_', $library_name);
    if (!is_array($arr)) {
        exit('----------------->library路径有问题');
    }
    preg_match('/([a-zA-z]+)/', $arr[count($arr) - 1], $fileName);
    if (empty($fileName[1])) {
        exit('Did not match the file name');
    }
    $library_path = $className = "";
    foreach ($arr as $key => $value) {
        $className .= "_" . $value;
        if ((count($arr) - 1) <= $key) {
            break;
        } else {
            $library_path .= "/" . ucfirst($value);
        }
    }
    $dirPath = APP_PATH . '/library' . $library_path;
    if (!is_dir($dirPath)) {
        mkdir($dirPath);
    }
    if (empty($argv[3])) {
        exit('----------------->第三个参数thrift文件是必须的');
    }
    $thriftArr = explode('/', $argv[3]);
    $consulName = $thriftArr[0];
    $thrift_file_path = THRIFT_PATH . '/' . $argv[3];
    $res = file_get_contents(THRIFT_PATH . '/' . $argv[3]);
    if (!empty($argv[4])) {
        $service = $argv[4];
        //匹配获取到服务名
        preg_match("/service ({$service}+)/", $res, $match);
        if (empty($match[1])) {
            exit('----------------->找不到service name，请检查文件名称是否规范');
        }
        $serviceName = $match[1];
        //匹配对应service片段
        $data = preg_replace("/[\t\n\r]+/", "", $res);
        $reg = '/service ' . $service . '{(.*?)}/';
        preg_match($reg, $data, $serviceFragment);
        //匹配service片段里边的所有的方法
        preg_match_all('/ ([a-zA-Z]+)\(+/', $serviceFragment[1], $function);
    } else {
        //匹配获取到服务名
        preg_match('/service ([a-zA-Z]+)/', $res, $match);
        if (empty($match[1])) {
            exit('----------------->找不到service name，请检查文件名称是否规范');
        }
        $serviceName = $match[1];
        //匹配所有的方法
        preg_match_all('/ ([a-zA-Z]+)\(+/', $res, $function);
    }

    $className = substr($className, 1);
    $time = Date("Y-m-d H:i:s", time());
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
    foreach ($function[1] as $functionName) {
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
        Source_Shixi_Notice::sendMicroserviceErrorNotice($errConsulName ,$errServiceName , $errFunctionName , $code, $msg);
    }
}
heredoc;
    // 以上文件结束，等待生成
    $path = $dirPath . '/' . ucfirst($fileName[1]) . '.php';
    if (!file_exists($path)) {
        file_put_contents($path, $str);
        echo "----------------->Rpc文件类库生成成功,地址：{$path}" . PHP_EOL;
        //生成thrift文件
        shell_exec('thrift -out application/library/lib/ --gen php:server ' . $thrift_file_path);
        echo "----------------->thrift文件编译成功" . PHP_EOL;
    } else {
        shell_exec('rm -r ' . $path);
        echo "----------------->Rpc类库生成失败，文件已存在,文件已删除，请再次执行命令！" . PHP_EOL;
    }
} else {
    exit('未知的脚本命令！');
}
