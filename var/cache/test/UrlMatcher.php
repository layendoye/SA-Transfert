<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/entreprises/liste' => [[['_route' => 'entreprises', '_controller' => 'App\\Controller\\EntrepriseController::lister'], null, ['GET' => 0], null, false, false, null]],
        '/partenaires/add' => [[['_route' => 'add_entreprise', '_controller' => 'App\\Controller\\EntrepriseController::addPartenaire'], null, ['POST' => 0], null, false, false, null]],
        '/nouveau/depot' => [[['_route' => 'app_entreprise_depot', '_controller' => 'App\\Controller\\EntrepriseController::depot'], null, ['POST' => 0], null, false, false, null]],
        '/changer/compte' => [[['_route' => 'change_compte', '_controller' => 'App\\Controller\\EntrepriseController::changeCompte'], null, null, null, false, false, null]],
        '/gestion/comptes/liste' => [[['_route' => 'user_comptes', '_controller' => 'App\\Controller\\EntrepriseController::listerUserCompt'], null, ['GET' => 0], null, false, false, null]],
        '/inscription' => [[['_route' => 'inscription', '_controller' => 'App\\Controller\\SecurityController::inscriptionUtilisateur'], null, ['POST' => 0], null, false, false, null]],
        '/connexion' => [[['_route' => 'connexion', '_controller' => 'App\\Controller\\SecurityController::login'], null, ['POST' => 0], null, false, false, null]],
        '/transation/envoie' => [[['_route' => 'transation_envoie', '_controller' => 'App\\Controller\\TransationController::envois'], null, null, null, false, false, null]],
        '/transation/retrait' => [[['_route' => 'transation_retrait', '_controller' => 'App\\Controller\\TransationController::retrait'], null, null, null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/entreprise(?:/([^/]++))?(*:32)'
                .'|/partenaires/update/([^/]++)(*:67)'
                .'|/bloque/(?'
                    .'|entreprises/([^/]++)(*:105)'
                    .'|user/([^/]++)(*:126)'
                .')'
                .'|/nouveau/compte/([^/]++)(*:159)'
                .'|/gestion/compte(?:/([^/]++))?(*:196)'
                .'|/user/(?'
                    .'|([^/]++)(*:221)'
                    .'|update/([^/]++)(*:244)'
                .')'
                .'|/contrat/([^/]++)(*:270)'
                .'|/transation/(?'
                    .'|user/([^/]++)/([^/]++)(*:315)'
                    .'|partenaire/([^/]++)/([^/]++)(*:351)'
                .')'
                .'|/api(?'
                    .'|(?:/(index)(?:\\.([^/]++))?)?(*:395)'
                    .'|/(?'
                        .'|d(?'
                            .'|ocs(?:\\.([^/]++))?(*:429)'
                            .'|epots(?'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:463)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:501)'
                                .')'
                            .')'
                        .')'
                        .'|co(?'
                            .'|ntexts/(.+)(?:\\.([^/]++))?(*:543)'
                            .'|mptes(?'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:577)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:615)'
                                .')'
                            .')'
                        .')'
                        .'|entreprises(?'
                            .'|(?:\\.([^/]++))?(?'
                                .'|(*:658)'
                            .')'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                .'|(*:696)'
                            .')'
                        .')'
                        .'|profils(?'
                            .'|(?:\\.([^/]++))?(?'
                                .'|(*:734)'
                            .')'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                .'|(*:772)'
                            .')'
                        .')'
                        .'|transactions(?'
                            .'|(?:\\.([^/]++))?(?'
                                .'|(*:815)'
                            .')'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                .'|(*:853)'
                            .')'
                        .')'
                        .'|user_compte_actuels(?'
                            .'|(?:\\.([^/]++))?(?'
                                .'|(*:903)'
                            .')'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                .'|(*:941)'
                            .')'
                        .')'
                    .')'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        32 => [[['_route' => 'entreprise', 'id' => null, '_controller' => 'App\\Controller\\EntrepriseController::lister'], ['id'], ['GET' => 0], null, false, true, null]],
        67 => [[['_route' => 'update_entreprise', '_controller' => 'App\\Controller\\EntrepriseController::updatePartenaire'], ['id'], ['POST' => 0], null, false, true, null]],
        105 => [[['_route' => 'bloque_entreprise', '_controller' => 'App\\Controller\\EntrepriseController::bloqueEntrep'], ['id'], ['GET' => 0], null, false, true, null]],
        126 => [[['_route' => 'bloque_user', '_controller' => 'App\\Controller\\EntrepriseController::bloqueUser'], ['id'], ['GET' => 0], null, false, true, null]],
        159 => [[['_route' => 'nouveau_compte', '_controller' => 'App\\Controller\\EntrepriseController::addCompte'], ['id'], ['GET' => 0], null, false, true, null]],
        196 => [[['_route' => 'user_compte', 'id' => null, '_controller' => 'App\\Controller\\EntrepriseController::listerUserCompt'], ['id'], ['GET' => 0], null, false, true, null]],
        221 => [[['_route' => 'user_entreprise', '_controller' => 'App\\Controller\\EntrepriseController::listerUser'], ['id'], ['GET' => 0], null, false, true, null]],
        244 => [[['_route' => 'update_user', '_controller' => 'App\\Controller\\SecurityController::updateUser'], ['id'], ['POST' => 0], null, false, true, null]],
        270 => [[['_route' => 'contrat', '_controller' => 'App\\Controller\\EntrepriseController::contrat'], ['id'], ['GET' => 0], null, false, true, null]],
        315 => [[['_route' => 'transation_user', '_controller' => 'App\\Controller\\TransationController::transactionUser'], ['action', 'id'], null, null, false, true, null]],
        351 => [[['_route' => 'transation_partenaire', '_controller' => 'App\\Controller\\TransationController::transactionPartenaire'], ['action', 'id'], null, null, false, true, null]],
        395 => [[['_route' => 'api_entrypoint', '_controller' => 'api_platform.action.entrypoint', '_format' => '', '_api_respond' => 'true', 'index' => 'index'], ['index', '_format'], null, null, false, true, null]],
        429 => [[['_route' => 'api_doc', '_controller' => 'api_platform.action.documentation', '_format' => '', '_api_respond' => 'true'], ['_format'], null, null, false, true, null]],
        463 => [
            [['_route' => 'api_depots_get_collection', '_controller' => 'api_platform.action.get_collection', '_format' => null, '_api_resource_class' => 'App\\Entity\\Depot', '_api_collection_operation_name' => 'get'], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_depots_post_collection', '_controller' => 'api_platform.action.post_collection', '_format' => null, '_api_resource_class' => 'App\\Entity\\Depot', '_api_collection_operation_name' => 'post'], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        501 => [
            [['_route' => 'api_depots_get_item', '_controller' => 'api_platform.action.get_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Depot', '_api_item_operation_name' => 'get'], ['id', '_format'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_depots_delete_item', '_controller' => 'api_platform.action.delete_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Depot', '_api_item_operation_name' => 'delete'], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
            [['_route' => 'api_depots_put_item', '_controller' => 'api_platform.action.put_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Depot', '_api_item_operation_name' => 'put'], ['id', '_format'], ['PUT' => 0], null, false, true, null],
        ],
        543 => [[['_route' => 'api_jsonld_context', '_controller' => 'api_platform.jsonld.action.context', '_format' => 'jsonld', '_api_respond' => 'true'], ['shortName', '_format'], null, null, false, true, null]],
        577 => [
            [['_route' => 'api_comptes_get_collection', '_controller' => 'api_platform.action.get_collection', '_format' => null, '_api_resource_class' => 'App\\Entity\\Compte', '_api_collection_operation_name' => 'get'], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_comptes_post_collection', '_controller' => 'api_platform.action.post_collection', '_format' => null, '_api_resource_class' => 'App\\Entity\\Compte', '_api_collection_operation_name' => 'post'], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        615 => [
            [['_route' => 'api_comptes_get_item', '_controller' => 'api_platform.action.get_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Compte', '_api_item_operation_name' => 'get'], ['id', '_format'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_comptes_delete_item', '_controller' => 'api_platform.action.delete_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Compte', '_api_item_operation_name' => 'delete'], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
            [['_route' => 'api_comptes_put_item', '_controller' => 'api_platform.action.put_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Compte', '_api_item_operation_name' => 'put'], ['id', '_format'], ['PUT' => 0], null, false, true, null],
        ],
        658 => [
            [['_route' => 'api_entreprises_get_collection', '_controller' => 'api_platform.action.get_collection', '_format' => null, '_api_resource_class' => 'App\\Entity\\Entreprise', '_api_collection_operation_name' => 'get'], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_entreprises_post_collection', '_controller' => 'api_platform.action.post_collection', '_format' => null, '_api_resource_class' => 'App\\Entity\\Entreprise', '_api_collection_operation_name' => 'post'], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        696 => [
            [['_route' => 'api_entreprises_get_item', '_controller' => 'api_platform.action.get_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Entreprise', '_api_item_operation_name' => 'get'], ['id', '_format'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_entreprises_delete_item', '_controller' => 'api_platform.action.delete_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Entreprise', '_api_item_operation_name' => 'delete'], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
            [['_route' => 'api_entreprises_put_item', '_controller' => 'api_platform.action.put_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Entreprise', '_api_item_operation_name' => 'put'], ['id', '_format'], ['PUT' => 0], null, false, true, null],
        ],
        734 => [
            [['_route' => 'api_profils_get_collection', '_controller' => 'api_platform.action.get_collection', '_format' => null, '_api_resource_class' => 'App\\Entity\\Profil', '_api_collection_operation_name' => 'get'], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_profils_post_collection', '_controller' => 'api_platform.action.post_collection', '_format' => null, '_api_resource_class' => 'App\\Entity\\Profil', '_api_collection_operation_name' => 'post'], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        772 => [
            [['_route' => 'api_profils_get_item', '_controller' => 'api_platform.action.get_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Profil', '_api_item_operation_name' => 'get'], ['id', '_format'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_profils_delete_item', '_controller' => 'api_platform.action.delete_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Profil', '_api_item_operation_name' => 'delete'], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
            [['_route' => 'api_profils_put_item', '_controller' => 'api_platform.action.put_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Profil', '_api_item_operation_name' => 'put'], ['id', '_format'], ['PUT' => 0], null, false, true, null],
        ],
        815 => [
            [['_route' => 'api_transactions_get_collection', '_controller' => 'api_platform.action.get_collection', '_format' => null, '_api_resource_class' => 'App\\Entity\\Transaction', '_api_collection_operation_name' => 'get'], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_transactions_post_collection', '_controller' => 'api_platform.action.post_collection', '_format' => null, '_api_resource_class' => 'App\\Entity\\Transaction', '_api_collection_operation_name' => 'post'], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        853 => [
            [['_route' => 'api_transactions_get_item', '_controller' => 'api_platform.action.get_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Transaction', '_api_item_operation_name' => 'get'], ['id', '_format'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_transactions_delete_item', '_controller' => 'api_platform.action.delete_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Transaction', '_api_item_operation_name' => 'delete'], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
            [['_route' => 'api_transactions_put_item', '_controller' => 'api_platform.action.put_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Transaction', '_api_item_operation_name' => 'put'], ['id', '_format'], ['PUT' => 0], null, false, true, null],
        ],
        903 => [
            [['_route' => 'api_user_compte_actuels_get_collection', '_controller' => 'api_platform.action.get_collection', '_format' => null, '_api_resource_class' => 'App\\Entity\\UserCompteActuel', '_api_collection_operation_name' => 'get'], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_user_compte_actuels_post_collection', '_controller' => 'api_platform.action.post_collection', '_format' => null, '_api_resource_class' => 'App\\Entity\\UserCompteActuel', '_api_collection_operation_name' => 'post'], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        941 => [
            [['_route' => 'api_user_compte_actuels_get_item', '_controller' => 'api_platform.action.get_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\UserCompteActuel', '_api_item_operation_name' => 'get'], ['id', '_format'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_user_compte_actuels_delete_item', '_controller' => 'api_platform.action.delete_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\UserCompteActuel', '_api_item_operation_name' => 'delete'], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
            [['_route' => 'api_user_compte_actuels_put_item', '_controller' => 'api_platform.action.put_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\UserCompteActuel', '_api_item_operation_name' => 'put'], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
