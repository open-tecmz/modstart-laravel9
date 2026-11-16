<div class="row ub-welcome-info">
    <div class="col-md-6">
        <div class="ub-panel ub-welcome-info-system">
            <div class="head">
                <div class="title">
                    <i class="iconfont icon-file"></i>
                    系统信息
                </div>
            </div>
            <div class="body">
                <div class="ub-welcome-info-line">
                    <span class="ub-welcome-info-label">当前版本</span>
                    <span class="ub-welcome-info-name">{{\App\Constant\AppConstant::APP_NAME}}</span>
                    <span class="ub-welcome-info-version">V{{\App\Constant\AppConstant::VERSION}}</span>
                </div>
                @if(!config('modstart.admin.versionCheckDisable',false))
                    <div data-admin-version class="ub-welcome-info-notice"></div>
                @endif
                <div data-module-notice class="tw-hidden ub-welcome-info-notice"></div>
                <script type="text/javascript">
                    // 最新版本检测：由 modstart.com 返回脚本填充上面的 data-admin-version / data-module-notice
                    $('body').append('<script src="https://modstart.com/api/app/version_check?app={{\App\Constant\AppConstant::APP}}&version={{\App\Constant\AppConstant::VERSION}}"><' + '/script>');
                </script>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="ub-panel ub-welcome-info-copyright">
            <div class="head">
                <div class="title">
                    <i class="iconfont icon-corp"></i>
                    版权说明
                </div>
            </div>
            <div class="body">
                <div class="ub-copyright-box">
                    <div class="ub-copyright-text">
                        <div class="tw-mb-2">
                            使用遇到问题请
                            <a href="javascript:;" data-dialog-title="工单反馈" data-dialog-height="95%"
                               data-dialog-request="https://modstart.com/feedback_ticket">
                                <i class="iconfont icon-description"></i>
                                提交工单
                            </a>
                            反馈给我们
                        </div>
                        <div class="tw-mb-2">
                            本软件基于 Apache 2.0 协议开源，支持免费商用
                        </div>
                        <div>
                            在使用过程中请保留版权，如需定制请
                            <a href="https://www.tecmz.com/product/{{\App\Constant\AppConstant::APP}}"
                               target="_blank"><i class="iconfont icon-call"></i> 联系我们</a>
                        </div>
                    </div>
                    <div class="ub-copyright-qr">
                        <div class="ub-copyright-qr-title">
                            <i class="iconfont icon-customer"></i>
                            专属客服
                        </div>
                        <div class="tw-cursor-pointer" data-image-preview="https://modstart.com/code_dynamic/modstart_wx">
                            <img class="ub-copyright-qr-img" alt="专属客服二维码" loading="lazy"
                                 src="https://modstart.com/code_dynamic/modstart_wx"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    /* Two panels side by side must share one height: the row is already a flex
       container, so make each column a flex box as well and let the panel
       stretch to the tallest one instead of leaving a visible step under it. */
    .ub-welcome-info > .col-md-6 {
        display: flex;
    }

    .ub-welcome-info > .col-md-6 > .ub-panel {
        display: flex;
        flex-direction: column;
        width: 100%;
    }

    .ub-welcome-info > .col-md-6 > .ub-panel > .body {
        flex: 1 1 auto;
    }

    /* Version line and upgrade notice stay vertically centred inside the shared
       height instead of being pushed to the two edges */
    .ub-welcome-info-system > .body {
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 0.5rem;
    }

    .ub-welcome-info-line {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem;
        line-height: 1.5;
    }

    .ub-welcome-info-label {
        color: var(--color-tertiary);
    }

    .ub-welcome-info-name {
        color: var(--color-heading);
        font-weight: 600;
        overflow-wrap: break-word;
        word-break: break-word;
    }

    /* Version reads as metadata, not as part of the product name */
    .ub-welcome-info-version {
        display: inline-flex;
        align-items: center;
        padding: 0.0625rem 0.375rem;
        border-radius: var(--size-radius-sm);
        background: var(--color-body-block-bg);
        color: var(--color-body-block);
        font-size: 0.75rem;
        font-weight: 500;
        line-height: 1.5;
        font-variant-numeric: tabular-nums;
    }

    .ub-welcome-info-notice {
        margin-top: 0.5rem;
        line-height: 1.6;
    }

    .ub-welcome-info-notice:empty {
        display: none;
    }

    /* Two columns instead of a floated QR code: the card keeps its own height
       and the QR no longer overlaps the panel title separator. */
    .ub-copyright-box {
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }

    .ub-copyright-text {
        flex: 1 1 auto;
        min-width: 0;
        line-height: 1.6;
        overflow-wrap: break-word;
        word-break: break-word;
    }

    .ub-copyright-qr {
        flex: 0 0 auto;
        padding: 0.375rem;
        border: 0.0625rem solid var(--color-body-line);
        border-radius: var(--size-radius);
        text-align: center;
    }

    .ub-copyright-qr-title {
        margin-bottom: 0.25rem;
        color: var(--color-heading);
        font-size: 0.75rem;
        font-weight: 600;
        line-height: 1.5;
    }

    .ub-copyright-qr-img {
        display: block;
        width: 5.5rem;
        height: 5.5rem;
        border-radius: var(--size-radius-sm);
        background: var(--color-body-block-bg);
        object-fit: contain;
    }

    /* Narrow screens: stack, and read the text before the QR code */
    @media (max-width: 47.9375rem) {
        .ub-copyright-box {
            flex-direction: column;
            align-items: stretch;
            gap: 0.75rem;
        }

        .ub-copyright-qr {
            align-self: center;
        }

        .ub-copyright-qr-img {
            width: 5rem;
            height: 5rem;
        }
    }
</style>
