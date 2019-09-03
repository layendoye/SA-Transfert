<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/_profiler' => [[['_route' => '_profiler_home', '_controller' => 'web_profiler.controller.profiler::homeAction'], null, null, null, true, false, null]],
        '/_profiler/search' => [[['_route' => '_profiler_search', '_controller' => 'web_profiler.controller.profiler::searchAction'], null, null, null, false, false, null]],
        '/_profiler/search_bar' => [[['_route' => '_profiler_search_bar', '_controller' => 'web_profiler.controller.profiler::searchBarAction'], null, null, null, false, false, null]],
        '/_profiler/phpinfo' => [[['_route' => '_profiler_phpinfo', '_controller' => 'web_profiler.controller.profiler::phpinfoAction'], null, null, null, false, false, null]],
        '/_profiler/open' => [[['_route' => '_profiler_open_file', '_controller' => 'web_profiler.controller.profiler::openAction'], null, null, null, false, false, null]],
        '/entreprises/liste' => [[['_route' => 'entreprises', '_controller' => 'App\\Controller\\EntrepriseController::lister'], null, ['GET' => 0], null, false, false, null]],
        '/partenaires/add' => [[['_route' => 'add_entreprise', '_controller' => 'App\\Controller\\EntrepriseController::addPartenaire'], null, ['POST' => 0], null, false, false, null]],
        '/nouveau/depot' => [[['_route' => 'app_entreprise_depot', '_controller' => 'App\\Controller\\EntrepriseController::depot'], null, ['POST' => 0], null, false, false, null]],
        '/changer/compte' => [[['_route' => 'change_compte', '_controller' => 'App\\Controller\\EntrepriseController::changeCompte'], null, null, null, false, false, null]],
        '/MesComptes' => [[['_route' => 'compte_userCon', '_controller' => 'App\\Controller\\EntrepriseController::getCompte'], null, ['GET' => 0], null, false, false, null]],
        '/gestion/comptes/liste' => [[['_route' => 'user_comptes', '_controller' => 'App\\Controller\\EntrepriseController::listerUserCompt'], null, ['GET' => 0], null, false, false, null]],
        '/lister/users' => [[['_route' => 'user_entreprise', '_controller' => 'App\\Controller\\EntrepriseController::listerLesUser'], null, ['GET' => 0], null, false, false, null]],
        '/lister/users/all' => [[['_route' => 'user_entrepriseAll', '_controller' => 'App\\Controller\\EntrepriseController::listerTousUser'], null, ['GET' => 0], null, false, false, null]],
        '/compte/Mesdepots' => [[['_route' => 'showDepotCompte', '_controller' => 'App\\Controller\\EntrepriseController::showDepotCompte'], null, ['POST' => 0], null, false, false, null]],
        '/compte/numeroCompte' => [[['_route' => 'leCompte', '_controller' => 'App\\Controller\\EntrepriseController::getLeCompte'], null, ['POST' => 0], null, false, false, null]],
        '/inscription' => [[['_route' => 'inscription', '_controller' => 'App\\Controller\\SecurityController::inscriptionUtilisateur'], null, ['POST' => 0], null, false, false, null]],
        '/connexion' => [[['_route' => 'connexion', '_controller' => 'App\\Controller\\SecurityController::login'], null, ['POST' => 0], null, false, false, null]],
        '/profil' => [[['_route' => 'profil', '_controller' => 'App\\Controller\\SecurityController::profil'], null, ['GET' => 0], null, false, false, null]],
        '/userConnecte' => [[['_route' => 'userConnecte', '_controller' => 'App\\Controller\\SecurityController::userConnecte'], null, ['GET' => 0], null, false, false, null]],
        '/transation/envoie' => [[['_route' => 'transation_envoie', '_controller' => 'App\\Controller\\TransationController::envois'], null, null, null, false, false, null]],
        '/transation/retrait' => [[['_route' => 'transation_retrait', '_controller' => 'App\\Controller\\TransationController::retrait'], null, null, null, false, false, null]],
        '/info/transaction' => [[['_route' => 'infoTransaction', '_controller' => 'App\\Controller\\TransationController::infoTransaction'], null, ['POST' => 0], null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/_(?'
                    .'|error/(\\d+)(?:\\.([^/]++))?(*:38)'
                    .'|wdt/([^/]++)(*:57)'
                    .'|profiler/([^/]++)(?'
                        .'|/(?'
                            .'|search/results(*:102)'
                            .'|router(*:116)'
                            .'|exception(?'
                                .'|(*:136)'
                                .'|\\.css(*:149)'
                            .')'
                        .')'
                        .'|(*:159)'
                    .')'
                .')'
                .'|/entreprise(?'
                    .'|(?:/([^/]++))?(*:197)'
                    .'|/responsable/([^/]++)(*:226)'
                .')'
                .'|/p(?'
                    .'|artenaires/update/([^/]++)(*:266)'
                    .'|df/([^/]++)(*:285)'
                .')'
                .'|/bloque/(?'
                    .'|entreprises/([^/]++)(*:325)'
                    .'|user/([^/]++)(*:346)'
                .')'
                .'|/nouveau/compte/([^/]++)(*:379)'
                .'|/co(?'
                    .'|mpte(?'
                        .'|/(?'
                            .'|entreprise(?:/([^/]++))?(*:428)'
                            .'|user/([^/]++)(*:449)'
                        .')'
                        .'|s/affecte/user/([^/]++)(*:481)'
                    .')'
                    .'|ntrat/([^/]++)(*:504)'
                .')'
                .'|/gestion/compte(?:/([^/]++))?(*:542)'
                .'|/user/(?'
                    .'|([^/]++)(*:567)'
                    .'|update/([^/]++)(*:590)'
                .')'
                .'|/transation/(?'
                    .'|user/([^/]++)/([^/]++)(*:636)'
                    .'|partenaire/([^/]++)/([^/]++)(*:672)'
                .')'
                .'|/api(?'
                    .'|(?:/(index)(?:\\.([^/]++))?)?(*:716)'
                    .'|/(?'
                        .'|d(?'
                            .'|ocs(?:\\.([^/]++))?(*:750)'
                            .'|epots(?'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:784)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:822)'
                                .')'
                            .')'
                        .')'
                        .'|co(?'
                            .'|ntexts/(.+)(?:\\.([^/]++))?(*:864)'
                            .'|mptes(?'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:898)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:936)'
                                .')'
                            .')'
                        .')'
                        .'|transactions(?'
                            .'|(?:\\.([^/]++))?(?'
                                .'|(*:980)'
                            .')'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                .'|(*:1018)'
                            .')'
                        .')'
                        .'|user_compte_actuels(?'
                            .'|(?:\\.([^/]++))?(?'
                                .'|(*:1069)'
                            .')'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                .'|(*:1108)'
                            .')'
                        .')'
                        .'|entreprises(?'
                            .'|(?:\\.([^/]++))?(?'
                                .'|(*:1151)'
                            .')'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                .'|(*:1190)'
                            .')'
                        .')'
                        .'|profils(?'
                            .'|(?:\\.([^/]++))?(?'
                                .'|(*:1229)'
                            .')'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                .'|(*:1268)'
                            .')'
                        .')'
                    .')'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        38 => [[['_route' => '_twig_error_test', '_controller' => 'twig.controller.preview_error::previewErrorPageAction', '_format' => 'html'], ['code', '_format'], null, null, false, true, null]],
        57 => [[['_route' => '_wdt', '_controller' => 'web_profiler.controller.profiler::toolbarAction'], ['token'], null, null, false, true, null]],
        102 => [[['_route' => '_profiler_search_results', '_controller' => 'web_profiler.controller.profiler::searchResultsAction'], ['token'], null, null, false, false, null]],
        116 => [[['_route' => '_profiler_router', '_controller' => 'web_profiler.controller.router::panelAction'], ['token'], null, null, false, false, null]],
        136 => [[['_route' => '_profiler_exception', '_controller' => 'web_profiler.controller.exception::showAction'], ['token'], null, null, false, false, null]],
        149 => [[['_route' => '_profiler_exception_css', '_controller' => 'web_profiler.controller.exception::cssAction'], ['token'], null, null, false, false, null]],
        159 => [[['_route' => '_profiler', '_controller' => 'web_profiler.controller.profiler::panelAction'], ['token'], null, null, false, true, null]],
        197 => [[['_route' => 'entreprise', 'id' => null, '_controller' => 'App\\Controller\\EntrepriseController::lister'], ['id'], ['GET' => 0], null, false, true, null]],
        226 => [[['_route' => 'adminPartenaire', '_controller' => 'App\\Controller\\EntrepriseController::getResponsable'], ['id'], ['GET' => 0], null, false, true, null]],
        266 => [[['_route' => 'update_entreprise', '_controller' => 'App\\Controller\\EntrepriseController::updatePartenaire'], ['id'], ['POST' => 0], null, false, true, null]],
        285 => [[['_route' => 'app_pdf_pdf', '_controller' => 'App\\Controller\\PdfController::pdf'], ['action'], null, null, false, true, null]],
        325 => [[['_route' => 'bloque_entreprise', '_controller' => 'App\\Controller\\EntrepriseController::bloqueEntrep'], ['id'], ['GET' => 0], null, false, true, null]],
        346 => [[['_route' => 'bloque_user', '_controller' => 'App\\Controller\\EntrepriseController::bloqueUser'], ['id'], ['GET' => 0], null, false, true, null]],
        379 => [[['_route' => 'nouveau_compte', '_controller' => 'App\\Controller\\EntrepriseController::addCompte'], ['id'], ['GET' => 0], null, false, true, null]],
        428 => [[['_route' => 'compte_entr', 'id' => null, '_controller' => 'App\\Controller\\EntrepriseController::getCompte'], ['id'], ['GET' => 0], null, false, true, null]],
        449 => [[['_route' => 'userCompte', '_controller' => 'App\\Controller\\EntrepriseController::userCompte'], ['id'], ['GET' => 0], null, false, true, null]],
        481 => [[['_route' => 'userCompteAffecte', '_controller' => 'App\\Controller\\EntrepriseController::userComptesAffecte'], ['id'], ['GET' => 0], null, false, true, null]],
        504 => [[['_route' => 'contrat', '_controller' => 'App\\Controller\\EntrepriseController::contrat'], ['id'], ['GET' => 0], null, false, true, null]],
        542 => [[['_route' => 'user_compte', 'id' => null, '_controller' => 'App\\Controller\\EntrepriseController::listerUserCompt'], ['id'], ['GET' => 0], null, false, true, null]],
        567 => [[['_route' => 'listeUser', '_controller' => 'App\\Controller\\EntrepriseController::listerUser'], ['id'], ['GET' => 0], null, false, true, null]],
        590 => [[['_route' => 'update_user', '_controller' => 'App\\Controller\\SecurityController::updateUser'], ['id'], ['POST' => 0], null, false, true, null]],
        636 => [[['_route' => 'transation_user', '_controller' => 'App\\Controller\\TransationController::transactionUser'], ['action', 'id'], null, null, false, true, null]],
        672 => [[['_route' => 'transation_partenaire', '_controller' => 'App\\Controller\\TransationController::transactionPartenaire'], ['action', 'id'], null, null, false, true, null]],
        716 => [[['_route' => 'api_entrypoint', '_controller' => 'api_platform.action.entrypoint', '_format' => '', '_api_respond' => 'true', 'index' => 'index'], ['index', '_format'], null, null, false, true, null]],
        750 => [[['_route' => 'api_doc', '_controller' => 'api_platform.action.documentation', '_format' => '', '_api_respond' => 'true'], ['_format'], null, null, false, true, null]],
        784 => [
            [['_route' => 'api_depots_get_collection', '_controller' => 'api_platform.action.get_collection', '_format' => null, '_api_resource_class' => 'App\\Entity\\Depot', '_api_collection_operation_name' => 'get'], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_depots_post_collection', '_controller' => 'api_platform.action.post_collection', '_format' => null, '_api_resource_class' => 'App\\Entity\\Depot', '_api_collection_operation_name' => 'post'], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        822 => [
            [['_route' => 'api_depots_get_item', '_controller' => 'api_platform.action.get_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Depot', '_api_item_operation_name' => 'get'], ['id', '_format'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_depots_delete_item', '_controller' => 'api_platform.action.delete_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Depot', '_api_item_operation_name' => 'delete'], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
            [['_route' => 'api_depots_put_item', '_controller' => 'api_platform.action.put_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Depot', '_api_item_operation_name' => 'put'], ['id', '_format'], ['PUT' => 0], null, false, true, null],
        ],
        864 => [[['_route' => 'api_jsonld_context', '_controller' => 'api_platform.jsonld.action.context', '_format' => 'jsonld', '_api_respond' => 'true'], ['shortName', '_format'], null, null, false, true, null]],
        898 => [
            [['_route' => 'api_comptes_get_collection', '_controller' => 'api_platform.action.get_collection', '_format' => null, '_api_resource_class' => 'App\\Entity\\Compte', '_api_collection_operation_name' => 'get'], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_comptes_post_collection', '_controller' => 'api_platform.action.post_collection', '_format' => null, '_api_resource_class' => 'App\\Entity\\Compte', '_api_collection_operation_name' => 'post'], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        936 => [
            [['_route' => 'api_comptes_get_item', '_controller' => 'api_platform.action.get_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Compte', '_api_item_operation_name' => 'get'], ['id', '_format'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_comptes_delete_item', '_controller' => 'api_platform.action.delete_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Compte', '_api_item_operation_name' => 'delete'], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
            [['_route' => 'api_comptes_put_item', '_controller' => 'api_platform.action.put_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Compte', '_api_item_operation_name' => 'put'], ['id', '_format'], ['PUT' => 0], null, false, true, null],
        ],
        980 => [
            [['_route' => 'api_transactions_get_collection', '_controller' => 'api_platform.action.get_collection', '_format' => null, '_api_resource_class' => 'App\\Entity\\Transaction', '_api_collection_operation_name' => 'get'], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_transactions_post_collection', '_controller' => 'api_platform.action.post_collection', '_format' => null, '_api_resource_class' => 'App\\Entity\\Transaction', '_api_collection_operation_name' => 'post'], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        1018 => [
            [['_route' => 'api_transactions_get_item', '_controller' => 'api_platform.action.get_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Transaction', '_api_item_operation_name' => 'get'], ['id', '_format'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_transactions_delete_item', '_controller' => 'api_platform.action.delete_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Transaction', '_api_item_operation_name' => 'delete'], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
            [['_route' => 'api_transactions_put_item', '_controller' => 'api_platform.action.put_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Transaction', '_api_item_operation_name' => 'put'], ['id', '_format'], ['PUT' => 0], null, false, true, null],
        ],
        1069 => [
            [['_route' => 'api_user_compte_actuels_get_collection', '_controller' => 'api_platform.action.get_collection', '_format' => null, '_api_resource_class' => 'App\\Entity\\UserCompteActuel', '_api_collection_operation_name' => 'get'], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_user_compte_actuels_post_collection', '_controller' => 'api_platform.action.post_collection', '_format' => null, '_api_resource_class' => 'App\\Entity\\UserCompteActuel', '_api_collection_operation_name' => 'post'], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        1108 => [
            [['_route' => 'api_user_compte_actuels_get_item', '_controller' => 'api_platform.action.get_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\UserCompteActuel', '_api_item_operation_name' => 'get'], ['id', '_format'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_user_compte_actuels_delete_item', '_controller' => 'api_platform.action.delete_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\UserCompteActuel', '_api_item_operation_name' => 'delete'], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
            [['_route' => 'api_user_compte_actuels_put_item', '_controller' => 'api_platform.action.put_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\UserCompteActuel', '_api_item_operation_name' => 'put'], ['id', '_format'], ['PUT' => 0], null, false, true, null],
        ],
        1151 => [
            [['_route' => 'api_entreprises_get_collection', '_controller' => 'api_platform.action.get_collection', '_format' => null, '_api_resource_class' => 'App\\Entity\\Entreprise', '_api_collection_operation_name' => 'get'], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_entreprises_post_collection', '_controller' => 'api_platform.action.post_collection', '_format' => null, '_api_resource_class' => 'App\\Entity\\Entreprise', '_api_collection_operation_name' => 'post'], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        1190 => [
            [['_route' => 'api_entreprises_get_item', '_controller' => 'api_platform.action.get_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Entreprise', '_api_item_operation_name' => 'get'], ['id', '_format'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_entreprises_delete_item', '_controller' => 'api_platform.action.delete_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Entreprise', '_api_item_operation_name' => 'delete'], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
            [['_route' => 'api_entreprises_put_item', '_controller' => 'api_platform.action.put_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Entreprise', '_api_item_operation_name' => 'put'], ['id', '_format'], ['PUT' => 0], null, false, true, null],
        ],
        1229 => [
            [['_route' => 'api_profils_get_collection', '_controller' => 'api_platform.action.get_collection', '_format' => null, '_api_resource_class' => 'App\\Entity\\Profil', '_api_collection_operation_name' => 'get'], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_profils_post_collection', '_controller' => 'api_platform.action.post_collection', '_format' => null, '_api_resource_class' => 'App\\Entity\\Profil', '_api_collection_operation_name' => 'post'], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        1268 => [
            [['_route' => 'api_profils_get_item', '_controller' => 'api_platform.action.get_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Profil', '_api_item_operation_name' => 'get'], ['id', '_format'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_profils_delete_item', '_controller' => 'api_platform.action.delete_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Profil', '_api_item_operation_name' => 'delete'], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
            [['_route' => 'api_profils_put_item', '_controller' => 'api_platform.action.put_item', '_format' => null, '_api_resource_class' => 'App\\Entity\\Profil', '_api_item_operation_name' => 'put'], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
