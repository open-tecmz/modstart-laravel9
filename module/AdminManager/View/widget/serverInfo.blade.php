<div class="ub-panel ub-cover ub-server-info">
    <div class="head">
        <div class="title">
            <i class="iconfont icon-desktop"></i>
            服务器信息
        </div>
    </div>
    <div class="body">
<?php
// Friendly names for values that are not readable to end users
$serverInfoOs = PHP_OS;
if ($serverInfoOs == 'Darwin') {
    $serverInfoOs = 'macOS（Darwin）';
} elseif ($serverInfoOs == 'WINNT') {
    $serverInfoOs = 'Windows';
}
$serverInfoRuntime = PHP_SAPI;
if (PHP_SAPI == 'fpm-fcgi') {
    $serverInfoRuntime = 'Nginx（FPM）';
} elseif (PHP_SAPI == 'cgi-fcgi') {
    $serverInfoRuntime = 'Nginx（FastCGI）';
} elseif (PHP_SAPI == 'apache2handler') {
    $serverInfoRuntime = 'Apache';
} elseif (PHP_SAPI == 'cli-server') {
    $serverInfoRuntime = 'PHP 内置服务器';
}
$serverInfoFramework = strtolower(ModStart\ModStart::env());
if (strpos($serverInfoFramework, 'laravel') === 0) {
    $serverInfoFramework = 'Laravel ' . substr($serverInfoFramework, 7);
} else {
    $serverInfoFramework = strtoupper($serverInfoFramework);
}
$serverInfoKernel = 'ModStart v' . ModStart\ModStart::$version;
if (!empty($serverInfoFramework)) {
    $serverInfoKernel .= ' · ' . $serverInfoFramework;
}
// Distribution builds append a long suffix to PHP_VERSION (e.g. 5.6.40-105+0~...),
// keep only the semantic version in the main line and show the full string as a
// secondary line on narrow screens (where the title attribute is not reachable).
$serverInfoPhpVersion = PHP_VERSION;
$serverInfoPhpVersionFull = PHP_VERSION;
if (($serverInfoPhpVersionPos = strpos($serverInfoPhpVersion, '-')) !== false) {
    $serverInfoPhpVersion = substr($serverInfoPhpVersion, 0, $serverInfoPhpVersionPos);
}
if (($serverInfoPhpVersionPos = strpos($serverInfoPhpVersion, '+')) !== false) {
    $serverInfoPhpVersion = substr($serverInfoPhpVersion, 0, $serverInfoPhpVersionPos);
}
// Only distributions with a build suffix need the secondary line
$serverInfoPhpVersionSub = $serverInfoPhpVersion !== $serverInfoPhpVersionFull ? $serverInfoPhpVersionFull : '';
$serverInfoPhpVersion = 'v' . $serverInfoPhpVersion . ' · ' . (PHP_INT_SIZE == 8 ? '64' : '32') . ' 位';
// Raw php.ini values are not readable: "-1" means "no limit" to PHP only
$serverInfoMemory = @ini_get('memory_limit');
$serverInfoMemoryDisplay = ($serverInfoMemory === '-1' || $serverInfoMemory === '-1M')
    ? '不限制'
    : $serverInfoMemory;
