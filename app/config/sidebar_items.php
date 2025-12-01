<?php

return [
    'admin' => [
        [
            'icon' => 'home',
            'label' => 'Tableau de bord',
            'link' => '/dashboard',
            'active' => false,
            'sub_items' => []
        ],
        [
            'icon' => 'users',
            'label' => 'Gestion des employés',
            'link' => '/employes',
            'active' => false,
            'sub_items' => [
                ['label' => 'Liste des employés', 'link' => '/employes/liste'],
                ['label' => 'Ajouter un employé', 'link' => '/employes/ajouter'],
                ['label' => 'Contrats', 'link' => '/employes/contrats']
            ]
        ],
        [
            'icon' => 'briefcase',
            'label' => 'Postes',
            'link' => '/postes',
            'active' => false,
            'sub_items' => []
        ],
        [
            'icon' => 'calendar',
            'label' => 'Gestion des congés',
            'link' => '/conges',
            'active' => false,
            'sub_items' => [
                ['label' => 'Demandes en attente', 'link' => '/conges/attente'],
                ['label' => 'Historique', 'link' => '/conges/historique'],
                ['label' => 'Types de congés', 'link' => '/conges/types']
            ]
        ],
        [
            'icon' => 'clock',
            'label' => 'Pointage',
            'link' => '/pointage',
            'active' => false,
            'sub_items' => [
                ['label' => 'Pointages du jour', 'link' => '/pointage/aujourd-hui'],
                ['label' => 'Historique', 'link' => '/pointage/historique'],
                ['label' => 'Heures supplémentaires', 'link' => '/pointage/heures-sup']
            ]
        ],
        [
            'icon' => 'alert-circle',
            'label' => 'Absences & Retards',
            'link' => '/absences',
            'active' => false,
            'sub_items' => [
                ['label' => 'Absences', 'link' => '/absences/liste'],
                ['label' => 'Retards', 'link' => '/retards/liste']
            ]
        ],
        [
            'icon' => 'dollar-sign',
            'label' => 'Paie & Primes',
            'link' => '/paie',
            'active' => false,
            'sub_items' => [
                ['label' => 'Calcul de paie', 'link' => '/paiement/employes'],
                ['label' => 'Primes', 'link' => '/paiement/prime'],
                ['label' => 'IRSA', 'link' => '/paiement/irsa']
            ]
        ],
        [
            'icon' => 'book-open',
            'label' => 'Competences & Formations',
            'link' => '/matching/postes',
            'active' => false,
            'sub_items' => [
                ['label' => 'Cartographie', 'link' => '/competences/cartographie'],
                ['label' => 'Matching profil', 'link' => '/matching/postes']
            ]
        ],
        [
            'icon' => 'file-text',
            'label' => 'Documents',
            'link' => '/documents',
            'active' => false,
            'sub_items' => []
        ],
        [
            'icon' => 'cpu',
            'label' => 'Assistant IA RH',
            'link' => '/admin/chatbot',
            'active' => false,
            // 'sub_items' => [
            //     ['label' => 'Chatbot FAQ', 'link' => '/admin/chatbot'],
            //     ['label' => 'Génération Documents', 'link' => '/admin/chatbot#documents'],
            //     ['label' => 'Prédiction Turnover', 'link' => '/admin/chatbot#turnover'],
            //     ['label' => 'Détection Anomalies', 'link' => '/admin/chatbot#anomalies'],
            //     ['label' => 'Recommandation CV', 'link' => '/admin/chatbot#candidates']
            // ]
        ],
        [
            'icon' => 'settings',
            'label' => 'Paramètres',
            'link' => '/parametres',
            'active' => false,
            'sub_items' => [
                ['label' => 'Départements', 'link' => '/parametres/departements'],
                ['label' => 'Jours fériés', 'link' => '/parametres/jours-feries'],
                ['label' => 'Horaires de travail', 'link' => '/parametres/horaires']
            ]
        ]
    ],

    'Ressources Humaines' => [
        [
            'icon' => 'home',
            'label' => 'Tableau de bord RH',
            'link' => '/dashboard',
            'active' => false,
            'sub_items' => []
        ],
        [
            'icon' => 'bar-chart-2',
            'label' => 'Statistiques',
            'link' => '/statistiques',
            'active' => false,
            'sub_items' => []
        ],
        [
            'icon' => 'shield',
            'label' => 'Logs d\'Audit',
            'link' => '/admin/audit-logs',
            'active' => false,
            'sub_items' => []
        ],
        [
            'icon' => 'message-circle',
            'label' => 'Messagerie',
            'link' => '/admin/messages',
            'active' => false,
            'sub_items' => []
        ],
        [
            'icon' => 'users',
            'label' => 'Gestion des employés',
            'link' => '/employes',
            'active' => false,
            'sub_items' => [
                ['label' => 'Liste des employés', 'link' => '/employes'],
                ['label' => 'Ajouter un employé', 'link' => '/employes/ajouter']
            ]
        ],
        [
            'icon' => 'calendar',
            'label' => 'Gestion des congés',
            'link' => '/conges',
            'active' => false,
            'sub_items' => [
                ['label' => 'Demandes en attente', 'link' => '/rh/conges/attente'],
                ['label' => 'Historique', 'link' => '/conges/historique'],
                ['label' => 'Calendrier des congés', 'link' => '/calendrier']
            ]
        ],
        [
            'icon' => 'clock',
            'label' => 'Pointage',
            'link' => '/pointage',
            'active' => false,
            'sub_items' => [
                ['label' => 'Pointages Arrivee', 'link' => '/pointage/arrivee'],
                ['label' => 'Pointage Depart', 'link' => '/pointage/depart']
            ]
        ],
        [
            'icon' => 'alert-circle',
            'label' => 'Absences & Retards',
            'link' => '/absences',
            'active' => false,
            'sub_items' => [
                ['label' => 'Releve de presence', 'link' => '/releve-presence'],
                ['label' => 'Fiche', 'link' => '/paie/integration'],
                ['label' => 'Fiche par Employe', 'link' => '/paie/fiche']
            ]
        ],
        [
            'icon' => 'users',
            'label' => 'Performances',
            'link' => '/performance',
            'active' => false,
            'sub_items' => [
                ['label' => 'Scoring', 'link' => '/scoring/evaluations'],
                ['label' => 'Evaluations de Performances', 'link' => '/scoring/rapports']
            ]
        ],
        [
            'icon' => 'dollar-sign',
            'label' => 'Paie & Primes',
            'link' => '/paie',
            'active' => false,
            'sub_items' => [
                ['label' => 'Calcul de paie', 'link' => '/paiement/employes'],
                ['label' => 'Primes', 'link' => '/paiement/prime'],
                ['label' => 'IRSA', 'link' => '/paiement/irsa'],
                ['label' => 'Historique', 'link' => '/paiement/historique']
            ]
        ],
        [
            'icon' => 'book-open',
            'label' => 'Competences & Formations',
            'link' => '/matching/postes',
            'active' => false,
            'sub_items' => [
                ['label' => 'Cartographie', 'link' => '/competences/cartographie'],
                ['label' => 'Matching profil', 'link' => '/matching/postes']
            ]
        ],
        [
            'icon' => 'cpu',
            'label' => 'Assistant IA RH',
            'link' => '/admin/chatbot',
            'active' => false,
            // 'sub_items' => [
            //     ['label' => 'Chatbot FAQ', 'link' => '/admin/chatbot'],
            //     ['label' => 'Génération Documents', 'link' => '/admin/chatbot#documents'],
            //     ['label' => 'Prédiction Turnover', 'link' => '/admin/chatbot#turnover'],
            //     ['label' => 'Détection Anomalies', 'link' => '/admin/chatbot#anomalies'],
            //     ['label' => 'Recommandation CV', 'link' => '/admin/chatbot#candidates']
            // ]
        ]
    ],

    'Production' => [
        [
            'icon' => 'home',
            'label' => 'Tableau de bord Production',
            'link' => '/dashboard',
            'active' => false,
            'sub_items' => []
        ],
        [
            'icon' => 'bar-chart-2',
            'label' => 'Statistiques',
            'link' => '/statistiques',
            'active' => false,
            'sub_items' => []
        ],
        [
            'icon' => 'calendar',
            'label' => 'Calendrier',
            'link' => '/calendrier',
            'active' => false,
        ],
        // [
        //     'icon' => 'users',
        //     'label' => 'Équipe de production',
        //     'link' => '/production/equipe',
        //     'active' => false,
        //     'sub_items' => [
        //         ['label' => 'Liste des employés', 'link' => '/production/equipe/liste'],
        //         ['label' => 'Planning', 'link' => '/production/equipe/planning'],
        //         ['label' => 'Affectations', 'link' => '/production/equipe/affectations']
        //     ]
        // ],
        // [
        //     'icon' => 'clipboard',
        //     'label' => 'Ordres de production',
        //     'link' => '/production/ordres',
        //     'active' => false,
        //     'sub_items' => [
        //         ['label' => 'Nouveaux ordres', 'link' => '/production/ordres/nouveaux'],
        //         ['label' => 'En cours', 'link' => '/production/ordres/en-cours'],
        //         ['label' => 'Terminés', 'link' => '/production/ordres/termines']
        //     ]
        // ],
        // [
        //     'icon' => 'clock',
        //     'label' => 'Pointage équipe',
        //     'link' => '/production/pointage',
        //     'active' => false,
        //     'sub_items' => [
        //         ['label' => 'Pointages du jour', 'link' => '/production/pointage/aujourd-hui'],
        //         ['label' => 'Historique', 'link' => '/production/pointage/historique']
        //     ]
        // ],
        [
            'icon' => 'calendar',
            'label' => 'Congés équipe',
            'link' => '/production/conges',
            'active' => false,
            'sub_items' => [
                ['label' => 'Demandes en attente', 'link' => '/conges/attente'],
                ['label' => 'Calendrier équipe', 'link' => '/calendrier'],
                ['label' => 'Historique', 'link' => '/conges/historique']

            ]
        ],
        // [
        //     'icon' => 'alert-circle',
        //     'label' => 'Incidents',
        //     'link' => '/production/incidents',
        //     'active' => false,
        //     'sub_items' => [
        //         ['label' => 'Signaler incident', 'link' => '/production/incidents/signaler'],
        //         ['label' => 'Liste incidents', 'link' => '/production/incidents/liste']
        //     ]
        // ],
        // [
        //     'icon' => 'bar-chart-2',
        //     'label' => 'Rapports production',
        //     'link' => '/production/rapports',
        //     'active' => false,
        //     'sub_items' => [
        //         ['label' => 'Performance', 'link' => '/production/rapports/performance'],
        //         ['label' => 'Production journalière', 'link' => '/production/rapports/journaliere']
        //     ]
        // ]
    ],

    'Achat et vente' => [
        [
            'icon' => 'home',
            'label' => 'Tableau de bord Commercial',
            'link' => '/dashboard',
            'active' => false,
            'sub_items' => []
        ],
        [
            'icon' => 'bar-chart-2',
            'label' => 'Statistiques',
            'link' => '/statistiques',
            'active' => false,
            'sub_items' => []
        ],
        [
            'icon' => 'calendar',
            'label' => 'Calendrier',
            'link' => '/calendrier',
            'active' => false,
        ],
        // [
        //     'icon' => 'shopping-cart',
        //     'label' => 'Achats',
        //     'link' => '/achats',
        //     'active' => false,
        //     'sub_items' => [
        //         ['label' => 'Bons de commande', 'link' => '/achats/commandes'],
        //         ['label' => 'Fournisseurs', 'link' => '/achats/fournisseurs'],
        //         ['label' => 'Réceptions', 'link' => '/achats/receptions'],
        //         ['label' => 'Historique', 'link' => '/achats/historique']
        //     ]
        // ],
        // [
        //     'icon' => 'trending-up',
        //     'label' => 'Ventes',
        //     'link' => '/ventes',
        //     'active' => false,
        //     'sub_items' => [
        //         ['label' => 'Devis', 'link' => '/ventes/devis'],
        //         ['label' => 'Commandes', 'link' => '/ventes/commandes'],
        //         ['label' => 'Clients', 'link' => '/ventes/clients'],
        //         ['label' => 'Factures', 'link' => '/ventes/factures']
        //     ]
        // ],
        // [
        //     'icon' => 'users',
        //     'label' => 'Équipe commerciale',
        //     'link' => '/commercial/equipe',
        //     'active' => false,
        //     'sub_items' => [
        //         ['label' => 'Liste équipe', 'link' => '/commercial/equipe/liste'],
        //         // ['label' => 'Objectifs', 'link' => '/commercial/equipe/objectifs'],
        //         // ['label' => 'Performance', 'link' => '/commercial/equipe/performance']
        //     ]
        // ],
        // [
        //     'icon' => 'bar-chart-2',
        //     'label' => 'Statistiques',
        //     'link' => '/commercial/statistiques',
        //     'active' => false,
        //     'sub_items' => [
        //         ['label' => 'CA & Marges', 'link' => '/commercial/statistiques/ca'],
        //         ['label' => 'Rapports mensuels', 'link' => '/commercial/statistiques/mensuels']
        //     ]
        // ],
        [
            'icon' => 'calendar',
            'label' => 'Congés équipe',
            'link' => '/commercial/conges',
            'active' => false,
            'sub_items' => [
                ['label' => 'Demandes en attente', 'link' => '/conges/attente'],
                ['label' => 'Calendrier équipe', 'link' => '/calendrier'],
                ['label' => 'Historique', 'link' => '/conges/historique']
            ]
        ]
    ],

    'Gestion de stock' => [
        [
            'icon' => 'home',
            'label' => 'Tableau de bord Stock',
            'link' => '/dashboard',
            'active' => false,
            'sub_items' => []
        ],
        [
            'icon' => 'bar-chart-2',
            'label' => 'Statistiques',
            'link' => '/statistiques',
            'active' => false,
            'sub_items' => []
        ],
        [
            'icon' => 'calendar',
            'label' => 'Calendrier',
            'link' => '/calendrier',
            'active' => false,
        ],
        // [
        //     'icon' => 'package',
        //     'label' => 'Stock',
        //     'link' => '/stock',
        //     'active' => false,
        //     'sub_items' => [
        //         ['label' => 'État du stock', 'link' => '/stock/etat'],
        //         ['label' => 'Articles', 'link' => '/stock/articles'],
        //         ['label' => 'Mouvements', 'link' => '/stock/mouvements'],
        //         ['label' => 'Inventaire', 'link' => '/stock/inventaire']
        //     ]
        // ],
        // [
        //     'icon' => 'alert-triangle',
        //     'label' => 'Alertes stock',
        //     'link' => '/stock/alertes',
        //     'active' => false,
        //     'sub_items' => [
        //         ['label' => 'Stock minimum', 'link' => '/stock/alertes/minimum'],
        //         ['label' => 'Ruptures', 'link' => '/stock/alertes/ruptures'],
        //         ['label' => 'Péremption', 'link' => '/stock/alertes/peremption']
        //     ]
        // ],
        // [
        //     'icon' => 'truck',
        //     'label' => 'Entrées/Sorties',
        //     'link' => '/stock/operations',
        //     'active' => false,
        //     'sub_items' => [
        //         ['label' => 'Entrées stock', 'link' => '/stock/operations/entrees'],
        //         ['label' => 'Sorties stock', 'link' => '/stock/operations/sorties'],
        //         ['label' => 'Transferts', 'link' => '/stock/operations/transferts']
        //     ]
        // ],
        // [
        //     'icon' => 'map-pin',
        //     'label' => 'Emplacements',
        //     'link' => '/stock/emplacements',
        //     'active' => false,
        //     'sub_items' => [
        //         ['label' => 'Zones de stockage', 'link' => '/stock/emplacements/zones'],
        //         ['label' => 'Rayonnages', 'link' => '/stock/emplacements/rayonnages']
        //     ]
        // ],
        [
            'icon' => 'calendar',
            'label' => 'Congés équipe',
            'link' => '/commercial/conges',
            'active' => false,
            'sub_items' => [
                ['label' => 'Demandes en attente', 'link' => '/conges/attente'],
                ['label' => 'Calendrier équipe', 'link' => '/calendrier'],
                ['label' => 'Historique', 'link' => '/conges/historique']
            ]
        ],
        // [
        //     'icon' => 'users',
        //     'label' => 'Équipe magasin',
        //     'link' => '/stock/equipe',
        //     'active' => false,
        //     'sub_items' => [
        //         ['label' => 'Liste équipe', 'link' => '/stock/equipe/liste'],
        //         ['label' => 'Planning', 'link' => '/stock/equipe/planning']
        //     ]
        // ],
        // [
        //     'icon' => 'file-text',
        //     'label' => 'Rapports stock',
        //     'link' => '/stock/rapports',
        //     'active' => false,
        //     'sub_items' => [
        //         ['label' => 'Valorisation', 'link' => '/stock/rapports/valorisation'],
        //         ['label' => 'Rotation', 'link' => '/stock/rapports/rotation']
        //     ]
        // ]
    ],

    'Gestion d\'immobilisation' => [
        [
            'icon' => 'home',
            'label' => 'Tableau de bord Immobilisation',
            'link' => '/dashboard',
            'active' => false,
            'sub_items' => []
        ],
        [
            'icon' => 'bar-chart-2',
            'label' => 'Statistiques',
            'link' => '/statistiques',
            'active' => false,
            'sub_items' => []
        ],
        [
            'icon' => 'calendar',
            'label' => 'Calendrier',
            'link' => '/calendrier',
            'active' => false,
        ],
        [
            'icon' => 'calendar',
            'label' => 'Congés équipe',
            'link' => '/commercial/conges',
            'active' => false,
            'sub_items' => [
                ['label' => 'Demandes en attente', 'link' => '/conges/attente'],
                ['label' => 'Calendrier équipe', 'link' => '/calendrier'],
                ['label' => 'Historique', 'link' => '/conges/historique']
            ]
        ]
    ],

    'employe' => [
        [
            'icon' => 'home',
            'label' => 'Mon espace',
            'link' => '/employe/dashboard',
            'active' => false,
            'sub_items' => []
        ],
        [
            'icon' => 'user',
            'label' => 'Mon profil',
            'link' => '/employe/profil',
            'active' => false,
            'sub_items' => []
        ],
        [
            'icon' => 'calendar',
            'label' => 'Mes congés',
            'link' => '/employe/conges',
            'active' => false,
            'sub_items' => [
                ['label' => 'Mes demandes', 'link' => '/employe/conges/liste']
            ]
        ],
        // [
        //     'icon' => 'clock',
        //     'label' => 'Mon pointage',
        //     'link' => '/employe/pointage',
        //     'active' => false,
        //     'sub_items' => [
        //         ['label' => 'Pointer', 'link' => '/employe/pointage/pointer'],
        //         ['label' => 'Historique', 'link' => '/employe/pointage/historique']
        //     ]
        // ],
        // [
        //     'icon' => 'file-text',
        //     'label' => 'Mes documents',
        //     'link' => '/employe/documents',
        //     'active' => false,
        //     'sub_items' => []
        // ],
        // [
        //     'icon' => 'dollar-sign',
        //     'label' => 'Mes bulletins de paie',
        //     'link' => '/employe/bulletins',
        //     'active' => false,
        //     'sub_items' => []
        // ],
        [
            'icon' => 'mail',
            'label' => 'Mes messages',
            'link' => '/employe/messages',
            'active' => false,
            'sub_items' => []
        ],
        [
            'icon' => 'message-circle',
            'label' => 'Chatbot RH',
            'link' => '#chatbotModal',
            'active' => false,
            'sub_items' => []
        ]
    ]
];
