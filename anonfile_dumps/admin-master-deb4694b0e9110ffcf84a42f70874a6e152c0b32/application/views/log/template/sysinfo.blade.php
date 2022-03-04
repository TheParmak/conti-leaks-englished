<div ng-controller="sysinfo">
    <div id="sysInfo" class="well well-small" ng-cloak ng-show="bigTotalItems" ng-init="apply()">
        <uib-accordion close-others="true">
            <div uib-accordion-group ng-repeat="(i, info) in data" is-open="isOpen" class="panel-default">
                <uib-accordion-heading>
                    <div class="panel-title" role="button">
                        @{{ info.created_at }}
                    </div>
                </uib-accordion-heading>
                <div datafiles-element="@{{ i }}">
                    <div class="software-visual-@{{ i }}"></div>
                </div>
                <hr>
                <pre class="datageneral-data-@{{ i }}" style="overflow-y: auto; max-height: 400px;" ng-bind-html="compileData(info.data)"></pre>
            </div>
        </uib-accordion>
        <div ng-show="showPagination()" class="text-center">
            <ul uib-pagination total-items="bigTotalItems" ng-model="bigCurrentPage" max-size="maxSize" class="pagination-sm" boundary-link-numbers="true" ng-change="changePage()" items-per-page="itemsPerPage" previous-text="&laquo;" next-text="&raquo;"></ul>
        </div>
    </div>
</div>