// Items are laid out row-first in two columns so every pair sits on the same
// baseline; the last item spans the full row instead of leaving an empty cell.
$serverInfoItems = array(
    array('icon' => 'icon-cube', 'label' => '系统内核', 'value' => $serverInfoKernel),
    array('icon' => 'icon-desktop', 'label' => '操作系统', 'value' => $serverInfoOs),
    array('icon' => 'icon-pc', 'label' => '运行环境', 'value' => $serverInfoRuntime),
    array('icon' => 'icon-code', 'label' => 'PHP 版本', 'value' => $serverInfoPhpVersion, 'valueTitle' => $serverInfoPhpVersionFull, 'valueSub' => $serverInfoPhpVersionSub),
    array('icon' => 'icon-tools', 'label' => '内存限制', 'value' => $serverInfoMemoryDisplay, 'valueTitle' => $serverInfoMemory),
    array('icon' => 'icon-language', 'label' => '服务器时区', 'value' => date_default_timezone_get()),
    array('icon' => 'icon-time', 'label' => '服务器时间', 'value' => date('Y-m-d H:i:s'), 'valueAttr' => 'data-server-time', 'wide' => true),
);
// Friendly labels instead of raw php.ini keys
$serverInfoParams = array(
    array('label' => '上传大小', 'key' => 'upload_max_filesize'),
    array('label' => 'POST 大小', 'key' => 'post_max_size'),
    array('label' => '上传文件数', 'key' => 'max_file_uploads'),
);
?>
        <div class="ub-server-info-row ub-server-info-notice">
            <div class="ub-server-info-label">
                <i class="iconfont icon-shield-check ub-text-muted"></i>
                安全公告
            </div>
            <div class="ub-server-info-value" data-system-notice>
                <span class="ub-server-info-state">
                    <i class="iconfont icon-shield-check"></i>
                    暂无公告
                </span>
            </div>
        </div>
        <div class="ub-server-info-grid">
            @foreach($serverInfoItems as $serverInfoItem)
                <div class="ub-server-info-row @if(!empty($serverInfoItem['wide'])) ub-server-info-row-wide @endif">
                    <div class="ub-server-info-label">
                        <i class="iconfont {{$serverInfoItem['icon']}} ub-text-muted"></i>
                        {{$serverInfoItem['label']}}
                    </div>
                    <div class="ub-server-info-value" {{ isset($serverInfoItem['valueAttr']) ? $serverInfoItem['valueAttr'] : '' }} @if(!empty($serverInfoItem['valueTitle'])) title="{{$serverInfoItem['valueTitle']}}" @endif>
                        <span class="ub-server-info-text">{{$serverInfoItem['value']}}</span>
                        @if(!empty($serverInfoItem['valueSub']))
                            <span class="ub-server-info-sub">{{$serverInfoItem['valueSub']}}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <div class="ub-server-info-row ub-server-info-params">
            <div class="ub-server-info-label">
                <i class="iconfont icon-list ub-text-muted"></i>
                服务器参数
            </div>
            <div class="ub-server-info-tags">
                @foreach($serverInfoParams as $serverInfoParam)
                    <span class="ub-tag ub-server-info-tag">
                        <span class="ub-server-info-tag-label">{{$serverInfoParam['label']}}</span>
                        <b class="ub-server-info-tag-value">{{@ini_get($serverInfoParam['key'])}}</b>
                    </span>
                @endforeach
            </div>
        </div>
        <script type="text/javascript">
            // 请勿删除，用于获取最新的安全通告（比如框架、模块有重大缺陷的应急通知等）
            $('body').append('<script src="https://modstart.com/api/modstart/notice?modules={{urlencode($modules)}}&t={{date('YmdH')}}"><' + '/script>');
            $(function () {
                var $serverTime = $('[data-server-time]');
                var timeDiff = {{time()*1000}} - (new Date()).getTime();
                function serverInfoPad(n) {
                    return n < 10 ? '0' + n : '' + n;
                }
                function serverInfoFormat(date) {
                    return date.getFullYear() + '-' + serverInfoPad(date.getMonth() + 1) + '-' + serverInfoPad(date.getDate())
                        + ' ' + serverInfoPad(date.getHours()) + ':' + serverInfoPad(date.getMinutes()) + ':' + serverInfoPad(date.getSeconds());
                }
                function serverInfoTick() {
                    $serverTime.text(serverInfoFormat(new Date((new Date()).getTime() + timeDiff)));
                }
                serverInfoTick();
                setInterval(serverInfoTick, 1000);
            });
        </script>
    </div>
</div>
<style>
    .ub-server-info-notice {
        border-bottom: 0.0625rem solid var(--color-body-line);
        margin-bottom: 0.25rem;
        padding-bottom: 0.5rem;
    }

    .ub-server-info-state {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        color: var(--color-tertiary);
    }

    .ub-server-info-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 0 1.5rem;
    }

    @media (min-width: 64rem) {
        .ub-server-info-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        /* Odd item count: the last one takes the whole row so no cell is left
           empty and every pair keeps the same baseline */
        .ub-server-info-row-wide {
            grid-column: 1 / -1;
        }
    }

    .ub-server-info-row {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        padding: 0.25rem 0;
    }

    .ub-server-info-label {
        flex: 0 0 7rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--color-heading);
        font-weight: 500;
        line-height: 1.5;
    }

    .ub-server-info-value {
        flex: 1 1 auto;
        min-width: 0;
        color: var(--color-text);
        line-height: 1.5;
        font-variant-numeric: tabular-nums;
        overflow-wrap: break-word;
        word-break: break-word;
    }

    /* Full version string (e.g. 8.5.12-105+0~...) only shows on narrow screens,
       where there is no title tooltip to hover */
    .ub-server-info-sub {
        display: none;
        margin-top: 0.125rem;
        color: var(--color-tertiary);
        font-size: 0.75rem;
        overflow-wrap: break-word;
        word-break: break-all;
    }

    .ub-server-info-tags {
        flex: 1 1 auto;
        min-width: 0;
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .ub-server-info-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .ub-server-info-tag-value {
        color: var(--color-heading);
    }

    /* Narrow screens: parameter tags share the row (2 per line) instead of
       stacking one per line under a squeezed label column */
    @media (max-width: 47.9375rem) {
        .ub-server-info-sub {
            display: block;
        }

        .ub-server-info-params {
            flex-direction: column;
            gap: 0.375rem;
        }

        .ub-server-info-params .ub-server-info-label {
            flex: none;
        }

        .ub-server-info-tags {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            width: 100%;
        }

        .ub-server-info-tag {
            justify-content: space-between;
        }
    }
</style>
