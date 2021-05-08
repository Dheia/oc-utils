<?php

return [
    'civ' => ['Mme/M.' => 'Mme/M.', 'Mme' => 'Mme', 'M.' => 'M.', 'Dr' => 'Dr', 'Pr' => 'Pr'],
    'btns' => [
        'duplicate' => [
            'label' => 'Duplicate',
            'ajaxCaller' => 'onLoadDuplicateForm',
            'ajaxInlineCaller' => 'onLoadDuplicateContentForm',
            'icon' => 'oc-icon-files-o',
        ],
         'lot_fnc' => [
            'label' => 'Fonctions par lot',
            'class' => 'btn-secondary',
            'ajaxInlineCaller' => 'onExecuteLotFnc',
            'icon' => 'oc-icon-calculator',
        ],
    ],
    'ImageOptions' => [
        'width' => [
            'label' => "Largeur",
            'type' => "text",
            'span' => 'left',
        ],
        'height' => [
            'label' => "hauteur",
            'type' => "text",
            'span' => 'right',
        ],
    ],
    'scopesType' => [
        'model_value' => [
            'label' => "Restriction depuis une valeur d'un champ",
            'config' => 'scope_value',
        ],

        'model_values' => [
            'label' => "Restriction sur plusieurs valeurs d'un champ",
            'config' => 'scope_values',
        ],
        'model_relation' => [
            'label' => "Restriction en fonction d'une relation",
            'config' => 'scope_relation',
        ],
        'model_bool' => [
            'label' => "Restriction Vrai/Faux d'un champ",
            'config' => 'scope_bool',
        ],
        'user' => [
            'label' => "Restriction lié à l'utilisateur",
            'config' => 'scope_user',
        ],
        'user_role' => [
            'label' => "Restriction lié aux groupes d'utilisateurs",
            'config' => 'scope_user_role',
        ],
    ],
    'transformers' => [
        'word' => "$" . "{%s}",
        'twig' => "{{ %s }}",
        'types' => [
            'date' => [
                'word' => '${%s*date}',
                'twig' => "{{%s | localeDate('date')}}",
            ],
            'date-medium' => [
                'word' => '${%s*date-medium}',
                'twig' => "{{%s | localeDate('date-medium')}}",
            ],
            'date-full' => [
                'word' => '${%s*date-full}',
                'twig' => "{{%s | localeDate('date-full')}}",
            ],
            'date-tiny' => [
                'word' => '${%s*date-tiny}',
                'twig' => "{{%s | localeDate('date-tiny')}}",
            ],
            'date-time' => [
                'word' => '${%s*date-time}',
                'twig' => "{{%s | localeDate('date-time')}}",
            ],
            'date-short' => [
                'word' => '${%s*date-medium}',
                'twig' => "{{%s|localeDate('date-short')}}",
            ],
            'date-short-time' => [
                'word' => '${%s*date-short-time}',
                'twig' => "{{%s|localeDate('date-short-time')}}",
            ],
            'float' => [
                'word' => '${%s*float}',
                'twig' => "{{%s|number_format(2,',',' ')}}",
            ],
            'int' => [
                'word' => '${%s*number}',
                'twig' => "{{%s|number_format(0,',',' ')}}",
            ],
            'euro' => [
                'word' => '${%s*euro}',
                'twig' => "{{%s|number_format(2,',',' ')}} €",
            ],
            'euro-int' => [
                'word' => '${%s*number}',
                'twig' => "{{%s|number_format(0,',',' ')}} €",
            ],
            'switch' => [
                'word' => '${%s*switch}',
                'twig' => "{{%s ? 'Oui' : 'Non'}}",
            ],
            'image' => [
                'word' => '${%s*IMG}',
                'twig' => "{{%s.path}}",
            ],
            'modelImage' => [
                'word' => '${IMG.%s}',
                'twig' => "{{IMG.%s.path}}",
            ],
            'htm' => [
                'word' => '${%s*HTM}',
                'twig' => "{{%s|raw}}",
            ],
            'md' => [
                'word' => '${%s*MD}',
                'twig' => "{{%s|md_safe}}",
            ],
            'txt' => [
                'word' => '${%s*TXT}',
                'twig' => "{{%s|striptags('<br><p>') }}",
            ],
        ],

    ],
];
