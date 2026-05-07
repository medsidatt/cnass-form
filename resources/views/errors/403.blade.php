@extends('errors.layout', ['title' => 'Accès refusé'])
@section('code', '403')
@section('title', 'Accès refusé')
@section('message', "Vous n'avez pas les autorisations nécessaires pour accéder à cette ressource.")
