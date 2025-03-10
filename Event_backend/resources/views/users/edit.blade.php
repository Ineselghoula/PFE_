@extends('layouts.app')

@section('content')
    <h1>Éditer l'Utilisateur</h1>
    <form action="{{ route('users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label for="first_name">Prénom:</label>
            <input type="text" name="first_name" id="first_name" value="{{ $user->first_name }}" required>
        </div>
        <div>
            <label for="last_name">Nom:</label>
            <input type="text" name="last_name" id="last_name" value="{{ $user->last_name }}" required>
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="{{ $user->email }}" required>
        </div>
        <div>
            <label for="phone">Téléphone:</label>
            <input type="text" name="phone" id="phone" value="{{ $user->phone }}" required>
        </div>
        <button type="submit">Mettre à jour</button>
    </form>
@endsection