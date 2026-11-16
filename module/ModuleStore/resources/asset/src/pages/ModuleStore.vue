<template>
    <div>
        <div v-if="storeConfig.disable" class="ub-alert danger">
            <i class="iconfont icon-close-o" aria-hidden="true"></i>
            当前环境禁止「模块管理」相关操作
        </div>
        <div v-if="safetyTipVisible" class="ub-alert warning pb-alert-dismissible">
            <i class="iconfont icon-warning" aria-hidden="true"></i>
            <span>为了系统和数据安全，在线 <b>安装</b>、<b>卸载</b>、<b>升级</b> 模块前请做好代码和数据备份</span>
            <a href="javascript:;" class="pb-alert-dismiss" @click="doSafetyTipDismiss()">不再提示</a>
        </div>
        <div class="tw-bg-white tw-rounded tw-relative">
            <div class="pb-store-head">
                <el-tabs v-model="search.tab" class="pb-store-tabs">
                    <el-tab-pane name="store">
                        <span slot="label">
                            <i class="iconfont icon-cart" aria-hidden="true"></i> 模块市场
                        </span>
                    </el-tab-pane>
                    <el-tab-pane name="installed">
                        <span slot="label">
                            <i class="iconfont icon-list-alt" aria-hidden="true"></i> 已安装
                        </span>
                    </el-tab-pane>
                    <el-tab-pane name="enabled">
                        <span slot="label">
                            <i class="iconfont icon-checked" aria-hidden="true"></i> 已启用
                        </span>
                    </el-tab-pane>
                    <el-tab-pane name="disabled">
                        <span slot="label">
                            <i class="iconfont icon-close" aria-hidden="true"></i> 已禁用
                        </span>
                    </el-tab-pane>
                    <el-tab-pane name="local">
                        <span slot="label">
                            <i class="iconfont icon-pc" aria-hidden="true"></i> 本地模块
                            <i class="iconfont icon-warning" data-tip-popover="本地存在且模块市场不存在的模块"
                               aria-hidden="true"></i>
                        </span>
                    </el-tab-pane>
                    <el-tab-pane name="upgradeable">
                        <span slot="label">
                            <i class="iconfont icon-direction-up" aria-hidden="true"></i> 可升级
                        </span>
                    </el-tab-pane>
                    <el-tab-pane name="system">
                        <span slot="label">
                            <i class="iconfont icon-cog" aria-hidden="true"></i> 内置模块
                        </span>
                    </el-tab-pane>
                </el-tabs>
                <div class="pb-store-head-extra">
                    <el-button size="mini" round :loading="memberUserLoading" @click="doMemberLoginShow()">
                        <span v-if="memberUserLoading">
                            登录中
                        </span>
                        <span v-else-if="memberUser.id>0">
                            <span v-if="memberUser.avatar" class="pb-inline-avatar"
                                  :style="{backgroundImage:`url(${memberUser.avatar})`}"
                                  aria-hidden="true"></span>
                            <i v-else class="iconfont icon-user" aria-hidden="true"></i>
                            {{ memberUser.username }}
                        </span>
                        <span v-else>
                            <i class="iconfont icon-user" aria-hidden="true"></i>
                            未登录
                        </span>
                    </el-button>
                </div>
            </div>
            <div class="pb-store-toolbar">
                <div class="pb-store-toolbar-left" v-if="search.tab!=='system'">
                    <!-- price filter shares the chip look with the filters below,
                         so the toolbar never mixes two different control shapes -->
                    <div class="pb-price-chips" role="radiogroup" aria-label="价格类型">
                        <button type="button" class="pb-chip pb-chip--price" role="radio"
                                :class="{'pb-chip--active':search.priceType==='all'}"
                                :aria-checked="search.priceType==='all'?'true':'false'"
                                @click="search.priceType='all'">
                            <i class="iconfont icon-list-alt" aria-hidden="true"></i> 全部
                        </button>
                        <button type="button" class="pb-chip pb-chip--price" role="radio"
                                :class="{'pb-chip--active':search.priceType==='free'}"
                                :aria-checked="search.priceType==='free'?'true':'false'"
                                @click="search.priceType='free'">
                            <i class="iconfont icon-gift" aria-hidden="true"></i> 免费
                        </button>
                        <button type="button" class="pb-chip pb-chip--price" role="radio"
                                :class="{'pb-chip--active':search.priceType==='fee'}"
                                :aria-checked="search.priceType==='fee'?'true':'false'"
                                @click="search.priceType='fee'">
                            <i class="iconfont icon-cny" aria-hidden="true"></i> 付费
                        </button>
                    </div>
                    <button type="button" class="pb-chip pb-chip--recommend"
                            :class="{'pb-chip--active':search.isRecommend}"
                            :aria-pressed="search.isRecommend?'true':'false'"
                            @click="search.isRecommend=!search.isRecommend">
                        <i class="iconfont icon-star" aria-hidden="true"></i> 推荐
                    </button>
                </div>
                <div class="pb-store-toolbar-left" v-else>
                    <span class="pb-store-toolbar-hint">内置模块随系统发布，仅支持配置和升级</span>
                </div>
                <div class="pb-store-toolbar-right">
                    <el-select v-model="search.sort" size="small" class="pb-sort-select"
                               v-if="search.tab!=='system'">
                        <el-option label="默认排序" value="default"></el-option>
                        <el-option label="价格从低到高" value="priceAsc"></el-option>
                        <el-option label="价格从高到低" value="priceDesc"></el-option>
                        <el-option label="名称" value="name"></el-option>
                    </el-select>
                    <el-input prefix-icon="el-icon-search"
                              clearable
                              v-model="search.keywords"
                              size="small"
                              class="pb-search-input"
                              placeholder="搜索模块"></el-input>
                </div>
            </div>
            <div class="pb-store-filters" v-if="search.tab!=='system'">
                <div class="pb-filter-row pb-filter-row--main">
                    <div class="pb-filter-group" v-if="topCategories.length>0">
                        <span class="pb-filter-label">
                            <i class="iconfont icon-category" aria-hidden="true"></i>分类
                        </span>
                        <div class="pb-filter-chips">
                            <button type="button" class="pb-chip"
                                    :class="{'pb-chip--active':!search.categoryPid}"
                                    :aria-pressed="!search.categoryPid?'true':'false'"
                                    @click="doCategorySelect(0)">全部分类</button>
                            <button type="button" class="pb-chip"
                                    v-for="(cat,catIndex) in visibleCategories" :key="'cat'+catIndex"
                                    :class="{'pb-chip--active':search.categoryPid===cat.id}"
                                    :aria-pressed="search.categoryPid===cat.id?'true':'false'"
                                    @click="doCategorySelect(cat.id)">
                                {{ cat.title }}
                                <span class="pb-chip-count" v-if="cat.count">{{ cat.count }}</span>
                            </button>
                            <button type="button" class="pb-chip pb-chip--more"
                                    v-if="topCategories.length>categoryLimit"
                                    @click="categoryExpand=!categoryExpand">
                                {{ categoryExpand ? '收起' : '更多 ' + (topCategories.length - categoryLimit) }}
                                <i class="iconfont" :class="categoryExpand?'icon-up':'icon-down'" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                    <div class="pb-filter-group" v-if="types.length>0">
                        <span class="pb-filter-label">
                            <i class="iconfont icon-desktop" aria-hidden="true"></i>类型
                        </span>
                        <div class="pb-filter-chips">
                            <button type="button" class="pb-chip"
                                    :class="{'pb-chip--active':!search.type}"
                                    :aria-pressed="!search.type?'true':'false'"
                                    @click="search.type=''">全部类型</button>
                            <button type="button" class="pb-chip"
                                    v-for="(type,typeIndex) in types" :key="'type'+typeIndex"
                                    :class="{'pb-chip--active':search.type===type.value}"
                                    :aria-pressed="search.type===type.value?'true':'false'"
                                    @click="search.type=type.value">
                                {{ type.title }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="pb-filter-row pb-filter-row--sub" v-if="childCategories.length>0">
                    <span class="pb-filter-label">
                        <i class="iconfont icon-list-alt" aria-hidden="true"></i>细分
                    </span>
                    <div class="pb-filter-chips">
                        <button type="button" class="pb-chip pb-chip--sub"
                                :class="{'pb-chip--active':!search.categoryId}"
                                :aria-pressed="!search.categoryId?'true':'false'"
                                @click="search.categoryId=0">全部细分</button>
                        <button type="button" class="pb-chip pb-chip--sub"
                                v-for="(child,childIndex) in childCategories" :key="'sub'+childIndex"
                                :class="{'pb-chip--active':search.categoryId===child.id}"
                                :aria-pressed="search.categoryId===child.id?'true':'false'"
                                @click="search.categoryId=child.id">
                            {{ child.title }}
                            <span class="pb-chip-count" v-if="child.count">{{ child.count }}</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="pb-result-bar" v-if="!loading">
                <span class="pb-result-count">共 <b>{{ total }}</b> 个模块</span>
                <span v-if="search.tab==='upgradeable' && total>0" class="pb-result-hint">按需升级，升级前请先备份</span>
                <div class="pb-result-tags" v-if="activeFilters.length>0">
                    <button type="button" class="pb-result-tag"
                            v-for="filter in activeFilters" :key="'filter-'+filter.key"
                            :title="'移除 ' + filter.label"
                            @click="doRemoveFilter(filter)">
                        {{ filter.label }}
                        <i class="iconfont icon-close" aria-hidden="true"></i>
                    </button>
                </div>
                <a href="javascript:;" class="pb-result-clear" v-if="hasFilter" @click="doSearchReset()">清除筛选</a>
            </div>
            <div class="pb-module-grid" v-if="loading">
                <div v-for="n in 6" :key="'skeleton'+n" class="pb-module-card pb-module-card--skeleton">
                    <div class="pb-card-main">
                        <div class="pb-card-cover pb-skeleton-block"></div>
                        <div class="pb-card-info">
                            <div class="pb-skeleton-line" style="width:55%"></div>
                            <div class="pb-skeleton-line" style="width:92%"></div>
                            <div class="pb-skeleton-line" style="width:70%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ub-empty" v-else-if="!total">
                <div class="icon">
                    <i class="iconfont icon-empty-box" aria-hidden="true"></i>
                </div>
                <div class="text">{{ hasFilter ? '没有符合筛选条件的模块' : '暂无记录' }}</div>
                <div class="tw-mt-3" v-if="hasFilter">
                    <el-button size="mini" @click="doSearchReset()">清除筛选条件</el-button>
                </div>
            </div>
            <div class="pb-module-grid" v-else>
                <div v-for="module in pagedModules" :key="module.name"
                     class="pb-module-card"
                     :class="{
                        'pb-module-card--disabled':module._isInstalled && !module._isEnabled && !module._isSystem,
                        'pb-module-card--static':!isCardClickable(module)
                     }"
                     :role="isCardClickable(module)?'button':null"
                     :tabindex="isCardClickable(module)?0:null"
                     @click="doCardClick($event,module)"
                     @keydown.enter.self="doCardClick($event,module)">
                    <div class="pb-card-main">
                        <div class="pb-card-cover">
                            <span class="pb-badge" v-if="moduleStatus(module)"
                                  :class="'pb-badge--' + moduleStatus(module).type">{{ moduleStatus(module).text }}</span>
                            <span class="pb-price" v-if="!module._isSystem"
                                  :class="{'pb-price--free':!module.isFee}">{{ modulePriceText(module) }}</span>
                            <img v-if="module.cover" class="pb-card-cover-img" :src="module.cover"
                                 :alt="module.title" loading="lazy" @error="doCoverError(module)"/>
                            <div v-else class="pb-card-cover-placeholder"
                                 :style="{background:coverColor(module.name)}">
                                <i class="iconfont icon-cube" aria-hidden="true"></i>
                            </div>
                            <span class="pb-card-cover-hint" v-if="isCardClickable(module)">
                                查看详情 <i class="iconfont icon-angle-right" aria-hidden="true"></i>
                            </span>
                        </div>
                        <div class="pb-card-info">
                            <div class="pb-card-title-row">
                                <a class="pb-card-title"
                                   :href="module._isSystem?'javascript:;':module.url"
                                   :target="module._isSystem?'':'_blank'"
                                   :class="{'tw-cursor-default':module._isSystem}"
                                   :title="module.title">
                                    <span v-html="$highlight(module.title,search.keywords)"></span>
                                </a>
                            </div>
                            <div class="pb-card-desc" :title="module.description"
                                 v-html="$highlight(module.description,search.keywords)"></div>
                            <div class="pb-card-meta">
                                <template v-if="module._isLocal && !module._isInstalled">
                                    <span>本地 v{{ module._localVersion }}</span>
                                    <span class="pb-meta-dot">·</span>
                                    <span>未安装</span>
                                </template>
                                <template v-else-if="module._isInstalled">
                                    <span>已安装 v{{ module._localVersion }}</span>
                                    <template v-if="canUpgradeTo(module)">
                                        <span class="pb-meta-arrow">→</span>
                                        <span class="pb-meta-latest">最新 v{{ module.latestVersion }}</span>
                                    </template>
                                </template>
                                <template v-else-if="module.latestVersion">
                                    <span>最新版 v{{ module.latestVersion }}</span>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="pb-card-footer" :class="{'pb-card-footer--plain':!hasCardActions(module)}">
                        <span class="pb-card-code" :title="module.name">{{ module.name }}</span>
                        <div class="pb-card-actions">
                            <template v-if="module._isSystem">
                                <button type="button" class="pb-action" v-if="module._isInstalled && module._hasConfig"
                                        :data-tk-event="'ModuleStore,Config,'+module.name"
                                        @click.stop="doConfig(module)">
                                    <i class="iconfont icon-cog" aria-hidden="true"></i> 配置
                                </button>
                                <button type="button" class="pb-action ub-text-warning"
                                        v-if="canSystemUpgrade(module)"
                                        :data-tk-event="'ModuleStore,UpgradeSystem,'+module.name"
                                        @click.stop="doUpgrade(module)">
                                    <i class="iconfont icon-direction-up" aria-hidden="true"></i> 升级
                                </button>
                            </template>
                            <template v-else>
                                <button type="button" class="pb-action" v-if="module._isInstalled && module._hasConfig"
                                        :data-tk-event="'ModuleStore,Config,'+module.name"
                                        @click.stop="doConfig(module)">
                                    <i class="iconfont icon-cog" aria-hidden="true"></i> 配置
                                </button>
                                <button type="button" class="pb-action"
                                        v-if="!module._isInstalled && !module._isLocal"
                                        @click.stop="doInstallVersion(module)">
                                    其他版本
                                </button>
                                <button type="button" class="pb-action ub-text-warning" v-if="canUpgradeTo(module)"
                                        :data-tk-event="'ModuleStore,Upgrade,'+module.name"
                                        @click.stop="doUpgrade(module)">
                                    <i class="iconfont icon-direction-up" aria-hidden="true"></i> 升级
                                </button>
                                <button type="button" class="pb-action ub-text-danger"
                                        v-if="module._isInstalled && module._isEnabled"
                                        :data-tk-event="'ModuleStore,Disable,'+module.name"
                                        @click.stop="doDisable(module)">
                                    禁用
                                </button>
                                <button type="button" class="pb-action pb-action--primary"
                                        v-if="module._isInstalled && !module._isEnabled"
                                        :data-tk-event="'ModuleStore,Enable,'+module.name"
                                        @click.stop="doEnable(module)">
                                    启用
                                </button>
                                <button type="button" class="pb-action ub-text-danger"
                                        v-if="module._isInstalled && !module._isEnabled"
                                        :data-tk-event="'ModuleStore,Uninstall,'+module.name"
                                        @click.stop="doUninstall(module)">
                                    卸载
                                </button>
                                <el-button size="mini" type="primary" v-if="!module._isInstalled"
                                           :data-tk-event="'ModuleStore,Install,'+module.name"
                                           @click.stop="doInstall(module)">
                                    <i class="iconfont icon-plus" aria-hidden="true"></i> 安装
                                </el-button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pb-store-pager" v-if="!loading && total>pageSize">
                <el-pagination background
                               layout="prev, pager, next"
                               :total="total"
                               :page-size="pageSize"
                               :current-page="page"
                               @current-change="doPageChange"></el-pagination>
            </div>
        </div>
        <el-dialog :visible.sync="memberUserShow" append-to-body custom-class="pb-member-info-dialog">
            <div slot="title">
                <a href="https://modstart.com" target="_blank">
                    <img class="tw-h-8" :src="$url.cdn('vendor/ModuleStore/image/logo_modstart.png')" alt="ModStart"/>
                </a>
            </div>
            <div v-if="!memberUser.id" class="pb-login-body">
                <div class="pb-login-intro">
                    <i class="iconfont icon-code-alt" aria-hidden="true"></i>
                    登录 ModStart 账号后即可购买、安装市场模块
                </div>
                <div class="ub-form vertical">
                    <div class="line margin-bottom">
                        <div class="field">
                            <label class="pb-login-label" for="pbLoginUsername">用户名</label>
                            <input id="pbLoginUsername" type="text" class="form" v-model="memberLoginInfo.username"
                                   @keyup="doSubmitCheck" placeholder="请输入用户名"/>
                        </div>
                    </div>
                    <div class="line margin-bottom">
                        <div class="field">
                            <div class="pb-login-label-row">
                                <label class="pb-login-label" for="pbLoginPassword">密码</label>
                                <a class="pb-login-link" href="https://modstart.com/retrieve"
                                   target="_blank" rel="noopener">忘记密码？</a>
                            </div>
                            <input id="pbLoginPassword" type="password" class="form"
                                   v-model="memberLoginInfo.password"
                                   @keyup="doSubmitCheck"
                                   placeholder="请输入密码"/>
                        </div>
                    </div>
                    <div class="line margin-bottom">
                        <div class="field">
                            <label class="pb-login-label" for="pbLoginCaptcha">验证码</label>
                            <div class="pb-login-captcha">
                                <input id="pbLoginCaptcha" type="text" class="form"
                                       v-model="memberLoginInfo.captcha"
                                       autocomplete="off" @keyup="doSubmitCheck"
                                       placeholder="请输入图片验证码"/>
                                <img class="pb-login-captcha-img" title="点击刷新验证码"
                                     :src="memberLoginCaptchaImage"
                                     alt="图片验证码"
                                     @click="doMemberLoginCaptchaRefresh()"/>
                            </div>
                        </div>
                    </div>
                    <div class="line">
                        <div class="field">
                            <el-checkbox v-model="memberLoginInfo.agree" class="pb-login-agree">
                                同意
                                <a target="_blank"
                                   href="https://modstart.com/article/module_agreement">《使用协议》</a>
                                <a target="_blank" href="https://modstart.com/article/disclaimer">《免责声明》</a>
                            </el-checkbox>
                        </div>
                    </div>
                    <div class="line">
                        <div class="field">
                            <button type="submit" class="btn btn-primary btn-block btn-round"
                                    @click="doMemberLoginSubmit()">
                                登录
                            </button>
                        </div>
                    </div>
                    <div class="line">
                        <div class="field pb-login-footer">
                            <span class="pb-login-footer-left">
                                还没有账号？<a href="https://modstart.com" target="_blank">立即注册</a>
                            </span>
                            <a class="pb-login-wechat" href="javascript:;" @click="doScanLogin()">
                                <i class="iconfont icon-wechat" aria-hidden="true"></i>
                                微信扫一扫
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="pb-member-body">
                <div class="pb-member-card">
                    <div class="pb-member-avatar"
                         :style="{backgroundImage:`url(${memberUser.avatar||'/asset/image/avatar.svg'})`}"></div>
                    <div class="pb-member-info">
                        <div class="pb-member-name">{{ memberUser.username || '' }}</div>
                        <div class="pb-member-desc">已登录 ModStart 市场账号</div>
                    </div>
                    <button type="button" class="pb-member-logout" @click="doMemberUserLogout()">退出登录</button>
                </div>
                <div class="pb-member-actions">
                    <a class="pb-member-action pb-member-action--primary"
                       href="https://modstart.com/member_ms_module_license" target="_blank" rel="noopener">
                        <i class="iconfont icon-lock" aria-hidden="true"></i>
                        已授权的模块
                    </a>
                    <a class="pb-member-action" href="https://modstart.com/developer" target="_blank" rel="noopener">
                        <i class="iconfont icon-code" aria-hidden="true"></i>
                        成为模块开发者
                    </a>
                </div>
            </div>
        </el-dialog>
        <el-dialog :visible.sync="commandDialogShow"
                   :show-close="commandDialogFinish"
                   :close-on-press-escape="false"
                   :close-on-click-modal="false"
                   append-to-body>
            <div slot="title">
                <div class="ub-text-bold ub-text-primary">
                    <i class="iconfont icon-code" aria-hidden="true"></i>
                    {{ commandDialogTitle }}
                </div>
            </div>
            <div class="tw-bg-gray-900 tw-font-mono tw-leading-8 tw-p-4 tw-text-white" v-if="commandDialogShow">
                <div v-for="(msg,msgIndex) in commandDialogMsgs" v-html="msg"></div>
                <div v-if="!commandDialogFinish">
                    <i class="iconfont icon-loading tw-inline-block tw-animate-spin" aria-hidden="true"></i>
                    当前操作已运行 {{ commandDialogRunElapse }} s ...
                </div>
                <div v-else>
                    <i class="iconfont icon-check" aria-hidden="true"></i>
                    操作已运行完成
                </div>
            </div>
            <div class="tw-p-4 tw-text-center" v-if="commandDialogFinish">
                <el-button type="danger" @click="commandDialogShow=false">关闭</el-button>
            </div>
        </el-dialog>
        <el-dialog :visible.sync="installVersionDialogShow"
                   :close-on-press-escape="false"
                   :close-on-click-modal="false"
                   append-to-body
                   custom-class="pb-version-dialog">
            <div slot="title">
                <div class="pb-dialog-title" v-if="installVersionModule">
                    <i class="iconfont icon-code" aria-hidden="true"></i>
                    <span>选择版本</span>
                    <span class="pb-dialog-title-sub">{{ installVersionModule.title }}</span>
                </div>
            </div>
            <div v-if="installVersionModule">
                <table class="pb-version-table">
                    <thead>
                    <tr>
                        <th class="pb-version-col-version">版本</th>
                        <th>更新说明</th>
                        <th class="pb-version-col-time">发布时间</th>
                        <th class="pb-version-col-action">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(v,vIndex) in installVersionReleases" :key="'ver'+vIndex"
                        :class="{'pb-version-row--latest':vIndex===0}">
                        <td class="pb-version-col-version">
                            <span class="pb-version-no">v{{ v.version }}</span>
                            <span class="pb-version-tag" v-if="vIndex===0">最新</span>
                            <span class="ub-tag warning" v-if="v.status==='preview'">预览</span>
                        </td>
                        <td class="pb-version-feature" :title="v.feature">
                            <span class="pb-version-feature-text">{{ v.feature }}</span>
                        </td>
                        <td class="pb-version-col-time">{{ v.time }}</td>
                        <td class="pb-version-col-action">
                            <button type="button" class="pb-version-install"
                                    @click="doInstallVersionSubmit(installVersionModule,v.version)">
                                <i class="iconfont icon-plus" aria-hidden="true"></i>
                                安装
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="pb-version-tip">
                    <i class="iconfont icon-warning" aria-hidden="true"></i>
                    旧版本可能不兼容当前系统，建议使用最新版
                </div>
            </div>
        </el-dialog>
        <el-dialog :visible.sync="localUninstallChoiceShow"
                   :close-on-press-escape="false"
                   :close-on-click-modal="false"
                   append-to-body>
            <div slot="title">
                <div class="ub-text-bold ub-text-primary" v-if="localUninstallChoiceModule">
                    <i class="iconfont icon-code" aria-hidden="true"></i>
                    安装 {{ localUninstallChoiceModule.title }}（{{ localUninstallChoiceModule.name }}）
                </div>
            </div>
            <div v-if="localUninstallChoiceModule" class="tw-p-4">
                <div class="ub-alert warning tw-mb-3">
                    <i class="iconfont icon-warning" aria-hidden="true"></i>
                    检测到本地存在同名模块（V{{ localUninstallChoiceModule._localUninstalledVersion }}，尚未安装），
                    请选择安装方式：
                </div>
                <div class="tw-flex tw-flex-col tw-gap-3">
                    <div class="ub-border tw-rounded tw-p-3 tw-flex tw-items-center tw-gap-3">
                        <div class="tw-flex-1">
                            <div class="tw-font-bold tw-text-gray-700">安装本地模块</div>
                            <div class="tw-text-gray-400 tw-text-sm">直接安装本地的 module/{{ localUninstallChoiceModule.name }} 目录，不下载市场版本</div>
                        </div>
                        <el-button type="primary" @click="doInstallLocal(localUninstallChoiceModule)">安装本地</el-button>
                    </div>
                    <div class="ub-border tw-rounded tw-p-3 tw-flex tw-items-center tw-gap-3">
                        <div class="tw-flex-1">
                            <div class="tw-font-bold tw-text-gray-700">从市场下载强制覆盖</div>
                            <div class="tw-text-gray-400 tw-text-sm">
                                备份本地同名目录后，下载市场 V{{ localUninstallChoiceModule.latestVersion }} 强制覆盖本地
                            </div>
                        </div>
                        <el-button @click="doInstallRemote(localUninstallChoiceModule)">市场覆盖</el-button>
                    </div>
                </div>
            </div>
        </el-dialog>
    </div>
