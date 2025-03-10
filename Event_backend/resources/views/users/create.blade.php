@extends('layouts.app')

@section('content')
    <h1>Créer un Utilisateur</h1>
    <form action="{{ route('users.store') }}" method="POST">
        @csrf
        <div>
            <label for="first_name">Prénom:</label>
            <input type="text" name="first_name" id="first_name" required>
        </div>
        <div>
            <label for="last_name">Nom:</label>
            <input type="text" name="last_name" id="last_name" required>
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>
        </div>
        <div>
            <label for="password">Mot de passe:</label>
            <input type="password" name="password" id="password" required>
        </div>
        <div>
            <label for="phone">Téléphone:</label>
            <input type="text" name="phone" id="phone" required>
        </div>
        <div>
            <label for="role">Rôle:</label>
            <select name="role" id="role" required>
                <option value="participant">Participant</option>
                <option value="organisateur">Organisateur</option>
            </select>
        </div>
        <div id="organisateur_fields" style="display:none;">
            <label for="nom_societe">Nom de la société:</label>
            <input type="text" name="nom_societe" id="nom_societe">
            <label for="site_web">Site web:</label>
            <input type="text" name="site_web" id="site_web">
            <label for="reseau_social">Réseau social:</label>
            <input type="text" name="reseau_social" id="reseau_social">
            <label for="biographie">Biographie:</label>
            <textarea name="biographie" id="biographie"></textarea>
        </div>
        <div id="participant_fields" style="display:none;">
            <label for="date_naissance">Date de naissance:</label>
            <input type="date" name="date_naissance" id="date_naissance">
            <label for="adresse">Adresse:</label>
            <input type="text" name="adresse" id="adresse">
        </div>
        <button type="submit">Créer</button>
    </form>

    <script>
        document.getElementById('role').addEventListener('change', function() {
            var role = this.value;
            if (role === 'organisateur') {
                document.getElementById('organisateur_fields').style.display = 'block';
                document.getElementById('participant_fields').style.display = 'none';
            } else if (role === 'participant') {
                document.getElementById('participant_fields').style.display = 'block';
                document.getElementById('organisateur_fields').style.display = 'none';
            } else {
                document.getElementById('organisateur_fields').style.display = 'none';
                document.getElementById('participant_fields').style.display = 'none';
            }
        });
    </script>
@endsection