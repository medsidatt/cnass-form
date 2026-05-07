@extends('errors.layout', ['title' => 'Trop de requêtes'])
@section('code', '429')
@section('title', 'Trop de tentatives')
@section('message', "Vous avez effectué trop de requêtes en peu de temps. Veuillez patienter quelques instants avant de réessayer.")