</template>

<script>
import {BeanUtil} from '@ModStartAsset/svue/lib/util'
import {Storage} from '@ModStartAsset/svue/lib/storage'

const UrlWatcher = require('@ModStartAsset/lib/urlWatcher.js');

const STORAGE_SAFETY_TIP_DISMISSED = 'ModuleStoreSafetyTipDismissed'

export default {
    name: "ModuleStore",
    data() {
        return {
            loading: true,
            // safety tip can be dismissed forever, stored in localStorage
            safetyTipVisible: Storage.get(STORAGE_SAFETY_TIP_DISMISSED, '') !== '1',
            // category chips collapsed by default when there are too many categories
            categoryExpand: false,
            categoryLimit: 8,
            page: 1,
            // 每页数量 = 栅格列数 × 行数，随窗口宽度动态计算，保证最后一行不留空位
            pageSize: 24,
            pageRows: 6,
            gridColumns: 4,
            search: {
                tab: MS.url.getQuery('tab', 'store'),
                priceType: 'all',
                isRecommend: false,
                // 一级分类（产品线）与二级分类（具体分类）
                categoryPid: 0,
                categoryId: 0,
                type: '',
                keywords: '',
                sort: 'default',
            },
            commandDialogMsgs: [],
            commandDialogRunStart: 0,
            commandDialogRunElapse: 0,
            commandDialogShow: false,
            commandDialogFinish: false,
            commandDialogTitle: '',
            memberUserShow: false,
            memberUserLoading: false,
            memberLoginCaptchaImage: null,
            memberLoginInfo: {
                username: '',
                password: '',
                captcha: '',
                agree: false,
            },
            storeApiToken: Storage.get('storeApiToken', ''),
            memberUser: {
                id: 0,
                username: '',
                avatar: '',
            },
            categories: [],
            types: [],
            modules: [],
            storeConfig: {
                disable: false,
            },
            installVersionDialogShow: false,
            installVersionReleases: [],
            installVersionModule: null,
            localUninstallChoiceShow: false,
            localUninstallChoiceModule: null,
            payWatcher: null,
        }
    },
    watch: {
        memberUser: {
            handler(n, o) {
                this.doLoad()
            },
            deep: true
        },
        // 列数变化后每页数量跟着变，当前页超出范围时回退到最后一页
        pageSize() {
            const maxPage = Math.max(1, Math.ceil(this.total / this.pageSize))
            if (this.page > maxPage) {
                this.page = maxPage
            }
        },
        // any filter change goes back to the first page
        'search.tab'() {
            this.page = 1
            this.categoryExpand = false
        },
        'search.priceType'() {
            this.page = 1
        },
        'search.isRecommend'() {
            this.page = 1
        },
        'search.categoryId'() {
            this.page = 1
        },
        'search.type'() {
            this.page = 1
        },
        'search.keywords'() {
            this.page = 1
        },
        'search.sort'() {
            this.page = 1
        },
    },
    computed: {
        filterModules() {
            const keywords = (this.search.keywords || '').trim().toLowerCase()
            const results = this.modules.filter(module => {
                switch (this.search.tab) {
                    case 'store':
                        if (module._isLocal) return false
                        if (module._isSystem) return false
                        break
                    case 'installed':
                        if (!module._isInstalled) return false
                        if (module._isSystem) return false
                        break;
                    case 'enabled':
                        if (!module._isEnabled) return false
                        if (module._isSystem) return false
                        break;
                    case 'disabled':
                        if (!module._isInstalled || module._isEnabled) return false
                        if (module._isSystem) return false
                        break;
                    case 'local':
                        if (!module._isLocal) return false
                        if (module._isSystem) return false
                        break
                    case 'system':
                        if (!module._isSystem) return false
                        break
                    case 'upgradeable':
                        if (!(module._isInstalled && !module._isLocal && this.canUpgrade(module._localVersion, module.latestVersion))) {
                            return false
                        }
                        if (module._isSystem) return false
                        break
                }
                if (this.search.isRecommend) {
                    if (!module.isRecommend) {
                        return false
                    }
                }
                if (!!this.search.type) {
                    if ((module.types || []).indexOf(this.search.type) < 0) {
                        return false
                    }
                }
                switch (this.search.priceType) {
                    case 'free':
                        if (module.isFee) return false
                        break
                    case 'fee':
                        if (!module.isFee) return false
                        break
                }
                const categoryFilterId = this.search.categoryId || this.search.categoryPid
                if (categoryFilterId) {
                    // 分类支持两级：选一级时包含其所有子分类
                    const categoryIds = this.categoryTreeIds(categoryFilterId)
                    if (categoryIds.indexOf(module.categoryId) < 0) return false
                }
                if (keywords) {
                    const fields = [module.title, module.name, module.description]
                    for (let i = 0; i < fields.length; i++) {
                        if (String(fields[i] || '').toLowerCase().indexOf(keywords) >= 0) {
                            return true
                        }
                    }
                    return false
                }
                return true
            })
            return results
        },
        total() {
            return this.filterModules.length
        },
        sortedModules() {
            const list = this.filterModules.slice()
            const priceOf = (m) => {
                if (!m.isFee) return 0
                // 排序使用与卡片展示一致的最低价
                const price = this.lowestPrice(m)
                return price === null ? 0 : price
            }
            switch (this.search.sort) {
                case 'priceAsc':
                    list.sort((a, b) => priceOf(a) - priceOf(b))
                    break
                case 'priceDesc':
                    list.sort((a, b) => priceOf(b) - priceOf(a))
                    break
                case 'name':
                    list.sort((a, b) => String(a.title || '').localeCompare(String(b.title || '')))
                    break
                default:
                    // recommended modules first
                    list.sort((a, b) => (b.isRecommend ? 1 : 0) - (a.isRecommend ? 1 : 0))
            }
            return list
        },
        pagedModules() {
            const start = (this.page - 1) * this.pageSize
            return this.sortedModules.slice(start, start + this.pageSize)
        },
        topCategories() {
            // 服务端返回标准分类树（_child）时取一级；老服务端返回扁平列表时全部视为一级
            const tops = this.categories.filter(c => !c.pid)
            return tops.length ? tops : this.categories
        },
        childCategories() {
            if (!this.search.categoryPid) return []
            const top = this.categories.filter(c => c.id === this.search.categoryPid)[0]
            return (top && top._child) || []
        },
        visibleCategories() {
            const tops = this.topCategories
            if (this.categoryExpand || tops.length <= this.categoryLimit) {
                return tops
            }
            const list = tops.slice(0, this.categoryLimit)
            // 选中的一级分类被折叠时仍然显示，避免筛选状态丢失
            if (this.search.categoryPid) {
                const selected = tops.filter(c => c.id === this.search.categoryPid)
                if (selected.length && list.indexOf(selected[0]) < 0) {
                    return list.concat(selected[0])
                }
            }
            return list
        },
        hasFilter() {
            return this.search.priceType !== 'all'
                || this.search.isRecommend
                || !!this.search.categoryPid
                || !!this.search.categoryId
                || !!this.search.type
                || !!this.search.keywords
        },
        // 已选筛选条件，结果栏回显并可单条移除
        activeFilters() {
            const list = []
            if (this.search.priceType === 'free') list.push({key: 'priceType', label: '免费'})
            if (this.search.priceType === 'fee') list.push({key: 'priceType', label: '付费'})
            if (this.search.isRecommend) list.push({key: 'isRecommend', label: '推荐'})
            if (this.search.categoryPid) {
                const top = this.topCategories.filter(c => c.id === this.search.categoryPid)[0]
                if (top) list.push({key: 'categoryPid', label: top.title})
            }
            if (this.search.categoryId) {
                const child = this.childCategories.filter(c => c.id === this.search.categoryId)[0]
                if (child) list.push({key: 'categoryId', label: child.title})
            }
            if (this.search.type) {
                const type = this.types.filter(t => t.value === this.search.type)[0]
                list.push({key: 'type', label: type ? type.title : this.search.type})
            }
            const keywords = (this.search.keywords || '').trim()
            if (keywords) list.push({key: 'keywords', label: '搜索：' + keywords})
            return list
        },
    },
    mounted() {
        this.doLoad()
        this.doLoadStore()
        this.$nextTick(() => {
            this.doSyncGridColumns()
        })
        window.addEventListener('resize', this.doSyncGridColumns)
        setInterval(() => {
            this.commandDialogRunElapse = parseInt(((new Date()).getTime() - this.commandDialogRunStart) / 1000)
        }, 1000)
    },
    beforeDestroy() {
        window.removeEventListener('resize', this.doSyncGridColumns)
    },
    methods: {
        // 按当前栅格列数计算每页数量，保证最后一行排满
        doSyncGridColumns() {
            const grid = this.$el.querySelector('.pb-module-grid')
            if (!grid) {
                return
            }
            const template = window.getComputedStyle(grid).gridTemplateColumns || ''
            // 未完成布局时可能返回 none，此时跳过，避免误判为单列
            if (!template || template === 'none') {
                return
            }
            const columns = template.split(' ').filter(v => !!v).length
            if (columns > 0 && columns !== this.gridColumns) {
                this.gridColumns = columns
                // 多列时按「列数 × 行数」对齐，避免最后一行缺卡片；单列/双列保持每页 24 个
                this.pageSize = columns >= 3 ? columns * this.pageRows : 24
            }
        },
        // 分类树节点（含所有子孙）的 id 集合，用于「选一级分类包含其子分类」
        categoryTreeIds(categoryId) {
            const find = (nodes) => {
                for (let i = 0; i < nodes.length; i++) {
                    if (nodes[i].id === categoryId) return nodes[i]
                    if (nodes[i]._child && nodes[i]._child.length) {
                        const hit = find(nodes[i]._child)
                        if (hit) return hit
                    }
                }
                return null
            }
            const node = find(this.categories)
            if (!node) return [categoryId]
            const ids = []
            const walk = (nodes) => {
                for (let i = 0; i < nodes.length; i++) {
                    ids.push(nodes[i].id)
                    if (nodes[i]._child && nodes[i]._child.length) walk(nodes[i]._child)
                }
            }
            walk([node])
            return ids
        },
        doCategorySelect(categoryId) {
            this.search.categoryPid = categoryId || 0
            // 切换一级分类时清空二级分类选择
            this.search.categoryId = 0
        },
        versionCompare(left, right) {
            if (!left || !right) {
                return 0
            }
            let a = String(left).split('.'), b = String(right).split('.')
            for (let i = 0, len = Math.max(a.length, b.length); i < len; i++) {
                if ((a[i] && !b[i] && parseInt(a[i]) > 0) || (parseInt(a[i]) > parseInt(b[i]))) {
                    return 1;
                } else if ((b[i] && !a[i] && parseInt(b[i]) > 0) || (parseInt(a[i]) < parseInt(b[i]))) {
                    return -1;
                }
            }
            return 0;
        },
        canUpgrade(currentVersion, latestVersion) {
            return this.versionCompare(currentVersion, latestVersion) < 0
        },
        // installed, not a local-only module, and has a newer version in store
        canUpgradeTo(module) {
            if (!module._isInstalled || module._isLocal) {
                return false
            }
            return this.canUpgrade(module._localVersion, module.latestVersion)
        },
        canSystemUpgrade(module) {
            return !!module._isInstalled && !!module.latestVersion
                && this.canUpgrade(module._localVersion, module.latestVersion)
        },
        moduleStatus(module) {
            // the current tab already tells the state, no need to repeat it on every card
            const tab = this.search.tab
            if (module._isSystem) {
                return tab === 'system' ? null : {text: '内置', type: 'primary'}
            }
            if (this.canUpgradeTo(module)) {
                return tab === 'upgradeable' ? null : {text: '可升级', type: 'warning'}
            }
            if (module._isInstalled) {
                if (module._isEnabled) {
                    return tab === 'enabled' ? null : {text: '已启用', type: 'success'}
                }
                return tab === 'disabled' ? null : {text: '已禁用', type: 'muted'}
            }
            if (module._isLocal) {
                return tab === 'local' ? null : {text: '本地', type: 'muted'}
            }
            return null
        },
        formatPrice(value) {
            const number = parseFloat(value)
            if (isNaN(number)) {
                return String(value)
            }
            return number % 1 === 0 ? String(number) : number.toFixed(2)
        },
        modulePriceText(module) {
            if (!module.isFee) {
                return '免费'
            }
            // 价格统一只显示金额，不显示「/年」等计费周期；年付 / 永久授权中展示最低价
            const price = this.lowestPrice(module)
            if (price !== null) {
                return '¥' + this.formatPrice(price)
            }
            return '付费'
        },
        lowestPrice(module) {
            // 年付 / 永久授权中取已开启的最低价格，同时开启时展示年付价
            const prices = []
            if (module.priceYearEnable && module.priceYear) {
                prices.push(parseFloat(module.priceYear) || 0)
            }
            if (module.priceSuperEnable && module.priceSuper) {
                prices.push(parseFloat(module.priceSuper) || 0)
            }
            const valid = prices.filter((price) => price > 0)
            if (!valid.length) {
                return null
            }
            return Math.min.apply(null, valid)
        },
        doCoverError(module) {
            // fallback to the placeholder icon, background images cannot report load errors
            module.cover = ''
        },
        // stable color per module name, so local modules without cover are still distinguishable
        coverColor(name) {
            let hash = 0
            const text = String(name || '')
            for (let i = 0; i < text.length; i++) {
                hash = (hash * 31 + text.charCodeAt(i)) % 360
            }
            return 'linear-gradient(135deg, hsl(' + hash + ', 72%, 70%), hsl(' + ((hash + 24) % 360) + ', 64%, 56%))'
        },
        // only modules that can be browsed in store and not installed yet are clickable as a whole
        isCardClickable(module) {
            return !module._isSystem && !module._isInstalled && !!module.url
        },
        doCardClick(e, module) {
            if (!this.isCardClickable(module)) {
                return
            }
            const target = e.target
            if (target && target.closest && target.closest('a,button,input,label,.el-button,.el-dropdown')) {
                return
            }
            window.open(module.url, '_blank')
        },
        hasCardActions(module) {
            if (module._isSystem) {
                return (module._isInstalled && module._hasConfig) || this.canSystemUpgrade(module)
            }
            return true
        },
        doPageChange(page) {
            this.page = page
            const reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches
            this.$nextTick(() => {
                const el = this.$el.querySelector('.pb-store-toolbar')
                if (el && el.scrollIntoView) {
                    el.scrollIntoView({block: 'start', behavior: reduceMotion ? 'auto' : 'smooth'})
                }
            })
        },
        doSafetyTipDismiss() {
            this.safetyTipVisible = false
            Storage.set(STORAGE_SAFETY_TIP_DISMISSED, '1')
        },
        doSearchReset() {
            this.search.priceType = 'all'
            this.search.isRecommend = false
            this.search.categoryPid = 0
            this.search.categoryId = 0
            this.search.type = ''
            this.search.keywords = ''
        },
        // 结果栏单条筛选移除
        doRemoveFilter(filter) {
            switch (filter.key) {
                case 'priceType':
                    this.search.priceType = 'all'
                    break
                case 'isRecommend':
                    this.search.isRecommend = false
                    break
                case 'categoryPid':
                    this.doCategorySelect(0)
                    break
                case 'categoryId':
                    this.search.categoryId = 0
                    break
                case 'type':
                    this.search.type = ''
                    break
                case 'keywords':
                    this.search.keywords = ''
                    break
            }
        },
        doStoreRequest(url, data, cbSuccess, cbError) {
            const cbErrorDefault = (res) => {
                this.$dialog.tipError(res.msg)
            }
            if (!cbError) {
                cbError = cbErrorDefault
            }
            $.ajax({
                url: `${window.__data.apiBase}/api/${url}`,
                dataType: 'jsonp',
                timeout: 10 * 1000,
                data: Object.assign(data, {
                    api_token: this.storeApiToken,
                    modstartParam: JSON.stringify(window.__data.modstartParam),
                }),
                success: (res) => {
                    if (res.code) {
                        if (res.code === 1002) {
                            this.doMemberLoginCaptchaRefresh()
                        }
                        if (true !== cbError(res)) {
                            cbErrorDefault(res)
                        }
                    } else {
                        cbSuccess(res)
                    }
                },
                error: (res) => {
                    if (true !== cbError({code: -1, msg: '请求出现错误'})) {
                        cbErrorDefault({code: -1, msg: '请求出现错误'})
                    }
                },
                jsonp: 'callback',
            });
        },
        doMemberUserLogout() {
            this.$dialog.confirm('确认退出？', () => {
                this.storeApiToken = ''
                Storage.set('storeApiToken', '')
                this.memberUserShow = false
                this.doLoadStore()
            })
        },
        doSubmitCheck(e) {
            if (e.keyCode === 13) {
                this.doMemberLoginSubmit()
            }
        },
        doMemberLoginSubmit() {
            if (!this.memberLoginInfo.username || !this.memberLoginInfo.password || !this.memberLoginInfo.captcha) {
                return
            }
            if (!this.memberLoginInfo.agree) {
                this.$dialog.tipError('请先同意使用协议')
                return
            }
            const postData = {
                username: this.memberLoginInfo.username,
                password: this.memberLoginInfo.password,
                captcha: this.memberLoginInfo.captcha,
            }
            postData.ek = MS.util.randomString(8)
            postData.username = 'E.A:' + MS.util.encrypt.aesEncode(postData.ek, postData.username)
            postData.password = 'E.A:' + MS.util.encrypt.aesEncode(postData.ek, postData.password)
            this.$dialog.loadingOn()
            this.doStoreRequest('store/login', postData, res => {
                this.$dialog.loadingOff()
                this.$dialog.tipSuccess('登录成功')
                this.doLoadStoreMember()
                this.memberLoginInfo.username = ''
                this.memberLoginInfo.password = ''
                this.memberLoginInfo.captcha = ''
                this.memberUserShow = false
            }, res => {
                this.$dialog.loadingOff()
            })
        },
        doMemberLoginCaptchaRefresh(cb) {
            this.doStoreRequest('store/login_captcha', {}, res => {
                this.memberLoginCaptchaImage = res.data.image
                cb && cb()
            })
        },
        doMemberLoginShow() {
            if (this.memberUser.id > 0) {
                this.memberUserShow = true
            } else {
                this.$dialog.loadingOn()
                this.doMemberLoginCaptchaRefresh(() => {
                    this.$dialog.loadingOff()
                    this.memberUserShow = true
                })
            }
        },
        doLoadStoreMember() {
            this.memberUserLoading = true
            this.doStoreRequest('store/member', {}, res => {
                this.memberUserLoading = false
                BeanUtil.update(this.memberUser, res.data)
            }, res => {
                this.memberUserLoading = false
            })
        },
        doLoadStore() {
            if (!!this.storeApiToken) {
                this.doLoadStoreMember()
            } else {
                this.doStoreRequest('store/config', {}, res => {
                    this.storeApiToken = res.data.apiToken
                    Storage.set('storeApiToken', res.data.apiToken)
                    this.doLoadStoreMember()
                })
            }
        },
        doLoad() {
            this.$api.post(this.$url.admin('module_store/all'), {
                memberUserId: this.memberUser.id,
                apiToken: this.storeApiToken,
            }, res => {
                this.loading = false
                this.categories = res.data.categories
                this.types = res.data.types
                this.modules = res.data.modules
                this.storeConfig = res.data.storeConfig
                // keep the current page inside range after data reload
                const maxPage = Math.max(1, Math.ceil(this.filterModules.length / this.pageSize))
                if (this.page > maxPage) {
                    this.page = maxPage
                }
            })
        },
        commandDialogMsgsPush(msg) {
            if (!msg) {
                return
            }
            if (!Array.isArray(msg)) {
                msg = [msg]
            }
            msg = msg.map(m => {
                m = m.trim()
                if (!m.startsWith('<')) {
                    m = '<i class="iconfont icon-hr"></i> ' + m
                }
                return m
            })
            this.commandDialogMsgs = this.commandDialogMsgs.concat(msg)
        },
        doCommand(command, data, step, title) {
            step = step || null
            title = title || null
            if (null === step) {
                this.commandDialogMsgs = []
                this.commandDialogShow = true
                this.commandDialogFinish = false
            }
            if (title) {
                this.commandDialogTitle = title
                this.commandDialogMsgsPush(title)
            }
            this.commandDialogRunStart = (new Date()).getTime()
            this.commandDialogRunElapse = 0
            this.$api.post(this.$url.admin(`module_store/${command}`), {
                token: this.storeApiToken,
                step: step,
                data: JSON.stringify(data)
            }, res => {
                this.commandDialogMsgsPush(res.data.msg)
                if (res.data.finish) {
                    this.commandDialogFinish = true
                    this.doLoad()
                    return
                } else {
                    setTimeout(() => {
                        this.doCommand(res.data.command, res.data.data, res.data.step)
                    }, 1000)
                }
            }, res => {
                this.commandDialogMsgsPush('<i class="iconfont icon-close ub-text-danger"></i> <span class="ub-text-danger">' + res.msg + '</span>')
                if (res.data && res.data.msg) {
                    this.commandDialogMsgsPush(res.data.msg)
                }
                if (res.data && res.data.payWatchUrl) {
                    this.startPayWatch(res.data.buyCodeId, res.data.payWatchUrl)
                }
                this.commandDialogFinish = true
                return true
            })
        },
        startPayWatch(buyCodeId, payWatchUrl) {
            const buyCodeVisible = () => {
                return ($('[data-buy-code=' + buyCodeId + ']').length > 0)
            }
            this.$nextTick(() => {
                if (this.urlWatcher) {
                    this.urlWatcher.stop()
                }
                if (!buyCodeVisible()) {
                    return;
                }
                this.urlWatcher = new UrlWatcher({
                    url: payWatchUrl,
                    jsonp: true,
                    data: {},
                    maxRound: 100,
                    requestFinish: (res) => {
                        if (!buyCodeVisible()) {
                            this.urlWatcher.stop()
                            return;
                        }
                        MS.api.defaultCallback(res, {
                            success: (res) => {
                                switch (res.data.status) {
                                    case 'Payed':
                                        this.$dialog.alertSuccess('支付成功，请关闭弹窗重新安装', () => {
                                            this.commandDialogShow = false
                                        })
                                        break;
                                    case 'WaitPay':
                                        this.urlWatcher.next();
                                        break;
                                }
                            }
                        });
                    },
                    expired: () => {
                        this.$dialog.alertError('支付超时，请关闭弹窗重新请求支付二维码', () => {
                            this.commandDialogShow = false
                        })
                    },
                })
                this.urlWatcher.start()
            })
        },
        doInstallVersion(module) {
            this.$dialog.loadingOn()
            this.doStoreRequest('store/module_releases', {module: module.name}, res => {
                this.$dialog.loadingOff()
                this.installVersionModule = module
                this.installVersionReleases = res.data.releases
                this.installVersionDialogShow = true
            }, res => {
                this.$dialog.loadingOff()
            })
        },
        doInstall(module) {
            // local only module (not in remote store), install from local directory directly
            if (module._isLocal) {
                this.doInstallLocal(module)
                return
            }
            if (!this.memberUser.id) {
                this.doMemberLoginShow()
                return
            }
            if (module._hasLocalUninstalled) {
                this.localUninstallChoiceModule = module
                this.localUninstallChoiceShow = true
                return
            }
            this.doInstallRemote(module)
        },
        doInstallLocal(module) {
            this.localUninstallChoiceShow = false
            this.doCommand('install', {
                module: module.name,
                version: module._localUninstalledVersion,
                isLocal: true
            }, null, `安装本地模块 ${module.title}（${module.name}） V${module._localUninstalledVersion}`)
        },
        doInstallRemote(module) {
            this.localUninstallChoiceShow = false
            this.doCommand('install', {
                module: module.name,
                version: module.latestVersion,
                isLocal: false
            }, null, `安装模块 ${module.title}（${module.name}） V${module.latestVersion}`)
        },
        doInstallVersionSubmit(module, version) {
            if (!this.memberUser.id) {
                this.doMemberLoginShow()
                return
            }
            if (module._hasLocalUninstalled) {
                this.localUninstallChoiceModule = module
                this.localUninstallChoiceShow = true
                return
            }
            this.doCommand('install', {
                module: module.name,
                version: version,
                isLocal: module._isLocal
            }, null, `安装模块 ${module.title}（${module.name}） V${version}`)
        },
        doDisable(module) {
            this.doCommand('disable', {
                module: module.name,
                version: module._localVersion
            }, null, `禁用模块 ${module.title}（${module.name}）`)
        },
        doEnable(module) {
            this.doCommand('enable', {
                module: module.name,
                version: module._localVersion
            }, null, `启用模块 ${module.title}（${module.name}）`)
        },
        doUninstall(module) {
            this.$dialog.confirm('确认卸载？', () => {
                this.doCommand('uninstall', {
                    module: module.name,
                    version: module._localVersion,
                    isLocal: module._isLocal
                }, null, `卸载模块 ${module.title}（${module.name}）`)
            })
        },
        doUpgrade(module) {
            if (!this.memberUser.id) {
                this.doMemberLoginShow()
                return
            }
            this.$dialog.confirm('确认升级？', () => {
                this.doCommand('upgrade', {
                    module: module.name,
                    version: module.latestVersion,
                }, null, `升级模块 ${module.title}（${module.name}） V${module.latestVersion}`)
            })
        },
        doConfig(module) {
            this.$dialog.dialog(this.$url.admin(`module_store/config/${module.name}`))
        },
        doScanLogin() {
            if (!this.memberLoginInfo.agree) {
                this.$dialog.tipError('请先同意相关协议')
                return
            }
            this.$dialog.loadingOn()
            this.doStoreRequest('store/login_wechatmp_qrcode', {}, res => {
                this.$dialog.loadingOff()
                let isOpen = false
                let dialog = null
                const checkLogin = () => {
                    if (!isOpen) {
                        return
                    }
                    this.doStoreRequest('store/login_wechatmp_info', {}, res => {
                        if (res.data.memberUserId) {
                            this.doLoadStoreMember()
                            this.$dialog.dialogClose(dialog)
                            this.$dialog.tipSuccess('登录成功')
                        } else if (res.data.oauthUserInfo) {
                            this.$dialog.dialogClose(dialog)
                            this.$dialog.alertError('当前微信未绑定账号，请先注册')
                        } else {
                            setTimeout(() => {
                                checkLogin()
                            }, 3000)
                        }
                    })
                }
                checkLogin();
                dialog = this.$dialog.dialogContent(`<img style="width:200px;height:200px;" src="${res.data.qrcode}" />`, {
                    openCallback: () => {
                        isOpen = true
                        setTimeout(() => {
                            checkLogin()
                        }, 3000)
                    },
                    closeCallback: () => {
                        isOpen = false
                    },
                })
            }, res => {
                this.$dialog.loadingOff()
            })
        }
    }
}
</script>

