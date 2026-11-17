<?php

define('APP_PATH', realpath(__DIR__ . '/../../../'));

if (file_exists($f = APP_PATH . '/app/Constant/AppConstant.php')) {
    include $f;
}

include APP_PATH . '/vendor/modstart/modstart/src/Data/FileManager.php';
include APP_PATH . '/vendor/modstart/modstart/src/Core/Env/EnvUtil.php';
include APP_PATH . '/vendor/modstart/modstart/src/Core/Util/EnvUtil.php';
include APP_PATH . '/vendor/modstart/modstart/src/Core/Util/RandomUtil.php';
include APP_PATH . '/vendor/modstart/modstart/src/Core/Util/CurlUtil.php';
include APP_PATH . '/vendor/modstart/modstart/src/Core/Util/FileUtil.php';

define('INSTALL_LOCK_FILE', APP_PATH . '/storage/install.lock');
// ModStart 传统模板为 env.example，基于 Laravel 11+ 骨架的项目使用 .env.example，两者都支持
$envExampleFile = APP_PATH . '/env.example';
if (!file_exists($envExampleFile) && file_exists(APP_PATH . '/.env.example')) {
    $envExampleFile = APP_PATH . '/.env.example';
}
define('ENV_FILE_EXAMPLE', $envExampleFile);
define('ENV_FILE', APP_PATH . '/.env');
if (file_exists($licenseFile = APP_PATH . '/license_url.txt')) {
    define('LICENSE_URL', trim(file_get_contents($licenseFile)));
}
if (file_exists($demoData = APP_PATH . '/public/data_demo/data.php')) {
    define('DEMO_DATA', true);
}

if (class_exists(\App\Constant\AppConstant::class)) {
    if (defined('\\App\\Constant\\AppConstant::APP')) {
        define('INSTALL_APP', strtoupper(\App\Constant\AppConstant::APP));
    }
    if (defined('\\App\\Constant\\AppConstant::APP_NAME')) {
        define('INSTALL_APP_NAME', \App\Constant\AppConstant::APP_NAME);
    }
    if (defined('\\App\\Constant\\AppConstant::VERSION')) {
        define('INSTALL_APP_VERSION', \App\Constant\AppConstant::VERSION);
    }
}
if (!defined('INSTALL_APP')) {
    define('INSTALL_APP', 'APP');
}
if (!defined('INSTALL_APP_NAME')) {
    if (defined('INSTALL_APP')) {
        define('INSTALL_APP_NAME', INSTALL_APP);
    } else {
        define('INSTALL_APP_NAME', 'APP');
    }
}
if (!defined('INSTALL_APP_VERSION')) {
    define('INSTALL_APP_VERSION', '0.0.0');
}

if (!file_exists(ENV_FILE)) {
    // 安装前的引导 .env：必须先给出 Laravel 11+ 的安全默认值，
    // 否则 SESSION_DRIVER / CACHE_STORE 会回落到 database，数据库还不存在时 /install/* 直接 500
    file_put_contents(ENV_FILE, "APP_ENV=beta\nAPP_DEBUG=true\nAPP_KEY=" . \ModStart\Core\Util\RandomUtil::string(32)
        . "\nCACHE_DRIVER=file\nCACHE_STORE=file\nSESSION_DRIVER=file\nQUEUE_CONNECTION=sync\n");
}

function php_framework_major_version()
{
    // 优先读取 composer.json 的 laravel/framework 约束判断框架大版本（如 ^13.0 -> 13、9.52.6 -> 9），
    // 产品名（AppConstant::APP）会随产品线变化（CMS13 / LOOM / modstart-laravel13 ...），只作为兜底
    static $major = false;
    if (false !== $major) {
        return $major;
    }
    $major = null;
    if (file_exists($file = APP_PATH . '/composer.json')) {
        $composer = json_decode(file_get_contents($file), true);
        if (!empty($composer['require']['laravel/framework'])
            && preg_match('/(\d+)/', $composer['require']['laravel/framework'], $matched)) {
            $major = intval($matched[1]);
        }
    }
    return $major;
}

function php_is_laravel9()
{
    if (9 == php_framework_major_version()) {
        return true;
    }
    // MODSTART-LARAVEL9 为本仓库（框架站点）使用的产品名
    return in_array(INSTALL_APP, ['CMS9', 'BLOG9', 'MODSTART-LARAVEL9']);
}

