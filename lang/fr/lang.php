<?php

return [
    'menu' => [
        'data_sources' => 'Sources des données',
        'data_sources_description' => 'Gerer les sources et leur relation pour publisher, mailing, etc.',
        'settings_category' => 'Configuration ',
        'settings_category_model' => 'Gestion des Modèles',
        'job_list' => "Liste des taches",
        'job_list_s' => "Taches",
        'job_list_description' => "Liste des taches de l'application",
    ],
    'global' => [
        'details' => 'Détails',
        'slug' => 'Slug',
        'updated_at' => 'MAJ',
        'placeholder' => '--Choisissez une valeur--',
        'placeholder_contact' => '--Choisissez un contact--',
        'placeholder_client' => '--Choisissez un client--',
        'sort_order' => 'Ordre',
        'return' => 'Retour',
        'delete_selected' => 'Supprimer la séléction',
        'cancel' => 'Abandonner',
        'save' => 'Sauver',
        'close' => 'Fermer',
        'save_close' => 'Sauver & fermer',
        'create' => 'Créer',
        'create_close' => 'Créer & fermer',
        'update' => 'Mettre à jour',
        'validate' => 'Valider',
        'reorder' => 'Réordonner',
        'or' => 'ou',
        'action' => 'Action',
        'open' => 'Ouvrir',
        'icon' => 'Îcone',
        'placeholder_icon' => '--Choisissez une îcone',
        'saving' => 'Sauvegarde',
        'edit' => 'Editer',
        'code' => 'Code',
        'code_identification' => "Code d'identification",
        'test' => 'Tester',
        'unkown' => 'Inconnu',
        'confirm_delete' => 'Confirmez vous la suppression. Attention ! Cette action est irreversible',
        'save_indicator' => 'Sauvegarde en cours',
        'state' => "Etat",
        'test' => 'test',
        'is_ex' => 'Exemple',
    ],
    'datasource' => [
        'tab_path' => "Chemins des Classes",
        'tab_contact' => "Liaison contacts",
        'tab_relation' => "Relations",
        'name' => 'Intitulé de la source',
        'title' => 'Choisissez une source',
        'placeholder' => '--Choisissez une source--',
        'author' => 'Auteur du plugin',
        'plugin' => 'Nom du plugin',
        'model' => 'Nom du modèle',
        'section_controller' => 'Gestion des données',
        'controller' => 'Nom du controller',
        'specific_list' => "Adresse spécifique de liste",
        'specific_update' => "Adresse spécifique d'édition",
        'specific_create' => "Adresse spécifique de création",
        'section_relation' => 'Gestion des relations',
        'relations' => 'Liste des relations à utiliser',
        'relations_prompt' => 'ajouter une relation',
        'relation_name' => 'Nom de la relation',
        'attributes' => 'Liste des attributs à utiliser',
        'attributes_pompt' => 'Ajouter une relation',
        'attribute_name' => "Nom de l'attribut",
        'test_id' => "Model d'exemple",
        'test_id_prompt' => "--Choisissez un model d'exemple--",
        'sector_access' => "Accès relation sector",
        'param' => "Nom du Paramètre",
        'param_com' => "Paramètre qui fera transiter la clé",
        'key' => "key",
        'key_com' => "Modifier la clé si id n'existe pas. NE FONCTIONNE PAS ENCORE",
        'relation_collection_name' => "Nom de la relation collection",
        'section_contact' => "Gestion des relations pour acceder aux contacts emails",
        'contacts' => "Configuration yaml des contacts",
        'model_scopes' => 'Class Scope du model',
        'has_image' => 'Prendre les images',
        'function_class' => "Class fonctions d'éditions",
        'agg_class' => "Class d'aggregation",
        "name_from" => "Nom à utiliser si 'name' n'existe pas",
        'inde_class_list' => [
            'label' => 'Class independante à lier',
            'prompt' => 'Entrez les classes indépendantes',
            'name' => 'Nom de la class ( sera utiliser notamment dans word)',
            'class' => 'Nom de la class indépendante',
            'ids' => 'list des ID, si vide le premier sera pris',
        ],

    ],
    'job_list' => [
        'name' => "Nom de la tache",
        'started_at' => "Commencé à",
        'created_at' => "Crée à",
        'end_at' => "Terminé à",
        'state' => "Etat",
        'date_diff' => "Durée en S",
        "user_name" => "Utilisateur",
        "scopes" => [
            "not_end" => "Ne pas afficher les taches terminés",
            "only_user" => "Seulement vos taches",

        ],
    ],
    'scopes' => [
        "libelle" => "Intitulé de la restriction",
        "is_scope" => "Restriction ? ",
        "self" => "Fonction de restriction lié à ce modèle",
        "target" => "Nom de la relation portant la restriction",
        "field" => "Nom du champ",
        "field_com" => "Nom de la colonne qui portera la valeur",
        "target_com" => "Ecrire le nom de la relation. les relations parentes ne sont pas disponible",
        "scope_field" => "Nom du champ",
        "scope_value" => "Valeur unique du champ",
        "scope_values" => "Lister les valeurs",
        "scope_values_com" => "Saisissez une valeur et cliquez",
        "scope_bool" => "Vrai/Faux",
        "type" => "Type de restriction",
        "scope_relation" => "Choisir la relation",
        "scope_relation_com" => "Si vous recherchez chez un parent vous devez indiquer la relation avec le parent",
        "userRoles" => "Role des utilisateurs",
        'users' => 'Utilisateurs',
    ],
    'settings' => [
        "activate_dashboard" => "Activer le bouton du Dashboard",
        "activate_user_btn" => "Activer le bouton des utilisateurs",
        "activate_cms" => "Activer le bouton du CMS",
        "activate_builder" => "Activer le bouton du Builder",
        "activate_task_btn" => "Activer le bouton dynamiqe de taches",
        "label" => "Utilitaires",
        "description" => "Cachez des élements",

    ],

];
