<form method="POST" action="{{ route('password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">

    <label>Email</label>
    <input type="email" name="email" value="{{ $request->email }}" required autofocus>

    <label>Nueva Contraseña</label>
    <input type="password" name="password" required>

    <label>Confirmar Contraseña</label>
    <input type="password" name="password_confirmation" required>

    <button type="submit">Establecer Contraseña</button>
</form>