function php_is_laravel13()
{
    // Laravel 11 / 12 / 13 产品均为 PHP 8.2+，统一按「11 及以上」处理，
    // 避免新产品名（如 MODSTART-LARAVEL13）漏判后回落到 PHP 5.6 / 7.0 的要求
    $major = php_framework_major_version();
    if ($major && $major >= 11) {
        return true;
    }
    // 基于 Laravel 13 的产品（AppConstant::APP）：CMS13 / BLOG13 / LOOM / MODSTART-LARAVEL13
    return in_array(INSTALL_APP, ['CMS13', 'BLOG13', 'MODSTART-LARAVEL13']);
}

function php_version_min_required()
{
    // 从 composer.json 的 require.php 约束中取最低版本（如 ^8.3 / >=8.4.1）
    static $min = false;
    if (false !== $min) {
        return $min;
    }
    $min = null;
    if (file_exists($file = APP_PATH . '/composer.json')) {
        $composer = json_decode(file_get_contents($file), true);
        if (!empty($composer['require']['php']) && preg_match('/(\d+\.\d+(?:\.\d+)?)/', $composer['require']['php'], $matched)) {
            $min = $matched[1];
        }
    }
    return $min;
}

function php_version_requires()
{
    // Laravel 13 产品 PHP 最低版本按 composer.json 约束判断
    if (php_is_laravel13()) {
        $min = php_version_min_required();
        return $min ? ($min . ' 及以上') : '8.3 及以上';
    }
    if (php_is_laravel9()) {
        return join('，', [
            '8.1.x',
            '8.2.x',
        ]);
    }
    return join(', ', [
        '5.6.x',
        '7.0.x',
    ]);
}

function php_version_ok()
{
    if (php_is_laravel13()) {
        $min = php_version_min_required();
        if (empty($min)) {
            $min = '8.3';
        }
        if (2 == substr_count($min, '.')) {
            return version_compare(PHP_VERSION, $min, '>=');
        }
        return version_compare(PHP_VERSION, $min . '.0', '>=');
    }
    if (php_is_laravel9()) {
        // Laravel 9 产品：8.1.x / 8.2.x 均支持（8.3+ 未列入官方支持范围，仍保持宽松不阻塞）
        if (version_compare(PHP_VERSION, '8.1.0', '<')) {
            return false;
        }
        return true;
    }
    if (version_compare(PHP_VERSION, '5.5.9', '<')) {
        return false;
    }
    if (version_compare(PHP_VERSION, '7.1.0', '>=')) {
        return false;
    }
    return true;
}

function get_env_config($key, $default = '')
{
    static $envConfig = null;
    if (null == $envConfig) {
        $envConfig = array();
        if (!empty($configFiles = glob(APP_PATH . '/env.*.json'))) {
            foreach ($configFiles as $configFile) {
                $env = @json_decode(@file_get_contents($configFile), true);
                if (!empty($env)) {
                    $envConfig = array_merge($envConfig, $env);
                }
            }
        }
    }
    if (isset($envConfig[$key])) {
        return $envConfig[$key];
    }
    $osEnvMap = [
        'db_host' => ['DB_HOST', 'MYSQL_HOST'],
        'db_port' => ['DB_PORT', 'MYSQL_PORT'],
        'db_name' => ['DB_DATABASE', 'MYSQL_DB'],
        'db_username' => ['DB_USERNAME', 'MYSQL_USER'],
        'db_password' => ['DB_PASSWORD', 'MYSQL_PASSWORD'],
        'db_prefix' => ['DB_PREFIX'],
        'admin_username' => ['ADMIN_USERNAME'],
        'admin_password' => ['ADMIN_PASSWORD'],
    ];
    if (isset($osEnvMap[$key])) {
        foreach ($osEnvMap[$key] as $k) {
            $v = @getenv($k);
            if (!empty($v)) {
                return $v;
            }
        }
    }
    $envMap = [
        'db_host' => 'DB_HOST',
        'db_port' => 'DB_PORT',
        'db_name' => 'DB_DATABASE',
        'db_username' => 'DB_USERNAME',
        'db_password' => 'DB_PASSWORD',
        'db_prefix' => 'DB_PREFIX',
        'admin_username' => 'ADMIN_USERNAME',
        'admin_password' => 'ADMIN_PASSWORD',
    ];
    if (isset($envMap[$key])) {
        $v = env($envMap[$key], null);
        if (!is_null($v)) {
            return $v;
        }
    }
    return $default;
}

