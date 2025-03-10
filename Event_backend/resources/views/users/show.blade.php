@extends('layouts.app')

@section('content')
    <h1>Détails de l'Utilisateur</h1>
    <p><strong>Prénom:</strong> {{ $user->first_name }}</p>
    <p><strong>Nom:</strong> {{ $user->last_name }}</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Téléphone:</strong> {{ $user->phone }}</p>
    <p><strong>Rôle:</strong> {{ $user->role }}</p>
    @if ($user->role === 'organisateur')
        <p><strong>Nom de la société:</strong> {{ $user->organisateur->nom_societe }}</p>
        <p><strong>Site web:</strong> {{ $user->organisateur->site_web }}</p>
        <p><strong>Réseau social:</strong> {{ $user->organisateur->reseau_social }}</p>
        <p><strong>Biographie:</strong> {{ $user->organisateur->biographie }}</p>
    @elseif ($user->role === 'participant')
        <p><strong>Date de naissance:</strong> {{ $user->participant->date_naissance }}</p>
        <p><strong>Adresse:</strong> {{ $user->participant->adresse }}</p>
    @endif
    <a href="{{ route('users.index') }}" class="btn btn-secondary">Retour</a>
@endsection