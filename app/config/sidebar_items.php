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
                ['label' => 'Calcul de paie', 'link' => '/paie/calcul'],
                ['label' => 'Primes', 'link' => '/paie/primes'],
                ['label' => 'IRSA', 'link' => '/paie/irsa']
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
            'icon' => 'users',
            'label' => 'Gestion des employés',
            'link' => '/employes',
            'active' => false,
            'sub_items' => [
                ['label' => 'Liste des employés', 'link' => '/employes/liste'],
                ['label' => 'Ajouter un employé', 'link' => '/employes/ajouter'],
                ['label' => 'Contrats', 'link' => '/employes/contrats'],
                ['label' => 'Historique', 'link' => '/employes/historique']
            ]
        ],
        // [
        //     'icon' => 'briefcase',
        //     'label' => 'Postes',
        //     'link' => '/postes',
        //     'active' => false,
        //     'sub_items' => [
        //         ['label' => 'Liste des postes', 'link' => '/postes/liste'],
        //         ['label' => 'Ajouter un poste', 'link' => '/postes/ajouter']
        //     ]
        // ],
        [
            'icon' => 'calendar',
            'label' => 'Gestion des congés',
            'link' => '/conges',
            'active' => false,
            'sub_items' => [
                ['label' => 'Demandes en attente', 'link' => '/conges/attente'],
                ['label' => 'Historique', 'link' => '/conges/historique'],
                ['label' => 'Types de congés', 'link' => '/conges/types'],
                ['label' => 'Planification', 'link' => '/conges/planification']
            ]
        ],
        // [
        //     'icon' => 'clock',
        //     'label' => 'Pointage',
        //     'link' => '/pointage',
        //     'active' => false,
        //     'sub_items' => [
        //         ['label' => 'Pointages du jour', 'link' => '/pointage/aujourd-hui'],
        //         ['label' => 'Historique', 'link' => '/pointage/historique'],
        //         ['label' => 'Heures supplémentaires', 'link' => '/pointage/heures-sup']
        //     ]
        // ],
        // [
        //     'icon' => 'alert-circle',
        //     'label' => 'Absences & Retards',
        //     'link' => '/absences',
        //     'active' => false,
        //     'sub_items' => [
        //         ['label' => 'Absences', 'link' => '/absences/liste'],
        //         ['label' => 'Retards', 'link' => '/retards/liste'],
        //         ['label' => 'Justificatifs', 'link' => '/absences/justificatifs']
        //     ]
        // ],
        [
            'icon' => 'dollar-sign',
            'label' => 'Paie & Primes',
            'link' => '/paie',
            'active' => false,
            'sub_items' => [
                ['label' => 'Calcul de paie', 'link' => '/paie/calcul'],
                ['label' => 'Primes', 'link' => '/paie/primes'],
                ['label' => 'IRSA', 'link' => '/paie/irsa'],
                ['label' => 'Historique', 'link' => '/paie/historique']
            ]
        ],
        // [
        //     'icon' => 'file-text',
        //     'label' => 'Documents',
        //     'link' => '/documents',
        //     'active' => false,
        //     'sub_items' => [
        //         ['label' => 'Tous les documents', 'link' => '/documents/liste'],
        //         ['label' => 'Ajouter document', 'link' => '/documents/ajouter']
        //     ]
        // ],
        // [
        //     'icon' => 'settings',
        //     'label' => 'Paramètres RH',
        //     'link' => '/parametres',
        //     'active' => false,
        //     'sub_items' => [
        //         ['label' => 'Départements', 'link' => '/parametres/departements'],
        //         ['label' => 'Jours fériés', 'link' => '/parametres/jours-feries'],
        //         ['label' => 'Horaires de travail', 'link' => '/parametres/horaires']
        //     ]
        // ],
        [
            'icon' => 'calendar',
            'label' => 'Calendrier',
            'link' => '/calendrier',
            'active' => false,
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
            'icon' => 'users',
            'label' => 'Équipe de production',
            'link' => '/production/equipe',
            'active' => false,
            'sub_items' => [
                ['label' => 'Liste des employés', 'link' => '/production/equipe/liste'],
                ['label' => 'Planning', 'link' => '/production/equipe/planning'],
                ['label' => 'Affectations', 'link' => '/production/equipe/affectations']
            ]
        ],
        [
            'icon' => 'clipboard',
            'label' => 'Ordres de production',
            'link' => '/production/ordres',
            'active' => false,
            'sub_items' => [
                ['label' => 'Nouveaux ordres', 'link' => '/production/ordres/nouveaux'],
                ['label' => 'En cours', 'link' => '/production/ordres/en-cours'],
                ['label' => 'Terminés', 'link' => '/production/ordres/termines']
            ]
        ],
        [
            'icon' => 'clock',
            'label' => 'Pointage équipe',
            'link' => '/production/pointage',
            'active' => false,
            'sub_items' => [
                ['label' => 'Pointages du jour', 'link' => '/production/pointage/aujourd-hui'],
                ['label' => 'Historique', 'link' => '/production/pointage/historique']
            ]
        ],
        [
            'icon' => 'calendar',
            'label' => 'Congés équipe',
            'link' => '/production/conges',
            'active' => false,
            'sub_items' => [
                ['label' => 'Demandes en attente', 'link' => '/production/conges/attente'],
                ['label' => 'Calendrier équipe', 'link' => '/production/conges/calendrier']
            ]
        ],
        [
            'icon' => 'alert-circle',
            'label' => 'Incidents',
            'link' => '/production/incidents',
            'active' => false,
            'sub_items' => [
                ['label' => 'Signaler incident', 'link' => '/production/incidents/signaler'],
                ['label' => 'Liste incidents', 'link' => '/production/incidents/liste']
            ]
        ],
        [
            'icon' => 'bar-chart-2',
            'label' => 'Rapports production',
            'link' => '/production/rapports',
            'active' => false,
            'sub_items' => [
                ['label' => 'Performance', 'link' => '/production/rapports/performance'],
                ['label' => 'Production journalière', 'link' => '/production/rapports/journaliere']
            ]
        ]
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
            'icon' => 'shopping-cart',
            'label' => 'Achats',
            'link' => '/achats',
            'active' => false,
            'sub_items' => [
                ['label' => 'Bons de commande', 'link' => '/achats/commandes'],
                ['label' => 'Fournisseurs', 'link' => '/achats/fournisseurs'],
                ['label' => 'Réceptions', 'link' => '/achats/receptions'],
                ['label' => 'Historique', 'link' => '/achats/historique']
            ]
        ],
        [
            'icon' => 'trending-up',
            'label' => 'Ventes',
            'link' => '/ventes',
            'active' => false,
            'sub_items' => [
                ['label' => 'Devis', 'link' => '/ventes/devis'],
                ['label' => 'Commandes', 'link' => '/ventes/commandes'],
                ['label' => 'Clients', 'link' => '/ventes/clients'],
                ['label' => 'Factures', 'link' => '/ventes/factures']
            ]
        ],
        [
            'icon' => 'users',
            'label' => 'Équipe commerciale',
            'link' => '/commercial/equipe',
            'active' => false,
            'sub_items' => [
                ['label' => 'Liste équipe', 'link' => '/commercial/equipe/liste'],
                ['label' => 'Objectifs', 'link' => '/commercial/equipe/objectifs'],
                ['label' => 'Performance', 'link' => '/commercial/equipe/performance']
            ]
        ],
        [
            'icon' => 'bar-chart-2',
            'label' => 'Statistiques',
            'link' => '/commercial/statistiques',
            'active' => false,
            'sub_items' => [
                ['label' => 'CA & Marges', 'link' => '/commercial/statistiques/ca'],
                ['label' => 'Rapports mensuels', 'link' => '/commercial/statistiques/mensuels']
            ]
        ],
        [
            'icon' => 'calendar',
            'label' => 'Congés équipe',
            'link' => '/commercial/conges',
            'active' => false,
            'sub_items' => [
                ['label' => 'Demandes en attente', 'link' => '/commercial/conges/attente'],
                ['label' => 'Calendrier', 'link' => '/commercial/conges/calendrier']
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
            'icon' => 'package',
            'label' => 'Stock',
            'link' => '/stock',
            'active' => false,
            'sub_items' => [
                ['label' => 'État du stock', 'link' => '/stock/etat'],
                ['label' => 'Articles', 'link' => '/stock/articles'],
                ['label' => 'Mouvements', 'link' => '/stock/mouvements'],
                ['label' => 'Inventaire', 'link' => '/stock/inventaire']
            ]
        ],
        [
            'icon' => 'alert-triangle',
            'label' => 'Alertes stock',
            'link' => '/stock/alertes',
            'active' => false,
            'sub_items' => [
                ['label' => 'Stock minimum', 'link' => '/stock/alertes/minimum'],
                ['label' => 'Ruptures', 'link' => '/stock/alertes/ruptures'],
                ['label' => 'Péremption', 'link' => '/stock/alertes/peremption']
            ]
        ],
        [
            'icon' => 'truck',
            'label' => 'Entrées/Sorties',
            'link' => '/stock/operations',
            'active' => false,
            'sub_items' => [
                ['label' => 'Entrées stock', 'link' => '/stock/operations/entrees'],
                ['label' => 'Sorties stock', 'link' => '/stock/operations/sorties'],
                ['label' => 'Transferts', 'link' => '/stock/operations/transferts']
            ]
        ],
        [
            'icon' => 'map-pin',
            'label' => 'Emplacements',
            'link' => '/stock/emplacements',
            'active' => false,
            'sub_items' => [
                ['label' => 'Zones de stockage', 'link' => '/stock/emplacements/zones'],
                ['label' => 'Rayonnages', 'link' => '/stock/emplacements/rayonnages']
            ]
        ],
        [
            'icon' => 'users',
            'label' => 'Équipe magasin',
            'link' => '/stock/equipe',
            'active' => false,
            'sub_items' => [
                ['label' => 'Liste équipe', 'link' => '/stock/equipe/liste'],
                ['label' => 'Planning', 'link' => '/stock/equipe/planning']
            ]
        ],
        [
            'icon' => 'file-text',
            'label' => 'Rapports stock',
            'link' => '/stock/rapports',
            'active' => false,
            'sub_items' => [
                ['label' => 'Valorisation', 'link' => '/stock/rapports/valorisation'],
                ['label' => 'Rotation', 'link' => '/stock/rapports/rotation']
            ]
        ]
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
            'icon' => 'archive',
            'label' => 'Immobilisations',
            'link' => '/immobilisations',
            'active' => false,
            'sub_items' => [
                ['label' => 'Liste des biens', 'link' => '/immobilisations/liste'],
                ['label' => 'Ajouter un bien', 'link' => '/immobilisations/ajouter'],
                ['label' => 'Catégories', 'link' => '/immobilisations/categories']
            ]
        ],
        [
            'icon' => 'trending-down',
            'label' => 'Amortissements',
            'link' => '/immobilisations/amortissements',
            'active' => false,
            'sub_items' => [
                ['label' => 'Calcul amortissement', 'link' => '/immobilisations/amortissements/calcul'],
                ['label' => 'Tableau amortissement', 'link' => '/immobilisations/amortissements/tableau'],
                ['label' => 'Historique', 'link' => '/immobilisations/amortissements/historique']
            ]
        ],
        [
            'icon' => 'tool',
            'label' => 'Maintenance',
            'link' => '/immobilisations/maintenance',
            'active' => false,
            'sub_items' => [
                ['label' => 'Planning maintenance', 'link' => '/immobilisations/maintenance/planning'],
                ['label' => 'Interventions', 'link' => '/immobilisations/maintenance/interventions'],
                ['label' => 'Coûts maintenance', 'link' => '/immobilisations/maintenance/couts']
            ]
        ],
        [
            'icon' => 'map-pin',
            'label' => 'Localisation',
            'link' => '/immobilisations/localisation',
            'active' => false,
            'sub_items' => [
                ['label' => 'Sites', 'link' => '/immobilisations/localisation/sites'],
                ['label' => 'Affectations', 'link' => '/immobilisations/localisation/affectations'],
                ['label' => 'Transferts', 'link' => '/immobilisations/localisation/transferts']
            ]
        ],
        [
            'icon' => 'trash-2',
            'label' => 'Cessions & Rebuts',
            'link' => '/immobilisations/cessions',
            'active' => false,
            'sub_items' => [
                ['label' => 'Cessions', 'link' => '/immobilisations/cessions/liste'],
                ['label' => 'Mises au rebut', 'link' => '/immobilisations/cessions/rebuts']
            ]
        ],
        [
            'icon' => 'file-text',
            'label' => 'Rapports',
            'link' => '/immobilisations/rapports',
            'active' => false,
            'sub_items' => [
                ['label' => 'État du patrimoine', 'link' => '/immobilisations/rapports/patrimoine'],
                ['label' => 'Inventaire', 'link' => '/immobilisations/rapports/inventaire'],
                ['label' => 'Valeur nette comptable', 'link' => '/immobilisations/rapports/vnc']
            ]
        ],
        [
            'icon' => 'users',
            'label' => 'Équipe',
            'link' => '/immobilisations/equipe',
            'active' => false,
            'sub_items' => [
                ['label' => 'Liste équipe', 'link' => '/immobilisations/equipe/liste'],
                ['label' => 'Tâches', 'link' => '/immobilisations/equipe/taches']
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
                ['label' => 'Faire une demande', 'link' => '/employe/conges/demande'],
                ['label' => 'Mes demandes', 'link' => '/employe/conges/liste']
            ]
        ],
        [
            'icon' => 'clock',
            'label' => 'Mon pointage',
            'link' => '/employe/pointage',
            'active' => false,
            'sub_items' => [
                ['label' => 'Pointer', 'link' => '/employe/pointage/pointer'],
                ['label' => 'Historique', 'link' => '/employe/pointage/historique']
            ]
        ],
        [
            'icon' => 'file-text',
            'label' => 'Mes documents',
            'link' => '/employe/documents',
            'active' => false,
            'sub_items' => []
        ],
        [
            'icon' => 'dollar-sign',
            'label' => 'Mes bulletins de paie',
            'link' => '/employe/bulletins',
            'active' => false,
            'sub_items' => []
        ]
    ]
];