function response_json_error_quit($msg)
{
    response_json_quit(-1, $msg);
}

function response_json_quit($code, $msg, $data = null, $redirect = null)
{
    header('Content-type: application/json');
    echo json_encode([
        'code' => $code,
        'msg' => $msg,
        'data' => $data,
        'redirect' => $redirect,
    ]);
    exit();
}

function request_schema()
{
    $schema = 'http';
    if (is_https()) {
        $schema = 'https';
    }
    return $schema;
}

function request_domain()
{
    return $_SERVER['HTTP_HOST'];
}

function request_url()
{
    return request_schema() . '://' . request_domain();
}

function request_is_post()
{
    return !empty($_POST);
}

function is_https()
{
    if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
        return true;
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        return true;
    } elseif (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
        return true;
    }
    return false;
}

function request_post($k, $defaultValue = '')
{
    return isset($_POST[$k]) ? $_POST[$k] : $defaultValue;
}

function text_success($msg, $count = true)
{
    if ($count) {
        check_counter();
    }
    echo '<div class="ins-check is-ok">'
        . '<i class="iconfont icon-check" aria-hidden="true"></i>'
        . '<span class="ins-check-text">' . $msg . '</span>'
        . '</div>';
}

function text_error($msg, $solutionUrl = null, $count = true)
{
    if ($count) {
        error_counter();
        check_counter();
    }
    echo '<div class="ins-check is-error">'
        . '<i class="iconfont icon-close" aria-hidden="true"></i>'
        . '<span class="ins-check-text">' . $msg . '</span>'
        . ($solutionUrl ? '<a class="ins-check-link" target="_blank" rel="noopener" href="' . $solutionUrl . '">解决办法</a>' : '')
        . '</div>';
}

function text_warning($msg, $solutionUrl = null, $count = true)
{
    if ($count) {
        check_counter();
    }
    echo '<div class="ins-check is-warning">'
        . '<i class="iconfont icon-warning" aria-hidden="true"></i>'
        . '<span class="ins-check-text">' . $msg . '</span>'
        . ($solutionUrl ? '<a class="ins-check-link" target="_blank" rel="noopener" href="' . $solutionUrl . '">解决办法</a>' : '')
        . '</div>';
}

function error_counter($inc = 1)
{
    static $error = 0;
    $error += $inc;
    return $error;
}

function check_counter($inc = 1)
{
    static $count = 0;
    $count += $inc;
    return $count;
}

function env_writable()
{
    $file = APP_PATH . '/.env';
    if (!file_exists($file)) {
        if (false === file_put_contents($file, "")) {
            @unlink($file);
            return false;
        }
        @unlink($file);
        return true;
    }
    $content = @file_get_contents($file);
    if (false === file_put_contents($file, $content)) {
        return false;
    }
    return true;
}

function rewrite_check()
{
    if (file_exists(APP_PATH . '/storage/rewrite.skip')) {
        return ['code' => 0, 'msg' => 'ok'];
    }
    $domain = request_domain();
    $url = request_url() . '/install/ping';
    $ret = \ModStart\Core\Util\CurlUtil::get($url, [], [
        'timeout' => 3,
    ]);
    if ($ret['body'] === 'ok') {
        return ['code' => 0, 'msg' => ''];
    }
    $msgs = [];
    if (!empty($ret['error'])) {
        if (false !== strpos($ret['error'], 'Resolving timed out')) {
            $msgs[] = "- 域名 $domain 解析失败（可能您没有解析域名）";
            $msgs[] = "- 在服务器不能访问 $url ，需要在程序中通过改地址访问到程序";
        } else {
            $msgs[] = '- ERROR:' . $ret['error'];
        }
    }
    if (!empty($ret['code'])) {
        if (!empty($ret['body'])) {
            $msg = $ret['body'];
            $index = strpos($ret['body'], '<body>');
            if (false !== $index) {
                $msg = substr($msg, $index);
            }
            $index = strpos($ret['body'], '</body>');
            if (false !== $index) {
                $msg = substr($msg, 0, $index);
            }
            $msgs[] = '- 程序出错:' . preg_replace('/\\s+/', ' ', preg_replace('/<[^>]+>/', '', $msg));
        }
    }
    $msgs[] = "- 您还可以在浏览器访问 <a href='$url' target='_blank'>$url</a> 查看报错信息，调整配置保证测试页面提示“ok”字样";
    return ['code' => -1, 'msg' => 'Rewrite规则错误可能原因（仅供参考）：<br/>' . join('<br/>', $msgs)];
}

