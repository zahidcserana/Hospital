<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Site Settings Defaults
    |--------------------------------------------------------------------------
    |
    */

    'appSettings' => [
        'title' => 'MONIPAL AFC HOSPITALS',
        'limitCheck' => false,
        'stockLimit' => 0.10,
        'requestLimit' => 2, // 2 times Multiply with stock_limit
    ],

    'req_status_dept' => [
        'INITIATING' => 'INITIATING',
        'INITIATED' => 'SUBMIT',
        'VERIFIED' => 'VERIFIED',
        'APPROVED_BY_MS' => 'APPROVED BY MS',
        'APPROVED_BY_FD' => 'RECOMMENDED BY FD',
        'PROCESSING' => 'ACCEPT',
        'DELIVERED' => 'DELIVERED',
        'RECEIVED' => 'RECEIVE'
    ],
    'requisition_access_dept' => [
        'Admin' => ['INITIATING', 'INITIATED', 'VERIFIED', 'APPROVED_BY_MS', 'APPROVED_BY_FD', 'PROCESSING', 'DELIVERED', 'RECEIVED'],
        'SAdmin' => ['INITIATING', 'INITIATED', 'VERIFIED', 'APPROVED_BY_MS', 'APPROVED_BY_FD', 'PROCESSING', 'DELIVERED', 'RECEIVED'],
        'DA' => ['INITIATING', 'DELIVERED', 'RECEIVED'],
        'DH' => ['INITIATED'],
        'MS' => ['VERIFIED'],
        'FD' => ['APPROVED_BY_MS'],
        'SA' => ['APPROVED_BY_FD', 'PROCESSING'],
    ],

    'd2sPending' => [
        'Admin' => 'INITIATED',
        'SAdmin' => 'INITIATED',
        'DA' => 'INITIATING',
        'DH' => 'INITIATED',
        'MS' => 'VERIFIED',
        'FD' => 'APPROVED_BY_MS',
        'SA' => 'APPROVED_BY_FD',
    ],

    'd2sApproved' => [
        'Admin' => 'APPROVED_BY_FD',
        'SAdmin' => 'APPROVED_BY_FD',
        'DA' => 'INITIATED',
        'DH' => 'VERIFIED',
        'MS' => 'APPROVED_BY_MS',
        'FD' => 'APPROVED_BY_FD',
        'SA' => 'PROCESSING',
    ],

    'req_status' => [
        'LOCAL_PURCHASE' => 'LOCAL PURCHASE',
        'INITIATING' => 'INITIATING',
        'INITIATED' => 'SUBMIT',
        'APPROVED_BY_FD' => 'APPROVED BY FD',
        'ACCEPTED' => 'ACCEPT',
        'APPROVED_BY_GM' => 'RECOMMEND BY GM',
        'APPROVED_BY_DF' => 'APPROVED BY DF',
        'PROCESSING' => 'PROCESSING',
        'DELIVERED' => 'DELIVERED',
        'RECEIVED' => 'RECEIVE',
    ],

    'requisition_access' => [
        'Admin' => ['INITIATING', 'INITIATED', 'APPROVED_BY_FD', 'ACCEPTED', 'APPROVED_BY_GM', 'APPROVED_BY_DF', 'PROCESSING', 'DELIVERED', 'LOCAL_PURCHASE', 'RECEIVED'],
        'SAdmin' => ['INITIATING', 'INITIATED', 'APPROVED_BY_FD', 'ACCEPTED', 'APPROVED_BY_GM', 'APPROVED_BY_DF', 'PROCESSING', 'DELIVERED', 'LOCAL_PURCHASE', 'RECEIVED'],
        'SA' => ['INITIATING', 'DELIVERED', 'LOCAL_PURCHASE', 'RECEIVED'],
        'FD' => ['INITIATED'],
        'HO' => ['APPROVED_BY_FD', 'APPROVED_BY_DF', 'PROCESSING'],
        'GM' => ['ACCEPTED'],
        'DF' => ['APPROVED_BY_GM'],
    ],

    's2hPending' => [
        'Admin' => 'INITIATED',
        'SAdmin' => 'INITIATED',
        'SA' => 'INITIATING',
        'FD' => 'INITIATED',
        'HO' => 'APPROVED_BY_FD',
        'GM' => 'ACCEPTED',
        'DF' => 'APPROVED_BY_GM',
    ],

    's2hApproved' => [
        'Admin' => 'APPROVED_BY_DF',
        'SAdmin' => 'APPROVED_BY_DF',
        'SA' => 'INITIATED',
        'FD' => 'APPROVED_BY_FD',
        'HO' => 'ACCEPTED',
        'GM' => 'APPROVED_BY_GM',
        'DF' => 'APPROVED_BY_FD',
    ],

    'menu_access' => [
        'Admin' => [
            'mySummery', 'dashboard', 'accounts', 'doctors',  'system_user',  'users', 'patients', 'patientAdmissions', 'requisitions', 's2h_requisitions', 'd2s_requisitions',
            'inventories', 'consumptions', 'medicine-consumption', 'payments', 'opdBillings', 'ipdBillings', 'reports', 'accounts', 'settings',
            'opdBilling', 'ipdBilling', 'pendingRefund', 'payment', 'patientAdmission', 'corporateClient', 'referralSetting',
        ],
        'SAdmin' => [
            'mySummery', 'dashboard', 'users', 'patients', 'patientAdmissions', 'requisitions', 's2h_requisitions', 'd2s_requisitions',
            'inventories', 'consumptions', 'medicine-consumption', 'payments', 'opdBillings', 'ipdBillings', 'reports', 'accounts', 'settings',
            'opdBilling', 'ipdBilling', 'pendingRefund', 'payment', 'patientAdmission', 'corporateClient', 'referralSetting',
        ],
        'SA' => ['requisitions', 's2h_requisitions', 'd2s_requisitions', 'reports'],
        'FD' => ['requisitions', 's2h_requisitions', 'd2s_requisitions', 'reports', 'pendingRefund', 'accounts'],
        'HO' => ['requisitions', 's2h_requisitions', 'reports'],
        'GM' => ['requisitions', 's2h_requisitions', 'reports', 'opdBillings', 'ipdBillings'],
        'DF' => ['requisitions', 's2h_requisitions', 'reports', 'opdBillings', 'ipdBillings'],
        'DA' => ['requisitions', 'd2s_requisitions', 'reports', 'consumptions', 'medicine-consumption'],
        'DH' => ['requisitions', 'd2s_requisitions', 'reports', 'consumptions', 'medicine-consumption'],
        'MS' => ['requisitions', 'd2s_requisitions', 'reports'],
        'Accounts' => ['accounts', 'reports'],
        'BM' => ['mySummery', 'opdBilling', 'reports', 'referralSetting', 'payment', 'ipdBilling', 'patients',  'corporateClient', 'patientAdmission', 'accounts', 'doctors', 'users', 'pendingRefund'],
        'BE' => ['mySummery', 'opdBilling', 'reports', 'payment', 'ipdBilling', 'patients',  'corporateClient', 'patientAdmission', 'accounts', 'doctors', 'users'],
    ],

    'purchaseTypes' => [
        'WORK_ORDER' => 'Work Order',
        'CASH' => 'Cash',
    ]

];
