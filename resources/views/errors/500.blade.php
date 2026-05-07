@extends('errors.layout', ['title' => 'Erreur serveur'])
@section('code', '500')
@section('title', 'Erreur serveur')
@section('message', "Une erreur inattendue s'est produite. Notre équipe a été notifiée. Veuillez réessayer dans quelques instants.")