function env($key, $defaultValue = '')
{
    static $values = null;
    if (null === $values) {
        $values = \ModStart\Core\Util\EnvUtil::all(ENV_FILE_EXAMPLE);
    }
    return isset($values[$key]) ? $values[$key] : $defaultValue;
}

function is_dir_really_writable($dir)
{
    $dir = rtrim($dir, '/\\') . '/';
    $testFile = $dir . '.writable_test_file';
    @file_put_contents($testFile, 'test');
    if (!file_exists($testFile)) {
        return false;
    }
    if (file_get_contents($testFile) != 'test') {
        return false;
    }
    @unlink($testFile);
    return true;
}

/* ---------------------------------------------------------------------------
 * 环境检查增强：扩展能力探测 + 诊断信息 + PHP 8.5 专项
 *
 * 扩展检测不再只依赖“某个函数 / 类是否存在”这一条判断，原因是：
 * 1) 扩展可能已编译进 PHP（宝塔等面板的「安装扩展」里不会列出 curl / openssl /
 *    mbstring / zip / pdo_mysql 这类内置项，但它们确实在工作）；
 * 2) 函数可能被 php.ini 的 disable_functions 禁用，此时 function_exists() 同样返回
 *    false，而面板里明明写着“已安装”。
 * 两者都会造成“面板里有扩展、安装页却提示缺少”的误判，统一走 env_extension_state()，
 * 并在有异常时输出站点实际使用的 php.ini / 扩展目录等信息，便于客服定位。
 * ------------------------------------------------------------------------- */

/**
 * 当前 PHP 版本是否不低于指定版本
 * @param string $version 如 8.5
 * @return bool
 */
function php_version_at_least($version)
{
    return version_compare(PHP_VERSION, $version, '>=');
}

/**
 * 函数是否被 disable_functions 禁用
 * function_exists() 对被禁用函数返回 false，这里通过 php.ini 的 disable_functions 反向确认
 * （get_defined_functions() 各版本都会排除被禁用函数，靠它判断并不可靠）。
 * @param string $function
 * @return bool
 */
function env_function_disabled($function)
{
    if (function_exists($function)) {
        return false;
    }
    $disabled = ini_get('disable_functions');
    if (empty($disabled)) {
        return false;
    }
    $disabled = array_map('trim', explode(',', strtolower($disabled)));
    return in_array(strtolower($function), $disabled, true);
}

/**
 * 探测扩展状态
 * @param string $extension extension_loaded() 使用的扩展名
 * @param array $probes 代表性函数名 / 类名，任意一个可用即认为扩展可用
 * @return array state：ok | disabled（已加载但函数被禁用） | missing；probe：探测点
 */
function env_extension_state($extension, $probes = array())
{
    $entry = isset($probes[0]) ? $probes[0] : $extension;
    foreach ($probes as $probe) {
        if (function_exists($probe) || class_exists($probe)) {
            return array('state' => 'ok', 'probe' => $probe);
        }
    }
    if (extension_loaded($extension)) {
        return array('state' => 'disabled', 'probe' => $entry);
    }
    foreach ($probes as $probe) {
        if (env_function_disabled($probe)) {
            return array('state' => 'disabled', 'probe' => $probe);
        }
    }
    return array('state' => 'missing', 'probe' => $entry);
}

/**
 * 扩展异常项收集器（供页面底部「环境诊断」使用）
 * @return array
 */
function &env_diag_store()
{
    static $list = array();
    return $list;
}

/**
 * 记录一条扩展异常项
 * @param string $extension
 * @param string $label
 * @param string $state disabled | missing
 * @param string $probe
 * @return void
 */
function env_diag_push($extension, $label, $state, $probe = '')
{
    $store = &env_diag_store();
    $store[] = array(
        'extension' => $extension,
        'label' => $label,
        'state' => $state,
        'probe' => $probe,
    );
}