<style lang="less">
.pb-member-info-dialog {
    max-width: 24rem;
}

.pb-member-info-dialog .el-dialog__body {
    padding: 1rem 1.5rem 1.5rem;
}

/* ---- dialog title ---- */
.pb-dialog-title {
    display: flex;
    align-items: center;
    gap: .375rem;
    font-weight: 600;
    color: var(--color-heading, #101828);
}

.pb-dialog-title .iconfont {
    color: var(--color-primary, #2F6BFF);
}

.pb-dialog-title-sub {
    font-weight: 400;
    color: var(--color-tertiary, #667085);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* ---- install version dialog ---- */
.pb-version-dialog {
    width: 46rem;
    max-width: calc(100vw - 2rem);
}

.pb-version-dialog .el-dialog__body {
    padding: .5rem 1.5rem 1.25rem;
}

.pb-version-table {
    width: 100%;
    border-collapse: collapse;
    font-size: .8125rem;
}

.pb-version-table th {
    padding: .5rem .75rem;
    background: var(--color-body-block-bg, #F2F4F7);
    border-bottom: 1px solid var(--color-body-line, #EAECF0);
    font-size: .75rem;
    font-weight: 500;
    color: var(--color-tertiary, #667085);
    text-align: left;
    white-space: nowrap;
}

.pb-version-table th:first-child {
    border-top-left-radius: var(--size-radius-xs, .375rem);
}

.pb-version-table th:last-child {
    border-top-right-radius: var(--size-radius-xs, .375rem);
    text-align: right;
}

.pb-version-table td {
    padding: .625rem .75rem;
    border-bottom: 1px solid var(--color-body-block-bg, #F2F4F7);
    color: var(--color-text, #344054);
    vertical-align: middle;
}

.pb-version-table tbody tr {
    transition: background-color .15s var(--ease-out, ease);
}

.pb-version-table tbody tr:hover td {
    background: var(--color-primary-light-bg, #EDF3FF);
}

.pb-version-row--latest td {
    background: rgba(47, 107, 255, .04);
}

.pb-version-col-version {
    width: 9rem;
    white-space: nowrap;
}

.pb-version-col-time {
    width: 7rem;
    white-space: nowrap;
    color: var(--color-tertiary, #667085);
}

.pb-version-col-action {
    width: 5.5rem;
    text-align: right;
}

.pb-version-no {
    font-family: var(--font-family-mono, monospace);
    font-weight: 500;
    color: var(--color-heading, #101828);
}

.pb-version-tag {
    display: inline-block;
    margin-left: .375rem;
    padding: 0 .3125rem;
    border-radius: .25rem;
    background: var(--color-primary-light-bg, #EDF3FF);
    font-size: .6875rem;
    line-height: 1.125rem;
    color: var(--color-primary-dark, #1D51E0);
}

.pb-version-feature {
    color: var(--color-tertiary, #667085);
}

/* 说明列最多两行，避免长文案撑高行；不能用 td 自身做 line-clamp（会破坏表格布局） */
.pb-version-feature-text {
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    overflow: hidden;
    overflow-wrap: anywhere;
}

.pb-version-install {
    display: inline-flex;
    align-items: center;
    gap: .125rem;
    height: 1.875rem;
    padding: 0 .75rem;
    border: 1px solid var(--color-body-line, #EAECF0);
    border-radius: var(--size-radius-xs, .375rem);
    background: #fff;
    font: inherit;
    font-size: .75rem;
    color: var(--color-body-block, #475467);
    cursor: pointer;
    white-space: nowrap;
    transition: color .15s var(--ease-out, ease), border-color .15s var(--ease-out, ease),
        background-color .15s var(--ease-out, ease);
}

.pb-version-install:hover {
    border-color: var(--color-primary, #2F6BFF);
    background: var(--color-primary-light-bg, #EDF3FF);
    color: var(--color-primary, #2F6BFF);
}

.pb-version-install:focus-visible {
    outline: 2px solid var(--color-focus-ring, rgba(47, 107, 255, .4));
    outline-offset: 1px;
}

.pb-version-tip {
    display: flex;
    align-items: center;
    gap: .375rem;
    margin-top: .75rem;
    padding: .5rem .625rem;
    border-radius: var(--size-radius-xs, .375rem);
    background: var(--color-warning-light, #FFFAEB);
    font-size: .75rem;
    color: var(--color-warning-dark, #B54708);
}

/* ---- member login dialog ---- */
.pb-login-intro {
    display: flex;
    align-items: center;
    gap: .375rem;
    margin-bottom: 1rem;
    font-size: .8125rem;
    color: var(--color-tertiary, #667085);
}

.pb-login-intro .iconfont {
    color: var(--color-primary, #2F6BFF);
}

/* SUI 默认把 .ub-form .field label 渲染成胶囊选项，这里恢复为普通字段标签 */
.ub-form .line .field label.pb-login-label,
.ub-form .line .field label.pb-login-label:hover {
    display: block;
    width: auto;
    margin: 0 0 .375rem;
    padding: 0;
    border: 0;
    border-radius: 0;
    background: none;
    font-size: .75rem;
    font-weight: 500;
    color: var(--color-body-block, #475467);
    cursor: default;
    transition: none;
}

.pb-login-label-row {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
}

.pb-login-label-row .pb-login-label {
    margin-bottom: .375rem;
}

.pb-login-link {
    font-size: .8125rem;
}

/* 协议勾选框加大热区与尺寸，移动端也容易点中 */
.pb-login-agree.el-checkbox {
    display: flex;
    align-items: center;
    padding: .375rem 0;
    margin-right: 0;
}

.pb-login-agree .el-checkbox__inner {
    width: 1rem;
    height: 1rem;
}

.pb-login-agree .el-checkbox__inner::after {
    left: .3125rem;
    top: .0625rem;
    width: .25rem;
    height: .5rem;
}

.pb-login-agree .el-checkbox__label {
    line-height: 1.375rem;
    white-space: normal;
}

.pb-login-captcha {
    display: flex;
    align-items: stretch;
    gap: .5rem;
}

.pb-login-captcha .form {
    flex: 1;
    min-width: 0;
}

.pb-login-captcha-img {
    flex: none;
    width: auto;
    min-width: 5.5rem;
    height: 2rem;
    border: 1px solid var(--color-body-line, #EAECF0);
    border-radius: var(--size-radius-xs, .375rem);
    background: #fff;
    object-fit: contain;
    cursor: pointer;
}

.pb-login-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .5rem;
    font-size: .8125rem;
    color: var(--color-tertiary, #667085);
}

.pb-login-wechat {
    display: inline-flex;
    align-items: center;
    gap: .25rem;
    color: #19B84D;
    white-space: nowrap;
}

.pb-login-wechat:hover {
    color: #128A38;
}

/* ---- member info dialog ---- */
.pb-member-card {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: .75rem;
    border: 1px solid var(--color-body-line, #EAECF0);
    border-radius: var(--size-radius-sm, .5rem);
    background: var(--color-body-block-bg, #F2F4F7);
}

.pb-member-avatar {
    flex: none;
    width: 3rem;
    height: 3rem;
    border: 1px solid var(--color-body-line, #EAECF0);
    border-radius: 50%;
    background-color: #fff;
    background-size: cover;
    background-position: center;
}

.pb-member-info {
    flex: 1;
    min-width: 0;
}

.pb-member-name {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-weight: 600;
    color: var(--color-heading, #101828);
}

.pb-member-desc {
    margin-top: .125rem;
    font-size: .75rem;
    color: var(--color-tertiary, #667085);
}

.pb-member-logout {
    flex: none;
    height: 1.875rem;
    padding: 0 .75rem;
    border: 1px solid var(--color-tertiary-light, #D0D5DD);
    border-radius: var(--size-radius-xs, .375rem);
    background: #fff;
    font: inherit;
    font-size: .75rem;
    color: var(--color-body-block, #475467);
    cursor: pointer;
    white-space: nowrap;
    transition: color .15s var(--ease-out, ease), border-color .15s var(--ease-out, ease);
}

.pb-member-logout:hover {
    border-color: var(--color-danger, #F04438);
    color: var(--color-danger, #F04438);
}

.pb-member-logout:focus-visible {
    outline: 2px solid var(--color-focus-ring, rgba(47, 107, 255, .4));
    outline-offset: 1px;
}

.pb-member-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: .5rem;
    margin-top: .75rem;
}

.pb-member-action {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .375rem;
    height: 2.25rem;
    padding: 0 .5rem;
    border: 1px solid var(--color-body-line, #EAECF0);
    border-radius: var(--size-radius-sm, .5rem);
    background: #fff;
    font-size: .8125rem;
    color: var(--color-body-block, #475467);
    white-space: nowrap;
    transition: color .15s var(--ease-out, ease), border-color .15s var(--ease-out, ease),
        background-color .15s var(--ease-out, ease);
}

.pb-member-action:hover {
    border-color: var(--color-primary, #2F6BFF);
    color: var(--color-primary, #2F6BFF);
}

.pb-member-action--primary {
    border-color: var(--color-primary, #2F6BFF);
    background: var(--color-primary-light-bg, #EDF3FF);
    font-weight: 500;
    color: var(--color-primary-dark, #1D51E0);
}

.pb-member-action--primary:hover {
    background: var(--color-primary-soft, rgba(47, 107, 255, .1));
    color: var(--color-primary-dark, #1D51E0);
}

.pb-member-action:focus-visible {
    outline: 2px solid var(--color-focus-ring, rgba(47, 107, 255, .4));
    outline-offset: 1px;
}

/* ---- module store layout ---- */
.pb-alert-dismissible {
    display: flex;
    align-items: center;
    gap: .5rem;
}

.pb-alert-dismiss {
    margin-left: auto;
    color: inherit;
    opacity: .7;
    white-space: nowrap;
}

.pb-alert-dismiss:hover {
    opacity: 1;
}

.pb-store-head {
    display: flex;
    align-items: flex-end;
    gap: .75rem;
    padding: 0 .75rem;
    border-bottom: 1px solid var(--color-body-line, #EAECF0);
}

.pb-store-tabs {
    flex: 1;
    min-width: 0;
    position: relative;
}

.pb-store-tabs .el-tabs__header {
    margin: 0;
    border: 0;
}

.pb-store-tabs .el-tabs__nav-wrap::after {
    display: none;
}

.pb-store-tabs .el-tabs__item {
    height: 2.75rem;
    line-height: 2.75rem;
}

.pb-store-tabs .el-tabs__item .iconfont {
    margin-right: .125rem;
}

/* 窄屏：tab 改为横向滚动，隐藏 Element 的左右翻页箭头 */
.pb-store-tabs .el-tabs__nav-wrap.is-scrollable {
    padding: 0;
}

.pb-store-tabs .el-tabs__nav-prev,
.pb-store-tabs .el-tabs__nav-next {
    display: none;
}

.pb-store-tabs .el-tabs__nav-scroll {
    overflow-x: auto;
    overflow-y: hidden;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
}

.pb-store-tabs .el-tabs__nav-scroll::-webkit-scrollbar {
    display: none;
}

.pb-store-head-extra {
    display: flex;
    align-items: center;
    gap: .5rem;
    flex: none;
    padding-bottom: .75rem;
}

.pb-store-head-extra .el-button {
    margin-left: 0;
}

.pb-store-toolbar-hint {
    font-size: .8125rem;
    color: var(--color-tertiary, #667085);
}

.pb-inline-avatar {
    display: inline-block;
    width: 1rem;
    height: 1rem;
    border-radius: 50%;
    background-size: cover;
    background-position: center;
    vertical-align: middle;
}

/* ---- toolbar ---- */
.pb-store-toolbar {
    display: flex;
    align-items: center;
    gap: .75rem;
    flex-wrap: wrap;
    padding: .75rem;
}

.pb-store-toolbar-left {
    display: flex;
    align-items: center;
    gap: .5rem;
    flex-wrap: wrap;
}

.pb-store-toolbar-left .el-checkbox {
    margin-right: 0;
}

.pb-store-toolbar-right {
    display: flex;
    align-items: center;
    gap: .5rem;
    margin-left: auto;
}

.pb-search-input {
    width: 15rem;
}

.pb-sort-select {
    width: 8rem;
}

/* ---- filter chips ---- */
.pb-store-filters {
    padding: 0 .75rem .25rem;
}

.pb-filter-row {
    display: flex;
    align-items: flex-start;
    gap: .5rem;
    padding: .125rem 0;
}

/* 分类与类型合并到同一行，空间不足自动换行 */
.pb-filter-row--main {
    flex-wrap: wrap;
    gap: .25rem 1.25rem;
}

.pb-filter-group {
    display: flex;
    align-items: flex-start;
    gap: .5rem;
    min-width: 0;
}

/* 推荐、价格按钮与分类 chip 形态统一（图标 + 文字同一行，间距一致） */
.pb-chip--recommend,
.pb-chip--price {
    display: inline-flex;
    align-items: center;
    gap: .25rem;
}

/* 价格按钮组与分类 chip 同一行间距 */
.pb-price-chips {
    display: flex;
    align-items: center;
    gap: .375rem;
}

.pb-filter-label {
    flex: none;
    width: 3.5rem;
    display: flex;
    align-items: center;
    gap: .25rem;
    line-height: 1.75rem;
    font-size: .8125rem;
    color: var(--color-tertiary, #667085);
}

.pb-filter-chips {
    display: flex;
    flex-wrap: wrap;
    gap: .375rem;
    min-width: 0;
}

.pb-chip {
    height: 1.75rem;
    line-height: 1.625rem;
    padding: 0 .625rem;
    border: 1px solid var(--color-body-line, #EAECF0);
    border-radius: 1rem;
    background: #fff;
    font: inherit;
    font-size: .8125rem;
    color: var(--color-body-block, #475467);
    cursor: pointer;
    white-space: nowrap;
    transition: background-color .15s ease, border-color .15s ease, color .15s ease;
}

.pb-chip:hover {
    border-color: var(--color-primary, #2F6BFF);
    color: var(--color-primary, #2F6BFF);
}

.pb-chip--active,
.pb-chip--active:hover {
    background: var(--color-primary-light-bg, #EDF3FF);
    border-color: var(--color-primary-light-bg, #EDF3FF);
    color: var(--color-primary-dark, #1D51E0);
    font-weight: 500;
}

.pb-chip--more {
    border-color: transparent;
    background: transparent;
    color: var(--color-tertiary, #667085);
}

.pb-chip--more:hover {
    border-color: var(--color-body-line, #EAECF0);
    color: var(--color-tertiary, #667085);
}

/* 分类数量角标：融入 chip 内，不做成独立色块 */
.pb-chip-count {
    display: inline-block;
    margin-left: .25rem;
    font-size: .6875rem;
    line-height: 1rem;
    color: inherit;
    opacity: .65;
}

/* 二级分类行：缩进弱化，体现与一级分类的层级关系 */
.pb-filter-row--sub {
    padding-top: 0;
}

.pb-filter-row--sub .pb-filter-label {
    font-size: .75rem;
    opacity: .85;
}

.pb-chip--sub {
    height: 1.5rem;
    line-height: 1.375rem;
    font-size: .75rem;
}

.pb-chip:focus-visible {
    outline: 2px solid var(--color-focus-ring, rgba(47, 107, 255, .4));
    outline-offset: 1px;
}

/* ---- result bar ---- */
.pb-result-bar {
    display: flex;
    align-items: center;
    gap: .5rem;
    padding: .25rem .75rem .5rem;
    font-size: .8125rem;
    color: var(--color-tertiary, #667085);
}

.pb-result-count b {
    font-weight: 600;
    color: var(--color-heading, #101828);
}

.pb-result-hint {
    color: var(--color-tertiary, #667085);
}

.pb-result-clear {
    margin-left: auto;
}

/* 已选条件回显 */
.pb-result-tags {
    display: flex;
    align-items: center;
    gap: .375rem;
    flex-wrap: wrap;
}

.pb-result-tag {
    display: inline-flex;
    align-items: center;
    gap: .25rem;
    height: 1.375rem;
    padding: 0 .5rem;
    border: 1px solid var(--color-primary-light-bg, #EDF3FF);
    border-radius: 1rem;
    background: var(--color-primary-light-bg, #EDF3FF);
    font: inherit;
    font-size: .75rem;
    color: var(--color-primary-dark, #1D51E0);
    cursor: pointer;
    white-space: nowrap;
}

.pb-result-tag:hover {
    border-color: var(--color-primary, #2F6BFF);
}

.pb-result-tag .iconfont {
    font-size: .625rem;
    opacity: .7;
}

.pb-result-tag:hover .iconfont {
    opacity: 1;
}

.pb-result-tag:focus-visible {
    outline: 2px solid var(--color-focus-ring, rgba(47, 107, 255, .4));
    outline-offset: 1px;
}

/* ---- card grid ---- */
.pb-module-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(19rem, 1fr));
    gap: 1rem;
    padding: 0 .75rem .75rem;
}

.pb-module-card {
    display: flex;
    flex-direction: column;
    padding: 0;
    overflow: hidden;
    background: #fff;
    border: 1px solid var(--color-body-line, #EAECF0);
    border-radius: var(--size-radius-sm, .5rem);
    cursor: pointer;
    transition: box-shadow .18s ease, border-color .18s ease, transform .18s ease;
}

.pb-module-card:hover {
    border-color: var(--color-primary, #2F6BFF);
    box-shadow: 0 8px 24px rgba(16, 24, 40, .08);
    transform: translateY(-2px);
}

.pb-module-card--skeleton {
    cursor: default;
    min-height: 17rem;
}

.pb-module-card--skeleton:hover {
    border-color: var(--color-body-line, #EAECF0);
    box-shadow: none;
    transform: none;
}

.pb-module-card--disabled .pb-card-cover {
    opacity: .65;
}

.pb-module-card--static {
    cursor: default;
}

.pb-module-card--static:hover {
    border-color: var(--color-body-line, #EAECF0);
    box-shadow: none;
    transform: none;
}

.pb-module-card:focus-visible,
.pb-module-card:focus-within {
    border-color: var(--color-primary, #2F6BFF);
    box-shadow: 0 0 0 3px var(--color-focus-ring, rgba(47, 107, 255, .25));
}

.pb-card-main {
    display: flex;
    flex-direction: column;
    flex: 1;
    min-width: 0;
}

/* 应用市场式大封面：全宽 16:9，padding-top 兼容旧浏览器 */
.pb-card-cover {
    position: relative;
    width: 100%;
    height: 0;
    padding-top: 56.25%;
    overflow: hidden;
    background: var(--color-body-block-bg, #F2F4F7);
}

.pb-card-cover-img {
    position: absolute;
    top: 0;
    left: 0;
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .32s var(--ease-out, cubic-bezier(.16, 1, .3, 1));
}

.pb-module-card:hover .pb-card-cover-img {
    transform: scale(1.04);
}

.pb-card-cover-placeholder {
    position: absolute;
    top: 0;
    left: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    font-size: 2rem;
    color: #fff;
    background: linear-gradient(135deg, #93B4FF, #5B8CFF);
}

/* 整卡可点击的提示：居中胶囊，避免遮挡截图内容 */
.pb-card-cover-hint {
    position: absolute;
    top: 50%;
    left: 50%;
    z-index: 3;
    display: inline-flex;
    align-items: center;
    gap: .25rem;
    height: 2rem;
    padding: 0 .75rem;
    border-radius: 1rem;
    font-size: .75rem;
    color: #fff;
    white-space: nowrap;
    background: rgba(16, 24, 40, .72);
    opacity: 0;
    transform: translate(-50%, -50%) scale(.96);
    transition: opacity .18s var(--ease-out, cubic-bezier(.16, 1, .3, 1)),
        transform .18s var(--ease-out, cubic-bezier(.16, 1, .3, 1));
    pointer-events: none;
}

.pb-module-card:hover .pb-card-cover-hint,
.pb-module-card:focus-within .pb-card-cover-hint {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
}

.pb-badge {
    position: absolute;
    top: 0;
    left: 0;
    z-index: 2;
    padding: .25rem .375rem;
    border-radius: 0 0 .375rem 0;
    font-size: .6875rem;
    line-height: 1;
    font-weight: 500;
    color: #fff;
}

.pb-badge--success {
    background: var(--color-success, #12B76A);
}

.pb-badge--warning {
    background: var(--color-warning, #F79009);
}

.pb-badge--primary {
    background: var(--color-primary, #2F6BFF);
}

.pb-badge--muted {
    background: rgba(16, 24, 40, .78);
}

.pb-card-info {
    display: flex;
    flex-direction: column;
    gap: .375rem;
    flex: 1;
    min-width: 0;
    padding: .75rem .75rem 0;
}

.pb-card-title-row {
    display: flex;
    align-items: flex-start;
    gap: .375rem;
    min-width: 0;
}

.pb-card-title {
    flex: 1 1 auto;
    min-width: 0;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    overflow: hidden;
    font-size: .875rem;
    line-height: 1.375rem;
    font-weight: 600;
    color: var(--color-body-block, #344054);
    overflow-wrap: anywhere;
}

.pb-card-title:hover {
    color: var(--color-primary, #2F6BFF);
}

/* 价格浮层：白底 + 描边，保证落在任何封面上都可读 */
.pb-price {
    position: absolute;
    top: .5rem;
    right: .5rem;
    z-index: 2;
    padding: 0 .5rem;
    border: 1px solid rgba(16, 24, 40, .08);
    border-radius: 1rem;
    font-size: .75rem;
    line-height: 1.375rem;
    font-weight: 500;
    white-space: nowrap;
    background: #fff;
    color: var(--color-primary-dark, #1D51E0);
    box-shadow: 0 1px 3px rgba(16, 24, 40, .16);
}

.pb-price--free {
    background: #fff;
    border-color: rgba(2, 122, 72, .2);
    color: var(--color-success-dark, #027A48);
}

.pb-card-desc {
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    overflow: hidden;
    min-height: 2.4375rem;
    font-size: .8125rem;
    line-height: 1.21875rem;
    color: var(--color-tertiary, #667085);
    overflow-wrap: anywhere;
}

.pb-card-meta {
    display: flex;
    align-items: center;
    gap: .25rem;
    flex-wrap: wrap;
    margin-top: auto;
    padding-top: .125rem;
    font-size: .75rem;
    color: var(--color-tertiary, #667085);
}

.pb-meta-dot {
    color: var(--color-tertiary-light, #D0D5DD);
}

.pb-meta-arrow,
.pb-meta-latest {
    color: var(--color-warning-dark, #B54708);
}

.pb-card-footer {
    display: flex;
    align-items: center;
    gap: .5rem;
    margin: 0 .75rem;
    padding: .625rem 0 .75rem;
    border-top: 1px solid var(--color-body-block-bg, #F2F4F7);
}

/* 模块标识对普通用户价值低，hover 时才显示，给操作区让位 */
.pb-card-code {
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-size: .75rem;
    color: var(--color-tertiary, #667085);
    opacity: 0;
    transition: opacity .18s var(--ease-out, cubic-bezier(.16, 1, .3, 1));
}

.pb-module-card:hover .pb-card-code,
.pb-module-card:focus-within .pb-card-code {
    opacity: 1;
}

.pb-card-footer--plain {
    border-top-color: transparent;
    padding: .25rem 0 .75rem;
}

.pb-card-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    flex-wrap: wrap;
    gap: .625rem;
    margin-left: auto;
    flex: none;
}

.pb-action {
    padding: 0;
    border: 0;
    background: none;
    font: inherit;
    font-size: .8125rem;
    color: var(--color-body-block, #475467);
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: .125rem;
    white-space: nowrap;
}

.pb-action:hover {
    color: var(--color-primary, #2F6BFF);
}

/* 启用是已安装卡片的主操作，需与危险的「卸载」拉开层级 */
.pb-action--primary {
    color: var(--color-primary, #2F6BFF);
    font-weight: 500;
}

.pb-action--primary:hover {
    color: var(--color-primary-dark, #1D51E0);
}

.pb-action.ub-text-danger:hover {
    color: var(--color-danger-dark, #B42318);
}

.pb-action.ub-text-warning:hover {
    color: var(--color-warning-dark, #B54708);
}

.pb-action:focus-visible {
    outline: 2px solid var(--color-focus-ring, rgba(47, 107, 255, .4));
    outline-offset: 2px;
}

/* ---- skeleton ---- */
.pb-skeleton-block,
.pb-skeleton-line {
    background: linear-gradient(90deg, #F2F4F7 25%, #E9ECF2 37%, #F2F4F7 63%);
    background-size: 400% 100%;
    animation: pb-skeleton-loading 1.4s ease infinite;
}

.pb-skeleton-line {
    height: .75rem;
    border-radius: .25rem;
}

.pb-skeleton-line + .pb-skeleton-line {
    margin-top: .5rem;
}

@keyframes pb-skeleton-loading {
    0% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0 50%;
    }
}

/* ---- pager ---- */
.pb-store-pager {
    display: flex;
    justify-content: center;
    padding: .5rem 0 1rem;
}

@media (max-width: 40rem) {
    .pb-store-toolbar-right {
        margin-left: 0;
        width: 100%;
    }

    .pb-search-input {
        width: auto;
        flex: 1;
    }

    .pb-filter-label {
        width: 3.25rem;
    }

    .pb-filter-row--main {
        gap: .25rem 0;
    }

    .pb-filter-group {
        flex: 1 1 100%;
    }

    /* 移动端筛选 chip 单行横向滚动，避免 5 行 chip 占满首屏 */
    .pb-filter-chips {
        flex-wrap: nowrap;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        padding-bottom: .125rem;
    }

    .pb-filter-chips::-webkit-scrollbar {
        display: none;
    }

    /* 横向滚动的筛选行右缘渐隐，提示后面还有内容 */
    .pb-filter-group,
    .pb-filter-row--sub {
        position: relative;
    }

    .pb-filter-group::after,
    .pb-filter-row--sub::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        width: 1.5rem;
        background: linear-gradient(to right, rgba(255, 255, 255, 0), #fff 72%);
        pointer-events: none;
    }

    /* tab 右缘渐隐，提示后面还有 tab */
    .pb-store-tabs::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        width: 2.5rem;
        background: linear-gradient(to right, rgba(255, 255, 255, 0), #fff 65%);
        pointer-events: none;
    }

    /* 版本弹窗在窄屏改为块状列表，避免四列被压成一字一行 */
    .pb-version-table thead {
        display: none;
    }

    .pb-version-table,
    .pb-version-table tbody,
    .pb-version-table tr,
    .pb-version-table td {
        display: block;
        width: auto;
    }

    .pb-version-table tr {
        position: relative;
        padding: .75rem;
        border-bottom: 1px solid var(--color-body-block-bg, #F2F4F7);
    }

    .pb-version-table tr:last-child {
        border-bottom: 0;
    }

    .pb-version-table td {
        padding: 0;
        border: 0;
    }

    .pb-version-col-version {
        padding-right: 5.5rem;
        margin-bottom: .25rem;
    }

    .pb-version-col-time {
        margin-top: .25rem;
        font-size: .75rem;
    }

    .pb-version-col-action {
        position: absolute;
        top: .75rem;
        right: .75rem;
        width: auto;
    }

    /* 触屏下安装按钮加大点击区 */
    .pb-version-col-action .pb-version-install {
        height: 2.25rem;
        padding: 0 .875rem;
    }

    .pb-version-row--latest td {
        background: none;
    }

    .pb-version-row--latest {
        background: rgba(47, 107, 255, .04);
    }
}

/* 触屏无 hover：标识与操作常驻可见 */
@media (hover: none) {
    .pb-card-code {
        opacity: 1;
    }
}

@media (prefers-reduced-motion: reduce) {
    .pb-module-card {
        transition: none;
    }

    .pb-module-card:hover {
        transform: none;
    }

    .pb-module-card:hover .pb-card-cover-img {
        transform: none;
    }

    .pb-card-cover-img,
    .pb-card-cover-hint,
    .pb-card-code {
        transition: none;
    }

    .pb-skeleton-block,
    .pb-skeleton-line {
        animation: none;
    }
}
</style>
