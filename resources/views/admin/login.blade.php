<x-admin.layout title="Вход">
    <form method="post" action="{{ route('admin.login.attempt') }}" class="card narrow">
        @csrf
        <h1>Вход в админку</h1>
        <label>E-mail
            <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
        </label>
        <label>Пароль
            <input type="password" name="password" required autocomplete="current-password">
        </label>
        @error('email')
            <p class="error">{{ $message }}</p>
        @enderror
        <button type="submit">Войти</button>
    </form>
</x-admin.layout>