/**
 * 渲染单个扩展检查项
 * @param string $label 展示名称，如 PHP curl 扩展
 * @param string $extension 扩展名，如 curl
 * @param array $probes 代表性函数名 / 类名
 * @param bool $optional 选填扩展（缺失只提示，不阻塞安装）
 * @return void
 */
function env_check_extension($label, $extension, $probes = array(), $optional = false)
{
    $ret = env_extension_state($extension, $probes);
    if ('ok' === $ret['state']) {
        text_success($label);
        return;
    }
    env_diag_push($extension, $label, $ret['state'], $ret['probe']);
    if ('disabled' === $ret['state']) {
        $msg = $label . ' 已加载，但 ' . $ret['probe'] . ' 不可用（多被 disable_functions 禁用）';
        $optional ? text_warning($msg) : text_error($msg);
        return;
    }
    $optional ? text_warning('缺少 ' . $label) : text_error('缺少 ' . $label);
}

/**
 * PDO mysql 驱动检测
 * @return void
 */
function env_check_pdo_mysql()
{
    $drivers = array();
    if (class_exists('PDO')) {
        $drivers = PDO::getAvailableDrivers();
    }
    if (in_array('mysql', $drivers)) {
        text_success('PHP PDO mysql 驱动');
        return;
    }
    env_diag_push('pdo_mysql', 'PHP PDO mysql 驱动', extension_loaded('pdo_mysql') ? 'disabled' : 'missing', 'pdo_mysql');
    text_error('缺少 PHP PDO mysql 驱动');
}

/**
 * PHP 8.5 专项检查
 * PHP 8.5 与之前的版本在环境影响上有几处明确差异，需要在安装页单独给出结论：
 * - opcache 自 PHP 8.5 起已内置（不再作为扩展安装，也无法在面板里单独安装）；
 * - disable_classes 配置项自 PHP 8.5 起被移除，配置后不生效；
 * - curl_close / finfo_close / imagedestroy 自 PHP 8.5 起弃用，依赖内置兼容层。
 * @return void
 */
function env_check_php85()
{
    if (function_exists('opcache_get_status')) {
        text_success('PHP opcache 已启用');
    } else {
        text_warning('PHP 8.5 已内置 opcache，当前未启用（建议 php.ini 开启 opcache.enable=1）');
    }
    $disableClasses = ini_get('disable_classes');
    $disableClasses = false === $disableClasses ? '' : trim($disableClasses);
    if ('' === $disableClasses) {
        text_success('PHP disable_classes 未配置');
    } else {
        text_warning('PHP 8.5 已移除 disable_classes 配置，当前配置不生效：' . $disableClasses);
    }
    if (file_exists(APP_PATH . '/vendor/modstart/modstart/src/Core/Compat/Runtime.php')) {
        text_success('PHP 8.5 兼容层');
    } else {
        text_warning('缺少 PHP 8.5 兼容层文件（vendor/modstart/modstart/src/Core/Compat/Runtime.php），请确认程序文件完整');
    }
}

/**
 * 是否为宝塔面板环境（用于给出面板内的操作路径）
 * @return bool
 */
function env_is_baota()
{
    return file_exists('/www/server/panel') || file_exists('C:/BtSoft') || file_exists('D:/BtSoft');
}

/**
 * 是否为 Windows 环境
 * @return bool
 */
function env_is_windows()
{
    return 0 === strpos(strtoupper(PHP_OS), 'WIN');
}

/**
 * 生成单个异常扩展的修复指引（HTML）
 * @param array $item
 * @return string
 */
