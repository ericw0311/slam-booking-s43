<?php
namespace App\Entity;

class Constants
{
    const LIST_DEFAULT_NUMBER_COLUMNS = 2;
    const LIST_DEFAULT_NUMBER_LINES = 20;

    // Nombre maximum de lignes et de colonnes dans l'affichage des utilisateurs dans la page des groupes d'utilisateurs
    const UG_USER_MAX_NUMBER_COLUMNS = 4;
    const UG_USER_MAX_NUMBER_LINES = 8;


    const PLANNING_DEFAULT_NUMBER_COLUMNS = 1; // Contrôle le nombre de jours affichés dans le planning
    const PLANNING_DEFAULT_NUMBER_LINES = 1;

    const PLANNING_MAX_NUMBER_COLUMNS = 4;
    const PLANNING_MAX_NUMBER_LINES = 4;

    const PLANNING_MIN_NUMBER_PLANIFICATION_LIST = 21; // Nombre de planifications minimum pour accéder au planning via la liste.

    const NUMBER_LINES_BEFORE_AFTER_UPDATE = 3; // Nombre de lignes a afficher avant et apres la ligne mise a jour (cas de multilignes type creneaux horaires)
    const NUMBER_LINES_MINI_DUAL_BUTTON_LIST = 8; // Nombre de lignes minimum a partir duquel on affiche la serie de bouttons actions avant et après la liste en question

    const RESOURCE_TYPE = array('PLACE', 'VEHICLE', 'TOOL', 'SPORT', 'USER');

    const DISPLAYED_RESOURCE_TYPE = array('PLACE', 'VEHICLE', 'TOOL', 'SPORT');

    const RESOURCE_TYPE_ICON = array(
            'PLACE' => 'map-marker',
            'VEHICLE' => 'bus',
            'TOOL' => 'television',
            'SPORT' => 'futbol-o',
            'USER' => 'user-o'
        );

    const RESOURCE_CLASSIFICATION = array(
            'PLACE' => array('ROOM', 'FLAT', 'HOUSE', 'MOBILE-HOME', 'TENT'),
            'VEHICLE' => array('CAR', 'TRUCK', 'TRACTOR', 'BIKE', 'MOTORBIKE', 'BOAT', 'PLANE', 'GLIDER'),
            'TOOL' => array('COMPUTER', 'CAMERA', 'PROJECTOR'),
            'SPORT' => array('COURT', 'PITCH', 'GYMNASIUM', 'HORSE'),
            'USER' => array('TEACHER', 'CONTRACTOR', 'DOCTOR', 'DENTIST')
        );

    const RESOURCE_CLASSIFICATION_ACTIVE = array(
            'PLACE' => array('ROOM', 'HOUSE'),
            'VEHICLE' => array('CAR'),
            'TOOL' => array('COMPUTER'),
            'SPORT' => array('COURT', 'GYMNASIUM'),
            'USER' => array('TEACHER')
        );

    const RESOURCE_CLASSIFICATION_ICON = array(
            'BIKE' => 'bike',
            'BOAT' => 'boat',
            'CAMERA' => 'camera',
            'CAR' => 'car',
            'COMPUTER' => 'computer',
            'CONTRACTOR' => 'contractor',
            'COURT' => 'court',
            'DENTIST' => 'dentist',
            'DOCTOR' => 'doctor',
            'FLAT' => 'flat',
            'GLIDER' => 'glider',
            'GYMNASIUM' => 'gymnasium',
            'HORSE' => 'horse',
            'HOUSE' => 'house',
            'MOBILE-HOME' => 'mobile-home',
            'MOTORBIKE' => 'motorbike',
            'PITCH' => 'pitch',
            'PLANE' => 'plane',
            'PROJECTOR' => 'projector',
            'ROOM' => 'room',
            'TEACHER' => 'teacher',
            'TENT' => 'tent',
            'TRACTOR' => 'tractor',
            'TRUCK' => 'truck'
        );

    const WEEK_DAY_CODE = array(
            1 => 'MON',
            2 => 'TUE',
            3 => 'WED',
            4 => 'THU',
            5 => 'FRI',
            6 => 'SAT',
            7 => 'SUN');

    const MAXIMUM_NUMBER_BOOKING_LINES = 50; // Nombre maximum de lignes dans une réservation

    const MAXIMUM_NUMBER_BOOKING_DATES_DISPLAYED = 5; // Nombre maximum de dates affichées (utilisé pour la mise a jour des périodes de début et de fin des réservations)

    // Couleurs d'affichage des réservations dans le calendrier
    const CALENDAR_COLOR = array('success' => '#dff0d8', 'warning' => '#fcf8e3', 'info' => '#d9edf7', 'danger' => '#f2dede');

    // Valeurs par défaut d'envoi de mail lors de la saisie/mise à jour/suppression des réservations (aux administrateurs du dossier et aux utilisateurs de la réservation)
    const BOOKING_MAIL_ADMINISTRATOR = true;
    const BOOKING_MAIL_USER = false;

    // Valeurs par défaut de réstriction de période de réservation avant la date du jour.
    const BOOKING_PERIOD_BEFORE = true;
    const BOOKING_PERIOD_BEFORE_TYPE = 'DAY';
    const BOOKING_PERIOD_BEFORE_NUMBER = 1;

    // Valeurs par défaut de réstriction de période de réservation après la date du jour.
    const BOOKING_PERIOD_AFTER = false;
    const BOOKING_PERIOD_AFTER_TYPE = 'WEEK';
    const BOOKING_PERIOD_AFTER_NUMBER = 2;
}