<script>
    $('#myModalDataFiles').on('show.bs.modal', function () {
        $('.modal-body').css('max-height',$( window ).height()*0.8);
    });

    app.directive('datafilesElement', function () {
        return function (scope, element, attrs) {
            scope.initSoftwareVisual(element, attrs.datafilesElement);
        };
    });

    app.controller('sysinfo', function($sce, $controller, $scope, $http){
        $controller('table', {$scope: $scope});
        $scope.sysinfo = {};
        $scope.client_system = '{{ trim($client->sys_ver) }}';
        $scope.isOpen = "$first";
        $scope.filter = {
            client_id: '{{ $client->id }}'
        };

        $scope.systems = {
            'Windows 7 x86 SP1': {
                operation_system: 'windows-7-32bit',
                service_pack: 'service-pack-1'
            },
            'Windows 8 x64': {
                operation_system: 'windows-8',
                service_pack: null
            },
            'Windows 7 x64 SP1': {
                operation_system: 'windows-7-64bit',
                service_pack:  'service-pack-1'
            },
            'Windows 7 x86 SP2': {
                operation_system: 'windows-7-64bit',
                service_pack: 'service-pack-2'
            },
            'Windows 10 x64': {
                operation_system: 'windows-10-64bit',
                service_pack: null
            },
            'Windows Server 2003 x86 SP3': {
                operation_system: 'windows-xp-32bit',
                service_pack: null
            },
            'x86 Windows 7': {
                operation_system: 'windows-7-32bit',
                service_pack: null
            },
            'x64 Windows 7': {
                operation_system: 'windows-7-64bit',
                service_pack: null
            },
            'Windows Vista x86': {
                operation_system: 'windows-vista-32bit',
                service_pack: null
            },
            'Windows Vista x86 SP2': {
                operation_system: 'windows-vista-32bit',
                service_pack: 'service-pack-2'
            },
            'Windows 7 x86': {
                operation_system: 'windows-7-32bit',
                service_pack: null
            },
            'Windows XP x86 SP3': {
                operation_system: 'windows-xp-32bit',
                service_pack: null
            },
            'Windows 8.1 x64': {
                operation_system: 'windows-8-64bit',
                service_pack: 'service-pack-1'
            },
            'x64 Windows 8.1': {
                operation_system: 'windows-8-64bit',
                service_pack: 'service-pack-1'
            },
            'Windows 7 x64': {
                operation_system: 'windows-7-64bit',
                service_pack: null
            },
            'x86 Windows 8': {
                operation_system: 'windows-8-32bit',
                service_pack: null
            },
            'Windows Server 2008 R2 x64': {
                operation_system: 'windows-xp',
                service_pack: null
            },
            'Windows Server 2008 R2 x64 SP1': {
                operation_system: 'windows-xp-64bit',
                service_pack: 'service-pack-1'
            },
            'Windows Server 2008 x86 SP1': {
                operation_system: 'windows-xp-32bit',
                service_pack: 'service-pack-1'
            },
            'Windows Server 2008 x86 SP2': {
                operation_system: 'windows-xp-32bit',
                service_pack: 'service-pack-2'
            },
            'Windows XP x86 SP4': {
                operation_system: 'windows-xp-32bit',
                service_pack: 'service-pack-4'
            },
            'Windows 8 x86': {
                operation_system: 'windows-8-32bit',
                service_pack: null
            },
            'Windows 7 x86 SP3': {
                operation_system: 'windows-7-32bit',
                service_pack: 'service-pack-3'
            },
            'x64 Windows 8': {
                operation_system: 'windows-8-64bit',
                service_pack: null
            },
            'Windows 10 Server x64': {
                operation_system: 'windows-10-64bit',
                service_pack: null
            },
            'x64 Windows 10': {
                operation_system: 'windows-10-64bit',
                service_pack: null
            },
            'Windows Server 2012 x64': {
                operation_system: 'windows-10-64bit',
                service_pack: null
            },
            'Windows Server 2012 R2 x64': {
                operation_system: 'windows-10-64bit',
                service_pack: null
            }
        };
        $scope.softwareMatch = {
            'browser': {
                label: 'Browser',
                packages: {
                    'firefox': {
                        title: 'Mozilla Firefox',
                        url: 'https://www.mozilla.org/firefox/',
                        regexs: [
                            'Mozilla Firefox ',
                            'Mozilla Maintenance Service', // Service
                        ]
                    },
                    'firefox-dev-ed': {
                        title: 'Mozilla Firefox (Developer Edition)',
                        url: 'https://www.mozilla.org/firefox/developer/',
                        regexs: [
                            'Firefox Developer Edition ',
                            'Mozilla Firefox Developer Edition ',
                        ]
                    },
                    'chrome': {
                        title: 'Google Chrome',
                        url: 'https://www.google.com/chrome/browser/desktop/',
                        regexs: [
                            'Google Chrome',
                            'Google Update Service \\(gupdate\\)', // Service
                            'Служба Google Update \\(gupdate\\)', // Service
                            'Google Update Service \\(gupdatem\\)', // Service
                            'Служба Google Update \\(gupdatem\\)', // Service
                            'Google Software Updater',
                            'Google Update Helper',
                        ]
                    },
                    'safari': {
                        title: 'Apple Safari',
                        url: 'https://support.apple.com/downloads/safari',
                        regexs: [
                            'Safari '
                        ]
                    },
                    'opera': {
                        title: 'Opera Software Opera Classic',
                        url: 'http://www.opera.com/download/guide/?ver=12.17&local=y',
                        regexs: [
                            'Opera \\d+\\.\\d+'
                        ]
                    },
                    'opera-blink': {
                        title: 'Opera Software Opera Blink',
                        url: 'http://www.opera.com/download/guide/',
                        regexs: [
                            'Opera Stable '
                        ]
                    },
                    'palemoon': {
                        title: 'Moonchild Productions Palemoon',
                        url: 'https://www.palemoon.org/',
                        regexs: [
                            'Pale Moon '
                        ]
                    },
                    'waterfox': {
                        title: 'Waterfox Project Waterfox',
                        url: 'https://www.waterfoxproject.org/',
                        regexs: [
                            'Waterfox '
                        ]
                    },
                    'dragon': {
                        title: 'Comodo Dragon Browser',
                        url: 'https://www.comodo.com/home/browsers-toolbars/browser.php',
                        regexs: [
                            'Comodo Dragon'
                        ]
                    },
                    'maxthon': {
                        title: 'Maxthon Maxthon',
                        url: 'http://www.maxthon.com/',
                        regexs: [
                            'Maxthon'
                        ]
                    },
                    'sogou': {
                        title: 'Sogou Explorer',
                        url: 'http://ie.sogou.com/',
                        regexs: [
                            '搜狗高速浏览器'
                        ]
                    },
                    '360safe': {
                        title: '360safe Browser',
                        url: 'http://www.360safe.com/browser.html',
                        regexs: [
                            '360安全浏览器 \\d+\\.\\d 正式版'
                        ]
                    }
                }
            },
            /* TODO get it from config  sysinfo */
            'protection': {
                label: 'Antivirus & Firewall',
                packages: {
                    'kaspersky': {
                        title: 'Kaspersky',
                        url: 'http://www.kaspersky.com/',
                        regexs: [
                            'Kaspersky '
                        ]
                    },
                    'bitdefender': {
                        title: 'BitDefender',
                        url: 'http://www.bitdefender.com/',
                        regexs: [
                            'BitDefender '
                        ]
                    },
                    'eset': {
                        title: 'ESET',
                        url: 'http://www.eset.com/',
                        regexs: [
                            'ESET '
                        ]
                    },
                    'avast': {
                        title: 'Avast',
                        url: 'https://www.avast.com/',
                        regexs: [
                            'Avast ',
                            'avast\!'
                        ]
                    },
                    'trendmicro': {
                        title: 'Trend Micro',
                        url: 'http://www.trendmicro.com/',
                        regexs: [
                            'Trend Micro '
                        ]
                    },
                    'drweb': {
                        title: 'Dr.Web',
                        url: 'http://www.drweb.com/',
                        regexs: [
                            'Dr\\.Web '
                        ]
                    },
                    'avg': {
                        title: 'AVG',
                        url: 'http://www.avg.com/',
                        regexs: [
                            'AVG '
                        ]
                    },
                    'avira': {
                        title: 'Avira',
                        url: 'https://www.avira.com/',
                        regexs: [
                            'Avira '
                        ]
                    },
                    'norton': {
                        title: 'Symantec Norton',
                        url: 'http://norton.com/products',
                        regexs: [
                            'Norton Internet Security',
                            'Norton Antivirus',
                            'Symantec Endpoint',
                            'Norton Endpoint',
                        ]
                    },
                    'mcafee': {
                        title: 'McAfee',
                        url: 'http://www.mcafee.com/',
                        regexs: [
                            'McAfee '
                        ]
                    },
                    '360safeguard': {
                        title: '360safe Guard',
                        url: 'http://www.360.cn/weishi/index.html',
                        regexs: [
                            '360安全卫士'
                        ]
                    },
                    'duba': {
                        title: 'Duba (Goku)',
                        url: 'http://www.xindubawukong.com/',
                        regexs: [
                            '新毒霸\\(悟空\\)'
                        ]
                    },
                    'sophos': {
                        title: 'Sophos',
                        url: 'http://www.sophos.com',
                        regexs: [
                            'Sophos '
                        ]
                    },
                    'microsoft-security-essentials': {
                        title: 'Microsoft Security Essentials',
                        url: 'http://windows.microsoft.com/en-us/windows/security-essentials-download',
                        regexs: [
                            'Microsoft Security Essentials$'
                        ]
                    },
                    'windows-defender': {
                        title: 'Windows Defender',
                        url: 'www.microsoft.com/security/pc-security/windows-defender.aspx',
                        regexs: [
                            'Windows Defender'
                        ]
                    }
                }
            },
            'document-management': {
                label: 'Office',
                packages: {
                    'word-2003': {
                        title: 'Microsoft Word 2003',
                        regexs: [
                            'Microsoft Office (Standard|Professional|Стандартный|Профессиональный|.*) 2003',
                            'Microsoft Office Word [^0-9]+2003'
                        ]
                    },
                    'excel-2003': {
                        title: 'Microsoft Excel 2003',
                        regexs: [
                            'Microsoft Office Excel [^0-9]+2003'
                        ]
                    },
                    'powerpoint-2003': {
                        title: 'Microsoft PowerPoint 2003',
                        regexs: [
                            'Microsoft Office PowerPoint [^0-9]+2003'
                        ]
                    },
                    'outlook-2003': {
                        title: 'Microsoft Outlook 2003',
                        regexs: [
                            'Microsoft Office Outlook [^0-9]+2003'
                        ]
                    },
                    'word-2007': {
                        title: 'Microsoft Word 2007',
                        regexs: [
                            'Microsoft Office (Standard|Professional|Professional Hybrid|Стандартный|Профессиональный|.*) 2007',
                            'Microsoft Office Word [^0-9]+2007'
                        ]
                    },
                    'excel-2007': {
                        title: 'Microsoft Excel 2007',
                        regexs: [
                            'Microsoft Office Excel [^0-9]+2007'
                        ]
                    },
                    'powerpoint-2007': {
                        title: 'Microsoft PowerPoint 2007',
                        regexs: [
                            'Microsoft Office PowerPoint [^0-9]+2007'
                        ]
                    },
                    'outlook-2007': {
                        title: 'Microsoft Outlook 2007',
                        regexs: [
                            'Microsoft Office Outlook [^0-9]+2007'
                        ]
                    },
                    'word-2010': {
                        title: 'Microsoft Word 2010',
                        regexs: [
                            'Microsoft Office (Standard|Professional|Professional Plus|Стандартный|Профессиональный|Office 64-bit Components|.*) 2010',
                            'Microsoft Office Word [^0-9]+2010'
                        ]
                    },
                    'excel-2010': {
                        title: 'Microsoft Excel 2010',
                        regexs: [
                            'Microsoft Office Excel [^0-9]+2010'
                        ]
                    },
                    'powerpoint-2010': {
                        title: 'Microsoft PowerPoint 2010',
                        regexs: [
                            'Microsoft Office PowerPoint [^0-9]+2010'
                        ]
                    },
                    'outlook-2010': {
                        title: 'Microsoft Outlook 2010',
                        regexs: [
                            'Microsoft Office Outlook [^0-9]+2010'
                        ]
                    },
                    'word-2013': {
                        title: 'Microsoft Word 2013',
                        regexs: [
                            'Microsoft Office (Standard|Professional|Стандартный|Профессиональный|.*) 2013',
                            'Microsoft Word [^0-9]+2013'
                        ]
                    },
                    'excel-2013': {
                        title: 'Microsoft Excel 2013',
                        regexs: [
                            'Microsoft Excel [^0-9]+2013'
                        ]
                    },
                    'powerpoint-2013': {
                        title: 'Microsoft PowerPoint 2013',
                        regexs: [
                            'Microsoft PowerPoint [^0-9]+2013'
                        ]
                    },
                    'outlook-2013': {
                        title: 'Microsoft Outlook 2013',
                        regexs: [
                            'Microsoft Outlook [^0-9]+2013'
                        ]
                    }
                }
            }
        };

        $scope.compileData = function(html){
            return $sce.trustAsHtml(html);
        };

        $scope.initSoftwareVisual = function(visual, number){
            var softwareVisual = $(visual);
            var datageneral = $scope.data[number].data;
            var img = undefined;

            console.log($scope.client_system, $scope.systems.hasOwnProperty($scope.client_system)); // todo

            if (!$scope.systems.hasOwnProperty($scope.client_system)) {
                angular.element('<img>').prop({
                    'title': $scope.client_system,
                    'alt': 'unknown',
                    'src': '/template/img/os-unknown.png',
                    'style': 'margin-right: 4px',
                }).appendTo($scope.addElement('os', 'OS', softwareVisual));
            } else {
                var system = $scope.systems[$scope.client_system];

                $img = angular.element('<img>').prop({
                    'title': $scope.client_system,
                    'alt': system.operation_system,
                    'src': '/template/img/os-' + system.operation_system + '.png',
                    'style': 'margin-right: 4px'
                }).appendTo($scope.addElement('os', 'OS', softwareVisual));

                if ( system.service_pack ) {
                    angular.element('<img>').prop({
                        'title': $scope.client_system,
                        'alt': system.operation_system,
                        'src': '/template/img/os-' + system.service_pack + '.png',
                        'style': 'margin-right: 4px'
                    }).appendTo($img.parent());
                }
            }
            $scope.fastTooltip($img);

            for(var softwareType in $scope.softwareMatch) {
                var software = $scope.softwareMatch[softwareType];

                var imgs = [];

                for(var package in software.packages) {
                    var packageMeta = software.packages[package];
                    var regexs = packageMeta.regexs;

                    for(var i = 0; i < regexs.length; ++i) {
                        var matcher = new RegExp(regexs[i], 'm');
                        if ( matcher.test(datageneral) ) {
                            imgs.push(
                                $scope.fastTooltip(
                                    angular.element('<img>').prop({
                                        'title': packageMeta.title ? packageMeta.title: '',
                                        'alt': software.label + ' ' + package,
                                        'src': '/template/img/' + softwareType + '-' + package + '.png',
                                        'style': 'margin-right: 4px'
                                    })
                                )
                            );
                            break;
                        }
                    }
                }

                if(imgs.length){
                    var merged = angular.element('<span></span>');

                    imgs.forEach(function(img) {
                        merged.append(img[0]);
                    });

                    merged.appendTo($scope.addElement(softwareType, software.label, softwareVisual));
                }
            }
        };

        $scope.fastTooltip = function($img){
            return $img.tooltip({show: {effect: "none", duration: 0}});
        };

        $scope.addElement = function (softwareType, label, softwareVisual) {
            return angular.element('<div>').prop({
                'class': 'detected-'+softwareType+'s',
                'style': 'margin-bottom: 6px'
            }).html(angular.element('<span>').prop({
                'style': 'display: inline-block; width: 150px;'
            }).html(label))
            .appendTo(softwareVisual);
        };

        $scope.sendPost = function(){
            $http.post('/rest/clients/sys_info/' + $scope.bigCurrentPage, $scope.post).success(function(data){
                $scope.data = data['data'];
                $scope.bigCurrentPage = data['current_page'];
                $scope.bigTotalItems = data['total_items'];
                $scope.itemsPerPage = data['items_per_page'];
            });
        };

        $scope.changePage = function(){
            $scope.isOpen = "";
            $scope.sendPost();
        };
    });
</script>

<style>
    .panel-default > .panel-heading{
        background-image: none;
        background-color: #202020;
        color: #c6c6c6;
    }

    .panel-heading{
        padding-bottom: 2px;
        padding-top:  2px;
    }
    .panel-heading .panel-title .collapse-toggle{
        display: block;
        padding: 10px;
    }

    .accordion-toggle:focus{outline: none !important;}

    ul {
        padding: 0;
        margin-left: 10px;
    }

    .pagination{
        margin-bottom: 0;
        margin-top: 0;
    }
</style>