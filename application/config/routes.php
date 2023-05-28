<?php

return [
	// MainController
	'' => [
		'controller' => 'main',
		'action' => 'index',
	],
	'main/index/{page:\d+}' => [
		'controller' => 'main',
		'action' => 'index',
	],
	'post/{id:\d+}' => [
		'controller' => 'main',
		'action' => 'post',
	],
    'auth/login' => [
        'controller' => 'auth\login',
        'action' => 'login',
    ],'auth/index' => [
        'controller' => 'auth\login',

        'action' => 'index',
    ],'auth/logout' => [
        'controller' => 'auth\logout',
        'action' => 'logout',
    ],'SurveyCreate' => [
        'controller' => 'Survey',
        'action' => 'create',
    ],'logout' => [
        'controller' => 'auth\logout',
        'action' => 'logout',
    ],'surveys' => [
        'controller' => 'Survey',
        'action' => 'getAllSurveys',
    ],
    'surveys/sort/([a-zA-Z_]+)' => [
        'controller' => 'Survey',
        'action' => 'getAllSurveys',
        'params' => ['sort'],
    ],'surveys/sort/date_create' => [
        'controller' => 'Survey',
        'action' => 'getAllSurveys',
        'params' => ['sort' => 'date_create'],
    ],
    'surveys/sort/title' => [
        'controller' => 'Survey',
        'action' => 'getAllSurveys',
        'params' => ['sort' => 'title'],
    ],
    'surveys/sort/status' => [
        'controller' => 'Survey',
        'action' => 'getAllSurveys',
        'params' => ['sort' => 'status'],
    ],'Home/submitAnswer' => [
        'controller' => 'Home',
        'action' => 'submitAnswer'
    ],'Home' => [
        'controller' => 'Home',
        'action' => 'getAllSurveys',

    ], 'survey/delete/{id:\d+}' => [
        'controller' => 'Survey',
        'action' => 'delete',
    ],
    'survey/edit/{id:\d+}' => [
        'controller' => 'Survey',
        'action' => 'edit',
    ],

    'survey/update/{survey_id}' => [
        'controller' => 'Survey',
        'action' => 'update',
    ],

];