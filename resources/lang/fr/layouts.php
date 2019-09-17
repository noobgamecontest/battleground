<?php

return [
    'common' => [
        'name' => 'Nom',
        'begin' => 'Début',
        'subscribe' => 'Inscription',
        'register' => 'Enregistrer',
        'action' => 'Action',
        'id' => 'Identifiant',
        'team' => 'Equipe',
        'teams' => 'Equipes',
        'team_name' => "Nom d'équipe",
        'delete' => 'Supprimer',
        'platform' => 'Plate-forme',
        'game' => 'Jeu',
        'number_of_teams' => 'Nombre d\'équipe(s)',
        'state' => 'Statut',
    ],

    'tournaments' => [
        'index' => [
            'title' => 'Liste des tournois',
        ],
        'show' => [
            'begin' => 'Début du tournoi le :date',
        ],
        'subscribe' => [
            'success' => "L'équipe a été inscrite avec succès",
            'error' => [
                'name_exists' => "Ce nom d'équipe existe déjà",
                'max_slots' => "Toutes les places sont déjà occupées pour ce tournois",
            ]
        ],
        'unsubscribe' => [
            'success' => "L'équipe a été désinscrite avec succès",
        ]
    ],
];