function env_extension_guide($item)
{
    $name = htmlspecialchars($item['extension'], ENT_QUOTES, 'UTF-8');
    $probe = htmlspecialchars($item['probe'], ENT_QUOTES, 'UTF-8');
    if ('disabled' === $item['state']) {
        return '扩展已加载，但 <code class="ins-code">' . $probe . '</code> 不可用：多为 php.ini 的 '
            . '<code class="ins-code">disable_functions</code> 把相关函数禁用（'
            . (env_is_baota() ? '宝塔「PHP 设置 → 禁用函数」中删除该函数，或' : '')
            . '从 php.ini 的 disable_functions 中移除），保存后重启 PHP。';
    }
    $tips = array();
    if (in_array($item['extension'], array('pdo', 'tokenizer', 'json', 'ctype', 'filter', 'hash', 'session', 'spl', 'random', 'zlib'), true)) {
        return '「' . $name . '」属于 PHP 内置扩展（编译时启用），面板「安装扩展」里不会单独提供：'
            . '请确认 PHP 编译参数包含它，或直接更换一个包含该扩展的 PHP 版本。';
    }
    if ('zip' === $item['extension'] && php_version_at_least('8.4') && env_is_baota()) {
        $tips[] = '宝塔 PHP 8.4 / 8.5 的「极速安装」包编译参数可能未包含 <code class="ins-code">--enable-zip</code>，'
            . '面板「安装扩展」里也找不到 zip：卸载该 PHP 后改用「编译安装」，在「添加自定义模块」中填写 '
            . '<code class="ins-code">--enable-zip --with-zlib --with-openssl</code> 重新编译，或先切换到 PHP 8.1 / 8.2 等成熟版本。';
    }
    if (env_is_windows()) {
        $tips[] = 'Windows 在 php.ini 中启用 <code class="ins-code">extension=php_' . $name . '.dll</code>'
            . '（PHP 7.2+ 也可写 <code class="ins-code">extension=' . $name . '</code>），'
            . '并确认 <code class="ins-code">extension_dir</code> 指向 PHP 的 ext 目录。';
    } else {
        $tips[] = '在 php.ini 中启用 <code class="ins-code">extension=' . $name . '.so</code>。';
    }
    if (env_is_baota()) {
        $tips[] = '宝塔：软件商店 → 已安装 → 当前 PHP 版本 → 设置，在「安装扩展」中安装、在「配置文件」中确认 extension 已启用。';
    }
    return join(' ', $tips);
}

/**
 * 渲染「环境诊断」面板，没有任何异常时返回空字符串
 * @return string
 */
function env_diag_render()
{
    $list = env_diag_store();
    if (empty($list)) {
        return '';
    }
    $h = function ($v) {
        return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
    };
    $loaded = get_loaded_extensions();
    sort($loaded);
    $iniFile = php_ini_loaded_file();
    $scanned = php_ini_scanned_files();
    $rows = array(
        'PHP 版本' => PHP_VERSION . '（' . php_sapi_name() . '，' . (defined('PHP_ZTS') && PHP_ZTS ? 'TS' : 'NTS') . '，' . (PHP_INT_SIZE * 8) . ' 位）',
        '操作系统' => $h(PHP_OS) . ' ' . $h(php_uname('r')),
        'php.ini' => $iniFile ? $h($iniFile) : '未加载 php.ini（请检查 PHP 是否使用了配置文件）',
        '附加 ini' => $scanned ? $h(str_replace(array("\r", "\n", "\t"), ' ', $scanned)) : '无',
        'extension_dir' => $h((string)ini_get('extension_dir')),
        '已加载扩展' => $h(join(', ', $loaded)),
    );
    $html = '<div class="ins-diag">';
    $html .= '<div class="ins-diag-head"><i class="iconfont icon-info" aria-hidden="true"></i> 环境诊断与修复指引</div>';
    $html .= '<ul class="ins-diag-list">';
    foreach ($list as $item) {
        $html .= '<li><strong>' . $h($item['label']) . '</strong>：' . env_extension_guide($item) . '</li>';
    }
    $html .= '</ul>';
    $html .= '<div class="ins-diag-note">修改 php.ini 后必须重启 PHP（PHP-FPM / Apache）才会生效，可用 '
        . '<code class="ins-code">php -m</code> 或 phpinfo 确认扩展已经在列表中；Debian / Ubuntu 也可安装 '
        . '<code class="ins-code">php' . PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . '-扩展名</code> 包。'
        . '若面板里已安装扩展、安装页仍提示缺少，请展开下方信息核对站点实际使用的 PHP 版本与 php.ini，'
        . '多版本共存时站点可能运行在另一个 PHP 上。</div>';
    $html .= '<details class="ins-diag-more"><summary>查看站点实际使用的 PHP 信息（排查 php.ini / 版本不一致时使用）</summary><dl>';
    foreach ($rows as $label => $value) {
        $html .= '<dt>' . $h($label) . '</dt><dd>' . $value . '</dd>';
    }
    $html .= '</dl></details>';
    $html .= '</div>';
    return $html;
}
